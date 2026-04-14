<script>
    (() => {
        try {
            if (localStorage.getItem('theme') === 'light') {
                document.documentElement.setAttribute('data-theme', 'light');
                document.documentElement.style.colorScheme = 'light';
            } else {
                document.documentElement.style.colorScheme = 'dark';
            }
        } catch (_) {
            document.documentElement.style.colorScheme = 'dark';
        }
    })();
</script>
