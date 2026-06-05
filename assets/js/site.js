(function () {
    var menuButton = document.querySelector('[data-mobile-menu-toggle]');
    var menu = document.querySelector('[data-mobile-menu]');
    var closeButton = document.querySelector('[data-mobile-menu-close]');
    var lightbox = document.querySelector('[data-lightbox]');

    function setMenuState(isOpen) {
        if (!menuButton || !menu) {
            return;
        }

        document.body.classList.toggle('mobile-nav-open', isOpen);
        menuButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        menu.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
    }

    if (menuButton && menu) {
        menuButton.addEventListener('click', function () {
            setMenuState(!document.body.classList.contains('mobile-nav-open'));
        });

        if (closeButton) {
            closeButton.addEventListener('click', function () {
                setMenuState(false);
            });
        }

        menu.addEventListener('click', function (event) {
            if (event.target === menu) {
                setMenuState(false);
            }
        });

        menu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                setMenuState(false);
            });
        });
    }

    if (lightbox) {
        var items = Array.prototype.slice.call(document.querySelectorAll('[data-lightbox-item]'));
        var image = lightbox.querySelector('[data-lightbox-image]');
        var prevButton = lightbox.querySelector('[data-lightbox-prev]');
        var nextButton = lightbox.querySelector('[data-lightbox-next]');
        var closeButtons = lightbox.querySelectorAll('[data-lightbox-close]');
        var currentIndex = 0;

        function renderLightbox(index) {
            var item = items[index];
            if (!item || !image) {
                return;
            }

            currentIndex = index;
            image.src = item.getAttribute('data-lightbox-src') || item.getAttribute('href') || '';
            image.alt = item.querySelector('img') ? item.querySelector('img').alt : '';
        }

        function openLightbox(index) {
            renderLightbox(index);
            lightbox.classList.add('is-open');
            lightbox.setAttribute('aria-hidden', 'false');
            document.body.classList.add('lightbox-open');
        }

        function closeLightbox() {
            lightbox.classList.remove('is-open');
            lightbox.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('lightbox-open');
        }

        function showNext(step) {
            if (!items.length) {
                return;
            }

            currentIndex = (currentIndex + step + items.length) % items.length;
            renderLightbox(currentIndex);
        }

        items.forEach(function (item, index) {
            item.addEventListener('click', function (event) {
                event.preventDefault();
                openLightbox(index);
            });
        });

        if (prevButton) {
            prevButton.addEventListener('click', function () {
                showNext(-1);
            });
        }

        if (nextButton) {
            nextButton.addEventListener('click', function () {
                showNext(1);
            });
        }

        closeButtons.forEach(function (button) {
            button.addEventListener('click', closeLightbox);
        });
    }

    var carousel = document.querySelector('[data-carousel]');
    if (carousel) {
        var slides = Array.prototype.slice.call(carousel.querySelectorAll('[data-carousel-slide]'));
        var dots = Array.prototype.slice.call(carousel.querySelectorAll('[data-carousel-dot]'));
        var prev = carousel.querySelector('[data-carousel-prev]');
        var next = carousel.querySelector('[data-carousel-next]');
        var currentSlide = 0;
        var timer = null;

        function renderSlide(index) {
            if (!slides.length) {
                return;
            }

            currentSlide = (index + slides.length) % slides.length;

            slides.forEach(function (slide, slideIndex) {
                slide.classList.toggle('is-active', slideIndex === currentSlide);
            });

            dots.forEach(function (dot, dotIndex) {
                dot.classList.toggle('is-active', dotIndex === currentSlide);
            });
        }

        function restartTimer() {
            if (timer) {
                window.clearInterval(timer);
            }

            if (slides.length > 1) {
                timer = window.setInterval(function () {
                    renderSlide(currentSlide + 1);
                }, 6000);
            }
        }

        if (prev) {
            prev.addEventListener('click', function () {
                renderSlide(currentSlide - 1);
                restartTimer();
            });
        }

        if (next) {
            next.addEventListener('click', function () {
                renderSlide(currentSlide + 1);
                restartTimer();
            });
        }

        dots.forEach(function (dot) {
            dot.addEventListener('click', function () {
                renderSlide(parseInt(dot.getAttribute('data-carousel-index') || '0', 10));
                restartTimer();
            });
        });

        renderSlide(0);
        restartTimer();
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            setMenuState(false);
            if (lightbox && lightbox.classList.contains('is-open')) {
                lightbox.classList.remove('is-open');
                lightbox.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('lightbox-open');
            }
        }

        if (!lightbox || !lightbox.classList.contains('is-open')) {
            return;
        }

        if (event.key === 'ArrowLeft') {
            var prevControl = lightbox.querySelector('[data-lightbox-prev]');
            if (prevControl) {
                prevControl.click();
            }
        }

        if (event.key === 'ArrowRight') {
            var nextControl = lightbox.querySelector('[data-lightbox-next]');
            if (nextControl) {
                nextControl.click();
            }
        }
    });
})();
