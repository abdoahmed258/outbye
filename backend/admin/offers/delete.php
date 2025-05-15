<?php
include "../../connect.php";

// استلام ID
$id = filterRequest("id");

// تحقق من أن المعرف موجود
if (empty($id)) {
    echo json_encode([
        "status" => "error",
        "message" => "لم يتم توفير معرف العرض."
    ]);
    exit;
}

// تحديث حالة العرض إلى محذوف (is_deleted = 1)
$result = updateData("offers", ["is_deleted" => 1], "id = $id");

// الرد على العميل
if ($result > 0) {
    echo json_encode([
        "status" => "success",
        "message" => "تم نقل العرض إلى سلة المهملات."
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "فشل في نقل العرض أو لم يتم تعديل شيء."
    ]);
}
?>
