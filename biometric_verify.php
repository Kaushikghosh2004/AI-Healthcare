<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

header('Content-Type: application/json');

// Helper function to calculate Euclidean Distance between two 128-dimensional vectors
function calculateEuclideanDistance($vector1, $vector2) {
    if (count($vector1) !== count($vector2)) return 1.0; // Mismatch in vector size
    
    $sum = 0.0;
    for ($i = 0; $i < count($vector1); $i++) {
        $diff = $vector1[$i] - $vector2[$i];
        $sum += $diff * $diff;
    }
    return sqrt($sum);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_face'])) {
    
    $pid = $_POST['pid'] ?? null;
    // Expecting a JSON-stringified array of 128 numbers from the frontend webcam scan
    $live_descriptor_raw = $_POST['face_descriptor'] ?? null; 

    if (!$pid || !$live_descriptor_raw) {
        echo json_encode(['match' => false, 'error' => 'Missing face descriptor data.']);
        exit;
    }

    $live_descriptor = json_decode($live_descriptor_raw, true);

    $host = 'localhost:3306';
    $dbname = 'myhmsdb';
    $db_user = 'root';
    $db_pass = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 1. Fetch the stored 128-digit vector from the database
        $stmt = $pdo->prepare("SELECT face_image_path FROM patreg WHERE pid = ?");
        $stmt->execute([$pid]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$patient || empty($patient['face_image_path'])) {
            echo json_encode(['match' => false, 'error' => 'No local biometric profile registered for this ID.']);
            exit;
        }

        $stored_descriptor = json_decode($patient['face_image_path'], true);

        if (!is_array($stored_descriptor) || !is_array($live_descriptor)) {
            echo json_encode(['match' => false, 'error' => 'Invalid biometric data formatting.']);
            exit;
        }

        // 2. Calculate local vector distance
        $distance = calculateEuclideanDistance($stored_descriptor, $live_descriptor);

        // Threshold configuration: In facial recognition, a distance less than 0.6 
        // strictly indicates that the vectors belong to the same human face.
        $threshold = 0.55; 
        $is_match = ($distance < $threshold);

        // Convert distance to a readable accuracy percentage for your log UI
        $confidence_score = round((1 - $distance) * 100, 2);
        if ($confidence_score > 100) $confidence_score = 100;
        if ($confidence_score < 0) $confidence_score = 0;

        // 3. System Log
        $log_stmt = $pdo->prepare("
            INSERT INTO ai_triage_log (pid, symptoms, ai_analysis, recommended_specialist, urgency_level) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $log_action = $is_match ? 'SUCCESS' : 'FAILED_SECURITY_MATCH';
        $log_details = json_encode([
            'event' => 'Local Vector Biometric Scan',
            'vector_distance' => round($distance, 4),
            'status' => $log_action
        ]);

        $log_stmt->execute([
            $pid, 
            'LOCAL_BIOMETRIC_AUTH', 
            $log_details, 
            'Local Security Matrix', 
            $is_match ? 'LOW' : 'HIGH'
        ]);

        // 4. Return Final Decision
        echo json_encode([
            'match' => $is_match,
            'pid' => $pid,
            'distance' => $distance,
            'confidence' => $confidence_score . '%'
        ]);
        exit;

    } catch(PDOException $e) {
        echo json_encode(['match' => false, 'error' => 'Local database connection error.']);
        exit;
    }
}

echo json_encode(['match' => false, 'error' => 'Bad Request']);
exit;