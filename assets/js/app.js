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

        initNotifications();
    });

    /* ------------------------------------------------------------------
       helper escape HTML
       ------------------------------------------------------------------ */
    const escHtml = (s) => {
        const div = document.createElement('div');
        div.textContent = s == null ? '' : String(s);
        return div.innerHTML;
    };

    /* ------------------------------------------------------------------
       3b. NOTIFIKASI (badge + daftar + AJAX Polling)
       - Memuat daftar notifikasi belum dibaca via api_notifikasi_belum_baca
       - Menandai dibaca saat dropdown dibuka (tandai_notif_baca)
       - Polling rutin tiap 30 detik untuk real-time ringan
       ------------------------------------------------------------------ */
    function initNotifications() {
        const bell = document.getElementById('notif-bell');
        if (!bell) return;

        const badge = document.getElementById('notif-badge');
        const listEl = document.getElementById('notif-list');
        let lastItems = [];
        let unread = 0;
        let notifiedIds = []; // Melacak ID yang sudah ditampilkan Toast-nya

        const setUnread = (n) => {
            unread = n;
            if (n > 0) {
                badge.textContent = n;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        };

        const renderList = (items) => {
            lastItems = items || [];
            listEl.innerHTML = '';
            if (!items.length) {
                listEl.innerHTML = '<div class="text-muted small p-3">Tidak ada notifikasi.</div>';
                return;
            }
            items.forEach((it) => {
                const a = document.createElement('a');
                a.className = 'list-group-item list-group-item-action';
                a.href = 'index.php?page=dashboard';
                a.innerHTML =
                    '<small>' + escHtml(it.pesan) + '</small>' +
                    '<div class="text-muted" style="font-size:.75rem">' + escHtml(it.waktu) + '</div>';
                listEl.appendChild(a);
            });
        };

        const markRead = (ids) => {
            if (!ids.length) return;
            fetch('index.php?page=tandai_notif_baca', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ids: ids }),
            });
            // Hapus yang sudah dibaca dari memori client juga
            setUnread(0);
            renderList([]);
        };

        const refresh = (isPolling = false) =>
            fetch('index.php?page=api_notifikasi_belum_baca')
                .then((r) => r.json())
                .then((j) => {
                    if (j.success) {
                        setUnread(j.total || 0);
                        renderList(j.data || []);

                        if (isPolling && (j.data || []).length > 0) {
                            j.data.forEach(notif => {
                                if (!notifiedIds.includes(notif.id_notif)) {
                                    notifyHelper(notif.pesan);
                                    notifiedIds.push(notif.id_notif);
                                }
                            });
                        } else if (!isPolling) {
                            // Saat load awal, catat semua ID agar tidak ditampikan Toast-nya
                            notifiedIds = (j.data || []).map(n => n.id_notif);
                        }
                    }
                })
                .catch(() => {});

        // Fetch pertama kali saat load
        refresh(false);

        // Tandai sudah dibaca saat dropdown lonceng dibuka
        bell.addEventListener('show.bs.dropdown', () => {
            markRead(lastItems.map((d) => d.id_notif).filter(Boolean));
        });

        // Polling setiap 30 detik
        window.setInterval(() => { refresh(true); }, 30000);
    }

    function notifyHelper(pesan) {
        if (window.SKIN && typeof window.SKIN.notify === 'function') {
            window.SKIN.notify(pesan, 'info');
        }
    }

    /* ------------------------------------------------------------------
       4. CSRF PROTECTION FOR AJAX REQUESTS
       - Automatically inject CSRF token into all fetch requests
       - Ensure AJAX compatibility with CSRF middleware
       ------------------------------------------------------------------ */
    function setupCsrfInterceptor() {
        if (typeof window.CSRF_TOKEN === 'undefined') {
            console.warn('CSRF_TOKEN not defined. CSRF protection may not work for AJAX requests.');
            return;
        }

        // Store original fetch
        const originalFetch = window.fetch;

        // Override fetch to inject CSRF token
        window.fetch = function(resource, options = {}) {
            // Clone options to avoid mutation
            const newOptions = { ...options };

            // Check if this is a POST request
            const method = (newOptions.method || 'GET').toUpperCase();
            const isPostRequest = method === 'POST';

            // Only inject CSRF for POST requests to our own domain
            if (isPostRequest && window.CSRF_TOKEN) {
                // Ensure headers exist
                newOptions.headers = newOptions.headers || {};

                // Convert headers to Headers object if needed
                if (!(newOptions.headers instanceof Headers)) {
                    newOptions.headers = new Headers(newOptions.headers);
                }

                // Add CSRF token
                newOptions.headers.set('X-CSRF-Token', window.CSRF_TOKEN);
                newOptions.headers.set('X-Requested-With', 'XMLHttpRequest');
            }

            return originalFetch.call(this, resource, newOptions);
        };

        // Also patch XMLHttpRequest for legacy AJAX calls
        const originalXHROpen = XMLHttpRequest.prototype.open;
        const originalXHRSend = XMLHttpRequest.prototype.send;

        XMLHttpRequest.prototype.open = function(method, url) {
            this._method = method.toUpperCase();
            this._url = url;
            return originalXHROpen.apply(this, arguments);
        };

        XMLHttpRequest.prototype.send = function(body) {
            if (this._method === 'POST' && window.CSRF_TOKEN) {
                if (!this.requestHeaders) {
                    this.requestHeaders = {};
                }
                this.setRequestHeader('X-CSRF-Token', window.CSRF_TOKEN);
                this.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            }
            return originalXHRSend.apply(this, arguments);
        };
    }

    /* ------------------------------------------------------------------
       5. PONDASI NOTIFIKASI (siap integrasi realtime SSE)
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

    // Initialize CSRF protection for AJAX requests
    setupCsrfInterceptor();

    /* ------------------------------------------------------------------
       7. SERVER-SENT EVENTS (SSE) - NOTIFIKASI REAL-TIME & IN-APP CENTER
       ------------------------------------------------------------------ */
    function initSSENotifications() {
        const notifBadge = document.getElementById('notif-badge');
        const notifList = document.getElementById('notif-list');
        if (!notifBadge || !notifList) return;

        // Load awal data notifikasi (histori)
        fetch('index.php?page=api_notifikasi_belum_baca')
            .then(res => res.json())
            .then(res => {
                if (res.success && res.data) {
                    renderNotificationList(res.data, notifList);
                    updateBadgeCount(notifBadge, res.count);
                }
            });

        // Event listener saat user membuka dropdown
        document.getElementById('notif-bell').addEventListener('show.bs.dropdown', function () {
            // Tandai semua terbaca di server secara asinkron
            fetch('index.php?page=tandai_notif_baca', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ action: 'read_all' })
            }).then(() => {
                updateBadgeCount(notifBadge, 0); // Hilangkan badge merah
                // Hilangkan background kuning pada list item
                document.querySelectorAll('#notif-list .list-group-item').forEach(el => {
                    el.classList.remove('bg-warning', 'bg-opacity-10');
                });
            });
        });

        // SSE Real-time push
        if (typeof(EventSource) !== "undefined") {
            const eventSource = new EventSource('index.php?page=api_sse_notif');
            
            eventSource.addEventListener('new_notif', function(e) {
                const notifications = JSON.parse(e.data);
                if (notifications && notifications.length > 0) {
                    notifications.forEach(notif => {
                        window.SKIN.notify(notif.pesan, 'success'); // Toast
                        prependToNotificationList(notif, notifList); // Masuk ke dropdown list
                    });
                    
                    const currentCount = parseInt(notifBadge.innerText) || 0;
                    updateBadgeCount(notifBadge, currentCount + notifications.length);
                }
            });
        }
    }

    function updateBadgeCount(badgeEl, count) {
        if (count > 0) {
            badgeEl.innerText = count;
            badgeEl.classList.remove('d-none');
        } else {
            badgeEl.innerText = 0;
            badgeEl.classList.add('d-none');
        }
    }

    function renderNotificationList(dataArray, listEl) {
        if (dataArray.length === 0) {
            listEl.innerHTML = '<div class="text-center p-3 text-muted small">Tidak ada notifikasi.</div>';
            return;
        }
        
        let html = '';
        dataArray.forEach(notif => {
            const bgClass = notif.status === 'belum' ? 'bg-warning bg-opacity-10' : '';
            html += `
            <div class="list-group-item list-group-item-action ${bgClass}">
                <div class="d-flex w-100 justify-content-between">
                    <p class="mb-1 small">${notif.pesan}</p>
                </div>
                <small class="text-muted" style="font-size: 0.7rem;">${notif.waktu}</small>
            </div>`;
        });
        
        // Add footer "Tandai sudah dibaca" if there are items
        html += `<div class="p-2 text-center border-top">
                    <span class="text-primary small" style="cursor:pointer;">Tandai semua sudah dibaca</span>
                 </div>`;
        
        listEl.innerHTML = html;
    }

    function prependToNotificationList(notif, listEl) {
        // Hilangkan pesan "Tidak ada notifikasi" jika ada
        if (listEl.querySelector('.text-muted.small')) {
            listEl.innerHTML = '';
        }
        
        // Format waktu simple (HH:ii)
        const d = new Date();
        const timeStr = d.getHours() + ':' + (d.getMinutes()<10?'0':'') + d.getMinutes();
        
        const html = `
        <div class="list-group-item list-group-item-action bg-warning bg-opacity-10">
            <div class="d-flex w-100 justify-content-between">
                <p class="mb-1 small">${notif.pesan}</p>
            </div>
            <small class="text-muted" style="font-size: 0.7rem;">Hari ini, ${timeStr}</small>
        </div>`;
        
        listEl.insertAdjacentHTML('afterbegin', html);
    }
    
    // Inisialisasi SSE
    initSSENotifications();

})();