<?php

include "../../connect.php";

$userid = filterRequest("users_id");
$name   = filterRequest("users_name");
$email  = filterRequest("users_email");
$phone  = filterRequest("users_phone");

$stmt = $con->prepare("UPDATE users SET users_name = ?, users_email = ?, users_phone = ? WHERE users_id = ?");
$stmt->execute([$name, $email, $phone, $userid]);

$count = $stmt->rowCount();

if ($count > 0) {
    echo json_encode(["status" => "success", "message" => "User updated"]);
} else {
    echo json_encode(["status" => "fail", "message" => "No changes or user not found"]);
}
?>
