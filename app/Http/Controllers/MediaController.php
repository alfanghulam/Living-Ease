<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function show(string $path)
    {
        // Hanya nama file biasa; tolak "..", garis miring ganda, dan karakter aneh.
        abort_if(
            str_contains($path, '..') || str_contains($path, '//') || ! preg_match('#^[A-Za-z0-9_\-./]+$#', $path),
            404
        );

        $disk = Storage::disk('public');
        abort_unless($disk->exists($path), 404);

        $headers = ['Cache-Control' => 'public, max-age=86400'];

        if (str_ends_with(strtolower($path), '.glb')) {
            $headers['Content-Type'] = 'model/gltf-binary';
        }

        return $disk->response($path, null, $headers);
    }
}