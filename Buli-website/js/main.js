/**
 * MGMBPTC Website Skeleton JavaScript Helpers
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ----------------------------------------------------
    // 1. Sticky Header Compression on Scroll
    // ----------------------------------------------------
    const mainHeader = document.getElementById('mainHeader');
    
    function handleHeaderScroll() {
        if (!mainHeader) return;
        if (window.scrollY > 30) {
            mainHeader.classList.add('header-scrolled');
        } else {
            mainHeader.classList.remove('header-scrolled');
        }
    }
    
    window.addEventListener('scroll', handleHeaderScroll, { passive: true });
    handleHeaderScroll();

    // ----------------------------------------------------
    // 2. Off-Canvas Mobile Drawer Toggling
    // ----------------------------------------------------
    const navToggle = document.getElementById('navToggle');
    const mobileDrawer = document.getElementById('mobileDrawer');
    const mobileDrawerOverlay = document.getElementById('mobileDrawerOverlay');
    const mobileDrawerClose = document.getElementById('mobileDrawerClose');

    function openMobileDrawer() {
        if (mobileDrawer && mobileDrawerOverlay && navToggle) {
            mobileDrawer.classList.add('open');
            mobileDrawerOverlay.classList.add('open');
            navToggle.classList.add('active');
            navToggle.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden'; // Lock scroll
        }
    }

    function closeMobileDrawer() {
        if (mobileDrawer && mobileDrawerOverlay && navToggle) {
            mobileDrawer.classList.remove('open');
            mobileDrawerOverlay.classList.remove('open');
            navToggle.classList.remove('active');
            navToggle.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = ''; // Unlock scroll
        }
    }

    if (navToggle) {
        navToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            if (mobileDrawer && mobileDrawer.classList.contains('open')) {
                closeMobileDrawer();
            } else {
                openMobileDrawer();
            }
        });
    }

    if (mobileDrawerClose) {
        mobileDrawerClose.addEventListener('click', closeMobileDrawer);
    }

    if (mobileDrawerOverlay) {
        mobileDrawerOverlay.addEventListener('click', closeMobileDrawer);
    }

    // ----------------------------------------------------
    // 3. Mobile Accordion Sub-Menu Toggling
    // ----------------------------------------------------
    const accordionToggles = document.querySelectorAll('.mobile-accordion-toggle');
    accordionToggles.forEach(function(toggleBtn) {
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const parentDropdown = this.closest('.mobile-dropdown');
            if (parentDropdown) {
                const isOpen = parentDropdown.classList.contains('open');
                
                // Close other open submenus for clean accordion effect
                document.querySelectorAll('.mobile-dropdown.open').forEach(item => {
                    if (item !== parentDropdown) item.classList.remove('open');
                });

                parentDropdown.classList.toggle('open', !isOpen);
                this.textContent = isOpen ? '▾' : '▴';
            }
        });
    });

    // Close mobile drawer when pressing Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && mobileDrawer && mobileDrawer.classList.contains('open')) {
            closeMobileDrawer();
        }
    });

    // ----------------------------------------------------
    // 2. Client-side Form Validation
    // ----------------------------------------------------
    
    // Contact Form Validation
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(event) {
            const nameField = document.getElementById('contactName');
            const emailField = document.getElementById('contactEmail');
            const messageField = document.getElementById('contactMessage');
            let hasError = false;
            
            // Basic reset
            resetErrors(contactForm);
            
            if (!nameField.value.trim()) {
                showFieldError(nameField, "Name is required.");
                hasError = true;
            }
            
            if (!emailField.value.trim() || !validateEmail(emailField.value)) {
                showFieldError(emailField, "A valid email is required.");
                hasError = true;
            }
            
            if (!messageField.value.trim()) {
                showFieldError(messageField, "Message content is required.");
                hasError = true;
            }
            
            if (hasError) {
                event.preventDefault();
            }
        });
    }

    // Admission Form Validation
    const admissionForm = document.getElementById('admissionForm');
    if (admissionForm) {
        admissionForm.addEventListener('submit', function(event) {
            const fullName = document.getElementById('studentName');
            const email = document.getElementById('studentEmail');
            const phone = document.getElementById('studentPhone');
            const department = document.getElementById('studentDept');
            let hasError = false;
            
            resetErrors(admissionForm);
            
            if (!fullName.value.trim()) {
                showFieldError(fullName, "Full name is required.");
                hasError = true;
            }
            
            if (!email.value.trim() || !validateEmail(email.value)) {
                showFieldError(email, "A valid email address is required.");
                hasError = true;
            }
            
            if (!phone.value.trim()) {
                showFieldError(phone, "Contact number is required.");
                hasError = true;
            }
            
            if (department.value === "") {
                showFieldError(department, "Please select an academic program.");
                hasError = true;
            }
            
            if (hasError) {
                event.preventDefault();
            }
        });
    }

    // Helper functions
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(email).toLowerCase());
    }

    function showFieldError(element, errorMessage) {
        element.style.borderColor = "#e53e3e"; // Red border
        const errorDiv = document.createElement('div');
        errorDiv.className = "field-error-msg";
        errorDiv.style.color = "#e53e3e";
        errorDiv.style.fontSize = "0.8rem";
        errorDiv.style.marginTop = "0.25rem";
        errorDiv.innerText = errorMessage;
        element.parentNode.appendChild(errorDiv);
    }

    function resetErrors(form) {
        const errors = form.querySelectorAll('.field-error-msg');
        errors.forEach(e => e.remove());
        
        const inputs = form.querySelectorAll('.form-control');
        inputs.forEach(i => i.style.borderColor = "");
    }
});
