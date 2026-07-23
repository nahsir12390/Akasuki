<script>
    function toggleDarkMode() {
        const html = document.documentElement;
        const isDark = html.classList.contains('dark');
        
        if (isDark) {
            html.classList.remove('dark');
            localStorage.setItem('dark-mode', 'light');
        } else {
            html.classList.add('dark');
            localStorage.setItem('dark-mode', 'dark');
        }
        
        updateThemeText();
        updateToggleSwitches();
    }

    function updateToggleSwitches() {
        const isDark = document.documentElement.classList.contains('dark');
        const toggles = document.querySelectorAll('#theme-toggle, #theme-toggle-mobile');
        
        toggles.forEach(toggle => {
            toggle.checked = isDark;
            const dot = toggle.nextElementSibling?.nextElementSibling;
            if (dot) {
                dot.style.transform = isDark ? 'translateX(20px)' : 'translateX(0)';
            }
        });
    }

    function updateThemeText() {
        const isDark = document.documentElement.classList.contains('dark');
        const themeText = document.getElementById('theme-text');
        const themeTextMobile = document.getElementById('theme-text-mobile');
        const text = isDark ? 'Light Mode' : 'Dark Mode';
        
        if (themeText) themeText.textContent = text;
        if (themeTextMobile) themeTextMobile.textContent = text;
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const savedTheme = localStorage.getItem('dark-mode') || 'light';
        if (savedTheme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        
        updateThemeText();
        updateToggleSwitches();
    });
</script>

<style>
    .dot {
        transition: transform 0.3s ease;
    }
    
    #theme-toggle:checked ~ .dot,
    #theme-toggle-mobile:checked ~ .dot {
        transform: translateX(20px);
    }
    
    #theme-toggle:checked ~ .block,
    #theme-toggle-mobile:checked ~ .block {
        background-color: #2563eb;
    }
</style>