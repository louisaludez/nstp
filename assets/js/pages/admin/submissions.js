// assets/js/pages/submissions.js
document.querySelectorAll('#submissionTabs button').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        var tab = new bootstrap.Tab(this);
        tab.show();
    });
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
