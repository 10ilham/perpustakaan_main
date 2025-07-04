/**
 * Landing Page Responsive Enhancements
 * This script improves the landing page responsiveness
 */

document.addEventListener('DOMContentLoaded', function() {
    // Mobile navigation improvements
    const navbarToggle = document.querySelector('.navbar-toggle');
    const navbarCollapse = document.querySelector('.navbar-collapse');

    if (navbarToggle && navbarCollapse) {
        // Ensure that clicking on navigation links on mobile closes the menu
        const navLinks = document.querySelectorAll('.navbar-nav a');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    navbarCollapse.classList.remove('in');
                }
            });
        });
    }

    // Smooth scroll enhancements for mobile
    const smoothScrollLinks = document.querySelectorAll('a.smoothScroll');
    if (smoothScrollLinks.length > 0) {
        smoothScrollLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                // Get target element
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);

                if (targetElement) {
                    // Calculate position considering mobile header
                    const navHeight = document.querySelector('.custom-navbar').offsetHeight;
                    const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset;
                    const offsetPosition = targetPosition - navHeight - 20;

                    // Smooth scroll
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: "smooth"
                    });

                    // Close mobile menu
                    if (window.innerWidth <= 768 && navbarCollapse.classList.contains('in')) {
                        navbarCollapse.classList.remove('in');
                    }
                }
            });
        });
    }

    // Responsive form inputs for contact form
    const contactForm = document.getElementById('kontak-form');
    if (contactForm) {
        const formInputs = contactForm.querySelectorAll('input, textarea');
        formInputs.forEach(input => {
            if (window.innerWidth <= 768) {
                input.style.width = '100%';
            }
        });
    }

    // Make sure Google Map is responsive
    const googleMap = document.querySelector('.google-map iframe');
    if (googleMap) {
        googleMap.style.width = '100%';
        googleMap.style.height = '300px';
    }

    // Handle window resize
    window.addEventListener('resize', function() {
        // Adjust form inputs on resize
        if (contactForm) {
            const formInputs = contactForm.querySelectorAll('input, textarea');
            formInputs.forEach(input => {
                if (window.innerWidth <= 768) {
                    input.style.width = '100%';
                } else {
                    input.style.width = '';
                }
            });
        }

        // Adjust Google Map on resize
        if (googleMap && window.innerWidth <= 768) {
            googleMap.style.height = '300px';
        } else if (googleMap) {
            googleMap.style.height = '400px';
        }
    });
});
