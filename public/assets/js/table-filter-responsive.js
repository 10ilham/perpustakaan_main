/**
 * Table and Filter Responsive Enhancements
 * This script improves the responsiveness of tables and filters
 */

document.addEventListener('DOMContentLoaded', function() {
    // Make filter buttons more responsive
    const filterForms = document.querySelectorAll('.filter form');

    filterForms.forEach(form => {
        const formButtons = form.querySelectorAll('button, a.btn');

        // If on mobile, make the buttons full width
        if (window.innerWidth <= 768) {
            formButtons.forEach(button => {
                button.style.width = '100%';
                button.style.marginTop = '10px';
                button.style.display = 'flex';
                button.style.justifyContent = 'center';
                button.style.alignItems = 'center';
            });

            // Make date inputs responsive
            const dateInputs = form.querySelectorAll('input[type="date"]');
            dateInputs.forEach(input => {
                const parent = input.closest('div[style*="display: flex"]');
                if (parent) {
                    parent.style.width = '100%';
                    parent.style.marginBottom = '10px';
                }
                input.style.flexGrow = '1';
            });
        }
    });

    // Make DataTables responsive - ensure all DataTable instances use responsive extension
    if ($.fn.DataTable) {
        $.extend(true, $.fn.dataTable.defaults, {
            responsive: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.0.2/i18n/id.json'
            }
        });

        // Check for any DataTables that need to be responsive
        const tables = document.querySelectorAll('.table');
        if (tables.length > 0) {
            tables.forEach(function(table) {
                const tableId = table.id;
                if (tableId && !$.fn.DataTable.isDataTable('#' + tableId) && $(table).is(':visible')) {
                    try {
                        $(table).DataTable({
                            responsive: true
                        });
                    } catch (e) {
                        console.log("Error initializing DataTable: ", e);
                    }
                }
            });
        }
    }

    // Make tab navigation scrollable on mobile
    const tabNavigations = document.querySelectorAll('.nav-tabs');
    if (tabNavigations.length > 0) {
        tabNavigations.forEach(nav => {
            if (window.innerWidth <= 768) {
                nav.style.display = 'flex';
                nav.style.flexWrap = 'nowrap';
                nav.style.overflowX = 'auto';
                nav.style.scrollbarWidth = 'none'; // Firefox

                // Hide scrollbar for WebKit browsers
                nav.style.webkitScrollbar = 'none';
                nav.style.msOverflowStyle = 'none';

                const navItems = nav.querySelectorAll('.nav-item');
                navItems.forEach(item => {
                    item.style.flexShrink = '0';
                    item.style.whiteSpace = 'nowrap';
                });
            }
        });
    }

    // Handle window resize for responsive adjustments
    window.addEventListener('resize', function() {
        if (window.innerWidth <= 768) {
            // Mobile adjustments
            filterForms.forEach(form => {
                const formButtons = form.querySelectorAll('button, a.btn');
                formButtons.forEach(button => {
                    button.style.width = '100%';
                    button.style.marginTop = '10px';
                });

                // Make date inputs responsive
                const dateInputs = form.querySelectorAll('input[type="date"]');
                dateInputs.forEach(input => {
                    const parent = input.closest('div[style*="display: flex"]');
                    if (parent) {
                        parent.style.width = '100%';
                        parent.style.marginBottom = '10px';
                    }
                    input.style.flexGrow = '1';
                });
            });
        } else {
            // Desktop adjustments
            filterForms.forEach(form => {
                const formButtons = form.querySelectorAll('button, a.btn');
                formButtons.forEach(button => {
                    button.style.width = '';
                    button.style.marginTop = '';
                });

                // Reset date inputs
                const dateInputs = form.querySelectorAll('input[type="date"]');
                dateInputs.forEach(input => {
                    const parent = input.closest('div[style*="display: flex"]');
                    if (parent) {
                        parent.style.width = '';
                        parent.style.marginBottom = '';
                    }
                    input.style.flexGrow = '';
                });
            });
        }

        // Adjust tab navigation based on screen size
        tabNavigations.forEach(nav => {
            if (window.innerWidth <= 768) {
                nav.style.display = 'flex';
                nav.style.flexWrap = 'nowrap';
                nav.style.overflowX = 'auto';
            } else {
                nav.style.display = '';
                nav.style.flexWrap = '';
                nav.style.overflowX = '';
            }
        });
    });
});
