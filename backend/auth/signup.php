<?php

include "../connect.php";
require_once "../jwt.php"; // استدعاء ملف JWT

$username = filterRequest("username");
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // 🔑 استخدم hash أكثر أمانًا
$email = filterRequest("email");
$phone = filterRequest("phone");
$verfiycode = rand(10000, 99999); // رمز تحقق عشوائي

// ✅ تحقق من أن الاسم يحتوي فقط على حروف ومسافات
if (!preg_match('/^[\p{L} ]+$/u', $username)) {
    printFailure("Name must contain only letters and spaces.");
    exit;
}

// ✅ تحقق من أن رقم الهاتف يحتوي على 11 رقم بالضبط
if (!preg_match('/^[0-9]{11}$/', $phone)) {
    printFailure("Phone number must be exactly 11 digits.");
    exit;
}

// ✅ تحقق من صحة البريد الإلكتروني
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    printFailure("Invalid email format.");
    exit;
}

try {
    $stmt = $con->prepare("SELECT * FROM users WHERE users_email = ? OR users_phone = ?");
    $stmt->execute([$email, $phone]);
    $count = $stmt->rowCount();

    if ($count > 0) {
        printFailure("Email or phone number already exists");
    } else {
        $data = array(
            "users_name" => $username,
            "users_password" => $password,
            "users_email" => $email,
            "users_phone" => $phone,
            "users_verfiycode" => $verfiycode,
        );

        $subject = "Welcome to Outbye – Let’s Get Started!";

// تأكد من وضع رابط صحيح لصورة الشعار
$logoUrl = "https://abdulrahmanantar.com/outbye/outbye-logo.png"; 

$message = '
<div style="font-family: Arial, sans-serif; background-color: #f9f9f9; color: #333; padding: 30px; border-radius: 10px;">
    <div style="text-align: center;">
        <img src="' . $logoUrl . '" alt="Outbye Logo" style="width: 120px; margin-bottom: 20px;">
        <h2 style="color: #00bcd4;">Welcome to Outbye!</h2>
    </div>

    <p>Hi there 👋,</p>

    <p>We\'re excited to have you on board! You\'re one step away from unlocking a seamless experience with Outbye.</p>

    <p style="font-size: 16px; margin-top: 30px;"><strong>Your verification code is:</strong></p>

    <div style="text-align: center; margin: 20px 0;">
        <span style="font-size: 24px; font-weight: bold; color: #007bff; background-color: #e6f2ff; padding: 12px 24px; border-radius: 8px; display: inline-block;">
            ' . $verfiycode . '
        </span>
    </div>

    <p>Use this code to verify your account and start exploring everything Outbye has to offer—trips, restaurants, hidden gems, and more!</p>

    <p style="margin-top: 40px;">Happy exploring 🌍<br><strong>The Outbye Team</strong></p>
</div>
';


$additional_message = ""; // تم تضمين كل شيء في $message

$emailResult = sendEmail($email, $subject, $message, $additional_message);


        if ($emailResult === true) {
            if (insertData("users", $data)) {
                $user_id = $con->lastInsertId();
                $token = generateJWT($user_id);

                echo json_encode([
                    "status" => "success",
                    "message" => "User registered successfully. Please check your email to complete the registration.",
                    "user_id" => $user_id,
                    "token" => $token
                ]);
            } else {
                printFailure("Failed to insert user data.");
            }
        } else {
            printFailure("Failed to send the verification email: $emailResult");
        }
    }
} catch (PDOException $e) {
    printFailure("Database error: " . $e->getMessage());
}







