<?php





include "../connect.php";
require_once "../jwt.php"; // استدعاء ملف JWT

$login = filterRequest("email"); // هذا الحقل قد يحتوي إما إيميل أو رقم هاتف
$password = $_POST['password'];

// ✅ التحقق من أن كلمة المرور غير فارغة
if (empty($password)) {
    echo json_encode(["status" => "error", "message" => "Password is required"]);
    exit;
}

// ✅ تحديد ما إذا كان المستخدم أدخل بريد إلكتروني أو رقم هاتف
if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
    $field = "users_email";
} elseif (preg_match("/^[0-9]{11}$/", $login)) {
    $field = "users_phone";
} else {
    echo json_encode(["status" => "error", "message" => "Invalid email or phone number"]);
    exit;
}

// ✅ جلب بيانات المستخدم بناءً على الحقل المحدد
$stmt = $con->prepare("SELECT * FROM users WHERE $field = ? AND users_approve = 1");
$stmt->execute([$login]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    if (password_verify($password, $user['users_password'])) {
        $token = generateJWT($user['users_id']);

        echo json_encode([
            "status" => "success",
            "message" => "Login successful",
            "user_id" => $user['users_id'],
            "token" => $token
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid password"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "User not found or not approved"]);
}
