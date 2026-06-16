document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('theme-toggle');
    const htmlEl = document.documentElement;
    
    // Check local storage for theme preference
    const savedTheme = localStorage.getItem('theme');
    
    // Default to dark theme since the original design was dark
    let currentTheme = savedTheme || 'dark';
    
    // Apply initial theme
    htmlEl.setAttribute('data-theme', currentTheme);
    updateIcon(currentTheme);
    
    if(toggleBtn) {
        toggleBtn.addEventListener('click', (event) => {
            const newTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            
            // Fallback for browsers that don't support view transitions
            if (!document.startViewTransition) {
                toggleBtn.classList.add('animating');
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                setTimeout(() => {
                    updateIcon(newTheme);
                    toggleBtn.classList.remove('animating');
                }, 150);
                return;
            }
            
            // Get click coordinates to create a circular clip-path effect starting from the button
            const x = event.clientX ?? innerWidth / 2;
            const y = event.clientY ?? innerHeight / 2;
            const endRadius = Math.hypot(
                Math.max(x, innerWidth - x),
                Math.max(y, innerHeight - y)
            );
            
            // Temporarily disable CSS transitions so they don't fight the View Transition
            const style = document.createElement('style');
            style.innerHTML = '*, *::before, *::after { transition: none !important; }';
            document.head.appendChild(style);

            const transition = document.startViewTransition(() => {
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateIcon(newTheme);
            });
            
            transition.ready.then(() => {
                const clipPath = [
                    `circle(0px at ${x}px ${y}px)`,
                    `circle(${endRadius}px at ${x}px ${y}px)`
                ];
                
                const isDark = newTheme === 'dark';
                
                document.documentElement.animate(
                    {
                        clipPath: isDark ? clipPath : [...clipPath].reverse(),
                    },
                    {
                        duration: 600,
                        easing: 'ease-in-out',
                        pseudoElement: isDark ? '::view-transition-new(root)' : '::view-transition-old(root)',
                    }
                );
            });

            transition.finished.finally(() => {
                document.head.removeChild(style);
            });
        });

        // Initial icon state setup
        const theme = localStorage.getItem('theme') || 'dark';
        updateIcon(theme);
    }
    
    function updateIcon(theme) {
        if(!toggleBtn) return;
        if(theme === 'dark') {
            toggleBtn.innerHTML = '<i data-lucide="sun"></i>'; // show sun to switch to light
            toggleBtn.title = "Switch to Light Mode";
        } else {
            toggleBtn.innerHTML = '<i data-lucide="moon"></i>'; // show moon to switch to dark
            toggleBtn.title = "Switch to Dark Mode";
        }
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }
});
