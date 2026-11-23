<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;



use Illuminate\Http\Request;
use App\Support\Phone;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::allCached();

        return view('admin.settings.index', compact('settings'));
    }

public function update(Request $request)
{
    $request->merge(['origin_phone' => Phone::normalize($request->input('origin_phone')) ?: null]);

    $data = $request->validate([
        'store_name' => ['required', 'string', 'max:100'],
        'store_phone' => ['nullable', 'string', 'max:40'],
        'store_address' => ['nullable', 'string', 'max:180'],
        'store_email' => ['nullable', 'email', 'max:120'],
        'promo_code' => ['nullable', 'string', 'max:40'],
        'promo_percent' => ['required', 'integer', 'min:0', 'max:100'],
        'promo_active' => ['nullable', 'boolean'],
        'chatbot_whatsapp' => ['nullable', 'string', 'max:30'],
        'origin_name' => ['nullable', 'string', 'max:100'],
        'origin_phone' => ['nullable', 'regex:/^08\d{8,11}$/'],
        'origin_address' => ['nullable', 'string', 'max:255'],
        'origin_city' => ['nullable', 'string', 'max:100'],
        'origin_province' => ['nullable', 'string', 'max:100'],
        'origin_postal_code' => ['nullable', 'digits:5'],
    ], [
        'origin_phone.regex' => 'Nomor HP gudang tidak valid. Contoh: 081234567890.',
        'origin_postal_code.digits' => 'Kode pos harus 5 angka.',
    ]);

    $data['promo_active'] = $request->boolean('promo_active') ? '1' : '0';

    foreach ($data as $key => $value) {
        Setting::put($key, $value === null ? null : (string) $value);
    }

    return back()->with('success', 'Pengaturan disimpan.');
}
}
