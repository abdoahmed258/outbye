<?php


include "../../connect.php";
$email = filterRequest("email");

// إنشاء كود التحقق العشوائي
$verfiycode = rand(10000, 99999);

// التحقق مما إذا كان البريد الإلكتروني موجودًا في قاعدة البيانات
$stmt = $con->prepare("SELECT * FROM `admin` WHERE admin_email = ?");
$stmt->execute([$email]);
$count = $stmt->rowCount();

if ($count > 0) {
    // تحديث كود التحقق في قاعدة البيانات
    $updateStmt = $con->prepare("UPDATE `admin` SET admin_verfiycode = ? WHERE admin_email = ?");
    $isUpdated = $updateStmt->execute([$verfiycode, $email]);

    if ($isUpdated) {
        // إرسال البريد الإلكتروني بعد نجاح التحديث
        $subject = "No worries! Resetting it is simple.";
        $message = "Your verification code is: $verfiycode\n\nUse this code to regain access and continue your journey with Outbye!";
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