{{-- Global Dashboard Split-Curtain Splash Screen, Page Transition & Unified Action Loader --}}
@php
    $loaderMode = $loaderMode ?? 'app';
    $isDashboardSplash = $loaderMode === 'app' && (bool) session()->pull('just_logged_in', false);
@endphp

<style>
    /* =====================================================================
       1. TOP NAVIGATION PROGRESS BAR
       ===================================================================== */
    #appNavProgress {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        z-index: 10995;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    #appNavProgress.is-active {
        opacity: 1;
    }

    #appNavProgressBar {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #1462ad 0%, #3b82f6 55%, #93c5fd 100%);
        box-shadow: 0 0 14px rgba(59, 130, 246, 0.65);
        transition: width 0.45s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* =====================================================================
       2. LOGO-ONLY CINEMATIC SPLASH SCREEN (Hanya Setelah Login)
       ===================================================================== */
    #appSplitReveal {
        position: fixed;
        inset: 0;
        z-index: 10990;
        display: flex;
        align-items: center;
        justify-content: center;
        background: radial-gradient(circle at 50% 50%, #f1f5f9 0%, #ffffff 70%);
        overflow: hidden;
        pointer-events: all;
        will-change: transform, opacity, filter;
        transition:
            opacity 0.58s cubic-bezier(0.22, 1, 0.36, 1),
            transform 0.58s cubic-bezier(0.22, 1, 0.36, 1),
            filter 0.58s cubic-bezier(0.22, 1, 0.36, 1);
    }

    html.dark-mode #appSplitReveal,
    body.dark-mode #appSplitReveal {
        background: radial-gradient(circle at 50% 48%, #111d38 0%, #070c18 68%);
    }

    #appSplitReveal.is-leaving {
        opacity: 0;
        transform: scale(1.08);
        filter: blur(14px);
        pointer-events: none;
    }

    .app-splash-stage {
        position: relative;
        width: 220px;
        height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .app-splash-halo {
        position: absolute;
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(20, 98, 173, 0.22) 0%, rgba(59, 130, 246, 0.06) 55%, transparent 75%);
        animation: appSplashHalo 1.6s ease-in-out infinite alternate;
    }

    html.dark-mode .app-splash-halo,
    body.dark-mode .app-splash-halo {
        background: radial-gradient(circle, rgba(59, 130, 246, 0.34) 0%, rgba(37, 99, 235, 0.1) 55%, transparent 75%);
    }

    .app-splash-ripple {
        position: absolute;
        width: 96px;
        height: 96px;
        border-radius: 50%;
        border: 1.5px solid rgba(20, 98, 173, 0.38);
        opacity: 0;
        animation: appSplashRipple 1.35s cubic-bezier(0.16, 1, 0.3, 1) 0.15s forwards;
    }

    .app-splash-ripple--delay {
        animation-delay: 0.38s;
    }

    html.dark-mode .app-splash-ripple,
    body.dark-mode .app-splash-ripple {
        border-color: rgba(96, 165, 250, 0.45);
    }

    .app-splash-emblem {
        position: relative;
        width: 96px;
        height: 96px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.96);
        box-shadow:
            0 0 0 1px rgba(20, 98, 173, 0.15),
            0 20px 50px -12px rgba(20, 98, 173, 0.35);
        animation: appSplashEmblemIn 0.68s cubic-bezier(0.34, 1.56, 0.64, 1) both;
        transition:
            transform 0.52s cubic-bezier(0.22, 1, 0.36, 1),
            opacity 0.42s ease,
            filter 0.42s ease;
    }

    html.dark-mode .app-splash-emblem,
    body.dark-mode .app-splash-emblem {
        box-shadow:
            0 0 0 1px rgba(96, 165, 250, 0.32),
            0 0 42px rgba(59, 130, 246, 0.36),
            0 22px 50px -12px rgba(2, 6, 23, 0.75);
    }

    /* Kilau cahaya melintas di atas logo */
    .app-splash-emblem::after {
        content: '';
        position: absolute;
        inset: -20%;
        background: linear-gradient(115deg, transparent 35%, rgba(255, 255, 255, 0.85) 50%, transparent 65%);
        transform: translateX(-140%);
        animation: appSplashSheen 0.75s cubic-bezier(0.22, 1, 0.36, 1) 0.36s forwards;
        pointer-events: none;
    }

    #appSplitReveal.is-leaving .app-splash-emblem {
        transform: scale(1.48);
        opacity: 0;
        filter: blur(6px);
    }

    .app-splash-logo {
        width: 72px;
        height: 72px;
        object-fit: contain;
    }

    /* Efek kedalaman pada halaman dashboard saat splash terbuka */
    @if($isDashboardSplash)
    body.master-ui .wrapper {
        animation: appDashboardReveal 0.68s cubic-bezier(0.16, 1, 0.3, 1) 0.78s both;
    }
    @endif

    @keyframes appSplashHalo {
        from { transform: scale(0.92); opacity: 0.75; }
        to   { transform: scale(1.14); opacity: 1; }
    }

    @keyframes appSplashRipple {
        0%   { transform: scale(0.95); opacity: 0.65; }
        100% { transform: scale(2.35); opacity: 0; }
    }

    @keyframes appSplashEmblemIn {
        0%   { opacity: 0; transform: scale(0.62) translateY(12px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }

    @keyframes appSplashSheen {
        to { transform: translateX(140%); }
    }

    @keyframes appDashboardReveal {
        from { opacity: 0.35; transform: scale(0.975); }
        to   { opacity: 1; transform: scale(1); }
    }

    /* =====================================================================
       3. FLUID GLOBAL LOADING OVERLAY
       ===================================================================== */
    #appGlobalLoader {
        position: fixed;
        inset: 0;
        z-index: 10985;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(248, 250, 252, 0.65);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition:
            opacity 0.22s cubic-bezier(0.16, 1, 0.3, 1),
            visibility 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    }

    html.dark-mode #appGlobalLoader,
    body.dark-mode #appGlobalLoader {
        background: rgba(7, 11, 22, 0.76);
    }

    #appGlobalLoader.is-visible {
        opacity: 1;
        visibility: visible;
        pointer-events: all;
    }

    .app-loader-card {
        position: relative;
        display: flex;
        align-items: center;
        gap: 18px;
        min-width: 290px;
        max-width: 380px;
        padding: 20px 26px 22px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.96);
        border: 1px solid rgba(203, 213, 225, 0.85);
        box-shadow:
            0 24px 50px -14px rgba(15, 23, 42, 0.2),
            0 0 0 1px rgba(255, 255, 255, 0.7) inset;
        overflow: hidden;
        transform: translateY(10px) scale(0.96);
        opacity: 0;
        transition:
            transform 0.26s cubic-bezier(0.16, 1, 0.3, 1),
            opacity 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    }

    #appGlobalLoader.is-visible .app-loader-card {
        transform: translateY(0) scale(1);
        opacity: 1;
    }

    html.dark-mode .app-loader-card,
    body.dark-mode .app-loader-card {
        background: rgba(19, 28, 46, 0.95);
        border-color: rgba(96, 165, 250, 0.22);
        box-shadow:
            0 28px 60px -14px rgba(2, 6, 23, 0.82),
            0 0 0 1px rgba(255, 255, 255, 0.05) inset;
    }

    .app-loader-orbit {
        position: relative;
        width: 58px;
        height: 58px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .app-loader-svg-cw {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        animation: appFluidRotate 1.4s linear infinite;
    }

    .app-loader-svg-ccw {
        position: absolute;
        inset: 7px;
        width: calc(100% - 14px);
        height: calc(100% - 14px);
        animation: appFluidRotateCcw 1.9s linear infinite;
    }

    .app-loader-track {
        fill: none;
        stroke: rgba(20, 98, 173, 0.12);
        stroke-width: 2.5;
    }

    html.dark-mode .app-loader-track,
    body.dark-mode .app-loader-track {
        stroke: rgba(96, 165, 250, 0.15);
    }

    .app-loader-arc-1 {
        fill: none;
        stroke: #1462ad;
        stroke-width: 2.8;
        stroke-linecap: round;
        stroke-dasharray: 1, 160;
        animation: appFluidDashSm 1.25s ease-in-out infinite;
    }

    .app-loader-arc-2 {
        fill: none;
        stroke: #60a5fa;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-dasharray: 34 100;
    }

    html.dark-mode .app-loader-arc-1,
    body.dark-mode .app-loader-arc-1 {
        stroke: #60a5fa;
    }

    html.dark-mode .app-loader-arc-2,
    body.dark-mode .app-loader-arc-2 {
        stroke: #93c5fd;
    }

    .app-loader-emblem {
        width: 24px;
        height: 24px;
        object-fit: contain;
        position: relative;
        z-index: 1;
    }

    .app-loader-body {
        display: flex;
        flex-direction: column;
        min-width: 0;
        flex: 1;
    }

    .app-loader-text {
        font-family: 'Inter', sans-serif;
        font-size: 0.94rem;
        font-weight: 600;
        color: #0f172a;
        line-height: 1.3;
        letter-spacing: -0.01em;
    }

    html.dark-mode .app-loader-text,
    body.dark-mode .app-loader-text {
        color: #f8fafc;
    }

    .app-loader-subrow {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 5px;
    }

    .app-loader-hint {
        font-family: 'Inter', sans-serif;
        font-size: 0.74rem;
        color: #64748b;
    }

    html.dark-mode .app-loader-hint,
    body.dark-mode .app-loader-hint {
        color: #94a3b8;
    }

    .app-loader-wave {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        height: 12px;
    }

    .app-loader-wave span {
        display: block;
        width: 3px;
        height: 6px;
        border-radius: 999px;
        background: #1462ad;
        animation: appWaveBar 0.95s ease-in-out infinite;
    }

    html.dark-mode .app-loader-wave span,
    body.dark-mode .app-loader-wave span {
        background: #60a5fa;
    }

    .app-loader-wave span:nth-child(2) { animation-delay: 0.14s; }
    .app-loader-wave span:nth-child(3) { animation-delay: 0.28s; }
    .app-loader-wave span:nth-child(4) { animation-delay: 0.42s; }

    .app-loader-shimmer {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 3px;
        background: rgba(20, 98, 173, 0.08);
        overflow: hidden;
    }

    html.dark-mode .app-loader-shimmer,
    body.dark-mode .app-loader-shimmer {
        background: rgba(96, 165, 250, 0.1);
    }

    .app-loader-shimmer::after {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        width: 46%;
        background: linear-gradient(90deg, transparent, #1462ad, #60a5fa, transparent);
        animation: appShimmerSlide 1.15s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    }

    @if($loaderMode === 'app' && !$isDashboardSplash)
    body.master-ui .content-wrapper > .content {
        animation: appPageEnter 0.34s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    @endif

    @keyframes appFluidRise {
        from { opacity: 0; transform: translateY(12px) scale(0.95); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    @keyframes appGlowPulse {
        0%, 100% { transform: scale(0.92); opacity: 0.6; }
        50%      { transform: scale(1.1); opacity: 1; }
    }
    @keyframes appCurtainSweep {
        to { transform: scaleX(1); }
    }
    @keyframes appFluidRotate {
        100% { transform: rotate(360deg); }
    }
    @keyframes appFluidRotateCcw {
        100% { transform: rotate(-360deg); }
    }
    @keyframes appFluidDash {
        0%   { stroke-dasharray: 1, 280; stroke-dashoffset: 0; }
        50%  { stroke-dasharray: 175, 280; stroke-dashoffset: -45; }
        100% { stroke-dasharray: 175, 280; stroke-dashoffset: -275; }
    }
    @keyframes appFluidDashSm {
        0%   { stroke-dasharray: 1, 160; stroke-dashoffset: 0; }
        50%  { stroke-dasharray: 98, 160; stroke-dashoffset: -28; }
        100% { stroke-dasharray: 98, 160; stroke-dashoffset: -158; }
    }
    @keyframes appWaveBar {
        0%, 100% { height: 4px; opacity: 0.4; }
        50%      { height: 12px; opacity: 1; }
    }
    @keyframes appShimmerSlide {
        0%   { transform: translateX(-110%); }
        100% { transform: translateX(220%); }
    }
    @keyframes appPageEnter {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>

<div id="appNavProgress" aria-hidden="true"><div id="appNavProgressBar"></div></div>

@if($isDashboardSplash)
<div id="appSplitReveal" aria-hidden="true">
    <div class="app-splash-stage">
        <div class="app-splash-halo"></div>
        <div class="app-splash-ripple"></div>
        <div class="app-splash-ripple app-splash-ripple--delay"></div>
        <div class="app-splash-emblem">
            <img src="{{ asset('images/itb-logo.svg') }}" alt="ITB" class="app-splash-logo">
        </div>
    </div>
</div>
<script>
    (function() {
        var reveal = document.getElementById('appSplitReveal');
        if (!reveal) return;

        var dismissed = false;
        function dismiss() {
            if (dismissed) return;
            dismissed = true;
            setTimeout(function() {
                reveal.classList.add('is-leaving');
                setTimeout(function() {
                    if (reveal && reveal.parentNode) {
                        reveal.parentNode.removeChild(reveal);
                    }
                }, 600);
            }, 820);
        }

        if (document.readyState === 'interactive' || document.readyState === 'complete') {
            dismiss();
        } else {
            document.addEventListener('DOMContentLoaded', dismiss, { once: true });
        }
        setTimeout(dismiss, 1400);
    })();
</script>
@endif

{{-- Global Action & Navigation Loading Overlay --}}
<div id="appGlobalLoader" role="status" aria-live="polite" aria-hidden="true">
    <div class="app-loader-card">
        <div class="app-loader-orbit">
            <svg class="app-loader-svg-cw" viewBox="0 0 58 58" aria-hidden="true">
                <circle class="app-loader-track" cx="29" cy="29" r="25"></circle>
                <circle class="app-loader-arc-1" cx="29" cy="29" r="25"></circle>
            </svg>
            <svg class="app-loader-svg-ccw" viewBox="0 0 44 44" aria-hidden="true">
                <circle class="app-loader-arc-2" cx="22" cy="22" r="19"></circle>
            </svg>
            <img src="{{ asset('images/itb-logo.svg') }}" alt="" class="app-loader-emblem">
        </div>
        <div class="app-loader-body">
            <div class="app-loader-text" id="appGlobalLoaderText">Memproses data...</div>
            <div class="app-loader-subrow">
                <span class="app-loader-hint">Mohon tunggu sebentar</span>
                <span class="app-loader-wave" aria-hidden="true"><span></span><span></span><span></span><span></span></span>
            </div>
        </div>
        <div class="app-loader-shimmer" aria-hidden="true"></div>
    </div>
</div>

<script>
(function() {
    var overlay = document.getElementById('appGlobalLoader');
    var labelEl = document.getElementById('appGlobalLoaderText');
    var navWrap = document.getElementById('appNavProgress');
    var navBar = document.getElementById('appNavProgressBar');
    if (!overlay || !labelEl) return;

    var activeCount = 0;
    var shownAt = 0;
    var hideTimer = null;
    var safetyTimer = null;
    var MIN_VISIBLE_MS = 360;

    function startNavBar() {
        if (!navWrap || !navBar) return;
        navWrap.classList.add('is-active');
        navBar.style.width = '30%';
        setTimeout(function() { navBar.style.width = '78%'; }, 90);
    }

    function stopNavBar() {
        if (!navWrap || !navBar) return;
        navWrap.classList.remove('is-active');
        navBar.style.width = '0%';
    }

    function openOverlay(text) {
        clearTimeout(hideTimer);
        if (text) labelEl.textContent = text;
        if (!overlay.classList.contains('is-visible')) {
            shownAt = Date.now();
            overlay.setAttribute('aria-hidden', 'false');
            overlay.classList.add('is-visible');
        }
        startNavBar();
        clearTimeout(safetyTimer);
        safetyTimer = setTimeout(function() {
            hide(true);
        }, 30000);
    }

    function show(text) {
        activeCount++;
        openOverlay(text || 'Memproses data...');
    }

    function closeNow() {
        clearTimeout(safetyTimer);
        overlay.classList.remove('is-visible');
        overlay.setAttribute('aria-hidden', 'true');
        stopNavBar();
    }

    function hide(force) {
        if (force) {
            activeCount = 0;
            clearTimeout(hideTimer);
            closeNow();
            return;
        }
        activeCount = Math.max(0, activeCount - 1);
        if (activeCount === 0) {
            var elapsed = Date.now() - shownAt;
            var wait = Math.max(0, MIN_VISIBLE_MS - elapsed);
            clearTimeout(hideTimer);
            if (wait > 0) {
                hideTimer = setTimeout(function() {
                    if (activeCount === 0) closeNow();
                }, wait);
            } else {
                closeNow();
            }
        }
    }

    window.AppLoader = {
        show: function(text) { show(text); },
        hide: function(force) { hide(force); }
    };

    function isMutationMethod(method) {
        var m = String(method || 'GET').toUpperCase();
        return m === 'POST' || m === 'PUT' || m === 'PATCH' || m === 'DELETE';
    }

    function shouldSkipUrl(url) {
        var u = String(url || '').toLowerCase();
        return u.indexOf('/session/renew') !== -1 || u.indexOf('/notifications') !== -1;
    }

    function guessTextFromContext(method, url) {
        var m = String(method || '').toUpperCase();
        var u = String(url || '').toLowerCase();
        if (u.indexOf('login') !== -1) return 'Memproses masuk...';
        if (m === 'DELETE' || u.indexOf('delete') !== -1 || u.indexOf('hapus') !== -1) return 'Menghapus data...';
        if (u.indexOf('update') !== -1 || m === 'PUT' || m === 'PATCH') return 'Memperbarui data...';
        if (u.indexOf('approve') !== -1) return 'Memproses persetujuan...';
        if (u.indexOf('reject') !== -1 || u.indexOf('tolak') !== -1) return 'Memproses penolakan...';
        if (u.indexOf('lock') !== -1 || u.indexOf('kunci') !== -1) return 'Mengunci nilai...';
        if (u.indexOf('upload') !== -1) return 'Mengunggah berkas...';
        return 'Menyimpan data...';
    }

    // 1. Intercept standard non-AJAX form submissions
    document.addEventListener('submit', function(e) {
        var form = e.target;
        if (!form || form.tagName !== 'FORM') return;
        if (e.defaultPrevented) return;
        if (form.hasAttribute('data-no-loader')) return;
        if (form.getAttribute('target') === '_blank') return;

        var method = (form.getAttribute('method') || 'GET').toUpperCase();
        if (!isMutationMethod(method)) return;

        var onsubmitAttr = form.getAttribute('onsubmit') || '';
        if (onsubmitAttr.indexOf('preventDefault') !== -1 || onsubmitAttr.indexOf('return false') !== -1) {
            return;
        }

        var submitter = e.submitter || document.activeElement;
        var customText = (submitter && submitter.getAttribute && submitter.getAttribute('data-loader-text'))
            || form.getAttribute('data-loader-text')
            || guessTextFromContext(method, form.getAttribute('action'));
        show(customText);
    }, false);

    // 2. Intercept klik link pindah halaman -> "Memuat halaman..."
    document.addEventListener('click', function(e) {
        if (e.defaultPrevented || e.button !== 0) return;
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

        var link = e.target && e.target.closest ? e.target.closest('a[href]') : null;
        if (!link) return;
        if (link.hasAttribute('data-no-loader') || link.hasAttribute('download')) return;
        if (link.hasAttribute('data-toggle') || link.hasAttribute('data-widget')) return;
        if (link.getAttribute('target') === '_blank') return;

        var href = link.getAttribute('href') || '';
        if (!href || href.charAt(0) === '#' || href.indexOf('javascript:') === 0) return;
        if (href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0) return;

        var onclickAttr = link.getAttribute('onclick') || '';
        if (onclickAttr.indexOf('return false') !== -1 || onclickAttr.indexOf('modal(') !== -1) return;

        try {
            var targetUrl = new URL(href, window.location.href);
            if (targetUrl.origin !== window.location.origin) return;
            if (targetUrl.pathname === window.location.pathname && targetUrl.search === window.location.search && targetUrl.hash) {
                return;
            }
        } catch (err) {
            return;
        }

        show('Memuat halaman...');
    }, false);

    // 3. Tampilkan loader saat manual refresh (F5 / tombol reload browser)
    window.addEventListener('beforeunload', function() {
        if (!overlay.classList.contains('is-visible')) {
            show('Memuat halaman...');
        }
    });

    // Pastikan loader tertutup jika user menekan tombol Back (bfcache)
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) hide(true);
    });

    // 4. Intercept window.fetch untuk request mutasi
    if (window.fetch) {
        var origFetch = window.fetch;
        window.fetch = function(input, init) {
            var url = typeof input === 'string' ? input : (input && input.url ? input.url : '');
            var method = (init && init.method) || (input && input.method) || 'GET';
            var track = isMutationMethod(method) && !shouldSkipUrl(url);
            if (track) {
                show(guessTextFromContext(method, url));
            }
            return origFetch.apply(this, arguments).then(function(res) {
                if (track) hide(false);
                return res;
            }, function(err) {
                if (track) hide(false);
                throw err;
            });
        };
    }

    // 5. Hook jQuery.ajax untuk request mutasi
    function attachJqueryHook() {
        if (!window.jQuery) return;
        jQuery(document).ajaxSend(function(event, jqXHR, settings) {
            var method = (settings && (settings.type || settings.method)) || 'GET';
            var url = (settings && settings.url) || '';
            if (isMutationMethod(method) && !shouldSkipUrl(url) && !(settings && settings.noLoader)) {
                jqXHR.__appLoaderTracked = true;
                show((settings && settings.loaderText) || guessTextFromContext(method, url));
            }
        });
        jQuery(document).ajaxComplete(function(event, jqXHR) {
            if (jqXHR && jqXHR.__appLoaderTracked) {
                hide(false);
            }
        });
    }

    if (window.jQuery) {
        attachJqueryHook();
    } else {
        document.addEventListener('DOMContentLoaded', attachJqueryHook);
    }
})();
</script>
