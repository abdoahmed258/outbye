<?php


include "../connect.php";
require_once "../jwt.php";

$login = filterRequest("email");
$password = $_POST['password'];

if (empty($password)) {
    echo json_encode(["status" => "error", "message" => "Password is required"]);
    exit;
}

$field = filter_var($login, FILTER_VALIDATE_EMAIL) ? "users_email" : (preg_match("/^[0-9]{11}$/", $login) ? "users_phone" : null);
if (!$field) {
    echo json_encode(["status" => "error", "message" => "Invalid email or phone number"]);
    exit;
}

$stmt = $con->prepare("SELECT * FROM users WHERE $field = ? AND users_approve = 1");
$stmt->execute([$login]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['users_password'])) {
    $access_token = generateJWT($user['users_id']);
    $refresh_token = generateRefreshToken();
    $expires_at = date('Y-m-d H:i:s', time() + (60 * 60 * 24 * 30)); // شهر

    // احذف القديم وأضف الجديد
    $stmt = $con->prepare("DELETE FROM refresh_tokens WHERE user_id = ?");
    $stmt->execute([$user['users_id']]);

    $stmt = $con->prepare("INSERT INTO refresh_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$user['users_id'], $refresh_token, $expires_at]);

    echo json_encode([
        "status" => "success",
        "message" => "Login successful",
        "user_id" => $user['users_id'],
        "access_token" => $access_token,
        "refresh_token" => $refresh_token
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid credentials or unapproved account"]);
}


