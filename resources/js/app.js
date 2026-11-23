const root = document.documentElement;
const THEME_KEY = 'vanz-theme';
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

/* ---------------- tema ---------------- */
function applyTheme(theme, persist = true) {
    root.dataset.theme = theme;
    root.style.colorScheme = theme;

    if (persist) {
        try { localStorage.setItem(THEME_KEY, theme); } catch (e) { /* private mode */ }
    }

    const meta = document.querySelector('meta[name="theme-color"]');
    if (meta) meta.setAttribute('content', theme === 'dark' ? '#07040f' : '#ffffff');
}

applyTheme(root.dataset.theme === 'dark' ? 'dark' : 'light', false);

document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-theme-toggle]');
    if (toggle) {
        const next = root.dataset.theme === 'dark' ? 'light' : 'dark';

        if (document.startViewTransition && !reduceMotion.matches) {
            document.startViewTransition(() => applyTheme(next));
        } else {
            applyTheme(next);
        }
        return;
    }

    const burger = event.target.closest('[data-drawer-toggle]');
    if (burger) {
        const drawer = document.querySelector('[data-drawer]');
        if (!drawer) return;
        const open = drawer.classList.toggle('is-open');
        burger.setAttribute('aria-expanded', String(open));
        return;
    }

    /* sidebar admin (mobile) */
    const sideToggle = event.target.closest('[data-side-toggle]');
    if (sideToggle) {
        const side = document.querySelector('[data-side]');
        const scrim = document.querySelector('.ad-scrim');
        if (!side) return;
        const open = side.classList.toggle('is-open');
        scrim?.classList.toggle('is-open', open);
        return;
    }

    /* tab form admin */
    const atab = event.target.closest('[data-atab]');
    if (atab) {
        const root = atab.closest('[data-atabs]');
        if (!root) return;

        root.querySelectorAll('[data-atab]').forEach((btn) => {
            btn.classList.toggle('is-on', btn === atab);
        });

        root.querySelectorAll('[data-apane]').forEach((pane) => {
            pane.hidden = pane.dataset.apane !== atab.dataset.atab;
        });
        return;
    }

    const fav = event.target.closest('[data-fav]');
    if (fav) {
        const on = fav.getAttribute('aria-pressed') !== 'true';
        fav.setAttribute('aria-pressed', String(on));
        fav.classList.toggle('is-on', on);
    }
});

/* ikuti perubahan tema OS kalau pengunjung belum pernah memilih */
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (event) => {
    let stored = null;
    try { stored = localStorage.getItem(THEME_KEY); } catch (e) { /* noop */ }
    if (!stored) applyTheme(event.matches ? 'dark' : 'light', false);
});

/* ---------------- toast ---------------- */
let toastTimer;
function showToast(message) {
    const toast = document.getElementById('vs-toast');
    const text = document.getElementById('vs-toast-text');
    if (!toast || !text) return;

    text.textContent = message;
    toast.classList.add('is-show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('is-show'), 2200);
}

/* ---------------- keranjang ---------------- */
function setCartCount(count) {
    document.querySelectorAll('[data-cart-count]').forEach((el) => {
        el.textContent = count;
        el.classList.add('is-bump');
        setTimeout(() => el.classList.remove('is-bump'), 280);
    });
}

document.addEventListener('submit', async (event) => {
    const form = event.target.closest('form[data-cart-form]');
    if (!form) return;

    event.preventDefault();

    const button = form.querySelector('button[type="submit"]');
    if (button) button.disabled = true;

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]')?.value ?? '',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: new FormData(form),
            credentials: 'same-origin',
        });

        if (!response.ok) {
            const payload = await response.json().catch(() => ({}));

            if (response.status === 422 && payload.message) {
                showToast(payload.message);
                return;
            }

            throw new Error('HTTP ' + response.status);
        }

        const data = await response.json();
        if (typeof data.count === 'number') setCartCount(data.count);
        showToast(data.message || 'Masuk keranjang!');
    } catch (error) {
        form.submit();
    } finally {
        if (button) button.disabled = false;
    }
});

/* ---------------- qty stepper ---------------- */
document.addEventListener('click', (event) => {
    const step = event.target.closest('[data-qty-step]');
    if (!step) return;

    const wrap = step.closest('[data-qty]');
    const input = wrap?.querySelector('[data-qty-input]');
    if (!input) return;

    const min = parseInt(wrap.dataset.min ?? '1', 10);
    const max = parseInt(wrap.dataset.max ?? '99', 10);
    const current = parseInt(input.value, 10) || min;
    const next = Math.min(max, Math.max(min, current + parseInt(step.dataset.qtyStep, 10)));

    input.value = next;
    input.dispatchEvent(new Event('change', { bubbles: true }));
});

/* simpan jumlah ke input hidden di form terkait (PDP) */
document.querySelectorAll('[data-pdp]').forEach((pdp) => {
    const qty = pdp.querySelector('[data-qty-input]');

    pdp.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', () => {
            const hidden = form.querySelector('input[name="qty"]');
            if (hidden && qty) hidden.value = qty.value;
        });
    });
});

/* auto submit form saat qty di keranjang diubah */
document.addEventListener('change', (event) => {
    const input = event.target.closest('[data-auto-submit]');
    if (!input) return;

    const form = input.closest('form');
    if (form) form.submit();
});

/* ---------------- tilt konsol ---------------- */
document.querySelectorAll('[data-art]').forEach((art) => {
    const consoleEl = art.querySelector('[data-console]');
    if (!consoleEl) return;

    art.addEventListener('mousemove', (event) => {
        const rect = art.getBoundingClientRect();
        const x = (event.clientX - rect.left) / rect.width - 0.5;
        const y = (event.clientY - rect.top) / rect.height - 0.5;

        consoleEl.style.animation = 'none';
        consoleEl.style.transform = `rotateY(${-11 + x * 15}deg) rotateX(${6 - y * 11}deg) rotateZ(${1.2 + x * 2}deg)`;
    });

    art.addEventListener('mouseleave', () => {
        consoleEl.style.animation = '';
        consoleEl.style.transform = '';
    });
});

/* ---------------- chatbot ---------------- */
const chatRoot = document.querySelector('[data-chat]');

if (chatRoot) {
    const panel = chatRoot.querySelector('[data-chat-panel]');
    const log = chatRoot.querySelector('[data-chat-log]');
    const form = chatRoot.querySelector('[data-chat-form]');
    const input = chatRoot.querySelector('[data-chat-input]');
    const sendBtn = chatRoot.querySelector('[data-chat-send]');
    const toggleBtn = chatRoot.querySelector('[data-chat-toggle]');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    let historyLoaded = false;
    let busy = false;
    let renderedCount = 0;

    const scrollToEnd = () => { log.scrollTop = log.scrollHeight; };

    function addMessage(text, role = 'bot') {
        const el = document.createElement('div');
        el.className = 'vs-chat__msg vs-chat__msg--' + (role === 'user' ? 'user' : role === 'error' ? 'error' : 'bot');
        el.textContent = text;
        log.appendChild(el);
        scrollToEnd();
        return el;
    }

    function showTyping() {
        const el = document.createElement('div');
        el.className = 'vs-chat__msg vs-chat__msg--bot vs-chat__typing';
        el.dataset.typing = '1';
        el.innerHTML = '<i></i><i></i><i></i>';
        log.appendChild(el);
        scrollToEnd();
        return el;
    }

    function setOpen(open) {
        panel.classList.toggle('is-open', open);
        panel.setAttribute('aria-hidden', String(!open));
        toggleBtn.setAttribute('aria-expanded', String(open));

        if (open) {
            if (!historyLoaded) loadHistory();
            scrollToEnd();
            setTimeout(() => input.focus(), 80);
        }
    }

    async function loadHistory() {
        historyLoaded = true;

        try {
            const response = await fetch('/chat/history', {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });

            if (!response.ok) return;

            const data = await response.json();
            const messages = data.messages ?? [];

            if (messages.length && messages.length !== renderedCount) {
                log.innerHTML = '';
                messages.forEach((m) => addMessage(m.content, m.role === 'user' ? 'user' : 'bot'));
                renderedCount = messages.length;
            }
        } catch (error) {
            /* biarkan percakapan baru tetap jalan */
        }
    }

    async function send(text) {
        const message = (text ?? '').trim();
        if (!message || busy) return;

        busy = true;
        sendBtn.disabled = true;

        log.querySelector('[data-chat-chips]')?.remove();
        addMessage(message, 'user');
        input.value = '';

        const typing = showTyping();

        try {
            const response = await fetch('/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                credentials: 'same-origin',
                body: JSON.stringify({ message }),
            });

            const data = await response.json().catch(() => ({}));
            typing.remove();

            if (!response.ok) {
                addMessage(data.message ?? 'Pesan gagal dikirim. Coba lagi ya.', 'error');
            } else {
                addMessage(data.reply ?? 'Maaf, aku belum bisa jawab itu.', 'bot');
                renderedCount += 2;
            }
        } catch (error) {
            typing.remove();
            addMessage('Koneksi ke asisten terputus. Coba lagi sebentar.', 'error');
        } finally {
            busy = false;
            sendBtn.disabled = false;
            input.focus();
        }
    }

    toggleBtn.addEventListener('click', () => setOpen(!panel.classList.contains('is-open')));
    chatRoot.querySelectorAll('[data-chat-close]').forEach((btn) => {
        btn.addEventListener('click', () => setOpen(false));
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        send(input.value);
    });

    chatRoot.addEventListener('click', (event) => {
        const quick = event.target.closest('[data-chat-quick]');
        if (quick) send(quick.dataset.chatQuick);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && panel.classList.contains('is-open')) setOpen(false);
    });

    /* tarik balasan admin tanpa perlu menutup widget */
    setInterval(() => {
        if (panel.classList.contains('is-open') && !busy) loadHistory();
    }, 12000);
}

/* ---------------- reveal on scroll ---------------- */
if ('IntersectionObserver' in window && !reduceMotion.matches) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-in');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll('.vs-reveal').forEach((el) => observer.observe(el));
} else {
    document.querySelectorAll('.vs-reveal').forEach((el) => el.classList.add('is-in'));
}

/* ---------- Viewer produk 360° (model-viewer) ---------- */
document.querySelectorAll('[data-viewer]').forEach((root) => {
    const model = root.querySelector('[data-model]');
    if (!model) return;

    const panes = root.querySelectorAll('[data-pane]');
    const tabs = root.querySelectorAll('[data-tab]');

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            panes.forEach((pane) => { pane.hidden = pane.dataset.pane !== tab.dataset.tab; });
            tabs.forEach((t) => t.setAttribute('aria-selected', String(t === tab)));
            root.querySelector('[data-orbit-bar]')?.toggleAttribute('hidden', tab.dataset.tab !== '360');
        });
    });

    root.querySelectorAll('[data-orbit]').forEach((button) => {
        button.addEventListener('click', () => {
            model.cameraOrbit = button.dataset.orbit;
        });
    });
});


/* ---------- gerak halaman klasik: navbar, progres, parallax, cerita ---------- */
(() => {
    const nav = document.querySelector('[data-nav]');
    const prog = document.getElementById('vs-prog');
    const story = document.getElementById('vs-story');
    const band = document.querySelector('.vs-band');
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let words = [];
    let lastY = 0;
    let ticking = false;

    if (story) {
        story.innerHTML = story.textContent.trim().split(/\s+/).map((w) => `<span class="w">${w}</span>`).join(' ');
        words = [...story.querySelectorAll('.w')];
    }

    document.querySelectorAll('.vs-grid > .vs-card').forEach((card, i) => card.style.setProperty('--d', `${(i % 4) * 0.12}s`));
    document.querySelectorAll('.vs-values > .vs-val, .vs-catlist > .vs-catrow').forEach((el, i) => el.style.setProperty('--d', `${(i % 3) * 0.12}s`));

    function frame() {
        ticking = false;
        const y = window.scrollY || 0;
        const vh = window.innerHeight;
        const max = document.documentElement.scrollHeight - vh;

        if (prog) prog.style.transform = `scaleX(${max > 0 ? Math.min(1, y / max) : 0})`;

        if (nav) {
            nav.classList.toggle('is-scrolled', y > 60);
            const drawerOpen = nav.querySelector('[data-drawer].is-open, [data-drawer][data-open]');
            if (y > lastY + 6 && y > 260 && !drawerOpen) nav.classList.add('is-hidden');
            else if (y < lastY - 6 || y < 260) nav.classList.remove('is-hidden');
        }
        lastY = y;

        if (!reduce) {
            if (y < vh * 1.2) document.documentElement.style.setProperty('--py', `${y * 0.22}px`);
            if (band) {
                const r = band.getBoundingClientRect();
                if (r.bottom > 0 && r.top < vh) band.style.setProperty('--by', `${(r.top + r.height / 2 - vh / 2) * -0.14}px`);
            }
        }

        if (story && words.length) {
            const r = story.getBoundingClientRect();
            const p = reduce ? 1 : Math.max(0, Math.min(1, (vh * 0.82 - r.top) / (r.height + vh * 0.35)));
            const n = Math.round(p * words.length * 1.05);
            words.forEach((w, i) => w.classList.toggle('on', reduce || i < n));
        }
    }

    const request = () => { if (!ticking) { ticking = true; requestAnimationFrame(frame); } };
    window.addEventListener('scroll', request, { passive: true });
    window.addEventListener('resize', request);
    frame();

    const count = document.querySelector('[data-cart-count]');
    document.addEventListener('submit', (e) => {
        if (e.target.matches('[data-cart-form]') && count) {
            setTimeout(() => { count.classList.remove('vs-bump'); void count.offsetWidth; count.classList.add('vs-bump'); }, 300);
        }
    });
})();

/* ---------- lihat cepat: detail produk + model 3D yang bisa diputar ---------- */
(() => {
    const dlg = document.getElementById('vs-qv');
    if (!dlg || typeof dlg.showModal !== 'function') return;

    const stage = document.getElementById('qv-stage');
    const orbit = document.getElementById('qv-orbit');
    const set = (id, text) => { document.getElementById(id).textContent = text || ''; };
    let viewer = null;

    function loadViewerLib() {
        if (customElements.get('model-viewer') || document.getElementById('mv-lib')) return;
        const script = document.createElement('script');
        script.id = 'mv-lib';
        script.type = 'module';
        script.src = 'https://cdn.jsdelivr.net/npm/@google/model-viewer@4.0.0/dist/model-viewer.min.js';
        document.head.appendChild(script);
    }

    function open(d) {
        set('qv-cat', d.category);
        set('qv-name', d.name);
        set('qv-rate', d.rating || 'Belum ada ulasan');
        set('qv-price', d.price);
        set('qv-old', d.old);
        set('qv-desc', d.desc);
        document.getElementById('qv-link').href = d.url;

        const form = document.getElementById('qv-form');
        if (form) {
            form.action = d.cart;
            const btn = document.getElementById('qv-add');
            btn.disabled = !d.inStock;
            btn.textContent = d.inStock ? 'Tambah ke keranjang' : 'Stok habis';
        }

        stage.innerHTML = '';
        viewer = null;
        orbit.hidden = true;

        if (d.model) {
            loadViewerLib();
            viewer = document.createElement('model-viewer');
            viewer.setAttribute('src', d.model);
            viewer.setAttribute('poster', d.image);
            viewer.setAttribute('alt', `Model 3D ${d.name}`);
            viewer.setAttribute('camera-controls', '');
            viewer.setAttribute('disable-zoom', '');
            viewer.setAttribute('interaction-prompt', 'auto');
            viewer.setAttribute('shadow-intensity', '1');
            viewer.setAttribute('environment-image', 'neutral');
            viewer.setAttribute('camera-orbit', '-25deg 75deg auto');
            stage.appendChild(viewer);
            orbit.hidden = false;
        } else {
            const img = document.createElement('img');
            img.src = d.image;
            img.alt = d.name;
            stage.appendChild(img);
        }

        dlg.showModal();
    }

    orbit.addEventListener('click', (e) => {
        const b = e.target.closest('[data-orbit]');
        if (b && viewer) viewer.cameraOrbit = b.dataset.orbit;
    });

    document.addEventListener('click', (e) => {
        if (e.defaultPrevented || e.button || e.metaKey || e.ctrlKey || e.shiftKey) return;
        const target = e.target.closest('.vs-card__media, .vs-card__title a');
        const card = target?.closest('.vs-card');
        if (!card || !card.dataset.quick) return;
        e.preventDefault();
        try { open(JSON.parse(card.dataset.quick)); } catch (err) { window.location.href = target.href || target.closest('a').href; }
    });

    dlg.querySelector('[data-qv-close]').addEventListener('click', () => dlg.close());
    dlg.addEventListener('click', (e) => { if (e.target === dlg) dlg.close(); });
    dlg.addEventListener('close', () => { stage.innerHTML = ''; viewer = null; });
})();
