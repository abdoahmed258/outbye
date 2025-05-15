<?php 
include '../../connect.php';

$table = "items";

$service_id       = filterRequest("service_id");
$items_name       = filterRequest("items_name");
$items_name_ar    = filterRequest("items_name_ar");
$items_des        = filterRequest("items_des");
$items_des_ar     = filterRequest("items_des_ar");
$items_count      = filterRequest("items_count");
$items_active     = filterRequest("items_active") ?? 1; // افتراضي 1 إذا لم يُحدد
$items_price      = filterRequest("items_price");
$items_discount   = filterRequest("items_discount") ?? NULL; // اختياري
$items_cat        = filterRequest("items_cat");
$datenow          = date("Y-m-d H:i:s");

// رفع الصورة
$imagename = imageUpload("../../upload/items", "files");
$imageurl = "https://abdulrahmanantar.com/outbye/upload/items/" . $imagename;

$data = array( 
    "service_id"       => $service_id,
    "items_name"       => $items_name,
    "items_name_ar"    => $items_name_ar,
    "items_des"        => $items_des,
    "items_des_ar"     => $items_des_ar,
    "items_image"      => $imageurl, // رابط كامل للصورة
    "items_count"      => $items_count,
    "items_active"     => $items_active,
    "items_price"      => $items_price,
    "items_discount"   => $items_discount,
    "items_cat"        => $items_cat,
    "items_date"       => $datenow
);

// تنفيذ الإدخال
$result = insertData($table, $data);

// تحقق إذا كانت النتيجة true أو false
if ($result === false) {
    // في حالة فشل الإدخال
    echo json_encode([
        "status" => "error",
        "message" => "حدث خطأ أثناء إضافة العنصر"
    ]);
} else {
    // في حالة نجاح الإدخال
    echo json_encode([
        "status" => "success",
        "message" => "تمت إضافة العنصر بنجاح"
    ]);
}
