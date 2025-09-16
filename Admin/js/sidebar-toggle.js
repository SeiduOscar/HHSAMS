(function($) {
    "use strict";
    
    // Sidebar toggle functionality
    function initSidebarToggle() {
        // Toggle sidebar on button click
        $(document).on('click', '#sidebarToggleTop, #sidebarToggle', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $body = $('body');
            const $sidebar = $('.sidebar');
            const $overlay = $('#sidebar-overlay');
            
            $body.toggleClass('sidebar-toggled');
            $sidebar.toggleClass('toggled');
            $overlay.toggleClass('active');
            
            // Close all open collapses when toggling sidebar
            if ($sidebar.hasClass('toggled')) {
                $('.sidebar .collapse').collapse('hide');
            }
            
            // Save state to localStorage
            localStorage.setItem('sidebarToggled', $sidebar.hasClass('toggled'));
        });
        
        // Restore sidebar state on page load
        const savedState = localStorage.getItem('sidebarToggled');
        if (savedState === 'true') {
            $('body').addClass('sidebar-toggled');
            $('.sidebar').addClass('toggled');
            $('#sidebar-overlay').addClass('active');
        }
        
        // Handle responsive behavior
        function handleResponsive() {
            const windowWidth = $(window).width();
            const $sidebar = $('.sidebar');
            const $overlay = $('#sidebar-overlay');
            
            if (windowWidth < 768) {
                // On mobile, always start with sidebar hidden
                $('body').addClass('sidebar-toggled');
                $sidebar.addClass('toggled');
                $overlay.addClass('active');
                $('.sidebar .collapse').collapse('hide');
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
        
        // Initial responsive check
        handleResponsive();
        
        // Handle window resize
        $(window).on('resize', function() {
            handleResponsive();
        });
        
        // Close sidebar when clicking outside on mobile
        $(document).on('click', function(e) {
            if ($(window).width() < 768) {
                const $sidebar = $('.sidebar');
                const $overlay = $('#sidebar-overlay');
                const $toggleBtn = $('#sidebarToggleTop, #sidebarToggle');
                
                if (!$sidebar.is(e.target) && $sidebar.has(e.target).length === 0 && 
                    !$toggleBtn.is(e.target) && $toggleBtn.has(e.target).length === 0) {
                    $('body').addClass('sidebar-toggled');
                    $sidebar.addClass('toggled');
                    $overlay.removeClass('active');
                }
            }
        });
        
        // Prevent sidebar clicks from bubbling to document
        $('.sidebar').on('click', function(e) {
            e.stopPropagation();
        });

        // Close sidebar when clicking on overlay
        $('#sidebar-overlay').on('click', function() {
            $('body').removeClass('sidebar-toggled');
            $('.sidebar').removeClass('toggled');
            $(this).removeClass('active');
        });
    }
    
    // Initialize when DOM is ready
    $(document).ready(function() {
        initSidebarToggle();
    });
    
})(jQuery);
