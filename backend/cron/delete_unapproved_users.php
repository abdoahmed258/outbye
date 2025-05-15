<?php
include "../connect.php";

// استعلام لاختيار المستخدمين الغير مفعلين بدون Google ID
$stmt = $con->prepare("SELECT users_id FROM users WHERE users_approve = 0 AND (users_google_id IS NULL OR users_google_id = '')");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($users) > 0) {
    foreach ($users as $user) {
        $user_id = $user['users_id'];

        // حذف المستخدم
        $deleteStmt = $con->prepare("DELETE FROM users WHERE users_id = ?");
        $deleteStmt->execute([$user_id]);
    }

    echo json_encode([
        "status" => "success",
        "message" => "تم حذف " . count($users) . " مستخدم غير مفعل بدون Google ID"
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        "status" => "success",
        "message" => "لا يوجد مستخدمين بحاجة للحذف"
    ], JSON_UNESCAPED_UNICODE);
}
