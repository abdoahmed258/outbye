<?php

include "../../connect.php";

$orderid = filterRequest("ordersid");

$userid = filterRequest("usersid");

$data = array(
    "orders_status" => 1 // حالة مرفوض
);

// التعديل فقط إذا كان الطلب ما زال قيد الانتظار
updateData("orders", $data, "orders_id = $orderid AND orders_status = 0");

// إرسال إشعار للمستخدم بأن الطلب مرفوض
insertNotify("error", "The Order Has been Rejected", $userid, "users$userid", "none", "refreshorderpending");


