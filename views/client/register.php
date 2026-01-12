<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/styleReg.css">
    <link rel="stylesheet" href="https://cdn.hugeicons.com/font/hgi-stroke-rounded.css" />
    <title>Register</title>
</head>

<body style="margin: 0px;">
    <nav class="pink">

        <a class="navLink" href="home.php"><i class="hgi hgi-stroke hgi-home-09"></i>Home</a>
        <a class="navLink" href=""><i class="hgi hgi-stroke hgi-customer-service-01"></i>Help & Support</a>
        </div>
    </nav>
    <section class="logoArea" style="width: full; height: 70px; margin: 0px; padding: 5px;">
        <div class="logo">
            <a href="home.php"><img src="../../assets/images/logo.png" alt="" height="70px"></a>
            <!-- <h1 class="logoText">BestCart</h1> -->
        </div>
    </section>


    <!-- Login banner -->
    <section class="loginBanner">
        <div>
            <img src="../../assets/images/bags.png" alt="">
        </div>
        <div>
            <h1 class="signBanner">Register</h1>
            <p class="signText">To access the best products and deals <br>in Bangladesh in one touch!</p>
        </div>


        <!-- Form here -->
        <form action="../../controllers/registerCheck.php" method="post" onsubmit="return Confirm()">
            <div class="loginBox">

                <p>Email</p>
                <input id="mail" class="textField" type="email" name="email" placeholder="enter email" required>

                <p>Name</p>
                <input id="username" class="textField" type="text" name="username" placeholder="Your name" required>

                <p>Gender</p>
                <div class="genderGroup">
                    <label>
                        <input type="radio" name="gender" value="Male" required>
                        Male
                    </label>

                    <label>
                        <input type="radio" name="gender" value="Female">
                        Female
                    </label>

                    <label>
                        <input type="radio" name="gender" value="Other">
                        Other
                    </label>
                </div>
                <p>Your Address</p>
                <input id="address" class="textField" type="text" name="address" placeholder="Your Address" required>

                <p>Phone Number</p>
                <input id="phone" class="textField" type="tel" name="phone" placeholder="01XXXXXXXXX" required>

                <div id="passSection">
                    <p>Create password</p>
                    <input id="passField" class="textField" type="password" name="password"
                        placeholder="Create password" required>
                    <input type="submit" name="submit" id="confirmBtn" value="Confirm">
                </div>

                <div class="alreadySign">
                    <p class="aref">Already a user? </p><a class="signInLink" href="login.php">Sign in</a>
                </div>

            </div>
        </form>

    </section>


    <!-- footer -->
    <footer class="">
        <div class="footer">

            <div>
                <div class="about">
                    <i class="hgi hgi-stroke hgi-location-06"></i>
                    <p>Rockib Regnum Center, level-9, Chattogram, Bangladesh</p>
                </div>
                <div class="about">
                    <i class="hgi hgi-stroke hgi-call"></i>
                    <p>O1612975300</p>
                </div>
                <div class="about">
                    <i class="hgi hgi-stroke hgi-mail-01">
                    </i>
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

    <script src="../../assets/js/register.js"></script>
</body>

</html>