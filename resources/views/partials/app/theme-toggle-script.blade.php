<script>
    (() => {
        const html = document.documentElement;
        const toggle = document.getElementById(@json($toggleId ?? 'themeToggle'));
        const icon = document.getElementById(@json($iconId ?? 'themeIcon'));
        const sunPath = '<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>';
        const moonPath = '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>';
        const saved = localStorage.getItem('theme') || 'dark';

        if (saved === 'light') {
            html.setAttribute('data-theme', 'light');
            html.style.colorScheme = 'light';
        } else {
            html.style.colorScheme = 'dark';
        }

        if (!toggle || !icon) {
            return;
        }

        icon.innerHTML = saved === 'light' ? moonPath : sunPath;

        toggle.addEventListener('click', () => {
            const isLight = html.getAttribute('data-theme') === 'light';

            if (isLight) {
                html.removeAttribute('data-theme');
                html.style.colorScheme = 'dark';
                icon.innerHTML = sunPath;
                localStorage.setItem('theme', 'dark');
            } else {
                html.setAttribute('data-theme', 'light');
                html.style.colorScheme = 'light';
                icon.innerHTML = moonPath;
                localStorage.setItem('theme', 'light');
            }
        });
    })();
</script>
