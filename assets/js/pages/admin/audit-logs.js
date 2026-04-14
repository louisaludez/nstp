// assets/js/pages/audit-logs.js
document.getElementById('logSearch').addEventListener('keyup', function () {
    let filter = this.value.toLowerCase();
    document.querySelectorAll('.log-card').forEach(card => {
        card.style.display = card.textContent.toLowerCase().includes(filter) ? '' : 'none';
    });
});

document.querySelector('.filter-select').addEventListener('change', function () {
    let selectedAction = this.value.toLowerCase();
    document.querySelectorAll('.log-card').forEach(card => {
        let actionBadge = card.querySelector('.action-badge').textContent.toLowerCase();
        card.style.display = (selectedAction === 'all actions' || actionBadge.includes(selectedAction)) ? '' : 'none';
    });
});
