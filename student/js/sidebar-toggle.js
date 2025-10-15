(function($) {
    "use strict";

    // Sidebar toggle functionality for Student pages
    function initSidebarToggle() {

        // Toggle sidebar on button click
        $(document).on('click', '#sidebarToggleTop', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const $body = $('body');
            const $sidebar = $('.sidebar');
            const $overlay = $('#sidebar-overlay');
            const isToggled = $sidebar.hasClass('toggled');

            // Toggle sidebar and overlay
            $body.toggleClass('sidebar-toggled', !isToggled);
            $sidebar.toggleClass('toggled', !isToggled);
            $overlay.toggleClass('active', !isToggled);

            // Save state only on desktop
            if ($(window).width() >= 768) {
                localStorage.setItem('sidebarToggled', !isToggled);
            }
        });

        // Handle responsive behavior
        function handleResponsive() {
            const windowWidth = $(window).width();
            const $sidebar = $('.sidebar');
            const $overlay = $('#sidebar-overlay');

            if (windowWidth < 768) {
                // On mobile, always start hidden
                $('body').removeClass('sidebar-toggled');
                $sidebar.removeClass('toggled');
                $overlay.removeClass('active');
                localStorage.removeItem('sidebarToggled');
            } else {
                // On desktop, restore saved state
                const savedState = localStorage.getItem('sidebarToggled');
                if (savedState === 'true') {
                    $('body').addClass('sidebar-toggled');
                    $sidebar.addClass('toggled');
                    $overlay.addClass('active');
                } else {
                    $('body').removeClass('sidebar-toggled');
                    $sidebar.removeClass('toggled');
                    $overlay.removeClass('active');
                }
            }
        }

        // Initial responsive setup
        handleResponsive();

        // Handle window resize
        $(window).on('resize', handleResponsive);

        // Close sidebar when clicking outside (on mobile)
        $(document).on('click', function(e) {
            if ($(window).width() < 768) {
                const $sidebar = $('.sidebar');
                const $overlay = $('#sidebar-overlay');
                const $toggleBtn = $('#sidebarToggleTop');

                if (
                    !$sidebar.is(e.target) && $sidebar.has(e.target).length === 0 &&
                    !$toggleBtn.is(e.target) && $toggleBtn.has(e.target).length === 0
                ) {
                    $('body').removeClass('sidebar-toggled');
                    $sidebar.removeClass('toggled');
                    $overlay.removeClass('active');
                    localStorage.removeItem('sidebarToggled');
                }
            }
        });

        // Prevent sidebar clicks from closing it
        $('.sidebar').on('click', function(e) {
            e.stopPropagation();
        });

        // Close sidebar when clicking overlay
        $('#sidebar-overlay').on('click', function() {
            $('body').removeClass('sidebar-toggled');
            $('.sidebar').removeClass('toggled');
            $(this).removeClass('active');
            localStorage.removeItem('sidebarToggled');
        });
    }

    // Initialize when DOM is ready
    $(document).ready(function() {
        initSidebarToggle();
    });

})(jQuery);
