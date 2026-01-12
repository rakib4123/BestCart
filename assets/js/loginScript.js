let password = document.getElementById("loginPass");
let mail = document.getElementById("loginMail");
let loginBtn = document.getElementById("loginBtn");


signIn = () => {
    if (mail.value == "" || password.value == "") {
        alert("Please input mail and password.");
        return false;
    }
    else{
        return true;
    }
}

checkField = () => {
    if (mail.value !== "" && password.value !== "") {
        loginBtn.style.backgroundColor = "#2563eb";
        loginBtn.style.color = "white";

    }else{
        loginBtn.style.backgroundColor = "#EFF1F4";
        loginBtn.style.color = "#858D9E";
    }
}


mail.addEventListener("input", checkField);
password.addEventListener("input", checkField);