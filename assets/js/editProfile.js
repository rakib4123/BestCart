

function numberChecker() {
    let phone = document.getElementById("phone");
    if (phone.value.length !== 11) {
        alert("give a proper phone number");
        phone.focus();
        return false;
    }
    else {
        return true;
    }
}