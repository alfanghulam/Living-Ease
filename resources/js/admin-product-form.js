import { removeBackground } from '@imgly/background-removal';

/**
 * Hapus background otomatis untuk foto produk di form admin.
 * Semua proses jalan di browser (WASM/ONNX) — foto tidak dikirim ke server mana pun
 * sampai form disubmit.
 */
const root = document.querySelector('[data-image-upload]');

if (root) {
    const input = root.querySelector('[data-input]');
    const preview = root.querySelector('[data-preview]');
    const status = root.querySelector('[data-status]');
    const toggle = root.querySelector('[data-toggle]');
    const retry = root.querySelector('[data-retry]');

    let originalFile = null;
    let processedFile = null;
    let busy = false;

    const setStatus = (text, tone = 'idle') => {
        if (!status) return;
        status.textContent = text;
        status.dataset.tone = tone;
    };

    const showImage = (src) => {
        if (!preview) return;

        let img = preview.querySelector('img');
        if (!src) {
            img?.remove();
            preview.classList.remove('is-filled');
            return;
        }

        if (!img) {
            img = document.createElement('img');
            img.alt = 'Pratinjau gambar produk';
            preview.appendChild(img);
        }

        img.src = src;
        preview.classList.add('is-filled');
        preview.classList.toggle('is-transparent', Boolean(processedFile && src === preview.dataset.processedSrc));
    };

    /** Perkecil foto supaya proses AI cepat dan ukuran file tetap wajar. */
    async function normalize(file, max = 1200) {
        const bitmap = await createImageBitmap(file);
        const scale = Math.min(1, max / Math.max(bitmap.width, bitmap.height));
        const width = Math.max(1, Math.round(bitmap.width * scale));
        const height = Math.max(1, Math.round(bitmap.height * scale));

        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        canvas.getContext('2d').drawImage(bitmap, 0, 0, width, height);
        bitmap.close?.();

        return new Promise((resolve) => canvas.toBlob((blob) => resolve(blob), 'image/webp', 0.95));
    }

    function replaceInputFile(file) {
        const transfer = new DataTransfer();
        transfer.items.add(file);
        input.files = transfer.files;
    }

    async function process(file) {
        if (busy || !file) return;
        busy = true;

        const baseName = (file.name || 'produk').replace(/\.[^.]+$/, '');
        setStatus('Menyiapkan foto…', 'work');
        retry?.setAttribute('hidden', '');

        try {
            const normalized = await normalize(file);

            const result = await removeBackground(normalized, {
                output: { format: 'image/webp', quality: 0.92 },
                progress: (key, current, total) => {
                    if (!total) return;
                    const percent = Math.round((current / total) * 100);
                    const downloading = String(key).includes('fetch');
                    setStatus(
                        downloading ? `Mengunduh model AI… ${percent}%` : `Memproses foto… ${percent}%`,
                        'work'
                    );
                },
            });

            processedFile = new File([result], `${baseName}-nobg.webp`, { type: 'image/webp' });
            replaceInputFile(processedFile);

            const url = URL.createObjectURL(processedFile);
            preview.dataset.processedSrc = url;
            showImage(url);

            setStatus(`Background dihapus ✓ (${Math.round(processedFile.size / 1024)} KB, WebP transparan)`, 'ok');
        } catch (error) {
            console.error('[background-removal]', error);
            processedFile = null;
            restoreOriginal();
            setStatus('Gagal menghapus background. Foto asli dipakai — coba lagi kalau perlu.', 'error');
            retry?.removeAttribute('hidden');
        } finally {
            busy = false;
        }
    }

    function restoreOriginal() {
        if (!originalFile) return;
        replaceInputFile(originalFile);
        showImage(URL.createObjectURL(originalFile));
    }

    function handleFile(file) {
        originalFile = file;
        processedFile = null;
        showImage(file ? URL.createObjectURL(file) : null);

        if (!file) {
            setStatus('Belum ada gambar dipilih.', 'idle');
            return;
        }

        if (toggle?.checked) {
            process(file);
        } else {
            setStatus(`Foto asli dipakai (${Math.round(file.size / 1024)} KB).`, 'idle');
        }
    }

    input?.addEventListener('change', () => handleFile(input.files?.[0] ?? null));

    toggle?.addEventListener('change', () => {
        if (!originalFile) return;

        if (toggle.checked) {
            process(originalFile);
        } else {
            restoreOriginal();
            setStatus('AI dimatikan — foto asli yang akan di-upload.', 'idle');
        }
    });

    retry?.addEventListener('click', () => {
        if (originalFile && !busy) process(originalFile);
    });

    // kalau ada foto lama (halaman edit), tampilkan sebagai pratinjau awal
    const existing = root.dataset.existing;
    if (existing) showImage(existing);
}
