<?php

include "../../connect.php";

$userid = filterRequest("users_id");

$stmt = $con->prepare("DELETE FROM users WHERE users_id = ?");
$stmt->execute([$userid]);

$count = $stmt->rowCount();

if ($count > 0) {
    echo json_encode(["status" => "success", "message" => "User deleted"]);
} else {
    echo json_encode(["status" => "fail", "message" => "User not found"]);
}
?>
