<?php

include "../../connect.php";
require_once "../../jwt.php"; // استدعاء ملف JWT

// استلام البيانات
$username = filterRequest("username");
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$email    = filterRequest("email");

// ✅ تحقق من صحة البريد الإلكتروني
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    printFailure("Invalid email format.");
    exit;
}

// ✅ تحقق من أن الاسم يحتوي فقط على حروف ومسافات
if (!preg_match('/^[\p{L} ]+$/u', $username)) {
    printFailure("Name must contain only letters and spaces.");
    exit;
}

// ✅ تحقق إذا البريد موجود مسبقًا
$stmt = $con->prepare("SELECT * FROM admin WHERE admin_email = ?");
$stmt->execute([$email]);

if ($stmt->rowCount() > 0) {
    printFailure("Email already exists.");
    exit;
}

// ✅ توليد secret فريد لهذا الأدمن
$admin_secret = bin2hex(random_bytes(32));

// ✅ تجهيز البيانات للإدخال
$data = array(
    "admin_name"     => $username,
    "admin_password" => $password,
    "admin_email"    => $email,
    "admin_secret"   => $admin_secret,
    "admin_approve"  => 1  // موافق عليه مباشرة، غيّره حسب حاجتك
);

// ✅ إدخال البيانات في قاعدة البيانات
if (insertData("admin", $data)) {
    $admin_id = $con->lastInsertId();

    // توليد التوكن باستخدام الـ secret
    $token = generateJWT($admin_id, $admin_secret);

    echo json_encode([
        "status"  => "success",
        "message" => "Admin registered successfully",
        "admin_id" => $admin_id,
        "token"    => $token
    ]);
} else {
    printFailure("Failed to register admin.");
}
