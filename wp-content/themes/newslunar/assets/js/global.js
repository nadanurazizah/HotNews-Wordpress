jQuery(document).ready(function ($) {

    /* ============================================================
       Preloader
       ============================================================ */
    var $preloader = $('#newslunar-preloader');

    if ($preloader.length) {
        $(window).on('load', function () {
            $preloader.addClass('fade-out');

            setTimeout(function () {
                $preloader.remove();
            }, 300);
        });
    }


    /* ============================================================
       Mobile Navigation – Keyboard Accessibility
       ============================================================ */
    var $navToggle = $('.nav-toggle');
    var $mobileMenu = $('#mobile-menu');
    var $mobileMenuOverlay = $('#mobile-menu-overlay');
    var $mobileMenuClose = $('.mobile-menu-close');
    var $body = $('body');

    function getMobileMenuFocusables() {
        return $mobileMenu.find(
            'a[href], button:not([disabled]), input:not([disabled]), ' +
            'select:not([disabled]), textarea:not([disabled]), ' +
            '[tabindex]:not([tabindex="-1"])'
        ).filter(':visible');
    }

    function openMobileMenu() {
        $mobileMenu.addClass('active').attr('aria-hidden', 'false');
        $mobileMenuOverlay.addClass('active').attr('aria-hidden', 'false');
        $body.addClass('mobile-menu-open');

        $navToggle
            .addClass('active')
            .attr('aria-expanded', 'true')
            .attr(
                'aria-label',
                $navToggle.data('label-close') || 'Close mobile navigation'
            );
        
        // Focus the close button
        setTimeout(function() {
            $mobileMenuClose.trigger('focus');
        }, 100);
    }

    function closeMobileMenu(returnFocus) {
        $mobileMenu.removeClass('active').attr('aria-hidden', 'true');
        $mobileMenuOverlay.removeClass('active').attr('aria-hidden', 'true');
        $body.removeClass('mobile-menu-open');

        $navToggle
            .removeClass('active')
            .attr('aria-expanded', 'false')
            .attr(
                'aria-label',
                $navToggle.data('label-open') || 'Open mobile navigation'
            );

        if (returnFocus) {
            $navToggle.trigger('focus');
        }
    }

    // Store original labels
    $navToggle
        .data('label-open', $navToggle.attr('aria-label') || 'Open mobile navigation')
        .data('label-close', 'Close mobile navigation');

    // Toggle mobile menu
    $navToggle.on('click', function (e) {
        e.preventDefault();

        var isOpen = $(this).hasClass('active');

        if (isOpen) {
            closeMobileMenu(false);
        } else {
            openMobileMenu();
        }
    });
    
    // Close button click
    $mobileMenuClose.on('click', function(e) {
        e.preventDefault();
        closeMobileMenu(true);
    });
    
    // Overlay click closes menu
    $mobileMenuOverlay.on('click', function() {
        closeMobileMenu(true);
    });
    
    // Global Escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $mobileMenu.hasClass('active')) {
            closeMobileMenu(true);
        }
    });

    // Keyboard handling inside mobile menu
    $mobileMenu.on('keydown', function (e) {
        if (!$mobileMenu.hasClass('active')) {
            return;
        }

        if (e.key === 'Tab') {
            var $focusables = getMobileMenuFocusables();

            if ($focusables.length === 0) {
                e.preventDefault();
                return;
            }

            var $first = $focusables.first();
            var $last = $focusables.last();

            if (e.shiftKey) {
                // Shift+Tab on first item → move to last
                if ($(document.activeElement).is($first)) {
                    e.preventDefault();
                    $last.trigger('focus');
                }
            } else {
                // Tab on last item → move to first
                if ($(document.activeElement).is($last)) {
                    e.preventDefault();
                    $first.trigger('focus');
                }
            }
        }
    });


    /* ============================================================
       Search Toggle
       ============================================================ */
    $('.search-toggle').on('click', function (e) {
        e.preventDefault();

        $(this).toggleClass('active');

        $('.search-container')
            .stop(true, true)
            .slideToggle();

        if ($navToggle.hasClass('active')) {
            closeMobileMenu(false);
        }
    });


    /* ============================================================
       Dropdown Menus – Focus Class
       ============================================================ */
    $('.dropdown-menu a').on('blur focus', function () {
        $(this)
            .parents('li.menu-item-has-children')
            .toggleClass('focus');
    });


    /* ============================================================
       Hide Mobile Menu / Search at Desktop Width
       ============================================================ */
    $(window).on('resize', function () {
        var windowWidth = $(window).width();

        if (windowWidth >= 850) {
            if ($navToggle.hasClass('active')) {
                closeMobileMenu(false);
            }
        }

        if (windowWidth <= 850) {
            $('.search-toggle').removeClass('active');
            $('.search-container').hide();
        }
    });


    /* ============================================================
       Smooth Scroll to Top
       ============================================================ */
    $('.to-the-top').on('click', function (e) {
        e.preventDefault();

        $('html, body').animate({
            scrollTop: 0
        }, 500);
    });


    /* ============================================================
       Color Mode Switcher
       Light / Dark / System
       ============================================================ */
    var $colorModeSwitcher = $('.color-mode-switcher');

    if ($colorModeSwitcher.length) {

        var defaultMode =
            $colorModeSwitcher.data('default-mode') || 'light';
        
        var $modeOptions = $colorModeSwitcher.find('.mode-option');

        function getSystemDarkMode() {
            return (
                window.matchMedia &&
                window.matchMedia('(prefers-color-scheme: dark)').matches
            );
        }

        function applyColorMode(mode) {
            var $html = $('html');

            $html.removeClass(
                'color-mode-light color-mode-dark color-mode-system'
            );

            $modeOptions.removeClass('active');

            // Find and activate the corresponding button
            var $activeButton = $modeOptions.filter('[data-mode="' + mode + '"]');
            $activeButton.addClass('active');

            if (mode === 'system') {

                $html.addClass('color-mode-system');

                if (getSystemDarkMode()) {
                    $html.addClass('color-mode-dark');
                } else {
                    $html.addClass('color-mode-light');
                }

            } else if (mode === 'dark') {

                $html.addClass('color-mode-dark');

            } else {

                $html.addClass('color-mode-light');
                mode = 'light';
            }

            try {
                localStorage.setItem(
                    'newslunar_color_mode',
                    mode
                );
            } catch (error) {
                // localStorage may be unavailable
            }
        }

        function initColorMode() {
            var savedMode = null;

            try {
                savedMode = localStorage.getItem(
                    'newslunar_color_mode'
                );
            } catch (error) {
                savedMode = null;
            }

            applyColorMode(savedMode || defaultMode);
        }

        $modeOptions.on('click', function (e) {
            e.preventDefault();
            var mode = $(this).data('mode');
            applyColorMode(mode);
        });

        // System color preference changes
        if (window.matchMedia) {

            var colorSchemeQuery =
                window.matchMedia(
                    '(prefers-color-scheme: dark)'
                );

            var handleColorSchemeChange = function () {

                var currentMode;

                try {
                    currentMode =
                        localStorage.getItem(
                            'newslunar_color_mode'
                        ) || defaultMode;
                } catch (error) {
                    currentMode = defaultMode;
                }

                if (currentMode === 'system') {
                    applyColorMode('system');
                }
            };

            // Modern browsers
            if (colorSchemeQuery.addEventListener) {
                colorSchemeQuery.addEventListener(
                    'change',
                    handleColorSchemeChange
                );
            }
            // Older browsers
            else if (colorSchemeQuery.addListener) {
                colorSchemeQuery.addListener(
                    handleColorSchemeChange
                );
            }
        }

        initColorMode();
    }


    /* ============================================================
       Marquee Strip
       Supports Multiple Instances
       ============================================================ */
    $('.marquee-container').each(function () {

        var marqueeContainer = this;
        var $container = $(marqueeContainer);
        var track = marqueeContainer.querySelector('.marquee-track');

        // Skip if track doesn't exist
        if (!track) {
            return;
        }

        var autoplay =
            $container.data('autoplay') === true ||
            $container.data('autoplay') === 'true';

        var duration =
            parseFloat($container.data('duration')) || 30;

        var direction =
            $container.data('direction') || 'ltr';

        var marqueeId = marqueeContainer.id;

        var animationId = null;
        var isPaused = false;
        var isHovering = false;
        var startTime = null;
        var pausedElapsed = 0;
        var totalWidth = 0;


        /* ------------------------------------------------------------
           Calculate Width
           ------------------------------------------------------------ */
        function initializeMarquee() {

            var $items = $(track).find('.marquee-item');

            if (!$items.length) {
                return 0;
            }

            totalWidth = 0;

            /*
             * The marquee is expected to contain two identical sets
             * of items. Only measure the first set.
             */
            var halfLength =
                Math.ceil($items.length / 2);

            for (var i = 0; i < halfLength; i++) {
                totalWidth +=
                    $items.eq(i).outerWidth(true);
            }

            /*
             * If outerWidth(true) doesn't include the intended gap,
             * use the CSS gap as a fallback.
             */
            if (!totalWidth) {
                totalWidth = track.scrollWidth / 2;
            }

            return totalWidth;
        }


        /* ------------------------------------------------------------
           Animation
           ------------------------------------------------------------ */
        function animate(timestamp) {

            if (isPaused || !autoplay || isHovering) {
                return;
            }

            if (startTime === null) {
                startTime =
                    timestamp - pausedElapsed;
            }

            var elapsed =
                timestamp - startTime;

            var progress =
                (elapsed / (duration * 1000)) % 1;

            if (!totalWidth) {
                initializeMarquee();
            }

            if (!totalWidth) {
                return;
            }

            var translateX;

            if (direction === 'rtl') {
                translateX =
                    progress * totalWidth;
            } else {
                translateX =
                    -progress * totalWidth;
            }

            track.style.transform =
                'translate3d(' +
                translateX +
                'px, 0, 0)';

            animationId =
                requestAnimationFrame(animate);
        }


        /* ------------------------------------------------------------
           Start
           ------------------------------------------------------------ */
        initializeMarquee();

        if (autoplay) {
            animationId =
                requestAnimationFrame(animate);
        }


        /* ------------------------------------------------------------
           Pause / Play Button
           ------------------------------------------------------------ */
        var pauseBtn = marqueeId
            ? document.querySelector(
                '[data-target="' +
                marqueeId +
                '"]'
            )
            : null;

        if (pauseBtn) {

            pauseBtn.addEventListener('click', function (e) {

                e.preventDefault();

                if (isPaused) {

                    // Resume
                    isPaused = false;

                    startTime =
                        performance.now() -
                        pausedElapsed;

                    pauseBtn.innerHTML =
                        '<i class="bi bi-pause-fill"></i>';

                    pauseBtn.title = 'Pause';

                    if (!isHovering) {
                        animationId =
                            requestAnimationFrame(animate);
                    }

                } else {

                    // Pause
                    isPaused = true;

                    if (animationId !== null) {
                        cancelAnimationFrame(
                            animationId
                        );
                        animationId = null;
                    }

                    if (startTime !== null) {
                        pausedElapsed =
                            performance.now() -
                            startTime;
                    }

                    pauseBtn.innerHTML =
                        '<i class="bi bi-play-fill"></i>';

                    pauseBtn.title = 'Play';
                }
            });
        }


        /* ------------------------------------------------------------
           Pause on Hover
           ------------------------------------------------------------ */
        marqueeContainer.addEventListener(
            'mouseenter',
            function () {

                if (!autoplay || isPaused) {
                    return;
                }

                isHovering = true;

                if (animationId !== null) {
                    cancelAnimationFrame(
                        animationId
                    );
                    animationId = null;
                }

                if (startTime !== null) {
                    pausedElapsed =
                        performance.now() -
                        startTime;
                }
            }
        );


        /* ------------------------------------------------------------
           Resume on Mouse Leave
           ------------------------------------------------------------ */
        marqueeContainer.addEventListener(
            'mouseleave',
            function () {

                if (!autoplay || isPaused) {
                    return;
                }

                isHovering = false;

                startTime =
                    performance.now() -
                    pausedElapsed;

                animationId =
                    requestAnimationFrame(animate);
            }
        );


        /* ------------------------------------------------------------
           Resize
           ------------------------------------------------------------ */
        $(window).on('resize', function () {

            initializeMarquee();

            if (autoplay && !isPaused && !isHovering) {

                if (animationId !== null) {
                    cancelAnimationFrame(
                        animationId
                    );
                }

                startTime =
                    performance.now() -
                    pausedElapsed;

                animationId =
                    requestAnimationFrame(animate);
            }
        });

    });

});