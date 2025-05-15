<?php

include "../../../connect.php";

$id = filterRequest("id"); 

// الخطوة 1: استرجاع الخدمات المرتبطة بالكاتجوري
$servicesResult = getAllData("services", "service_cat = $id AND is_deleted = 1", null, false);

// تحقق أولًا أن العملية ناجحة
if ($servicesResult['status'] == "success") {
    foreach ($servicesResult['data'] as $service) {
        $serviceId = $service['service_id'];

        // استرجاع المنتجات المرتبطة بهذه الخدمة
        updateData("items", ["is_deleted" => 0], "service_id = $serviceId");

        // استرجاع الخدمة نفسها
        updateData("services", ["is_deleted" => 0], "service_id = $serviceId");
    }
}

// الخطوة 2: استرجاع الكاتجوري نفسه من سلة المهملات
updateData("categories", ["is_deleted" => 0], "categories_id = $id");

echo json_encode([
    "status" => "success",
    "message" => "تم استرجاع الكاتجوري وكل محتواه بنجاح"
]);