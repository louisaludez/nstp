document.addEventListener('DOMContentLoaded', function () {

    // ── View Student ──────────────────────────────────────────────────────────
    document.querySelectorAll('.view-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('view_student_id').textContent  = this.dataset.id;
            document.getElementById('view_name').textContent        = this.dataset.name;
            document.getElementById('view_course_year').textContent = this.dataset.course + ' - Year ' + this.dataset.year;
            document.getElementById('view_component').textContent   = this.dataset.component;
            document.getElementById('view_status').textContent      = this.dataset.status;
            document.getElementById('view_contact').textContent     = this.dataset.contact  || 'N/A';
            document.getElementById('view_email').textContent       = this.dataset.email    || 'N/A';
        });
    });

    // ── Edit Student ──────────────────────────────────────────────────────────
    document.querySelectorAll('.edit-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('edit_original_student_id').value = this.dataset.id;
            document.getElementById('edit_student_id').value          = this.dataset.id;
            document.getElementById('edit_full_name').value           = (this.dataset.fname + ' ' + (this.dataset.lname || '')).trim();
            document.getElementById('edit_course').value              = this.dataset.course;
            document.getElementById('edit_year_level').value          = this.dataset.year;
            document.getElementById('edit_contact').value             = this.dataset.contact;
            document.getElementById('edit_email').value               = this.dataset.email;
        });
    });

    // ── Delete Student ────────────────────────────────────────────────────────
    document.querySelectorAll('.delete-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('delete_student_id').value         = this.dataset.id;
            document.getElementById('delete_student_name').textContent = this.dataset.name;
        });
    });

    // ── Enroll Student (existing enroll-btn) ──────────────────────────────────
    const allSecs = window.allSectionsData || [];
    document.querySelectorAll('.enroll-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('enroll_student_id').value         = this.dataset.id;
            document.getElementById('enroll_student_name').textContent = this.dataset.name;

            var comp   = this.dataset.component;
            var select = document.getElementById('enroll_section_id');
            select.innerHTML = '<option value="" disabled selected>Select a section...</option>';
            allSecs.forEach(function (s) {
                if (s.component === comp) {
                    var opt = document.createElement('option');
                    opt.value       = s.id;
                    opt.textContent = s.section_name;
                    select.appendChild(opt);
                }
            });
        });
    });

    // ── Search ────────────────────────────────────────────────────────────────
    var searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            var q = this.value.toLowerCase();
            document.querySelectorAll('tbody tr').forEach(function (row) {
                var text = row.textContent.toLowerCase();
                row.style.display = text.includes(q) ? '' : 'none';
            });
        });
    }

    // ── Bulk CSV Import ───────────────────────────────────────────────────────
    var csvInput      = document.getElementById('csv_file_input');
    var bulkSectionSel= document.getElementById('bulk_section_id');
    var importBtn     = document.getElementById('bulkImportBtn');
    var previewWrapper= document.getElementById('csv_preview_wrapper');
    var previewBody   = document.getElementById('csv_preview_body');
    var rowCountEl    = document.getElementById('csv_row_count');
    var parseErrorEl  = document.getElementById('csv_parse_error');

    function checkImportReady() {
        var hasSection = bulkSectionSel && bulkSectionSel.value !== '';
        var hasFile    = csvInput && csvInput.files.length > 0;
        if (importBtn) importBtn.disabled = !(hasSection && hasFile);
    }

    if (bulkSectionSel) {
        bulkSectionSel.addEventListener('change', checkImportReady);
    }

    if (csvInput) {
        csvInput.addEventListener('change', function () {
            checkImportReady();

            var file = this.files[0];
            if (!file) {
                previewWrapper.classList.add('d-none');
                return;
            }

            var reader = new FileReader();
            reader.onload = function (e) {
                var text  = e.target.result;
                var lines = text.split(/\r?\n/).filter(function (l) { return l.trim() !== ''; });

                if (lines.length < 2) {
                    parseErrorEl.textContent = 'CSV must have a header row and at least one data row.';
                    parseErrorEl.classList.remove('d-none');
                    previewWrapper.classList.remove('d-none');
                    previewBody.innerHTML = '';
                    rowCountEl.textContent = '';
                    return;
                }

                parseErrorEl.classList.add('d-none');
                parseErrorEl.textContent = '';

                // Skip header (row 0), iterate data rows
                var dataLines = lines.slice(1);
                rowCountEl.textContent = '(' + dataLines.length + ' student' + (dataLines.length !== 1 ? 's' : '') + ')';

                previewBody.innerHTML = '';
                dataLines.forEach(function (line) {
                    var cols = line.split(',').map(function (c) { return c.trim(); });
                    var tr = document.createElement('tr');
                    tr.style.borderBottom = '1px solid #F3F4F6';
                    [cols[0]||'', cols[1]||'', cols[2]||'', cols[3]||'', cols[4]||'', cols[5]||''].forEach(function (val) {
                        var td = document.createElement('td');
                        td.className   = 'px-3 py-2';
                        td.style.color = '#4B5563';
                        td.textContent = val || '—';
                        tr.appendChild(td);
                    });
                    previewBody.appendChild(tr);
                });

                previewWrapper.classList.remove('d-none');
            };
            reader.readAsText(file);
        });
    }

    // Reset bulk modal state when it's closed
    var bulkModal = document.getElementById('bulkImportModal');
    if (bulkModal) {
        bulkModal.addEventListener('hidden.bs.modal', function () {
            if (csvInput)       csvInput.value = '';
            if (bulkSectionSel) bulkSectionSel.value = '';
            if (previewWrapper) previewWrapper.classList.add('d-none');
            if (previewBody)    previewBody.innerHTML = '';
            if (importBtn)      importBtn.disabled = true;
        });
    }
});
