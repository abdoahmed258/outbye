<?php
include "../connect.php";

$input = json_decode(file_get_contents("php://input"), true);
$refresh_token = $_POST['refresh_token'] ?? '';

if ($refresh_token) {
    $stmt = $con->prepare("DELETE FROM refresh_tokens WHERE token = ?");
    $stmt->execute([$refresh_token]);
}

echo json_encode(["status" => "success", "message" => "Logged out successfully"]);
?>
