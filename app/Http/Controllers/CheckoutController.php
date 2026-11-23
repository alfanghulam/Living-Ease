<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Services\BiteshipService;
use App\Support\Phone;
use App\Services\MidtransService;
use App\Services\PromoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use RuntimeException;

class CheckoutController extends Controller
{
    /**
     * Handle "Buy Now" – add a single product to the temporary checkout session.
     */
    public function buyNow(Request $request, Product $product)
    {
        if (! $product->is_active) {
            return back()->withErrors(['stock' => "{$product->title} sudah tidak dijual."]);
        }

        $qty = max(1, (int) $request->input('qty', 1));

        if ($product->stock < $qty) {
            return back()->withErrors(['stock' => "Stok {$product->title} tinggal {$product->stock}."]);
        }

        // Store temporary checkout data.
        session(['buy_now' => ['product_id' => $product->id, 'qty' => $qty]]);

        return redirect()->route('checkout.show');
    }

    /**
     * Show checkout page – works for both "Buy Now" and normal cart checkout.
     */
    public function show(Request $request, MidtransService $midtrans)
    {
        // Last used phone number for this user (if any).
        $lastPhone = Order::where('user_id', Auth::id())
            ->whereNotNull('phone')
            ->latest()
            ->value('phone');

        // Available payment channels from Midtrans (if configured).
        $channels = $midtrans->isConfigured() ? $midtrans->availableChannels() : [];

        // Determine items & total.
        if (session()->has('buy_now')) {
            $bn = session('buy_now');
            $product = Product::findOrFail($bn['product_id']);
            $items = collect([(object) ['product' => $product, 'qty' => $bn['qty']]]);
            $total = $product->final_price * $bn['qty'];
        } else {
            $items = CartItem::with('product')
                ->where('user_id', Auth::id())
                ->get();

            if ($items->isEmpty()) {
                return redirect()->route('cart.index')
                    ->with('success', 'Keranjang masih kosong.');
            }

            foreach ($items as $item) {
                if (! $item->product || ! $item->product->is_active) {
                    return redirect()->route('cart.index')
                        ->with('error', "Produk '{$item->product?->title}' sudah tidak dijual.");
                }
                if ($item->product->stock < $item->qty) {
                    return redirect()->route('cart.index')
                        ->with('error', "Stok '{$item->product->title}' tersisa {$item->product->stock}. Silakan sesuaikan jumlah di keranjang.");
                }
            }

            $total = $items->sum(fn ($i) => $i->qty * $i->product->final_price);
        }

        // Compute shipping rates via Biteship (if API key configured).
        $rates = [];
        if (config('services.biteship.api_key')) {
            $weight = $items->sum(fn ($i) => ($i->product->weight_grams ?? 0) * $i->qty);
            $payload = [
                'origin' => Setting::originReady(),
                'destination' => [
                    'address' => $request->input('address') ?? '',
                    'city' => $request->input('city') ?? '',
                    'state' => $request->input('state') ?? '',
                    'zip' => $request->input('zip') ?? '',
                ],
                'weight' => $weight,
            ];
            try {
                $rates = app(BiteshipService::class)->getRates($payload);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        // Fallback dummy rates when Biteship does not return data.
        if (empty($rates)) {
            $rates = [
                ['courier' => 'JNE', 'service_code' => 'REG', 'cost' => 10000, 'estimated' => '2-3 hari'],
                ['courier' => 'JNE', 'service_code' => 'YES', 'cost' => 20000, 'estimated' => '1 hari'],
                ['courier' => 'POS', 'service_code' => 'Standard', 'cost' => 8000, 'estimated' => '3-5 hari'],
            ];
        }

        return view('checkout.show', [
            'items'       => $items,
            'total'       => $total,
            'rates'       => $rates,
            'channels'    => $channels,
            'lastPhone'   => $lastPhone,
        ]);
    }

    /**
     * Store the submitted checkout form.
     */
    public function store(Request $request)
    {
        $midtrans = app(MidtransService::class);
        $channels = $midtrans->isConfigured() ? $midtrans->availableChannels() : [];
        $allowed = collect($channels)->flatMap(fn ($options) => array_keys($options))->all();

        // Normalise phone number.
        $request->merge(['phone' => Phone::normalize($request->input('phone'))]);

        $data = $request->validate([
            'full_name'       => 'required|string|max:255',
            'email'           => 'required|email',
            'phone'           => ['required', 'regex:/^08\d{8,11}$/'],
            'address'         => 'required|string|max:255',
            'city'            => 'required|string|max:255',
            'state'           => 'required|string|max:255',
            'zip'             => 'required|string|max:10',
            'promo_code'      => 'nullable|string|max:40',
            'payment_channel' => $allowed ? ['required', Rule::in($allowed)] : ['nullable'],
            'courier'         => ['nullable', 'string', 'max:50'],
            'service_code'    => ['nullable', 'string', 'max:50'],
            'shipping_fee'    => ['nullable', 'numeric', 'min:0'],
            'needs_shipping'  => ['boolean'],
        ], [
            'phone.required' => 'Nomor HP wajib diisi (dibutuhkan kurir).',
            'phone.regex'    => 'Nomor HP tidak valid. Contoh: 081234567890.',
            'payment_channel.required' => 'Pilih metode pembayaran dulu.',
            'payment_channel.in'       => 'Metode pembayaran yang dipilih tidak tersedia.',
        ]);

        // Conditional validation for shipping fields.
        if ($request->boolean('needs_shipping')) {
            $request->validate([
                'courier'      => ['required', 'string', 'max:50'],
                'service_code' => ['required', 'string', 'max:50'],
                'shipping_fee' => ['required', 'numeric', 'min:0'],
            ]);
        }

        // Promo code validation.
        if ($message = app(PromoService::class)->error($data['promo_code'] ?? null, Auth::id())) {
            return back()->withInput()->withErrors(['promo_code' => $message]);
        }

        $data['payment_channel'] = $allowed ? $data['payment_channel'] : null;

        // "Buy Now" flow.
        if (session()->has('buy_now')) {
            $bn = session('buy_now');
            $product = Product::findOrFail($bn['product_id']);

            if (! $product->is_active) {
                session()->forget('buy_now');
                return redirect()->route('home')
                    ->with('error', "{$product->title} sudah tidak dijual.");
            }

            $qty = $bn['qty'];

            if ($product->stock < $qty) {
                return back()->withErrors(['stock' => "Stok {$product->title} tinggal {$product->stock}."]);
            }

            try {
                $order = $this->placeOrder($data, [['product_id' => $product->id, 'qty' => $qty]]);
            } catch (RuntimeException $e) {
                return back()->withErrors(['stock' => $e->getMessage()]);
            }

            session()->forget('buy_now');
            return $this->afterOrder($order);
        }

        // Normal cart checkout.
        $items = CartItem::with('product')
            ->where('user_id', Auth::id())
            ->get();

        try {
            $order = $this->placeOrder($data, $items->map(fn ($i) => [
                'product_id' => $i->product->id,
                'qty'        => $i->qty,
            ])->toArray());
        } catch (RuntimeException $e) {
            return back()->withInput()->withErrors(['stock' => $e->getMessage()]);
        }

        // Clear cart after successful order.
        CartItem::where('user_id', Auth::id())->delete();

        return $this->afterOrder($order);
    }

    /**
     * Create an order with the given validated data and line items.
     *
     * @param  array  $data   Validated checkout fields
     * @param  array  $lines  Array of ['product_id' => int, 'qty' => int]
     * @return Order
     */
    protected function placeOrder(array $data, array $lines): Order
    {
        $promoService = app(PromoService::class);
        $promoCode = $promoService->normalize($data['promo_code'] ?? '');

        // Calculate subtotal from actual products.
        $subtotal = 0;
        $orderItems = [];

        foreach ($lines as $line) {
            $product = Product::findOrFail($line['product_id']);

            if (! $product->is_active) {
                throw new RuntimeException("{$product->title} sudah tidak dijual.");
            }

            if ($product->stock < $line['qty']) {
                throw new RuntimeException("Stok {$product->title} tinggal {$product->stock}.");
            }

            $price = $product->final_price;
            $subtotal += $price * $line['qty'];

            $orderItems[] = [
                'product_id'    => $product->id,
                'qty'           => $line['qty'],
                'price'         => $price,
                'product_title' => $product->title,
                'product_image' => $product->image,
            ];
        }

        // Promo discount.
        $discount = 0;
        if ($promoCode !== '') {
            $discount = $promoService->discountFor($subtotal);
        }

        // Shipping fee.
        $shippingFee = (int) ($data['shipping_fee'] ?? 0);

        $total = max(1, $subtotal - $discount + $shippingFee);

        $order = Order::create([
            'user_id'         => Auth::id(),
            'full_name'       => $data['full_name'],
            'email'           => $data['email'],
            'phone'           => $data['phone'],
            'address'         => $data['address'],
            'city'            => $data['city'],
            'state'           => $data['state'],
            'zip'             => $data['zip'],
            'total'           => $total,
            'promo_code'      => $promoCode ?: null,
            'discount_amount' => $discount,
            'courier'         => $data['courier'] ?? null,
            'service_code'    => $data['service_code'] ?? null,
            'shipping_fee'    => $shippingFee ?: null,
            'payment_channel' => $data['payment_channel'] ?? null,
            'status'          => 'pending',
        ]);

        // Attach order items.
        foreach ($orderItems as $item) {
            $order->items()->create($item);
        }

        return $order;
    }

    /**
     * After an order is placed, redirect to payment or success page.
     */
    protected function afterOrder(Order $order)
    {
        $midtrans = app(MidtransService::class);

        if ($midtrans->isConfigured()) {
            return redirect()->route('payment.show', $order);
        }

        return redirect()->route('checkout.success', $order)
            ->with('success', 'Pesanan berhasil dibuat! Admin akan mengonfirmasi pembayaran.');
    }

    /**
     * Show the order success page.
     */
    public function success(Order $order, MidtransService $midtrans)
    {
        // Only allow the owner to view their order success page.
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Sinkronkan status pembayaran langsung dari Midtrans API jika masih menunggu bayar
        if ($order->isAwaitingPayment() && $midtrans->isConfigured()) {
            try {
                $midtrans->syncStatus($order);
                $order->refresh();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Midtrans syncStatus error: '.$e->getMessage(), ['order_id' => $order->id]);
            }
        }

        $order->loadMissing('items.product');

        $canPay = $order->isAwaitingPayment() && $midtrans->isConfigured();

        return view('checkout.success', compact('order', 'canPay'));
    }
}
