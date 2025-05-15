<?php
include "../../../connect.php";

$id = filterRequest("id");

// استرجاع الخدمة من سلة المهملات
$serviceRestore = updateData("services", ["is_deleted" => 0], "service_id = $id");

// استرجاع المنتجات المرتبطة بالخدمة من سلة المهملات
$itemsRestore = updateData("items", ["is_deleted" => 0], "service_id = $id");

echo json_encode([
    "status" => ($serviceRestore > 0 || $itemsRestore > 0) ? "success" : "failure",
    "message" => ($serviceRestore > 0 || $itemsRestore > 0)
        ? "تم استرجاع الخدمة والمنتجات المرتبطة بها"
        : "لم يتم العثور على الخدمة أو المنتجات أو لم يتم تعديل شيء"
]);
?>
