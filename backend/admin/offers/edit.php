<?php
include '../../connect.php';

$table = "offers";

$id = filterRequest("id");
$title = filterRequest("title");
$description = filterRequest("description");
$price = filterRequest("price");
$start_date = filterRequest("start_date");
$end_date = filterRequest("end_date");
$service_id = filterRequest("service_id");
$imageold = filterRequest("imageold"); // الصورة القديمة
$datenow = date("Y-m-d H:i:s");

// التحقق من تنسيق التاريخ
if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $start_date) || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $end_date)) {
    echo json_encode([
        "status" => "error",
        "message" => "تنسيق التاريخ غير صحيح. يجب أن يكون YYYY-MM-DD"
    ]);
    exit;
}

// رفع الصورة الجديدة
$res = imageUpload("../../upload/offers", "files");

$upload_message = null;
$imageurl = $imageold; // الافتراضي هو استخدام الصورة القديمة

if ($res == "fail") {
    // إذا فشل رفع الصورة، نستمر مع الصورة القديمة
    $upload_message = "فشل رفع الصورة. تم الاحتفاظ بالصورة القديمة.";
} elseif ($res != "empty") {
    // إذا تم رفع صورة جديدة بنجاح
    // استخراج اسم الصورة القديمة فقط من الرابط
    $imageold_name = basename($imageold);
    
    // حذف الصورة القديمة
    deleteFile("../../upload/offers", $imageold_name); // حذف الصورة باستخدام الاسم فقط
    
    $imageurl = "https://abdulrahmanantar.com/outbye/upload/offers/" . $res; // رابط الصورة الجديدة
    $upload_message = "تم رفع الصورة الجديدة بنجاح.";
}

// إعداد البيانات للتحديث
$data = array(
    "title" => $title,
    "description" => $description,
    "price" => $price,
    "image" => $imageurl, // استخدام الصورة القديمة أو الجديدة
    "start_date" => $start_date,
    "end_date" => $end_date,
    "service_id" => $service_id,
    "updated_at" => $datenow
);

// تنفيذ التحديث
$result = updateData($table, $data, "id = $id");

// تحقق إذا كانت النتيجة true أو false
if ($result === false) {
    // في حالة فشل التحديث
    echo json_encode([
        "status" => "error",
        "message" => "حدث خطأ أثناء تحديث العرض"
    ]);
} else {
    // في حالة نجاح التحديث
    $response = [
        "status" => "success",
        "message" => "تم تحديث العرض بنجاح",
        "image_url" => $imageurl // إرجاع رابط الصورة للتأكيد
    ];
    if ($upload_message) {
        $response["upload_message"] = $upload_message;
    }
    echo json_encode($response);
}