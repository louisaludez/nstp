document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("searchInput");
    const sectionFilter = document.getElementById("sectionFilter");
    const tableBody = document.getElementById("studentsTableBody");
    const tableRows = document.querySelectorAll(".student-row");

    // Create No Results Row dynamically
    const noResultsRow = document.createElement("tr");
    noResultsRow.id = "noResultsRow";
    noResultsRow.style.display = "none";
    noResultsRow.innerHTML = `
        <td colspan="8" class="text-center py-5 text-muted">
            <i class="bi bi-search fs-1 d-block mb-2"></i>
            No matching students found.
        </td>
    `;
    tableBody.appendChild(noResultsRow);

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const filterSection = sectionFilter.value;
        let visibleCount = 0;

        tableRows.forEach(row => {
            const textContent = row.textContent.toLowerCase();
            const rowSectionId = row.getAttribute("data-section-id");
            
            const matchesSearch = textContent.includes(searchTerm);
            const matchesSection = filterSection === "All" || rowSectionId === filterSection;

            if (matchesSearch && matchesSection) {
                row.style.display = "";
                visibleCount++;
            } else {
                row.style.display = "none";
            }
        });

        // Toggle No Results Message
        noResultsRow.style.display = (visibleCount === 0 && tableRows.length > 0) ? "" : "none";
    }

    searchInput.addEventListener("input", filterTable);
    sectionFilter.addEventListener("change", filterTable);

    // Populate Modal Action dynamically
    document.querySelectorAll('.view-student-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('modalStudentName').textContent = this.getAttribute('data-name');
            document.getElementById('modalStudentId').textContent = this.getAttribute('data-id');
            document.getElementById('modalStudentCourse').textContent = this.getAttribute('data-course');
            document.getElementById('modalStudentSection').textContent = this.getAttribute('data-section');
            document.getElementById('modalStudentAttendance').textContent = this.getAttribute('data-attendance') + '%';
            document.getElementById('modalStudentGrade').textContent = this.getAttribute('data-grade');
            document.getElementById('modalStudentStatus').textContent = this.getAttribute('data-status');
        });
    });
});
