const element = document.getElementById("dateInput");
const form = document.getElementById("dateForm");

element.addEventListener("change", function () {
    const selectedDate = element.value; // yyyy-mm-dd
    window.location.href = "/admin/daily/" + selectedDate;
});
