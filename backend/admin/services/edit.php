<?php
include '../../connect.php';

$table = "services";

$id = filterRequest("id");
$service_name = filterRequest("service_name");
$service_name_ar = filterRequest("service_name_ar");
$service_description = filterRequest("service_description");
$service_description_ar = filterRequest("service_description_ar");
$service_location = filterRequest("service_location");
$service_rating   = filterRequest("service_rating");
$service_phone = filterRequest("service_phone");
$service_email = filterRequest("service_email");
$service_website = filterRequest("service_website");
$service_type = filterRequest("service_type"); // 'restaurant', 'cafe', 'hotel', 'tourist_place'
$service_cat = filterRequest("service_cat");  // يجب أن يكون رقمًا يشير إلى فئة موجودة
$service_active = filterRequest("service_active"); // افتراضي 1 إذا لم يُحدد
$imageold = filterRequest("imageold");

$res = imageUpload("../../upload/services", "files");
$baseURL = "https://abdulrahmanantar.com/outbye/upload/services/";

// إذا تم رفع صورة جديدة
if ($res != "empty") {
    // 🛡️ تحقق قبل الحذف
    if (!empty($imageold)) {
        // الحصول على اسم الصورة فقط (إزالة الرابط)
        $imageName = basename($imageold); // حذف الجزء الزائد من الرابط
        $filepath = "../../upload/services/" . $imageName;

        // تحقق إذا كان الملف موجودًا ثم حذفه
        if (is_file($filepath)) {
            unlink($filepath);  // حذف الصورة القديمة
        }
    }

    // قم بتكوين الرابط الكامل للصورة الجديدة
    $imageFullURL = $baseURL . $res;

    $data = array(
        "service_name" => $service_name,
        "service_name_ar" => $service_name_ar,
        "service_description" => $service_description,
        "service_description_ar" => $service_description_ar,
        "service_image" => $imageFullURL, // ⬅️ الرابط الكامل للصورة الجديدة
        "service_location" => $service_location,
        "service_rating"   => $service_rating,
        "service_phone" => $service_phone,
        "service_email" => $service_email,
        "service_website" => $service_website,
        "service_type" => $service_type,
        "service_cat" => $service_cat,
        "service_active" => $service_active,
    );
} else {
    // إذا لم يتم رفع صورة جديدة، يتم تحديث باقي البيانات فقط
    $data = array(
        "service_name" => $service_name,
        "service_name_ar" => $service_name_ar,
        "service_description" => $service_description,
        "service_description_ar" => $service_description_ar,
        "service_location" => $service_location,
        "service_rating"   => $service_rating,
        "service_phone" => $service_phone,
        "service_email" => $service_email,
        "service_website" => $service_website,
        "service_type" => $service_type,
        "service_cat" => $service_cat,
        "service_active" => $service_active,
    );
}

// تحديث البيانات في قاعدة البيانات
updateData($table, $data, "service_id = $id");
?>
