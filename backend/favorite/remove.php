<?php 

include "../connect.php" ; 
ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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

$usersid = filterRequest("usersid") ; 
$itemsid = filterRequest("itemsid") ; 

deleteData("favorite" , "favorite_usersid = $usersid AND favorite_itemsid = $itemsid") ; 