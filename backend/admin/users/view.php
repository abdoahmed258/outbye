<?php

include "../../connect.php";

// استعلام لجلب المستخدمين مع العناوين المرتبطة بهم
$stmt = $con->prepare("
    SELECT 
        users.*,
        address.address_id,
        address.address_name,
        address.address_phone,
        address.address_city,
        address.address_street,
        address.address_lat,
        address.address_long
    FROM users
    LEFT JOIN address ON users.users_id = address.address_usersid
");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// تجهيز الرد بصيغة JSON
$response = array();
$response["status"] = "success";
$response["data"] = $data;

echo json_encode($response);
