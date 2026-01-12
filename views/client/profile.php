<?php

require_once('../../controllers/authCheck.php');
require_once('../../models/userModel.php');

$email = $_SESSION['email'];

$con = getConnection();
$emailEsc = mysqli_real_escape_string($con, $email);
$sql = "SELECT username, phone, gender, address, email 
        FROM userinfo WHERE email='$emailEsc' LIMIT 1";
$result = mysqli_query($con, $sql);
$currentUserData = mysqli_fetch_assoc($result);

$myOrders = [];
$sqlOrders = "SELECT id, order_date, status, total_amount FROM orders WHERE email='$emailEsc' ORDER BY id DESC LIMIT 10";
$resOrders = mysqli_query($con, $sqlOrders);
if ($resOrders && mysqli_num_rows($resOrders) > 0) {
    while ($row = mysqli_fetch_assoc($resOrders)) {
        $myOrders[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/profileStyle.css">
    <title>Profile</title>
</head>

<body style="margin: 0;">

<nav class="pink">
    <a class="navLink" href="home.php">Home</a>
    <a class="navLink" href="#">Help & Support</a>
</nav>

<section class="logoArea">
    <div class="logo">
        <a href="home.php"><img src="../../assets/images/logo.png" height="70px"></a>
        <!-- <h1>BestCart</h1> -->
    </div>
</section>

<section id="center">

<div class="options">
    <div>
        <p>My Profile</p>
        <a class="option" id="logout" href="../../controllers/logout.php">Logout</a>
    </div>
</div>


<div id="optionDetails">
    <h2>My profile</h2>

    <div class="myProfile">

        <div>
            <h3>My full name</h3>
            <p><?php echo $currentUserData['username']; ?></p>

            <h3>Gender</h3>
            <p><?php echo $currentUserData['gender']; ?></p>

            <h3>Phone Number</h3>
            <p><?php echo $currentUserData['phone']; ?></p>
        </div>

        <div>
            <h3>My address</h3>
            <p><?php echo $currentUserData['address']; ?></p>

            <h3>Email</h3>
            <p><?php echo $currentUserData['email']; ?></p>
        </div>

    </div>


    
    <div class="btnGrp">

        <a href="changePassword.php">
            <button class="profileBtn">
                <i class="hgi hgi-stroke hgi-key-01"></i>
                Change Password
            </button>
        </a>

        <a href="editProfile.php">
            <button class="profileBtn">
                <i class="hgi hgi-stroke hgi-edit-02"></i>
                Edit Profile
            </button>
        </a>
</div>

<div style="margin-top:25px;">
    <h2 style="color:#2563eb;margin-bottom:10px;">My Orders</h2>

    <?php if (empty($myOrders)): ?>
        <div style="background:#00acd4;padding:18px;border-radius:10px;color:#black;">
            You have no orders yet.
        </div>
    <?php else: ?>
        <div style="overflow:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:#c0cfeeff;color:#2563eb;">
                        <th style="border:1px solid #2563eb;padding:10px;text-align:left;">Order ID</th>
                        <th style="border:1px solid #2563eb;padding:10px;text-align:left;">Date</th>
                        <th style="border:1px solid #2563eb;padding:10px;text-align:left;">Status</th>
                        <th style="border:1px solid #2563eb;padding:10px;text-align:left;">Total</th>
                        <th style="border:1px solid #2563eb;padding:10px;text-align:left;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($myOrders as $o): ?>
                        <tr>
                            <td style="border:1px solid #2563eb;padding:10px;"><?php echo (int)$o['id']; ?></td>
                            <td style="border:1px solid #2563eb;padding:10px;"><?php echo htmlspecialchars($o['order_date'] ?? ''); ?></td>
                            <td style="border:1px solid #2563eb;padding:10px;"><?php echo htmlspecialchars($o['status'] ?? ''); ?></td>
                            <td style="border:1px solid #2563eb;padding:10px;">৳ <?php echo (int)$o['total_amount']; ?></td>
                            <td style="border:1px solid #2563eb;padding:10px;">
                                <a href="order_invoice.php?id=<?php echo (int)$o['id']; ?>"
                                   style="text-decoration:none;background:#2563eb;color:#fff;padding:7px 10px;border-radius:8px;font-weight:600;display:inline-block;">
                                   Invoice
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top:12px;">
            <a href="order_history.php" style="color:#2563eb;font-weight:600;text-decoration:none;">
                View all orders →
            </a>
        </div>
    <?php endif; ?>
</div>


</div>

</section>

</body>
</html>
