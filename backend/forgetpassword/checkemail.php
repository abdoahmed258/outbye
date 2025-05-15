<?php


include "../connect.php";
$email = filterRequest("email");

// إنشاء كود التحقق العشوائي
$verfiycode = rand(10000, 99999);

// التحقق مما إذا كان البريد الإلكتروني موجودًا في قاعدة البيانات
$stmt = $con->prepare("SELECT users_id FROM users WHERE users_email = ?");
$stmt->execute([$email]);
$count = $stmt->rowCount();

if ($count > 0) {
    // تحديث كود التحقق في قاعدة البيانات
    $updateStmt = $con->prepare("UPDATE users SET users_verfiycode = ? WHERE users_email = ?");
    $isUpdated = $updateStmt->execute([$verfiycode, $email]);

    if ($isUpdated) {


        $subject = "No worries! Resetting your password is simple";

$message = "
<div style=\"font-family: Arial, sans-serif; background-color: #f9f9f9; color: #333; padding: 30px; border-radius: 10px;\">
    <div style=\"text-align: center;\">
        <img src=\"https://abdulrahmanantar.com/outbye/outbye-logo.png\" alt=\"Outbye Logo\" style=\"width: 120px; margin-bottom: 20px;\">
        
    </div>

    <p>Hi there 👋,</p>

    <p>No worries! We've received your request to reset your password.</p>

    <p style=\"font-size: 16px; margin-top: 30px;\"><strong>Your password reset code is:</strong></p>

    <div style=\"text-align: center; margin: 20px 0;\">
        <span style=\"font-size: 24px; font-weight: bold; color: #007bff; background-color: #e6f2ff; padding: 12px 24px; border-radius: 8px; display: inline-block;\">
            " . htmlspecialchars($verfiycode) . "
        </span>
    </div>

    <p>Use this code to verify your identity and reset your password securely.</p>

    <p style=\"margin-top: 30px;\">If you didn't request a password reset, please ignore this email.</p>

    <p style=\"margin-top: 40px;\">Stay safe,<br><strong>The Outbye Team</strong></p>
</div>
";

sendEmail($email, $subject, $message);

    
        // إرسال استجابة تفيد بأن العملية نجحت
        echo json_encode(["success" => true, "message" => "Verification code sent."]);
    } else {
        // في حال فشل التحديث
        echo json_encode(["success" => false, "message" => "Failed to update verification code."]);
    }
} else {
    // في حال عدم العثور على البريد الإلكتروني
    echo json_encode(["success" => false, "message" => "Email not found."]);
}
?>
