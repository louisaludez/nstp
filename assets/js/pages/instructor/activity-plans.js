// assets/js/pages/instructor-activity-plans.js
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggleFormBtn');
    const cancelBtnBottom = document.getElementById('cancelFormBtnBottom');
    const formContainer = document.getElementById('activityFormContainer');
    const fileUpload = document.getElementById('fileUpload');
    const fileList = document.getElementById('fileList');
    let isFormVisible = false;

    function toggleForm() {
        isFormVisible = !isFormVisible;
        if (isFormVisible) {
            formContainer.style.display = 'block';
            toggleBtn.innerHTML = 'Cancel';
        } else {
            formContainer.style.display = 'none';
            toggleBtn.innerHTML = '+ New Activity Plan';
        }
    }

    toggleBtn.addEventListener('click', toggleForm);
    cancelBtnBottom.addEventListener('click', toggleForm);
    
    fileUpload.addEventListener('change', function() {
        fileList.textContent = this.files.length > 0 ? this.files.length + ' file(s) selected' : '';
    });

    document.querySelectorAll('.obj-toggle').forEach(toggle => {
        toggle.addEventListener('click', function() {
            let icon = this.querySelector('i');
            if (this.classList.contains('collapsed')) {
                icon.classList.remove('bi-caret-down-fill');
                icon.classList.add('bi-caret-right-fill');
            } else {
                icon.classList.remove('bi-caret-right-fill');
                icon.classList.add('bi-caret-down-fill');
            }
        });
    });
});
