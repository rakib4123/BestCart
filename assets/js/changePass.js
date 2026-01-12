
const check = () => {
    let newPass = document.getElementById("newPass");
    let confirmPass = document.getElementById("confirmPass");
    if (newPass.value === confirmPass.value && newPass.value.length > 6 && confirmPass.value.length > 6) {
        return true;
    }
    else if(newPass.value.length < 6 || confirmPass.value.length < 6){
        alert("password is short")
        return false;
    }
    else if(newPass.value !== confirmPass.value){
        alert("passwords does not match!")
        return false;
    }
}