<meta name="color-scheme" content="light dark">
<style>
    :root { color-scheme: light; }
    html.dark-mode { color-scheme: dark; }
</style>
<script nonce="{{ request()->attributes->get('csp_nonce') ?? '' }}">
    (function () {
        var stored = null;
        try { stored = localStorage.getItem('themeMode'); } catch (e) {}
        var systemDark = !!(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
        var isDark = stored === 'dark' || (stored !== 'light' && systemDark);
        if (isDark) {
            document.documentElement.classList.add('dark-mode');
        }
        document.documentElement.setAttribute('data-theme-mode', stored === 'light' || stored === 'dark' ? stored : 'auto');
    })();
</script>
