<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$con = mysqli_connect("localhost", "root", "", "myhmsdb", 3306);

if(isset($_POST['patreg'])) {
    $fname = mysqli_real_escape_string($con, $_POST['fname']);
    $lname = mysqli_real_escape_string($con, $_POST['lname']);
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $contact = mysqli_real_escape_string($con, $_POST['contact']);
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    if($password == $cpassword) {
        
        $check_query = "SELECT * FROM patreg WHERE email='$email'";
        $check_res = mysqli_query($con, $check_query);
        
        if(mysqli_num_rows($check_res) > 0) {
             echo "<script>alert('Email already registered! Please use another email.'); window.location.href = 'registration.php';</script>";
             exit();
        }

        // Hash the password before storing
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // FIXED: Removed 'cpassword' from the columns and values
        $query = "INSERT INTO patreg(fname, lname, gender, email, contact, password) 
                  VALUES ('$fname', '$lname', '$gender', '$email', '$contact', '$hashed_password')";
        $result = mysqli_query($con, $query);

        if($result) {
            
            // FIXED: Use mysqli_insert_id to get the ID immediately instead of running another SELECT query
            $_SESSION['pid'] = mysqli_insert_id($con);
            $_SESSION['username'] = $fname . " " . $lname;
            $_SESSION['fname'] = $fname;
            $_SESSION['lname'] = $lname;
            $_SESSION['gender'] = $gender;
            $_SESSION['contact'] = $contact;
            $_SESSION['email'] = $email;

            header("Location: admin-panel.php");
            exit(); 
        } else {
            die("Registration Error: " . mysqli_error($con));
        }
    } else {
        echo "<script>alert('Passwords do not match'); window.location.href = 'registration.php';</script>";
        exit();
    }
}

if(isset($_POST['patsub'])) {
    // 1. Check if the database connection is actually working
    if (mysqli_connect_errno()) {
        die("DIAGNOSTIC ERROR: Database connection failed. " . mysqli_connect_error());
    }

    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password2'];

    $query = "SELECT * FROM patreg WHERE email='$email'";
    $result = mysqli_query($con, $query);

    if($result && mysqli_num_rows($result) == 1) {
        $user_data = mysqli_fetch_array($result);

        // 2. Check if the database contains a plain-text password instead of a hash
        if ($user_data['password'] === $password) {
            die("DIAGNOSTIC ERROR: Your database has a plain-text password. You need to delete this account and register a new one so it encrypts properly.");
        }

        // 3. Normal secure verification
        if(password_verify($password, $user_data['password'])) {
            $_SESSION['pid'] = $user_data['pid'];
            $_SESSION['username'] = $user_data['fname'] . " " . $user_data['lname'];
            $_SESSION['fname'] = $user_data['fname'];
            $_SESSION['lname'] = $user_data['lname'];
            $_SESSION['gender'] = $user_data['gender'];
            $_SESSION['contact'] = $user_data['contact'];
            $_SESSION['email'] = $user_data['email'];

            header("Location: admin-panel.php");
            exit();
        } else {
            die("DIAGNOSTIC ERROR: The email was found, but the password does not match the encrypted hash in the database.");
        }
    } else {
        die("DIAGNOSTIC ERROR: This email does not exist in the database. Registration likely failed.");
    }
}
?>