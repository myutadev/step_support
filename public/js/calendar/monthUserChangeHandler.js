const monthElement = document.getElementById("monthInput");
const userElement = document.getElementById("userInput");
const form = document.getElementById("monthUserForm");

monthElement.addEventListener("change", function () {
    const selectedMonth = monthElement.value; // yyyy-mm
    const selectedUser = userElement.value; // yyyy-mm
    window.location.href =
        "/admin/timecard/" + selectedMonth + "/" + selectedUser;
});

userElement.addEventListener("change", function () {
    const selectedMonth = monthElement.value; // yyyy-mm
    const selectedUser = userElement.value; // yyyy-mm
    window.location.href =
        "/admin/timecard/" + selectedMonth + "/" + selectedUser;
});

$(document).ready(function () {
    $("#userInput")
        .select2({
            theme: "bootstrap",
        })
        .on("change", function (e) {
            // Select2の選択が変更されたときの処理
            const selectedMonth = monthElement.value; // yyyy-mm
            const selectedUser = $("#userInput").val(); // 選択されたユーザーID
            window.location.href =
                "/admin/timecard/" + selectedMonth + "/" + selectedUser;
        });
});
