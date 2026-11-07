<script nonce="{{ request()->attributes->get('csp_nonce') ?? '' }}">
    (function () {
        var root = document.documentElement;
        var media = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;

        function getMode() {
            try {
                var saved = localStorage.getItem('themeMode');
                return saved === 'light' || saved === 'dark' ? saved : 'auto';
            } catch (e) {
                return 'auto';
            }
        }

        function resolveDark(mode) {
            if (mode === 'dark') return true;
            if (mode === 'light') return false;
            return !!(media && media.matches);
        }

        function applyTheme(mode) {
            var dark = resolveDark(mode);

            root.classList.toggle('dark-mode', dark);
            if (document.body) document.body.classList.toggle('dark-mode', dark);
            root.setAttribute('data-theme-mode', mode);
            root.setAttribute('data-theme', dark ? 'dark' : 'light');

            var icon = document.getElementById('darkModeIcon');
            if (icon) icon.className = dark ? 'fas fa-sun' : 'fas fa-moon';

            var toggle = document.getElementById('darkModeToggle');
            if (toggle) {
                toggle.title = mode === 'auto'
                    ? 'Tema otomatis mengikuti sistem (' + (dark ? 'gelap' : 'terang') + ')'
                    : 'Mode ' + (dark ? 'gelap' : 'terang') + ' — klik untuk beralih';
            }

            document.dispatchEvent(new CustomEvent('themechange', { detail: { mode: mode, dark: dark } }));
        }

        window.setThemeMode = function (mode) {
            if (mode !== 'light' && mode !== 'dark' && mode !== 'auto') mode = 'auto';
            try { localStorage.setItem('themeMode', mode); } catch (e) {}
            applyTheme(mode);
        };

        window.toggleDarkMode = function () {
            setThemeMode(resolveDark(getMode()) ? 'light' : 'dark');
        };

        applyTheme(getMode());

        if (media) {
            var onSystemChange = function () { if (getMode() === 'auto') applyTheme('auto'); };
            if (media.addEventListener) {
                media.addEventListener('change', onSystemChange);
            } else if (media.addListener) {
                media.addListener(onSystemChange);
            }
        }
    })();
</script>
