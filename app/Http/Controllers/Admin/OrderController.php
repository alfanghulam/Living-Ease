<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('q');

        $orders = Order::query()
            ->withSum('items', 'qty')
            ->withCount('items')
            ->status($status)
            ->search($search)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $counts = Order::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalAll = $counts->sum();

        return view('admin.orders.index', compact('orders', 'counts', 'totalAll', 'status', 'search'));
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'user']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', 'string', Rule::in(array_keys(Order::STATUS_LABELS))],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $target = $data['status'];

        if (! $order->canTransitionTo($target)) {
            return back()->with('error', "Status tidak bisa diubah dari “{$order->statusLabel()}” ke “".(Order::STATUS_LABELS[$target] ?? $target).'”.');
        }

        $order->loadMissing('items.product');

        DB::transaction(function () use ($order, $target, $data) {
            if ($target === 'paid') {
                if (! $order->stock_applied) {
                    foreach ($order->items as $item) {
                        $product = $item->product;
                        if ($product) {
                            $product->decrement('stock', min($product->stock, $item->qty));
                        }
                    }
                    $order->stock_applied = true;
                }
                $order->paid_at = now();
            }

            if ($target === 'shipped') {
                $order->shipped_at = now();
            }

            if ($target === 'completed') {
                $order->completed_at = now();
            }

            if ($target === 'cancelled') {
                if ($order->stock_applied) {
                    foreach ($order->items as $item) {
                        $item->product?->increment('stock', $item->qty);
                    }
                    $order->stock_applied = false;
                }
                $order->cancelled_at = now();
            }

            $order->status = $target;

            if (! empty($data['tracking_number'])) {
                $order->tracking_number = $data['tracking_number'];
            }

            if (! empty($data['note'])) {
                $order->note = trim(($order->note ? $order->note."\n" : '').'['.now()->format('d/m H:i').'] '.$data['note']);
            }

            $order->save();
        });

        return back()->with('success', "Pesanan #{$order->id} sekarang “{$order->fresh()->statusLabel()}”.");
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $rows = Order::query()
            ->withSum('items', 'qty')
            ->status($request->query('status'))
            ->search($request->query('q'))
            ->latest()
            ->get();

        $filename = 'pesanan-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');

            // BOM supaya Excel membaca UTF-8 dengan benar
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, ['ID', 'Tanggal', 'Nama', 'Email', 'Kota', 'Provinsi', 'Total', 'Status', 'Jumlah item', 'Resi']);

            foreach ($rows as $row) {
                fputcsv($out, [
                    '#'.$row->id,
                    $row->created_at?->format('Y-m-d H:i'),
                    $row->full_name,
                    $row->email,
                    $row->city,
                    $row->state,
                    $row->total,
                    $row->statusLabel(),
                    (int) ($row->items_sum_qty ?? 0),
                    $row->tracking_number ?? '',
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
