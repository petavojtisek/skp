document.addEventListener('DOMContentLoaded', () => {
    // 1. HAMBURGER MENU
    const toggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('.main-nav');

    if (toggle && nav) { // Pojistka, aby to nehĂˇzelo chyby na strĂˇnkĂˇch bez menu
        toggle.addEventListener('click', (e) => {
            e.stopPropagation();
            nav.classList.toggle('is-open');
            document.body.classList.toggle('menu-open');
        });

        // 1b. SUBMENU TOGGLE NA MOBILU
        document.querySelectorAll('.submenu-toggle').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const parentLi = btn.closest('li');
                parentLi.classList.toggle('is-open');
            });
        });

        // ZavĹ™enĂ­ pĹ™i kliknutĂ­ na odkaz (pokud to nenĂ­ toggle podmenu)
        document.querySelectorAll('.nav-list a').forEach(link => {
            link.addEventListener('click', () => {
                nav.classList.remove('is-open');
                document.body.classList.remove('menu-open');
            });
        });

        // ZavĹ™enĂ­ pĹ™i kliknutĂ­ mimo menu
        document.addEventListener('click', (e) => {
            if (nav.classList.contains('is-open') && !nav.contains(e.target) && !toggle.contains(e.target)) {
                nav.classList.remove('is-open');
                document.body.classList.remove('menu-open');
            }
        });
    }

    // 2. SWIPER (Můžeš ho přesunout sem z Latte)
    if (document.querySelector('.news-slider')) {
        new Swiper('.news-slider', {
            slidesPerView: 1,
            spaceBetween: 30,
            pagination: { el: '.swiper-pagination', clickable: true },
            breakpoints: {
                640: { slidesPerView: 2 },
                1024: { slidesPerView: 3 }
            }
        });
    }

    document.querySelectorAll('.email-link').forEach(link => {

        link.addEventListener('click', (e) => {
            const email = link.dataset.user + '@' + link.dataset.domain;
            link.href = 'mailto:' + email;
        });
    });


});
