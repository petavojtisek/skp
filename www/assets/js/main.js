document.addEventListener('DOMContentLoaded', () => {
    // 1. HAMBURGER MENU
    const toggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('.main-nav');

    if (toggle && nav) { // Pojistka, aby to neházelo chyby na stránkách bez menu
        toggle.addEventListener('click', () => {

            nav.classList.toggle('is-open');
            document.body.classList.toggle('menu-open');
        });

        // Zavření při kliknutí na odkaz
        document.querySelectorAll('.nav-list a').forEach(link => {
            link.addEventListener('click', () => {
                nav.classList.remove('is-open');
                document.body.classList.remove('menu-open');
            });
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
