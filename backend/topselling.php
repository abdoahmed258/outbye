<?php 
include "connect.php"; 

$alldata = array(); 
$alldata['status'] = "success"; 

// جلب العناصر غير المحذوفة فقط من itemstopselling
$items = getAllData("itemstopselling", "is_deleted = 0 ORDER BY countitems DESC", null, false);

$alldata['items'] = $items; 

echo json_encode($alldata);
