// assets/js/pages/instructor-attendance.js
document.addEventListener('DOMContentLoaded', function() {
    const radios = document.querySelectorAll('.status-radio');
    
    function updateCounts() {
        let present = 0, late = 0, absent = 0;
        radios.forEach(radio => {
            if (radio.checked) {
                if (radio.value === 'present') present++;
                if (radio.value === 'late') late++;
                if (radio.value === 'absent') absent++;
            }
        });
        document.getElementById('count-present').innerText = present;
        document.getElementById('count-late').innerText = late;
        document.getElementById('count-absent').innerText = absent;
    }

    radios.forEach(radio => radio.addEventListener('change', updateCounts));
});
