// Cookie Consent
document.addEventListener('DOMContentLoaded', function() {
    // Check if cookie consent was given
    if (!localStorage.getItem('cookieConsent')) {
        const cookieConsent = document.getElementById('cookieConsent');
        cookieConsent.style.display = 'block';
        
        document.getElementById('acceptCookies').addEventListener('click', function() {
            localStorage.setItem('cookieConsent', 'true');
            cookieConsent.style.display = 'none';
        });
    }
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Show join modal if URL has #join parameter
    if (window.location.hash === '#join') {
        const joinModal = new bootstrap.Modal(document.getElementById('joinUsModal'));
        joinModal.show();
    }
    
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
    
    // Password match validation
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirmPassword');
    
    if (password && confirmPassword) {
        function validatePassword() {
            if (password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity("Passwords don't match");
            } else {
                confirmPassword.setCustomValidity('');
            }
        }
        
        password.onchange = validatePassword;
        confirmPassword.onkeyup = validatePassword;
    }
    
    // Image preview for file inputs
    const imageInputs = document.querySelectorAll('input[type="file"][accept="image/*"]');
    imageInputs.forEach(function(input) {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const previewId = input.id + 'Preview';
                    let preview = document.getElementById(previewId);
                    
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.id = previewId;
                        preview.className = 'img-thumbnail mt-2';
                        preview.width = 150;
                        input.parentNode.appendChild(preview);
                    }
                    
                    preview.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    });
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Back to top button
    const backToTopButton = document.createElement('button');
    backToTopButton.innerHTML = '<i class="fas fa-arrow-up"></i>';
    backToTopButton.className = 'btn btn-success btn-lg rounded-circle back-to-top';
    backToTopButton.style.position = 'fixed';
    backToTopButton.style.bottom = '20px';
    backToTopButton.style.right = '20px';
    backToTopButton.style.display = 'none';
    backToTopButton.style.zIndex = '99';
    document.body.appendChild(backToTopButton);
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopButton.style.display = 'block';
        } else {
            backToTopButton.style.display = 'none';
        }
    });
    
    backToTopButton.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});

 // Auto-advance carousel every 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const myCarousel = document.getElementById('eventsCarousel');
        const carousel = new bootstrap.Carousel(myCarousel, {
            interval: 5000,
            wrap: true
        });
        
        // Pause on hover
        myCarousel.addEventListener('mouseenter', function() {
            carousel.pause();
        });
        myCarousel.addEventListener('mouseleave', function() {
            carousel.cycle();
        });
    });