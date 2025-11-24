document.addEventListener('DOMContentLoaded', function() {
    const navbarLinks = document.querySelectorAll('.nav-link[href^="#"]');
    
    navbarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId !== '#') {
                const targetSection = document.querySelector(targetId);
                if (targetSection) {
                    const navbarHeight = document.querySelector('.navbar').offsetHeight;
                    const targetPosition = targetSection.offsetTop - navbarHeight - 20;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                    
                    const navbarCollapse = document.querySelector('.navbar-collapse');
                    if (navbarCollapse.classList.contains('show')) {
                        const bsCollapse = new bootstrap.Collapse(navbarCollapse);
                        bsCollapse.hide();
                    }
                }
            }
        });
    });
    
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('shadow');
        } else {
            navbar.classList.remove('shadow');
        }
    });
    
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
        });
    }
    const toolsNavItems = document.querySelectorAll('.tools-nav-item');

    const closeAllToolMenus = () => {
        toolsNavItems.forEach(item => {
            const menu = item.querySelector('.dropdown-menu');
            const toggle = item.querySelector('.nav-dropdown-toggle');

            if (!menu) {
                return;
            }

            item.classList.remove('show');
            menu.classList.remove('show');
            menu.removeAttribute('data-bs-popper');

            if (toggle) {
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    };

    toolsNavItems.forEach(item => {
        const toggle = item.querySelector('.nav-dropdown-toggle');
        const menu = item.querySelector('.dropdown-menu');

        if (!toggle || !menu) {
            return;
        }

        toggle.addEventListener('click', event => {
            event.preventDefault();
            event.stopPropagation();

            const isOpen = item.classList.contains('show');

            closeAllToolMenus();

            if (!isOpen) {
                item.classList.add('show');
                menu.classList.add('show');
                menu.setAttribute('data-bs-popper', 'static');
                toggle.setAttribute('aria-expanded', 'true');
            }
        });

        menu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                closeAllToolMenus();
            });
        });
    });

    const navbarNav = document.getElementById('navbarNav');
    if (navbarNav) {
        navbarNav.addEventListener('hidden.bs.collapse', () => {
            closeAllToolMenus();
        });
    }

    document.addEventListener('click', event => {
        if (!event.target.closest('.tools-nav-item')) {
            closeAllToolMenus();
        }
    });
});
