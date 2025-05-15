<?php
include "../connect.php";
require_once "../jwt.php";

// ✅ استقبل التوكن سواء من JSON أو من form-data
$inputJSON = file_get_contents("php://input");
$input = json_decode($inputJSON, true);

$refresh_token = $input['refresh_token'] ?? ($_POST['refresh_token'] ?? '');

if (!$refresh_token) {
    echo json_encode(["error" => "No refresh token provided"]);
    exit;
}

// ✅ تحقق من وجود التوكن وصلاحيته
$stmt = $con->prepare("SELECT user_id FROM refresh_tokens WHERE token = ? AND expires_at > NOW()");
$stmt->execute([$refresh_token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $new_access_token = generateJWT($user['user_id']);

    echo json_encode([
        "status" => "success",
        "access_token" => $new_access_token,
        "expires_in" => 7200  // 2 ساعات بالثواني
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid or expired refresh token"
    ]);
}
?>
