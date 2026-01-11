<?php
    require_once('db.php');


    function getAllUser(){
        $con = getConnection();
        $result = mysqli_query($con, "SELECT * FROM users ORDER BY id DESC");
        $data = [];
        while($row = mysqli_fetch_assoc($result)) $data[] = $row;
        return $data;
    }

    function getUserById($id){
        $con = getConnection();
        $id = mysqli_real_escape_string($con, $id);
        $result = mysqli_query($con, "SELECT * FROM users WHERE id=$id");
        return mysqli_fetch_assoc($result);
    }

    function addUser($userOrUsername, $email = null, $password = null, $role = "customer"){
    $con = getConnection();

    
    
    
    if (is_array($userOrUsername)) {
        $user = $userOrUsername;
    } else {
        $user = [
            'username' => (string)$userOrUsername,
            'email' => (string)$email,
            'password' => (string)$password,
            'role' => (string)$role,
            'gender' => '',
            'phone' => '',
            'address' => ''
        ];
    }

    $username = mysqli_real_escape_string($con, $user['username'] ?? '');
    $email    = mysqli_real_escape_string($con, $user['email'] ?? '');
    $password = mysqli_real_escape_string($con, $user['password'] ?? '');
    $gender   = mysqli_real_escape_string($con, $user['gender'] ?? '');
    $phone    = mysqli_real_escape_string($con, $user['phone'] ?? '');
    $address  = mysqli_real_escape_string($con, $user['address'] ?? '');

    
    $roleRaw = strtolower(trim((string)($user['role'] ?? 'customer')));
    $roleDb  = ($roleRaw === 'admin') ? 'admin' : 'customer';

    
    $checkSql = "SELECT id FROM users WHERE email='$email' LIMIT 1";
    $checkResult = mysqli_query($con, $checkSql);
    if($checkResult && mysqli_num_rows($checkResult) > 0){
        return false;
    }

    
    $sql1 = "INSERT INTO users (username, password, email, role)
             VALUES ('$username', '$password', '$email', '$roleDb')";
    $status1 = mysqli_query($con, $sql1);

    
    $sql2 = "INSERT INTO userinfo (username, email, role, phone, address, gender)
             VALUES ('$username', '$email', '$roleDb', '$phone', '$address', '$gender')";
    $status2 = mysqli_query($con, $sql2);

    if($status1 && $status2){
        return true;
    }else{
        return false;
    }
}

    

    function getUserByUsername($username){
        $con = getConnection();
        $username = mysqli_real_escape_string($con, $username);
        $sql = "SELECT * FROM users WHERE username='$username' LIMIT 1";
        $res = mysqli_query($con, $sql);
        if($res && mysqli_num_rows($res) === 1){
            return mysqli_fetch_assoc($res);
        }
        return null;
    }


    function login($user){
    $con = getConnection();

    $email    = $user['email'];
    $password = $user['password'];

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password' LIMIT 1";
    $result = mysqli_query($con, $sql);

    if($result && ($row = mysqli_fetch_assoc($result))){
        return $row; 
    }
    return false;
    }

    
    function insertOrUpdateProfile($data){
    $con = getConnection();

    $email    = $data['email'];
    $username = $data['username'];
    $phone    = $data['phone'];
    $gender   = $data['gender'];
    $address  = $data['address'];

    
    $sql1 = "UPDATE userinfo 
            SET username='$username', phone='$phone', gender='$gender', address='$address'
            WHERE email='$email'";
    $ok1 = mysqli_query($con, $sql1);

    
    $sql2 = "UPDATE users 
            SET username='$username'
            WHERE email='$email'";
    $ok2 = mysqli_query($con, $sql2);

    if($ok1 && $ok2){
        return true;
    }
    return false;
}



    function updatePassword($email, $newPassword) {
    $con = getConnection();  
    
    $sql = "UPDATE users SET password = '$newPassword' WHERE email = '$email'";

    if (mysqli_query($con, $sql)) {
        return true;  
    } else {
        return false;  
    }
    }

    function updateUser($u)
    {
    $con = getConnection();
    $id = $u['id'];
    $username = $u['username'];
    $email = $u['email'];
    $role = $u['role'];
    $sql = "UPDATE users SET username='$username', email='$email', role='$role' WHERE id=$id";
    return mysqli_query($con, $sql);
    }

    function deleteUser($id){
        $con = getConnection();
        return mysqli_query($con, "DELETE FROM users WHERE id=$id");
    }
?>