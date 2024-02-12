const element = document.getElementById("monthInput");
const form = document.getElementById("monthForm");
const baseUrl = form.dataset.baseUrl; // data-base-url属性から値を取得

element.addEventListener("change", function () {
    const selectedMonth = element.value; // yyyy-mm
    window.location.href = baseUrl + selectedMonth;
});
