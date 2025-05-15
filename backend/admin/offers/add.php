<?php
include '../../connect.php';

$table = "offers";

$title = filterRequest("title");
$description = filterRequest("description");
$price = filterRequest("price");
$start_date = filterRequest("start_date");
$end_date = filterRequest("end_date");
$service_id = filterRequest("service_id");
$datenow = date("Y-m-d H:i:s");

// رفع الصورة
$imagename = imageUpload("../../upload/offers", "files");

// التحقق من نتيجة رفع الصورة
if ($imagename == "fail" || $imagename == "empty") {
    echo json_encode([
        "status" => "error",
        "message" => "فشل رفع الصورة. تأكد من إرسال ملف صورة صالح."
    ]);
    exit;
}

$imageurl = "https://abdulrahmanantar.com/outbye/upload/offers/" . $imagename;

$data = array(
    "title" => $title,
    "description" => $description,
    "price" => $price,
    "image" => $imageurl, // رابط كامل للصورة
    "start_date" => $start_date,
    "end_date" => $end_date,
    "service_id" => $service_id,
    "created_at" => $datenow,
    "updated_at" => $datenow
);

// تنفيذ الإدخال
$result = insertData($table, $data);

// تحقق إذا كانت النتيجة true أو false
if ($result === false) {
    // في حالة فشل الإدخال، حذف الصورة التي تم رفعها لتجنب تراكم الملفات
    deleteFile("../../upload/offers", $imagename);
    echo json_encode([
        "status" => "error",
        "message" => "حدث خطأ أثناء إضافة العرض"
    ]);
} else {
    echo json_encode([
        "status" => "success",
        "message" => "تمت إضافة العرض بنجاح",
    ]);
}