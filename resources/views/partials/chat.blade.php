@php
    $chatConfigured = filled(config('chatbot.key'));
@endphp

<div class="vs-chat" data-chat data-chat-configured="{{ $chatConfigured ? '1' : '0' }}">
    <button type="button" class="vs-chat__fab" data-chat-toggle aria-expanded="false" aria-controls="vs-chat-panel"
            aria-label="Buka asisten belanja">
        <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M21 12a8 8 0 0 1-8 8H8l-5 3 1.4-5.2A8 8 0 1 1 21 12z"/>
            <path d="M8.5 11h.01M12 11h.01M15.5 11h.01"/>
        </svg>
        <span class="vs-chat__ping" aria-hidden="true"></span>
    </button>

    <section class="vs-chat__panel" id="vs-chat-panel" data-chat-panel aria-hidden="true" aria-label="Asisten belanja LivingEase">
        <header class="vs-chat__head">
            <span class="vs-chat__avatar" aria-hidden="true">L</span>
            <div class="vs-chat__id">
                <b>LivingEase Assistant</b>
                <small><span class="vs-chat__dot"></span> AI · siap bantu</small>
            </div>
            <button type="button" class="vs-chat__close" data-chat-close aria-label="Tutup chat">
                <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </header>

        <div class="vs-chat__log" data-chat-log role="log" aria-live="polite" aria-label="Percakapan">
            <div class="vs-chat__msg vs-chat__msg--bot">
                Hai, aku <b>LivingEase</b> 👋 Mau tanya stok, ongkir, atau minta rekomendasi produk?
            </div>

            <div class="vs-chat__chips" data-chat-chips>
                <button type="button" data-chat-quick="Rekomendasi HP di bawah Rp 30.000">Rekomendasi lampu estetik</button>
                <button type="button" data-chat-quick="Ongkir ke Jakarta berapa?">Cek ongkir</button>
                <button type="button" data-chat-quick="Metode pembayaran apa saja?">Cara bayar</button>
            </div>

            @unless ($chatConfigured)
                <div class="vs-chat__notice">
                    Mode demo — <code>CHATBOT_API_KEY</code> belum diisi, jadi jawaban masih placeholder.
                </div>
            @endunless
        </div>

        <form class="vs-chat__form" data-chat-form>
            <input type="text" name="message" maxlength="500" autocomplete="off"
                   placeholder="Tulis pertanyaanmu..." data-chat-input aria-label="Tulis pesan">
            <button type="submit" data-chat-send aria-label="Kirim pesan">
                <svg class="vs-ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12l16-8-6 16-2.6-6.4z"/></svg>
            </button>
        </form>
    </section>
</div>
