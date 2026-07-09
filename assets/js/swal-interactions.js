document.addEventListener('DOMContentLoaded', () => {
    // Intercept links with class .needs-confirmation
    document.querySelectorAll('a.needs-confirmation').forEach(el => {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            const confirmMsg = this.getAttribute('data-confirm') || "Are you sure you want to proceed?";
            const confirmTitle = this.getAttribute('data-title') || "Confirm Action";
            const confirmIcon = this.getAttribute('data-icon') || "warning";
            const confirmBtnText = this.getAttribute('data-btn') || "Yes, proceed";

            Swal.fire({
                title: confirmTitle,
                text: confirmMsg,
                icon: confirmIcon,
                showCancelButton: true,
                confirmButtonColor: confirmIcon === 'warning' || confirmIcon === 'error' ? '#d33' : '#4F46E5',
                cancelButtonColor: '#6c757d',
                confirmButtonText: confirmBtnText
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    });

    // Intercept forms with class .needs-confirmation
    document.querySelectorAll('form.needs-confirmation').forEach(form => {
        form.addEventListener('submit', function(e) {
            // Check if it already passed confirmation
            if (form.getAttribute('data-confirmed') === 'true') {
                return true;
            }
            
            e.preventDefault();
            const confirmMsg = form.getAttribute('data-confirm') || "Are you sure you want to submit this?";
            const confirmTitle = form.getAttribute('data-title') || "Confirm Submission";
            const confirmIcon = form.getAttribute('data-icon') || "question";
            const confirmBtnText = form.getAttribute('data-btn') || "Yes, submit";

            Swal.fire({
                title: confirmTitle,
                text: confirmMsg,
                icon: confirmIcon,
                showCancelButton: true,
                confirmButtonColor: confirmIcon === 'warning' || confirmIcon === 'error' ? '#d33' : '#4F46E5',
                cancelButtonColor: '#6c757d',
                confirmButtonText: confirmBtnText
            }).then((result) => {
                if (result.isConfirmed) {
                    form.setAttribute('data-confirmed', 'true');
                    
                    // Trigger the actual submit button if there are multiple submit buttons
                    // For safety, we just call form.submit() which bypasses validation,
                    // but since validation already passed to trigger submit event, it's fine.
                    
                    // We need to pass the button name if it was clicked
                    if (e.submitter && e.submitter.name) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = e.submitter.name;
                        hiddenInput.value = e.submitter.value || '1';
                        form.appendChild(hiddenInput);
                    }
                    
                    form.submit();
                }
            });
        });
    });
});
