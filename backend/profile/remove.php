<?php
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
$user_id_from_request = filterRequest("users_id");

if ($user_id_from_token != $user_id_from_request) {
    http_response_code(403);
    echo json_encode(["message" => "Access denied. This token is not for this user."]);
    exit;
}

// ✅ التوكن يخص نفس المستخدم.. كمل تنفيذ العملية

$user_id = filterRequest("users_id");

// تأكد أن المستخدم موجود ومفعل
$stmt = $con->prepare("SELECT users_image FROM users WHERE users_id = ? AND users_approve = 1");
$stmt->execute([$user_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    if (!empty($row['users_image'])) {
        $image_path = "../upload/profile/" . basename($row['users_image']);

        // حذف الصورة من السيرفر
        if (file_exists($image_path)) {
            unlink($image_path);
        }

        // تحديث قاعدة البيانات لتفريغ users_image
        $stmt = $con->prepare("UPDATE users SET users_image = NULL WHERE users_id = ?");
        $stmt->execute([$user_id]);

        echo json_encode([
            "status" => "success",
            "message" => "تم حذف الصورة بنجاح"
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status" => "failure",
            "message" => "لا توجد صورة لحذفها"
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode([
        "status" => "failure",
        "message" => "المستخدم غير موجود أو غير مفعل"
    ], JSON_UNESCAPED_UNICODE);
}
