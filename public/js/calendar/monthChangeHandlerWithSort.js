const element = document.getElementById("monthInput");
const form = document.getElementById("monthForm");
const baseUrl = form.dataset.baseUrl; // data-base-url属性から値を取得
const sortField = form.dataset.sortField;
const sortOrder = form.dataset.sortOrder;

element.addEventListener("change", function () {
    console.log(sortField, sortOrder);
    const selectedYearMonth = element.value; // yyyy-mm
    window.location.href = `${baseUrl}?yearmonth=${selectedYearMonth}&sortField=${sortField}&sortOrder=${sortOrder}`;
});
