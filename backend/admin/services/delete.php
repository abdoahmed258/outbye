<?php
include "../../connect.php";

// استلام بيانات الخدمة
$id = filterRequest("id");

// تحقق من أن الـ ID ليس فارغًا
if (!empty($id)) {
    // تحديث جدول الخدمات: نقل الخدمة إلى سلة المهملات
    $serviceUpdate = updateData("services", ["is_deleted" => 1], "service_id = $id");

    // تحديث جدول المنتجات: نقل المنتجات المرتبطة بالخدمة إلى سلة المهملات
    $itemsUpdate = updateData("items", ["is_deleted" => 1], "service_id = $id");

    if ($serviceUpdate > 0 || $itemsUpdate > 0) {
        echo json_encode([
            "status" => "success",
            "message" => "تم نقل الخدمة والمنتجات المرتبطة بها إلى سلة المهملات"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "لم يتم العثور على الخدمة أو المنتجات أو لم يتم تعديل شيء"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "ID غير صالح."
    ]);
}
?>
