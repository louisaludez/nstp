document.addEventListener('DOMContentLoaded', function () {
    // View functionality
    document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('view_student_id').textContent = this.getAttribute('data-id');
            document.getElementById('view_name').textContent = this.getAttribute('data-name');
            document.getElementById('view_course_year').textContent = this.getAttribute('data-course') + ' - ' + this.getAttribute('data-year');
            document.getElementById('view_component').textContent = this.getAttribute('data-component');
            document.getElementById('view_status').textContent = this.getAttribute('data-status');
            document.getElementById('view_contact').textContent = this.getAttribute('data-contact') || 'N/A';
            document.getElementById('view_email').textContent = this.getAttribute('data-email') || 'N/A';
        });
    });

    const allSecs = window.allSectionsData || [];
    document.querySelectorAll('.enroll-btn').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('enroll_student_id').value = this.getAttribute('data-id');
            document.getElementById('enroll_student_name').textContent = this.getAttribute('data-name');
            
            let comp = this.getAttribute('data-component');
            let select = document.getElementById('enroll_section_id');
            select.innerHTML = '<option value="" disabled selected>Select a section...</option>';
            allSecs.forEach(s => {
                if(s.component === comp) {
                    select.innerHTML += `<option value="${s.id}">${s.section_name}</option>`;
                }
            });
        });
    });

    // Edit functionality
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('edit_original_student_id').value = this.getAttribute('data-id');
            document.getElementById('edit_student_id').value = this.getAttribute('data-id');
            document.getElementById('edit_full_name').value = this.getAttribute('data-fname') + ' ' + (this.getAttribute('data-lname') || '');
            document.getElementById('edit_course').value = this.getAttribute('data-course');
            document.getElementById('edit_year_level').value = this.getAttribute('data-year');
            document.getElementById('edit_contact').value = this.getAttribute('data-contact');
            document.getElementById('edit_email').value = this.getAttribute('data-email');
        });
    });

    // Delete functionality
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('delete_student_id').value = this.getAttribute('data-id');
            document.getElementById('delete_student_name').textContent = this.getAttribute('data-name');
        });
    });
});
