<?php
include "../../../connect.php";

$id = filterRequest("id");
$imagename = filterRequest("imagename");
$password = filterRequest("password");
$email = filterRequest("email"); // يجب تمريره أيضًا

// جلب بيانات الأدمن بناءً على البريد
$adminData = getAllData("admin", "admin_email = ?", [$email], false);

if ($adminData["status"] === "success" && isset($adminData["data"][0]["admin_password"])) {
    $adminPassword = $adminData["data"][0]["admin_password"];

    // التحقق من كلمة المرور
    if (password_verify($password, $adminPassword)) {
        // حذف الصورة
        $image_name_only = basename($imagename);
        deleteFile("../../../upload/categories", $image_name_only);

        // حذف السجل
        $delete = deleteData("categories", "categories_id = $id");

        if ($delete > 0) {
            echo json_encode([
                "status" => "success",
                "message" => "تم حذف الكاتيجوري نهائيًا."
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "فشل حذف الكاتيجوري من قاعدة البيانات."
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "كلمة المرور غير صحيحة."
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "لم يتم العثور على بيانات الأدمن."
    ]);
}
