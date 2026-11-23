<?php

namespace Tests\Feature;

use App\Jobs\GenerateProductModel;
use App\Models\User;
use App\Services\Model3D\GradioClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\Support\BuildsShop;
use Tests\TestCase;

class Model3DTest extends TestCase
{
    use RefreshDatabase, BuildsShop;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Storage::fake('public');
        config(['model3d.space' => 'stabilityai/TripoSR', 'model3d.token' => null, 'model3d.api_prefix' => null]);
    }

    public function test_space_url_is_derived_from_space_id(): void
    {
        $this->assertSame('https://stabilityai-triposr.hf.space', (new GradioClient('stabilityai/TripoSR'))->baseUrl());
        $this->assertSame('https://x.example.com', (new GradioClient('https://x.example.com/'))->baseUrl());
    }

    public function test_sse_stream_parsing(): void
    {
        $ok = "event: generating\ndata: null\n\nevent: complete\ndata: [{\"path\":\"/tmp/a.glb\"}]\n\n";
        $this->assertSame([['path' => '/tmp/a.glb']], GradioClient::resultFromStream($ok));

        $this->expectExceptionMessage('kuota habis');
        GradioClient::resultFromStream("event: error\ndata: \"kuota habis\"\n\n");
    }

    public function test_incomplete_stream_is_an_error(): void
    {
        $this->expectExceptionMessage('tidak lengkap');
        GradioClient::resultFromStream("event: heartbeat\ndata: null\n\n");
    }

    protected function fakeSpace(string $glb = "glTF\x02\x00\x00\x00binary"): void
    {
        Http::fake([
            '*/config' => Http::response(['api_prefix' => '/gradio_api', 'dependencies' => [['api_name' => 'preprocess'], ['api_name' => 'generate']]]),
            '*/gradio_api/upload' => Http::response(['/tmp/gradio/in.png']),
            '*/gradio_api/call/preprocess' => Http::response(['event_id' => 'e1']),
            '*/gradio_api/call/preprocess/e1' => Http::response("event: complete\ndata: [{\"path\":\"/tmp/gradio/pre.png\",\"url\":\"https://stabilityai-triposr.hf.space/gradio_api/file=/tmp/gradio/pre.png\"}]\n\n"),
            '*/gradio_api/call/generate' => Http::response(['event_id' => 'e2']),
            '*/gradio_api/call/generate/e2' => Http::response("event: complete\ndata: [{\"path\":\"/tmp/m.obj\",\"orig_name\":\"m.obj\"},{\"path\":\"/tmp/m.glb\",\"orig_name\":\"m.glb\",\"url\":\"https://stabilityai-triposr.hf.space/gradio_api/file=/tmp/m.glb\"}]\n\n"),
            '*/gradio_api/file=*' => Http::response($glb),
        ]);
    }

    public function test_job_builds_model_from_product_photo(): void
    {
        $this->fakeSpace();
        Storage::disk('public')->put('products/foto.png', 'fake-png');
        $product = $this->makeProduct(['image' => 'products/foto.png']);

        (new GenerateProductModel($product->id))->handle(app(\App\Services\Model3D\ProductModelGenerator::class));

        $product->refresh();
        $this->assertSame('ready', $product->model_status);
        Storage::disk('public')->assertExists($product->model_path);
        $this->assertTrue($product->hasModel());

        // langkah 2 harus menerima keluaran langkah 1 (bukan foto asli)
        Http::assertSent(fn ($r) => str_ends_with($r->url(), '/call/generate')
            && $r['data'][0]['path'] === '/tmp/gradio/pre.png' && $r['data'][1] === 256);
    }

    public function test_job_failure_is_recorded_not_thrown_to_admin(): void
    {
        $this->fakeSpace('<html>bukan glb</html>');
        Storage::disk('public')->put('products/foto.png', 'fake-png');
        $product = $this->makeProduct(['image' => 'products/foto.png']);

        $job = new GenerateProductModel($product->id);
        try {
            $job->handle(app(\App\Services\Model3D\ProductModelGenerator::class));
            $this->fail('Seharusnya melempar exception');
        } catch (\Throwable $e) {
            $job->failed($e);
        }

        $product->refresh();
        $this->assertSame('failed', $product->model_status);
        $this->assertStringContainsString('GLB', $product->model_error);
        $this->assertFalse($product->hasModel());
    }

    public function test_admin_can_queue_generation_and_guests_cannot(): void
    {
        Bus::fake();
        $product = $this->makeProduct();

        $this->post(route('admin.products.model.generate', $product->id))->assertRedirect(route('admin.login'));
        $this->actingAs(User::factory()->create())->post(route('admin.products.model.generate', $product->id))->assertRedirect(route('admin.login'));

        $this->actingAs($this->admin())->post(route('admin.products.model.generate', $product->id))->assertSessionHas('success');

        Bus::assertDispatched(GenerateProductModel::class, fn ($j) => $j->productId === $product->id);
        $this->assertSame('queued', $product->fresh()->model_status);
    }

    public function test_admin_can_upload_valid_glb_only(): void
    {
        $product = $this->makeProduct();
        $admin = $this->admin();

        $bad = UploadedFile::fake()->createWithContent('x.glb', 'bukan glb');
        $this->actingAs($admin)->post(route('admin.products.model.upload', $product->id), ['model' => $bad])->assertSessionHasErrors('model');

        $good = UploadedFile::fake()->createWithContent('x.glb', "glTF\x02\x00\x00\x00abc");
        $this->actingAs($admin)->post(route('admin.products.model.upload', $product->id), ['model' => $good])->assertSessionHas('success');

        $this->assertTrue($product->fresh()->hasModel());
    }

    public function test_product_page_uses_viewer_only_when_model_is_ready(): void
    {
        $product = $this->makeProduct();
        $this->get(route('products.show', $product))->assertOk()->assertDontSee('<model-viewer', false);

        $product->forceFill(['model_status' => 'ready', 'model_path' => 'models/1.glb'])->save();
        $this->get(route('products.show', $product))->assertOk()->assertSee('<model-viewer', false)->assertSee('Putar 360°');
    }

    public function test_storefront_pages_render(): void
    {
        $this->makeProduct();
        $this->get('/')->assertOk()->assertDontSee('Player One');
        $this->get(route('home'))->assertOk();
    }
}
