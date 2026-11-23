<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransNotificationController extends Controller
{
    public function handle(Request $request, MidtransService $midtrans)
    {
        $result = $midtrans->applyPayload($request->all());

        if ($result['result'] === 'invalid_signature') {
            Log::warning('Midtrans: signature tidak valid', ['ip' => $request->ip()]);

            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Selalu 200 untuk order tak dikenal / nominal salah supaya Midtrans tidak mengulang terus.
        return response()->json(['message' => 'OK', 'result' => $result['result']]);
    }
}
