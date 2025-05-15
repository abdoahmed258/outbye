<?php
header("Content-Type: application/json");

include "../connect.php"; 
$headers = getallheaders();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

if (!$authHeader) {
    http_response_code(401);
    echo json_encode(["message" => "Missing token"]);
    exit;
}

$token = str_replace("Bearer ", "", $authHeader);
$decoded = verifyJWT($token);

if (!$decoded) {
    http_response_code(401);
    echo json_encode(["message" => "Invalid token"]);
    exit;
}

$user_id_from_token = $decoded->user_id;
$user_id_from_request = filterRequest("user_id");

if ($user_id_from_token != $user_id_from_request) {
    http_response_code(403);
    echo json_encode(["message" => "Access denied. This token is not for this user."]);
    exit;
}


$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
$rating = $_POST['rating'];
$comment = $_POST['comment'];
$service_type = $_POST['service_type']; // مثلا Website, Application, Support

try {
    $stmt = $con->prepare("INSERT INTO general_reviews (user_id, rating, comment, service_type) 
                           VALUES (:user_id, :rating, :comment, :service_type)");

    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':rating', $rating);
    $stmt->bindParam(':comment', $comment);
    $stmt->bindParam(':service_type', $service_type);

    $stmt->execute();

    echo json_encode(["success" => true, "message" => "Thank you for your feedback!"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>
