let mail = document.getElementById("mail");
let phone = document.getElementById("phone");
let address = document.getElementById("address");
let username = document.getElementById("username");
let confirmBtn = document.getElementById("confirmBtn");
let passField = document.getElementById("passField");
let passSection = document.getElementById("passSection");


Confirm = () => {
    if (mail.value == "") {
        alert("Please a input mail.");
        return false;
    }
    else if(!mail.checkValidity()){
        alert("Input a proper mail.");
        return false;
    }
    else if(address.value == ""){
        alert("Input your address.");
        return false;
    }
    else if(phone.value == "" ||phone.value.length!=11){
        alert("Input proper phone number");
        return false;
    }
    else if(username.value == ""){
        alert("Input your name.");
        return false;
    }
    else if(passField.value.length < 5){
        alert("The password is too short, at least 6 characters is required");
        return false;
    }
    return true;
}



confirmBtn.addEventListener("click", Confirm);




checkMailField = () => {
    if (mail.value !== "") {
        continueBtn.style.backgroundColor = "#2563eb";
        continueBtn.style.color = "#ffffff";
    }
    
    else{
        continueBtn.style.backgroundColor = "#EFF1F4";
        continueBtn.style.color = "#858D9E";
    }
}

checkConfirm= ()=>{
    if(passField.value.length > 6){
        confirmBtn.style.backgroundColor = "#2563eb";
        confirmBtn.style.color = "white";
    }
    else{
        confirmBtn.style.backgroundColor = "#EFF1F4";
        confirmBtn.style.color = "#858D9E";
    }
}




mail.addEventListener("input", checkMailField);

passField.addEventListener("input", checkConfirm);

