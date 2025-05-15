<?php
include "../../../connect.php";

$id = filterRequest("id");

$result = updateData("offers", ["is_deleted" => 0], "id = $id");

echo json_encode([
    "status" => $result > 0 ? "success" : "failure",
    "message" => $result > 0 
        ? "تم استرجاع العنصر بنجاح"
        : "لم يتم العثور على العنصر أو لم يتم تعديل شيء"
]);
