@extends('admin.layout')

@section('title', 'Pengaturan')
@section('crumb', 'Sistem / Pengaturan')

@section('content')
    <div class="ad-head">
        <div>
            <h1>Pengaturan <em>toko</em></h1>
            <p>Identitas, kontak, dan promo yang dipakai di storefront.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="vs-errors">
            @foreach ($errors->all() as $error)
                <span>{{ $error }}</span>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" class="ad-form">
        @csrf
        @method('PUT')

        <div class="ad-panel">
            <h2>Identitas toko</h2>

            <div class="ad-form__grid">
                <div class="vs-field">
                    <label for="store_name">Nama toko</label>
                    <input id="store_name" class="vs-input" type="text" name="store_name"
                           value="{{ old('store_name', $settings['store_name']) }}" required>
                </div>

                <div class="vs-field">
                    <label for="store_email">Email toko</label>
                    <input id="store_email" class="vs-input" type="email" name="store_email"
                           value="{{ old('store_email', $settings['store_email']) }}">
                </div>
            </div>
        </div>

        <div class="ad-panel">
            <h2>Kontak</h2>

            <div class="ad-form__grid">
                <div class="vs-field">
                    <label for="store_phone">Nomor telepon</label>
                    <input id="store_phone" class="vs-input" type="text" name="store_phone"
                           value="{{ old('store_phone', $settings['store_phone']) }}">
                </div>

                <div class="vs-field">
                    <label for="chatbot_whatsapp">WhatsApp (format 62...)</label>
                    <input id="chatbot_whatsapp" class="vs-input" type="text" name="chatbot_whatsapp"
                           value="{{ old('chatbot_whatsapp', $settings['chatbot_whatsapp']) }}">
                </div>

                <div class="vs-field ad-form__full">
                    <label for="store_address">Alamat</label>
                    <input id="store_address" class="vs-input" type="text" name="store_address"
                           value="{{ old('store_address', $settings['store_address']) }}">
                </div>
            </div>

            <p class="ad-hint">Nomor WhatsApp ini dipakai chatbot saat mengarahkan pembeli ke admin.</p>
        </div>

        <div class="ad-panel">
        <h2>Alamat asal pengiriman (gudang)</h2>

        <div class="ad-form">
            <div class="vs-field">
                <label for="origin_name">Nama pengirim / kontak gudang</label>
                <input id="origin_name" class="vs-input" type="text" name="origin_name"
                    value="{{ old('origin_name', $settings['origin_name']) }}">
            </div>

            <div class="vs-field">
                <label for="origin_phone">Nomor HP gudang</label>
                <input id="origin_phone" class="vs-input" type="tel" name="origin_phone"
                    value="{{ old('origin_phone', $settings['origin_phone']) }}" placeholder="081234567890">
            </div>

            <div class="vs-field ad-form__full">
                <label for="origin_address">Alamat lengkap gudang</label>
                <input id="origin_address" class="vs-input" type="text" name="origin_address"
                    value="{{ old('origin_address', $settings['origin_address']) }}" placeholder="Jalan, nomor, RT/RW, kelurahan">
            </div>

            <div class="vs-field">
                <label for="origin_city">Kota / Kabupaten</label>
                <input id="origin_city" class="vs-input" type="text" name="origin_city"
                    value="{{ old('origin_city', $settings['origin_city']) }}">
            </div>

            <div class="vs-field">
                <label for="origin_province">Provinsi</label>
                <input id="origin_province" class="vs-input" type="text" name="origin_province"
                    value="{{ old('origin_province', $settings['origin_province']) }}">
                </div>

                <div class="vs-field">
                    <label for="origin_postal_code">Kode pos</label>
                    <input id="origin_postal_code" class="vs-input" type="text" name="origin_postal_code" inputmode="numeric" maxlength="5"
                        value="{{ old('origin_postal_code', $settings['origin_postal_code']) }}" placeholder="55271">
                </div>
            </div>

            <p class="ad-hint">
                Titik jemput paket oleh kurir, dan dasar perhitungan ongkir.
                Status: <b>{{ \App\Models\Setting::originReady() ? 'lengkap ✓' : 'belum lengkap' }}</b>
            </p>
        </div>

        <div class="ad-panel">
            <h2>Promo</h2>

            <div class="ad-form__grid">
                <div class="vs-field">
                    <label for="promo_code">Kode promo</label>
                    <input id="promo_code" class="vs-input" type="text" name="promo_code"
                           value="{{ old('promo_code', $settings['promo_code']) }}" placeholder="GERCEP40">
                </div>

                <div class="vs-field">
                    <label for="promo_percent">Besar diskon (%)</label>
                    <input id="promo_percent" class="vs-input" type="number" name="promo_percent" min="0" max="100"
                           value="{{ old('promo_percent', $settings['promo_percent']) }}" required>
                </div>

                <label class="ai-toggle ad-form__full">
                    <input type="checkbox" name="promo_active" value="1"
                           @checked(old('promo_active', $settings['promo_active'] === '1'))>
                    <span>Tampilkan blok promo di storefront</span>
                </label>
            </div>
        </div>

        <div class="ad-form__actions">
            <button type="submit" class="vs-btn vs-btn--primary">Simpan pengaturan</button>
        </div>
    </form>
@endsection
