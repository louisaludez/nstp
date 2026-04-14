// assets/js/pages/instructor-reports.js
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggleFormBtn');
    const cancelBtnBottom = document.getElementById('cancelFormBtnBottom');
    const formContainer = document.getElementById('reportFormContainer');
    let isFormVisible = false;

    function toggleForm() {
        isFormVisible = !isFormVisible;
        if (isFormVisible) {
            formContainer.style.display = 'block';
            toggleBtn.innerHTML = 'Cancel';
            toggleBtn.classList.remove('btn-green-brand');
            toggleBtn.classList.add('btn-cancel-light');
        } else {
            formContainer.style.display = 'none';
            toggleBtn.innerHTML = '<i class="bi bi-plus-lg me-1"></i> New Report';
            toggleBtn.classList.remove('btn-cancel-light');
            toggleBtn.classList.add('btn-green-brand');
        }
    }

    toggleBtn.addEventListener('click', toggleForm);
    cancelBtnBottom.addEventListener('click', toggleForm);
});
