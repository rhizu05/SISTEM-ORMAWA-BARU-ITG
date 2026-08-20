/* ==========================================================================
   SKIN — app.js
   JavaScript terpusat aplikasi (konsolidasi dari footer.php & sidebar.php inline).

   PENTING (alur desain):
   - Logika tema menggunakan SATU mekanisme: atribut [data-bs-theme] pada <html>.
   - Nilai/struktur tampilan diselaraskan dari prototipe Figma/Stitch via MCP.
   ========================================================================== */

(function () {
    'use strict';

    /* ------------------------------------------------------------------
       1. TEMA (terang/gelap) — satu mekanisme: data-bs-theme
       ------------------------------------------------------------------ */
    const storageKey = 'theme';
    const getStoredTheme = () => localStorage.getItem(storageKey);
    const setStoredTheme = (theme) => localStorage.setItem(storageKey, theme);
    const getPreferredTheme = () => {
        const stored = getStoredTheme();
        if (stored) return stored;
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    };
    const setDocumentTheme = (theme) => {
        document.documentElement.setAttribute('data-bs-theme', theme);
    };

    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.querySelector('.theme-icon-current');

    const syncThemeIcon = (theme) => {
        if (!themeIcon) return;
        if (theme === 'dark') {
            themeIcon.classList.remove('bi-moon-fill');
            themeIcon.classList.add('bi-sun-fill');
        } else {
            themeIcon.classList.remove('bi-sun-fill');
            themeIcon.classList.add('bi-moon-fill');
        }
    };

    if (themeToggle) {
        themeToggle.addEventListener('change', () => {
            const theme = themeToggle.checked ? 'dark' : 'light';
            setStoredTheme(theme);
            setDocumentTheme(theme);
            syncThemeIcon(theme);
        });
    }

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        if (!getStoredTheme()) {
            setDocumentTheme(getPreferredTheme());
        }
    });

    /* ------------------------------------------------------------------
       2. SIDEBAR — collapse (desktop) & toggled (mobile)
       ------------------------------------------------------------------ */
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('sidebar');
        const contentWrapper = document.getElementById('content-wrapper');
        const toggler = document.getElementById('sidebar-toggler');
        const backdrop = document.getElementById('sidebar-backdrop');
        const isMobile = () => window.innerWidth <= 992;

        const toggleSidebar = () => {
            if (!sidebar) return;
            if (isMobile()) {
                sidebar.classList.toggle('toggled');
            } else {
                sidebar.classList.toggle('collapsed');
                if (contentWrapper) contentWrapper.classList.toggle('collapsed');
            }
        };

        if (toggler) {
            toggler.addEventListener('click', toggleSidebar);
        }

        if (backdrop) {
            backdrop.addEventListener('click', () => {
                if (isMobile() && sidebar) sidebar.classList.remove('toggled');
            });
        }

        // State awal saat load
        if (!isMobile()) {
            if (sidebar) sidebar.classList.remove('collapsed');
            if (contentWrapper) contentWrapper.classList.remove('collapsed');
        } else {
            if (sidebar) sidebar.classList.add('collapsed');
            if (contentWrapper) contentWrapper.classList.add('collapsed');
        }

        /* --------------------------------------------------------------
           3. JUDUL TAB & FAVICON (data dari elemen sidebar via PHP)
           -------------------------------------------------------------- */
        if (sidebar) {
            const appName = sidebar.getAttribute('data-app-name') || 'SKIN';
            const pageTitle = sidebar.getAttribute('data-page-title') || '';
            const faviconUrl = sidebar.getAttribute('data-favicon') || '';

            document.title = pageTitle ? `${pageTitle} - ${appName}` : appName;

            if (faviconUrl) {
                let favicon = document.querySelector("link[rel*='icon']");
                if (!favicon) {
                    favicon = document.createElement('link');
                    favicon.rel = 'shortcut icon';
                    document.getElementsByTagName('head')[0].appendChild(favicon);
                }
                favicon.type = 'image/png';
                favicon.href = faviconUrl;
            }
        }

        // Sinkronkan toggle & ikon sesuai tema awal
        if (themeToggle) themeToggle.checked = getPreferredTheme() === 'dark';
        syncThemeIcon(getPreferredTheme());
    });

    /* ------------------------------------------------------------------
       4. PONDASI NOTIFIKASI (siap integrasi realtime SSE)
       - Ditampilkan sebagai toast Bootstrap. Nanti endpoint SSE/notifikasi
         akan memanggil SKIN.notify() saat ada peristiwa baru.
       ------------------------------------------------------------------ */
    const notificationContainer = () => document.querySelector('.toast-container');

    function notify(message, type) {
        const container = notificationContainer();
        if (!container) return;

        const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
        const headerClass = type === 'success' ? 'bg-success' : 'bg-danger';
        const toastId = 'app-toast-' + Math.random().toString(36).substr(2, 9);

        const html =
            '<div id="' + toastId + '" class="toast align-items-center border-0 text-white text-bg-' +
            (type === 'success' ? 'success' : 'danger') + '" role="alert" aria-live="assertive" aria-atomic="true">' +
            '<div class="d-flex">' +
            '<div class="toast-body"><i class="bi ' + icon + ' me-2"></i>' + message + '</div>' +
            '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
            '</div></div>';

        container.insertAdjacentHTML('beforeend', html);
        const el = document.getElementById(toastId);
        if (el) {
            const toast = new bootstrap.Toast(el, { delay: 4000 });
            toast.show();
        }
    }

    // Ekspos helper global agar dipakai modul lain (mis. SSE/Polling notifikasi)
    window.SKIN = window.SKIN || {};
    window.SKIN.notify = notify;
})();