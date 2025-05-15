<?php

include "../connect.php"; // تأكد أن هذا يستدعي الدوال sendGCM و insertNotify

// استقبال البيانات من الفورم
$title = filterRequest("title");
$body = filterRequest("body");

// التوبيك العام الذي يشترك فيه كل المستخدمين
$topic = "allusers";

// إرسال الإشعار
sendGCM($title, $body, $topic, "none", "refreshAll");

// لو حابب تسجله في قاعدة البيانات، ممكن تستخدم user ID = 0 للدلالة على إشعار عام
insertNotify($title, $body, 0, $topic, "none", "refreshAll");

echo json_encode(["status" => "success", "message" => "Notification sent to all users"]);

?>
