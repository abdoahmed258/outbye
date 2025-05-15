<?php



include '../../connect.php';

$table = "items";

$id = filterRequest("id");
$service_id = filterRequest("service_id");
$items_name = filterRequest("items_name");
$items_name_ar = filterRequest("items_name_ar");
$items_des = filterRequest("items_des");
$items_des_ar = filterRequest("items_des_ar");
$items_count = filterRequest("items_count");
$items_active = filterRequest("items_active") ?? 1; // افتراضي 1 إذا لم يُحدد
$items_price = filterRequest("items_price");
$items_discount = filterRequest("items_discount") ?? NULL; // اختياري
$items_cat = filterRequest("items_cat");
$imageold = filterRequest("imageold"); // الصورة القديمة

// رفع الصورة الجديدة
$res = imageUpload("../../upload/items", "files");

if ($res == "empty") {
    // إذا لم يتم رفع صورة جديدة
    $data = array(
        "service_id" => $service_id,
        "items_name" => $items_name,
        "items_name_ar" => $items_name_ar,
        "items_des" => $items_des,
        "items_des_ar" => $items_des_ar,
        "items_count" => $items_count,
        "items_active" => $items_active,
        "items_price" => $items_price,
        "items_discount" => $items_discount,
        "items_cat" => $items_cat,
    );
} else {
    // إذا تم رفع صورة جديدة
    // استخراج اسم الصورة القديمة فقط من الرابط
    $imageold_name = basename($imageold);
    
    // حذف الصورة القديمة
    deleteFile("../../upload/items", $imageold_name); // حذف الصورة باستخدام الاسم فقط
    
    $imageurl = "https://abdulrahmanantar.com/outbye/upload/items/" . $res; // رابط الصورة الجديدة

    $data = array(
        "service_id" => $service_id,
        "items_name" => $items_name,
        "items_name_ar" => $items_name_ar,
        "items_des" => $items_des,
        "items_des_ar" => $items_des_ar,
        "items_image" => $imageurl, // رابط كامل للصورة
        "items_count" => $items_count,
        "items_active" => $items_active,
        "items_price" => $items_price,
        "items_discount" => $items_discount,
        "items_cat" => $items_cat,
    );
}

// تنفيذ التحديث
$result = updateData($table, $data, "items_id = $id");

// تحقق إذا كانت النتيجة true أو false
if ($result === false) {
    // في حالة فشل التحديث
    echo json_encode([
        "status" => "error",
        "message" => "حدث خطأ أثناء تحديث العنصر"
    ]);
} else {
    // في حالة نجاح التحديث
    echo json_encode([
        "status" => "success",
        "message" => "تمت تحديث العنصر بنجاح"
    ]);
}
