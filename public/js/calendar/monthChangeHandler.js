const element = document.getElementById('monthInput');
const form = document.getElementById('monthForm');

element.addEventListener('change', function() {
    const selectedMonth = element.value  // yyyy-mm 
    window.location.href = '/attendances/timecard/' + selectedMonth;
});


