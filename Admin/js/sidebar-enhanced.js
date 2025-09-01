/**
 * Enhanced Sidebar Responsiveness - Touch Gestures & Advanced Features
 * Mobile-first responsive sidebar with touch gestures, accessibility, and performance optimizations
 */

(function($) {
    "use strict";

    // Enhanced sidebar functionality
    const SidebarEnhanced = {
        // Configuration
        config: {
            swipeThreshold: 50,
            swipeTimeThreshold: 300,
            breakpoints: {
                xs: 320,
                sm: 576,
                md: 768,
                lg: 992,
                xl: 1200
            },
            animationDuration: 300,
            storageKey: 'sidebar-enhanced-state'
        },

        // State management
        state: {
            isOpen: false,
            isAnimating: false,
            touchStartX: 0,
            touchStartY: 0,
            touchStartTime: 0,
            currentBreakpoint: 'lg'
        },

        // Initialize enhanced sidebar
        init: function() {
            this.setupEventListeners();
            this.restoreState();
            this.setupBreakpointDetection();
            this.setupAccessibility();
            this.setupTouchGestures();
            this.setupKeyboardNavigation();
        },

        // Event listeners setup
        setupEventListeners: function() {
            // Enhanced toggle functionality
            $(document).on('click', '#sidebarToggleTop, #sidebarToggle', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleSidebar();
            });

            // Close sidebar when clicking outside on mobile
            $(document).on('click', (e) => {
                if (this.shouldCloseOnOutsideClick(e)) {
                    this.closeSidebar();
                }
            });

            // Prevent sidebar clicks from bubbling
            $('.sidebar').on('click', (e) => {
                e.stopPropagation();
            });

            // Window resize handler
            $(window).on('resize', () => {
                this.handleResize();
            });

            // Orientation change handler
            $(window).on('orientationchange', () => {
                setTimeout(() => this.handleResize(), 100);
            });
        },

        // Touch gesture setup
        setupTouchGestures: function() {
            let touchStartX = 0;
            let touchStartY = 0;
            let touchStartTime = 0;

            $(document).on('touchstart', (e) => {
                touchStartX = e.originalEvent.touches[0].clientX;
                touchStartY = e.originalEvent.touches[0].clientY;
                touchStartTime = Date.now();
            });

            $(document).on('touchmove', (e) => {
                if (!this.isSwipeGesture(e, touchStartX, touchStartY)) return;
                
                e.preventDefault();
                const touchX = e.originalEvent.touches[0].clientX;
                const deltaX = touchX - touchStartX;
                
                // Swipe to open from left edge
                if (deltaX > this.config.swipeThreshold && touchStartX < 50 && !this.state.isOpen) {
                    this.openSidebar();
                }
                
                // Swipe to close from right edge
                if (deltaX < -this.config.swipeThreshold && this.state.isOpen) {
                    this.closeSidebar();
                }
            });

            $(document).on('touchend', (e) => {
                const touchEndTime = Date.now();
                if (touchEndTime - touchStartTime > this.config.swipeTimeThreshold) return;
            });
        },

        // Keyboard navigation setup
        setupKeyboardNavigation: function() {
            $(document).on('keydown', (e) => {
                if (!this.state.isOpen) return;

                switch(e.key) {
                    case 'Escape':
                        e.preventDefault();
                        this.closeSidebar();
                        break;
                    case 'Tab':
                        this.handleTabNavigation(e);
                        break;
                    case 'ArrowLeft':
                    case 'ArrowRight':
                        this.handleArrowNavigation(e);
                        break;
                }
            });
        },

        // Accessibility setup
        setupAccessibility: function() {
            const $sidebar = $('.sidebar');
            const $toggleBtn = $('#sidebarToggleTop');

            // Add ARIA attributes
            $sidebar.attr({
                'role': 'navigation',
                'aria-label': 'Main navigation',
                'aria-expanded': 'false'
            });

            $toggleBtn.attr({
                'aria-label': 'Toggle navigation',
                'aria-controls': 'sidebar',
                'aria-expanded': 'false'
            });

            // Add keyboard shortcuts info
            this.addKeyboardShortcutsInfo();
        },

        // Breakpoint detection
        setupBreakpointDetection: function() {
            const checkBreakpoint = () => {
                const width = window.innerWidth;
                let breakpoint = 'xl';
                
                if (width < this.config.breakpoints.sm) breakpoint = 'xs';
                else if (width < this.config.breakpoints.md) breakpoint = 'sm';
                else if (width < this.config.breakpoints.lg) breakpoint = 'md';
                else if (width < this.config.breakpoints.xl) breakpoint = 'lg';
                
                this.state.currentBreakpoint = breakpoint;
                this.handleBreakpointChange(breakpoint);
            };

            checkBreakpoint();
            $(window).on('resize', checkBreakpoint);
        },

        // Toggle sidebar with enhanced functionality
        toggleSidebar: function() {
            if (this.state.isAnimating) return;

            this.state.isAnimating = true;
            const $body = $('body');
            const $sidebar = $('.sidebar');
            const $overlay = $('.sidebar-overlay');

            if (this.state.isOpen) {
                this.closeSidebar();
            } else {
                this.openSidebar();
            }

            // Save state
            this.saveState();
            
            setTimeout(() => {
                this.state.isAnimating = false;
            }, this.config.animationDuration);
        },

        // Open sidebar with enhanced features
        openSidebar: function() {
            if (this.state.isOpen) return;

            const $body = $('body');
            const $sidebar = $('.sidebar');
            const $overlay = $('.sidebar-overlay');

            $body.addClass('sidebar-toggled');
            $sidebar.addClass('toggled');
            $overlay.addClass('show');

            this.state.isOpen = true;

            // Update ARIA attributes
            $sidebar.attr('aria-expanded', 'true');
            $('#sidebarToggleTop').attr('aria-expanded', 'true');

            // Focus management
            this.setFocusToSidebar();
        },

        // Close sidebar with enhanced features
        closeSidebar: function() {
            if (!this.state.isOpen) return;

            const $body = $('body');
            const $sidebar = $('.sidebar');
            const $overlay = $('.sidebar-overlay');

            $body.removeClass('sidebar-toggled');
            $sidebar.removeClass('toggled');
            $overlay.removeClass('show');

            this.state.isOpen = false;

            // Update ARIA attributes
            $sidebar.attr('aria-expanded', 'false');
            $('#sidebarToggleTop').attr('aria-expanded', 'false');

            // Return focus to toggle button
            $('#sidebarToggleTop').focus();
        },

        // Handle breakpoint changes
        handleBreakpointChange: function(breakpoint) {
            const $sidebar = $('.sidebar');
            const savedState = localStorage.getItem(this.config.storageKey);

            if (breakpoint === 'xs' || breakpoint === 'sm') {
                // Always start closed on mobile
                this.closeSidebar();
            } else if (savedState === 'true') {
                this.openSidebar();
            } else {
                this.closeSidebar();
            }
        },

        // Handle resize events
        handleResize: function() {
            this.setupBreakpointDetection();
        },

        // Check if should close on outside click
        shouldCloseOnOutsideClick: function(e) {
            const windowWidth = window.innerWidth;
            const $sidebar = $('.sidebar');
            const $toggleBtn = $('#sidebarToggleTop');

            return windowWidth < 992 && 
                   !$sidebar.is(e.target) && 
                   $sidebar.has(e.target).length === 0 && 
                   !$toggleBtn.is(e.target) && 
                   $toggleBtn.has(e.target).length === 0;
        },

        // Check if gesture is a swipe
        isSwipeGesture: function(e, startX, startY) {
            const touchX = e.originalEvent.touches[0].clientX;
            const touchY = e.originalEvent.touches[0].clientY;
            const deltaX = Math.abs(touchX - startX);
            const deltaY = Math.abs(touchY - startY);
            
            return deltaX > deltaY && deltaX > 10;
        },

        // Handle tab navigation
        handleTabNavigation: function(e) {
            const $sidebar = $('.sidebar');
            const $focusableElements = $sidebar.find('a, button, [tabindex]:not([tabindex="-1"])');
            const $firstElement = $focusableElements.first();
            const $lastElement = $focusableElements.last();
            
            if (e.shiftKey && $(e.target).is($firstElement)) {
                e.preventDefault();
                $lastElement.focus();
            } else if (!e.shiftKey && $(e.target).is($lastElement)) {
                e.preventDefault();
                $firstElement.focus();
            }
        },

        // Handle arrow navigation
        handleArrowNavigation: function(e) {
            const $current = $(e.target);
            const $items = $('.sidebar .nav-link');
            const currentIndex = $items.index($current);
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                $items.eq((currentIndex + 1) % $items.length).focus();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                $items.eq((currentIndex - 1 + $items.length) % $items.length).focus();
            }
        },

        // Set focus to sidebar
        setFocusToSidebar: function() {
            $('.sidebar .nav-link').first().focus();
        },

        // Add keyboard shortcuts info
        addKeyboardShortcutsInfo: function() {
            const $sidebar = $('.sidebar');
            const $info = $('<div class="sr-only" aria-live="polite">Press Escape to close, Tab to navigate</div>');
            $sidebar.append($info);
        },

        // Save state to localStorage
        saveState: function() {
            localStorage.setItem(this.config.storageKey, this.state.isOpen);
        },

        // Restore state from localStorage
        restoreState: function() {
            const savedState = localStorage.getItem(this.config.storageKey);
            if (savedState === 'true') {
                this.openSidebar();
            }
        }
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        SidebarEnhanced.init();
    });

})(jQuery);

// Additional utility functions
const SidebarUtils = {
    // Check if device is mobile
    isMobile: function() {
        return window.innerWidth < 768;
    },

    // Get current breakpoint
    getCurrentBreakpoint: function() {
        const width = window.innerWidth;
        if (width < 576) return 'xs';
        if (width < 768) return 'sm';
        if (width < 992) return 'md';
        if (width < 1200) return 'lg';
        return 'xl';
    },

    // Debounce function for performance
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
};
