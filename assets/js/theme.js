(function () {
    var root = document.documentElement;
    var toggleButtons = document.querySelectorAll('[data-theme-toggle]');

    function getTheme() {
        return root.getAttribute('data-theme') || 'light';
    }

    function setTheme(theme) {
        root.setAttribute('data-theme', theme);
        localStorage.setItem('site-theme', theme);

        toggleButtons.forEach(function (button) {
            var isDark = theme === 'dark';
            button.setAttribute('aria-pressed', isDark ? 'true' : 'false');
            button.setAttribute('aria-label', isDark ? 'Přepnout na světlý režim' : 'Přepnout na tmavý režim');
            button.classList.toggle('is-dark', isDark);
        });
    }

    toggleButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            setTheme(getTheme() === 'dark' ? 'light' : 'dark');
        });
    });

    setTheme(getTheme());
})();
