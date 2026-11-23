{{-- Lihat cepat: ketuk produk untuk melihat detail, 3D bisa diputar. Dibuka lewat resources/js/app.js --}}
<dialog class="vs-qv" id="vs-qv" aria-labelledby="qv-name">
    <div class="vs-qv__grid">
        <div class="vs-qv__stage" id="qv-stage"></div>

        <div class="vs-qv__info">
            <button type="button" class="vs-qv__x" data-qv-close aria-label="Tutup">&times;</button>
            <p class="vs-qv__cat" id="qv-cat"></p>
            <h3 id="qv-name"></h3>
            <p class="vs-qv__rate" id="qv-rate"></p>
            <div class="vs-qv__price"><b id="qv-price"></b><s id="qv-old"></s></div>
            <p class="vs-qv__desc" id="qv-desc"></p>

            <div class="vs-orbit" id="qv-orbit" hidden aria-label="Lihat dari sisi">
                <button type="button" data-orbit="0deg 90deg auto">Depan</button>
                <button type="button" data-orbit="90deg 90deg auto">Kanan</button>
                <button type="button" data-orbit="180deg 90deg auto">Belakang</button>
                <button type="button" data-orbit="-90deg 90deg auto">Kiri</button>
                <button type="button" data-orbit="0deg 0deg auto">Atas</button>
                <button type="button" data-orbit="0deg 180deg auto">Bawah</button>
            </div>

            <div class="vs-qv__actions">
                @auth
                    <form method="POST" action="#" id="qv-form" data-cart-form>
                        @csrf
                        <button type="submit" class="vs-btn vs-btn--primary" id="qv-add">Tambah ke keranjang</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="vs-btn vs-btn--primary">Masuk untuk membeli</a>
                @endauth
                <a href="#" class="vs-btn vs-btn--line" id="qv-link">Halaman lengkap</a>
            </div>
        </div>
    </div>
</dialog>
