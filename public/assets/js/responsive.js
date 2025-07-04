/**
 * Responsive enhancements for the application
 * This script adds mobile navigation and improves responsiveness
 * Optimized to handle zoom level changes
 */

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    const toggleBtn = document.querySelector('.toggle-sidebar');

    // Mobile detection - function instead of constant for reuse
    const isMobile = () => window.innerWidth <= 768;

    // Caching initial sidebar state
    let sidebarState = sidebar.classList.contains('hide') ? 'collapsed' : 'expanded';

    // Track zoom level
    let lastDevicePixelRatio = window.devicePixelRatio || 1;

    // Fungsi untuk membuka sidebar
    function showSidebar() {
        sidebar.classList.add('show');
        content.style.opacity = '0.7';
        content.style.width = '100%';
        content.style.marginLeft = '0';
    }

    // Fungsi untuk menutup sidebar
    function hideSidebar() {
        sidebar.classList.remove('show');
        content.style.opacity = '1';
        content.style.width = '100%';
        content.style.marginLeft = '0';
        content.style.left = '0';
    }

    // Fungsi untuk toggle sidebar
    function toggleSidebar(e) {
        e.preventDefault();
        e.stopPropagation();

        if (sidebar.classList.contains('show')) {
            hideSidebar();
        } else {
            showSidebar();
        }
    }

    // Add mobile classes if needed
    if (isMobile()) {
        document.body.classList.add('mobile-view');
    }

    // Make sure toggle sidebar button is working
    if (toggleBtn) {
        // Remove any existing event listeners to prevent conflicts
        toggleBtn.removeEventListener('click', toggleSidebar);

        // Add click listener for mobile toggle
        toggleBtn.addEventListener('click', function(e) {
            if (isMobile()) {
                // Mobile behavior - show/hide overlay
                toggleSidebar(e);
                setTimeout(applyCorrectLayout, 50);
            } else {
                // Desktop behavior - let app.js handle the toggle and then apply layout
                setTimeout(function() {
                    applyCorrectLayout();
                }, 100);
            }
        });

        // Close sidebar when clicking outside (for mobile only)
        document.addEventListener('click', function(e) {
            if (!sidebar.contains(e.target) &&
                !toggleBtn.contains(e.target) &&
                sidebar.classList.contains('show') &&
                isMobile()) {
                hideSidebar();
                setTimeout(applyCorrectLayout, 50);
            }
        });

        // Prevent clicks inside sidebar from closing it
        sidebar.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Function to fix and apply proper layout
    function applyCorrectLayout() {
        // Determine device type
        const isCurrentlyMobile = isMobile();

        // Get current state
        const isSidebarHidden = sidebar.classList.contains('hide');
        const isSidebarShown = sidebar.classList.contains('show');

        // Apply layout based on device and sidebar state
        if (isCurrentlyMobile) {
            document.body.classList.add('mobile-view');

            // Mobile layout - sidebar should be full width overlay
            content.style.width = '100%';
            content.style.marginLeft = '0';
            content.style.left = '0';

            if (isSidebarShown) {
                content.style.opacity = '0.7';
            } else {
                content.style.opacity = '1';
            }
        } else {
            // Desktop/tablet layout
            document.body.classList.remove('mobile-view');
            sidebar.classList.remove('show');
            content.style.opacity = '1';

            if (isSidebarHidden) {
                // Collapsed sidebar (60px width)
                content.style.width = 'calc(100% - 60px)';
                content.style.marginLeft = '60px';
                content.style.left = '0';
            } else {
                // Expanded sidebar (260px width)
                content.style.width = 'calc(100% - 260px)';
                content.style.marginLeft = '260px';
                content.style.left = '0';
            }
        }
    }

    // Handle window resize
    window.addEventListener('resize', function() {
        // Check if device pixel ratio has changed (zoom)
        const currentDevicePixelRatio = window.devicePixelRatio || 1;
        const hasZoomChanged = Math.abs(currentDevicePixelRatio - lastDevicePixelRatio) > 0.001;

        if (hasZoomChanged) {
            lastDevicePixelRatio = currentDevicePixelRatio;
            console.log('Zoom level changed:', currentDevicePixelRatio);
        }

        // Apply layout
        applyCorrectLayout();
    });

    // Watch for changes to sidebar classes (to detect when app.js toggles the hide class)
    if (sidebar) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    // Sidebar class changed, apply correct layout
                    setTimeout(applyCorrectLayout, 50);
                }
            });
        });

        observer.observe(sidebar, {
            attributes: true,
            attributeFilter: ['class']
        });
    }

    // Make DataTables responsive on all pages
    if ($.fn.dataTable) {
        // Check if there are any datatables that need to be responsive
        const tables = document.querySelectorAll('.table');
        if (tables.length > 0) {
            tables.forEach(function(table) {
                if ($.fn.DataTable.isDataTable(table)) {
                    // This table already has DataTable initialized
                    // Just make sure it's responsive
                    const dt = $(table).DataTable();
                    if (dt.responsive) {
                        dt.responsive.recalc();
                    }
                }
            });
        }
    }

    // Improve form layouts for mobile
    const forms = document.querySelectorAll('form');
    if (forms.length > 0 && window.innerWidth <= 768) {
        forms.forEach(form => {
            const formGroups = form.querySelectorAll('.form-group');
            formGroups.forEach(group => {
                const inputs = group.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    if (!input.classList.contains('form-control')) {
                        input.classList.add('form-control');
                    }
                });
            });
        });
    }

    // Make tab navigation scrollable on mobile
    const tabNavigations = document.querySelectorAll('.nav-tabs');
    if (tabNavigations.length > 0) {
        tabNavigations.forEach(nav => {
            nav.style.overflowX = 'auto';
            nav.style.flexWrap = 'nowrap';
        });
    }

    // Adjust filter areas for better mobile display
    const filters = document.querySelectorAll('.filter');
    if (filters.length > 0 && isMobile()) {
        filters.forEach(filter => {
            const formGroups = filter.querySelectorAll('.form-group');
            formGroups.forEach(group => {
                group.style.flexDirection = 'column';

                const dateInputGroups = group.querySelectorAll('div[style*="display: flex"]');
                dateInputGroups.forEach(dateGroup => {
                    dateGroup.style.width = '100%';
                });
            });
        });
    }

    // Add specific handlers for zoom changes
    window.addEventListener('wheel', function(e) {
        // Check if ctrl key is pressed (common zoom gesture)
        if (e.ctrlKey) {
            // Delay applying the layout to ensure the zoom has completed
            setTimeout(applyCorrectLayout, 150);
        }
    });

    // For touch devices, detect zoom with specific gesture events
    window.addEventListener('gestureend', function() {
        setTimeout(applyCorrectLayout, 150);
    });

    // Additional zoom detection for mobile devices
    window.addEventListener('touchend', function() {
        setTimeout(applyCorrectLayout, 200);
    });

    // Apply layout immediately and also after a delay (to account for browser rendering)
    applyCorrectLayout();
    setTimeout(applyCorrectLayout, 100);
});
