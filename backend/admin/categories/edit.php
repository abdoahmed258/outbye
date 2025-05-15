<?php

include '../../connect.php';

$table = "categories";

// جلب البيانات من الطلب
$id = filterRequest("id");
$name = filterRequest("name");
$namear = filterRequest("namear");
$imageold  = filterRequest("imageold");

// استخراج اسم الصورة فقط من الرابط الكامل
$imageold = basename($imageold); // ⬅️ يحذف الجزء الزائد من الرابط

// رفع الصورة الجديدة
$res = imageUpload("../../upload/categories", "files");

if ($res == "empty") {
    // إذا لم يتم رفع صورة جديدة، فقط قم بتحديث الاسم
    $data = array(
        "categories_name" => $name,
        "categories_name_ar" => $namear,
    );
    $message = "لا توجد صورة جديدة تم رفعها.";
} else {
    // إذا تم رفع صورة جديدة، قم بحذف الصورة القديمة وتحديث الحقل بالصورة الجديدة
    deleteFile("../../upload/categories", $imageold);

    // إنشاء الرابط الكامل للصورة الجديدة
    $imageUrl = "https://abdulrahmanantar.com/outbye/upload/categories/" . $res;

    $data = array(
        "categories_name"    => $name,
        "categories_name_ar" => $namear,
        "categories_image"   => $imageUrl,
    );
    $message = "تم رفع صورة جديدة.";
}

// تحديث بيانات الفئة في قاعدة البيانات
updateData($table, $data, "categories_id = $id");


echo json_encode([
    "status" => "success",
    "message" => $message
]);

?>
