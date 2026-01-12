<?php
    include('../../controllers/authCheck.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/profileStyle.css">
    <link rel="stylesheet" href="../../assets/css/changePassword.css">
    <link rel="stylesheet" href="https://cdn.hugeicons.com/font/hgi-stroke-rounded.css" />
    <title>Change Password</title>
</head>
<body style="margin: 0;">
    <!-- navbar -->
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


    <!-- mid section -->
    <section id="passContainer">
        <form action="../../controllers/passChanger.php" method="post" onsubmit="return check()">
            <div id="passBox">
                <h3>Email: <?php echo $_SESSION['email'];?></h3><br>
                <h3>Current Password: </h3><br>
                <input type="password" name="currentPassword" required>
                <h3>New Password: </h3><br>
                <input type="password" name="newPass" id="newPass" required>
                <h3>Confirm Password: </h3>
                <input type="password" name="confirmPass" id="confirmPass" required>
                <input type="submit" id="confirm" name="submit" value="Submit">
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
    <script src="../../assets/js/changePass.js"></script>
</body>
</html>