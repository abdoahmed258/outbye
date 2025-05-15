<?php 

include "connect.php";

$alldata = array();

$alldata['status'] = "success";

// جلب الإعدادات
$settings = getAllData("settings", "1 = 1", null, false);
$alldata['settings'] = $settings;

// جلب الكاتجوري غير المحذوفة
$categories = getAllData("categories", "is_deleted = 0");
$alldata['categories'] = $categories;

// جلب العناصر غير المحذوفة التي تحتوي على خصم
$items = getAllData("services_items_view", "items_discount != 0 AND items_is_deleted = 0", null, false);
$alldata['items'] = $items;

// يمكنك إضافة استعلام للعروض إذا كنت بحاجة له
// $offers = getAllData("offers", null, null, false);
// $alldata['offers'] = $offers;

echo json_encode($alldata);
