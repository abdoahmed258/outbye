<?php
include "../../../connect.php";

// استلام البيانات من الفورم
$id        = filterRequest("id");
$imagename = filterRequest("imagename");
$password  = filterRequest("password");
$email     = filterRequest("email"); // ← تمت إضافته

// استخراج اسم الصورة فقط من الرابط الكامل
$image_name_only = basename($imagename);

// جلب بيانات الأدمن بناءً على الإيميل
$adminData = getAllData("admin", "admin_email = ?", [$email], false);

if ($adminData["status"] === "success" && isset($adminData["data"][0]["admin_password"])) {
    $adminHashedPassword = $adminData["data"][0]["admin_password"];

    // التحقق من صحة كلمة المرور
    if (password_verify($password, $adminHashedPassword)) {
        // حذف الصورة من المجلد
        deleteFile("../../../upload/items", $image_name_only);

        // حذف السجل من قاعدة البيانات
        $deleteResult = deleteData("items", "items_id = $id");

        if ($deleteResult > 0) {
            echo json_encode([
                "status" => "success",
                "message" => "تم حذف المنتج والصورة نهائيًا بنجاح"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "فشل حذف المنتج من قاعدة البيانات"
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "كلمة المرور غير صحيحة"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "لم يتم العثور على الأدمن باستخدام هذا البريد الإلكتروني"
    ]);
}
