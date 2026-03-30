// Add this to your main JavaScript file or create contact.js
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const subject = document.getElementById('subject').value.trim();
            const message = document.getElementById('message').value.trim();
            const category = document.getElementById('category').value;

            let isValid = true;

            // Reset previous errors
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });

            // Validate name
            if (name.length < 2) {
                showError('name', 'Name must be at least 2 characters');
                isValid = false;
            }

            // Validate email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showError('email', 'Please enter a valid email address');
                isValid = false;
            }

            // Validate subject
            if (subject.length < 5) {
                showError('subject', 'Subject must be at least 5 characters');
                isValid = false;
            }

            // Validate category
            if (!category) {
                showError('category', 'Please select a category');
                isValid = false;
            }

            // Validate message
            if (message.length < 10) {
                showError('message', 'Message must be at least 10 characters');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    function showError(fieldId, message) {
        const field = document.getElementById(fieldId);
        field.classList.add('is-invalid');

        // Create or update error message
        let errorDiv = field.parentElement.querySelector('.invalid-feedback');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            field.parentElement.appendChild(errorDiv);
        }
        errorDiv.textContent = message;
    }
});
