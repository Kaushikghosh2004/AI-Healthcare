<?php
session_start();
require_once 'config.php'; // Your database connection ($pdo)

// 1. Mathematical formula to compare two faces
function calculateEuclideanDistance($descriptor1, $descriptor2) {
    if (count($descriptor1) !== count($descriptor2)) {
        return false;
    }
    $sum = 0.0;
    for ($i = 0; $i < count($descriptor1); $i++) {
        $diff = $descriptor1[$i] - $descriptor2[$i];
        $sum += ($diff * $diff);
    }
    return sqrt($sum);
}

// 2. The Security Threshold (The lower the number, the stricter the system)
// 0.60 is usually the default, which allows similar-looking people to pass.
// 0.40 to 0.45 is highly secure and prevents spoofing.
$SECURITY_THRESHOLD = 0.43;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read the incoming JSON payload from your frontend JavaScript
    $input = json_decode(file_get_contents('php://input'), true);
    
    $user_id = $input['user_id'] ?? null;
    $incoming_descriptor = $input['descriptor'] ?? null;

    // Validate the incoming data (Most facial recognition libs generate 128 points)
    if (!$user_id || !is_array($incoming_descriptor) || count($incoming_descriptor) !== 128) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Invalid face data format."]);
        exit;
    }

    try {
        // 3. Fetch the authorized admin's stored face data from MySQL
        // Note: You need a 'face_descriptor' column in your users table that stores the array as a JSON string.
        $stmt = $pdo->prepare("SELECT face_descriptor FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || empty($user['face_descriptor'])) {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "User not found or no face enrolled."]);
            exit;
        }

        // Convert the stored JSON string back into a PHP array
        $stored_descriptor = json_decode($user['face_descriptor'], true);

        // 4. Perform Server-Side Matching
        $distance = calculateEuclideanDistance($incoming_descriptor, $stored_descriptor);

        // 5. Grant or Deny Access
        if ($distance !== false && $distance < $SECURITY_THRESHOLD) {
            
            // MATCH SUCCESS: Grant the session
            $_SESSION['admin_loggedin'] = true;
            $_SESSION['admin_id'] = $user_id;
            
            echo json_encode([
                "status" => "success", 
                "message" => "Authentication verified securely.",
                "redirect" => "admin_dashboard.php"
            ]);
            
        } else {
            // MATCH FAILED: The face is a fake, a photo, or someone else.
            http_response_code(401);
            
            // Optional: Log this to a security table to track spoofing attempts
            error_log("Spoof attempt blocked for User ID: $user_id. Distance: $distance");
            
            echo json_encode(["status" => "error", "message" => "Face verification failed. Access Denied."]);
        }
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database error."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed."]);
}
?>