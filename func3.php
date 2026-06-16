<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. Connect to port 3306 (matching your updated database settings)
$con = mysqli_connect("localhost", "root", "", "myhmsdb", 3306);

// Diagnostic check if database connection fails
if (mysqli_connect_errno()) {
    die("<div style='background-color:#fee2e2; color:#991b1b; padding:20px; font-family:sans-serif;'>Admin Login Error: Database connection failed on port 3306.</div>");
}

if(isset($_POST['adsub'])) {
    $username = mysqli_real_escape_string($con, $_POST['username1']);
    $password = $_POST['password2'];

    $query = "SELECT * FROM admintb WHERE username='$username'";
    $result = mysqli_query($con, $query);

    if($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_array($result);

        // 2. Check the password. 
        // The default admin password in the SQL dump was plain text 'admin123'
        if($password === $row['password'] || password_verify($password, $row['password'])) {
            
            // Login successful! Set session and redirect to the admin dashboard
            $_SESSION['username'] = $username;
            header("Location: admin-panel1.php");
            exit();
            
        } else {
            echo "<script>alert('Invalid Admin Password! Please try again.'); window.location.href = 'index.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Admin Username not found in database!'); window.location.href = 'index.php';</script>";
        exit();
    }
} else {
    // If someone tries to access func3.php directly without submitting the form
    header("Location: index.php");
    exit();
}
?>