<?php



 include "../../connect.php";
require_once "../../jwt.php"; // استدعاء ملف JWT

$email = filterRequest("email");
$password = $_POST['password']; // 🔑 لا تشفرها هنا لأننا سنستخدم password_verify

// ✅ جلب بيانات المستخدم
$stmt = $con->prepare("SELECT * FROM `admin` WHERE admin_email = ? AND admin_approve = 1");
$stmt->execute(array($email));
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // ✅ استخدم password_verify لمطابقة كلمة المرور مع القيمة المخزنة
    if (password_verify($password, $user['admin_password'])) {
        // ✅ توليد JWT Token
        $token = generateJWT($user['admin_id']);

        // ✅ إرجاع البيانات مع التوكن
        echo json_encode([
            "status" => "success",
            "message" => "Login successful",
            "user_id" => $user['admin_id'],
            "token" => $token
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid password"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "User not found"]);
}
