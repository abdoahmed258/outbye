<?php 

include "../connect.php"; 

$search = filterRequest("search"); 

// جلب بيانات المنتجات
$items = getAllData("items", "items_name LIKE '%$search%' OR items_name_ar LIKE '%$search%'", null, false);

// جلب بيانات الخدمات
$services = getAllData("services", "service_name LIKE '%$search%' OR service_name_ar LIKE '%$search%' OR service_cat LIKE '%$search%'", null, false);

// إرجاع النتيجة بصيغة JSON
echo json_encode([
    "status" => "success",
    "items" => $items,
    "services" => $services
], JSON_UNESCAPED_UNICODE);
