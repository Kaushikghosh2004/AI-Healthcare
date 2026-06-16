<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$host = 'localhost:3306';
$dbname = 'myhmsdb';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("<script>alert('System Error: Database connection failed.'); window.location.href='index.php';</script>");
}


if(isset($_POST['docsub1'])) {
    
    
    
    $doctor_email = isset($_POST['email3']) ? trim($_POST['email3']) : (isset($_POST['username3']) ? trim($_POST['username3']) : ''); 
    $dpassword = $_POST['password3'];

    
    
    $stmt = $pdo->prepare("SELECT * FROM doctb WHERE (email = ? OR username = ?) AND password = ?");
    $stmt->execute([$doctor_email, $doctor_email, $dpassword]);
    $result = $stmt->fetch();

    if($result) {
        
        
        $_SESSION['dname'] = $result['username'];
        
        
        header("Location: doctor-panel.php");
        exit();
    } else {
        
        echo "<script>
                alert('Access Denied: Invalid Doctor Credentials.'); 
                window.location.href='index.php#portals';
              </script>";
        exit();
    }
}
?>