<?php
include "../../connect.php";

$id = filterRequest("id");


// نقل العنصر إلى سلة المهملات (تحديث is_deleted = 1 بدلاً من الحذف الكامل)
updateData("items", ["is_deleted" => 1], "items_id = $id");

// إرسال الاستجابة
echo json_encode([
    "status" => "success",
    "message" => "تم نقل العنصر إلى سلة المهملات وحذف الصورة بنجاح"
]);
