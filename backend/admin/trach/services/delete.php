<?php
include "../../../connect.php";

// استلام البيانات من الـ Request
$id        = filterRequest("id");
$imagename = filterRequest("imagename");
$password  = filterRequest("password");
$email     = filterRequest("email");

// جلب بيانات الأدمن بناءً على البريد الإلكتروني
$adminData = getAllData("admin", "admin_email = ?", [$email], false);

if ($adminData["status"] === "success") {
    $adminPassword = $adminData["data"][0]["admin_password"];

    // التحقق من كلمة المرور
    if (password_verify($password, $adminPassword)) {

        // حذف الصورة من المجلد
        $baseURL = "https://abdulrahmanantar.com/outbye/upload/services/";
        $imagename = str_replace($baseURL, "", $imagename);
        $filepath = "../../../upload/services/" . $imagename;

        if (!empty($imagename) && is_file($filepath)) {
            unlink($filepath); // حذف الصورة إذا وُجدت
        }

        // حذف الخدمة من جدول الخدمات
        $deleteService = deleteData("services", "service_id = $id");

        // حذف المنتجات المرتبطة بالخدمة من جدول items
        $deleteItems = deleteData("items", "service_id = $id");

        if ($deleteService > 0 || $deleteItems > 0) {
            echo json_encode([
                "status" => "success",
                "message" => "تم حذف الخدمة والمنتجات المرتبطة بها نهائيًا."
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "فشل حذف الخدمة أو لم يتم العثور على منتجات مرتبطة."
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
        "message" => "البريد الإلكتروني غير موجود في قاعدة البيانات."
    ]);
}
?>
