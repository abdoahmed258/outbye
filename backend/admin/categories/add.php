<?php
include '../../connect.php';

$msgError = array();

$table = "categories";

// استخراج البيانات من الـ POST
$name = filterRequest("name");
$namear = filterRequest("namear");
$datenow = date("Y-m-d H:i:s");

// رفع الصورة
$imagename = imageUpload("../../upload/categories", "files");

// التأكد من رفع الصورة بنجاح
if ($imagename == "empty") {
    $msgError[] = "لم يتم رفع الصورة.";
} elseif ($imagename == "error") {
    $msgError[] = "حدث خطأ أثناء رفع الصورة.";
} elseif ($imagename == "invalid_file_type") {
    $msgError[] = "نوع الملف غير مدعوم.";
}

if (empty($msgError)) {
    // دمج الرابط مع اسم الصورة
    $imageUrl = "https://abdulrahmanantar.com/outbye/upload/categories/" . $imagename;

    // تحضير البيانات لإدخالها في قاعدة البيانات
    $data = array(
        "categories_name" => $name,
        "categories_name_ar" => $namear,
        "categories_image" => $imageUrl,  // تخزين الرابط الكامل للصورة
        "categories_datetime" => $datenow
    );

    // إدخال البيانات في قاعدة البيانات
    if (insertData($table, $data)) {
        echo json_encode(array(
            "status" => "success",
            "message" => "تم إضافة الفئة بنجاح."
        ));
    } else {
        echo json_encode(array(
            "status" => "error",
            "message" => "حدث خطأ أثناء إضافة الفئة."
        ));
    }
} else {
    // في حالة وجود أخطاء
    echo json_encode(array(
        "status" => "error",
        "message" => implode(", ", $msgError)
    ));
}
?>
