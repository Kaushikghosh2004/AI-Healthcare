<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid = $_POST['pid'] ?? null;
    $descriptor = $_POST['face_descriptor'] ?? null;

    if (!$pid || !$descriptor) {
        echo json_encode(['registration_success' => false, 'error' => 'Invalid data stream packet payload.']);
        exit;
    }

    $host = 'localhost:3306';
    $dbname = 'myhmsdb';
    $db_user = 'root';
    $db_pass = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Bind and store the 128-digit array string directly inside the modified profile text column
        $stmt = $pdo->prepare("UPDATE patreg SET face_image_path = ? WHERE pid = ?");
        $stmt->execute([$descriptor, $pid]);

        echo json_encode(['registration_success' => true]);
        exit;

    } catch (PDOException $e) {
        echo json_encode(['registration_success' => false, 'error' => 'Database persistence layer execution fault.']);
        exit;
    }
}