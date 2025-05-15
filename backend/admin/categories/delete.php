<?php

include "../../connect.php";

$id = filterRequest("id"); 


// الخطوة 1: جلب الخدمات المرتبطة بالكاتجوري
$servicesResult = getAllData("services", "service_cat = $id AND is_deleted = 0", null, false);

// تحقق أولًا أن العملية ناجحة
if ($servicesResult['status'] == "success") {
    foreach ($servicesResult['data'] as $service) {
        $serviceId = $service['service_id'];

        // حذف المنتجات المرتبطة بهذه الخدمة
        updateData("items", ["is_deleted" => 1], "service_id = $serviceId");

        // حذف الخدمة نفسها (نقلها لسلة المهملات)
        updateData("services", ["is_deleted" => 1], "service_id = $serviceId");
    }
}

// الخطوة 2: نقل الكاتجوري نفسه إلى سلة المهملات
updateData("categories", ["is_deleted" => 1], "categories_id = $id");

echo json_encode([
    "status" => "success",
    "message" => "تم نقل الكاتجوري وكل محتواه إلى سلة المهملات"
]);