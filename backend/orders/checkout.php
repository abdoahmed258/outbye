<?php

include "../connect.php";
$headers = getallheaders();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

if (!$authHeader) {
    http_response_code(401);
    echo json_encode(["message" => "Missing token"]);
    exit;
}

$token = str_replace("Bearer ", "", $authHeader);
$decoded = verifyJWT($token);

if (!$decoded) {
    http_response_code(401);
    echo json_encode(["message" => "Invalid token"]);
    exit;
}

$user_id_from_token = $decoded->user_id;
$user_id_from_request = filterRequest("usersid");

if ($user_id_from_token != $user_id_from_request) {
    http_response_code(403);
    echo json_encode(["message" => "Access denied. This token is not for this user."]);
    exit;
}

// ✅ التوكن يخص نفس المستخدم.. كمل تنفيذ العملية

$usersid = filterRequest("usersid");
$addressid = filterRequest("addressid");
$orderstype = filterRequest("orderstype");
$pricedelivery = filterRequest("pricedelivery");
$ordersprice = filterRequest("ordersprice");
$couponid = filterRequest("couponid");
$paymentmethod = filterRequest("paymentmethod");
 $coupondiscount = filterRequest("coupondiscount");


if ($orderstype == "1") {
    $pricedelivery = 0;
}

$totalprice = $ordersprice  + $pricedelivery;


 // Check Coupon 

 $now = date("Y-m-d H:i:s");

$checkcoupon = getData("coupon", "coupon_id = '$couponid' AND coupon_expiredate > '$now' AND coupon_count > 0  ", null,  false);


if ($checkcoupon  > 0) {
    $totalprice =  $totalprice - $ordersprice * $coupondiscount / 100;
    $stmt = $con->prepare("UPDATE `coupon` SET  `coupon_count`= `coupon_count` - 1  WHERE coupon_id = '$couponid' ");
    $stmt->execute();
}




$data = array(
    "orders_usersid"  =>  $usersid,
    "orders_address"  =>  $addressid,
    "orders_type"  =>  $orderstype,
    "orders_pricedelivery"  =>  $pricedelivery,
    "orders_price"  =>  $ordersprice,
    "orders_coupon"  =>  $couponid,
    "orders_totalprice"  =>  $totalprice,
    "orders_paymentmethod"  =>  $paymentmethod
);

$count = insertData("orders", $data, false);

if ($count > 0) {

    $stmt = $con->prepare("SELECT MAX(orders_id) from orders ");
    $stmt->execute();
    $maxid = $stmt->fetchColumn();

    $data = array("cart_orders" => $maxid);

    updateData("cart", $data, "cart_usersid = $usersid  AND cart_orders = 0 ");
}