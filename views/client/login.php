<?php
session_start();

// IF USER IS ALREADY LOGGED IN:
if (isset($_SESSION['email'])) {
    // Check if Admin
    if (isset($_SESSION['admin_status'])) {
        header('Location: dashboard.php');
        exit();
    } 
    // Check if User
    else {
        header('Location: home.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
    <link rel="stylesheet" href="../../assets/css/styleLogin.css">
    <link rel="stylesheet" href="https://cdn.hugeicons.com/font/hgi-stroke-rounded.css" />
</head>

<body style="margin: 0px;">
    <nav class="pink">
        <a class="navLink" href="home.php"><i class="hgi hgi-stroke hgi-home-09"></i>Home</a>
        <a class="navLink" href="#"><i class="hgi hgi-stroke hgi-customer-service-01"></i>Help & Support</a>
    </nav>
    
    <section class="logoArea" style="width: 100%; height: 70px; margin: 0px; padding: 5px;">
        <div class="logo">
            <a href="home.php"><img src="../../assets/images/logo.png" alt="" height="70px"></a>
            <!-- <h1 class="logoText">BestCart</h1> -->
        </div>
    </section>

    <section class="loginBanner">
        <div>
            <img src="../../assets/images/loginpic.png" alt="">
        </div>
        <div>
            <h1 class="signBanner">Sign In</h1>
            <p class="signText">To access the best products and deals <br>in Bangladesh in one touch!</p>
        </div>
        
        <form action="../../controllers/loginCheck.php" method="post" onsubmit="return signIn();">
            <div class="loginBox">
                <p>Email</p>
                <input id="loginMail" class="textField" type="text" name="email" placeholder="Enter email or phone" required>
                
                <p>Password</p>
                <input id="loginPass" class="textField" type="password" name="password" placeholder="Enter Password" required>
                
                <div>
                    <input type="submit" name="submit" id="loginBtn" value="Sign In">               
                </div>
                
                <div class="regLine">
                    <p class="regP">New to BestCart?</p> <a class="Register" href="register.php">Register</a>
                </div>
            </div>
        </form>
    </section>

    <footer class="">
        <div class="footer">
            <div>
                <div class="about">
                    <i class="hgi hgi-stroke hgi-location-06"></i>
                    <p>Rockib Regnum Center, level-9, Chattogram, Bangladesh</p>
                </div>
                <div class="about">
                    <i class="hgi hgi-stroke hgi-call"></i>
                    <p>01612975300</p>
                </div>
                <div class="about">
                    <i class="hgi hgi-stroke hgi-mail-01"></i>
                    <p>customer.care@bestcart.com</p>
                </div>
            </div>

            <div class="aref">
                <p class="titleFooter">BestCart</p>
                <a href="">About Us</a>
                <a href="">BestCart Blog</a>
                <a href="">Cookies Policy</a>
            </div>

            <div class="aref">
                <p class="titleFooter">Customer Care</p>
                <a href="">Return & Refund</a>
                <a href="">Privacy Policy</a>
                <a href="">Return Policy</a>
                <a href="">Terms & Conditions</a>
            </div>
            
            <div>
                <p>Follow us on:</p>
                <a class="followLink" href=""><i class="hgi hgi-stroke hgi-instagram"></i></a>
                <a class="followLink" href=""><i class="hgi hgi-stroke hgi-youtube"></i></a>
                <a class="followLink" href=""><i class="hgi hgi-stroke hgi-linkedin-01"></i></a>
                <a class="followLink" href=""><i class="hgi hgi-stroke hgi-facebook-01"></i></a>
            </div>
        </div>
        <div>
            <div class="copyright">
                <i class="hgi hgi-stroke hgi-copyright"></i>
                <p>Copyright 2025 BestCart All Rights are Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="../../assets/js/loginScript.js"></script>
</body>
</html>