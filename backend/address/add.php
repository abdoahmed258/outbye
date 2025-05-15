<?php 

include '../connect.php';


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


$table = "address";

$usersid    = filterRequest("usersid");
$name       = filterRequest("name");
$city       = filterRequest("city");
$street     = filterRequest("street");
$phone       = filterRequest("phone");
$lat        = filterRequest("lat");
$long       = filterRequest("long");

$data = array(  
    "address_city" => $city,
    "address_usersid" => $usersid,
    "address_name"   => $name,
    "address_street" => $street,
    "address_phone" => $phone,
    "address_lat" => $lat,
    "address_long" => $long,
);

// محاولة إدخال البيانات في قاعدة البيانات
try {
    insertData($table, $data);

    // استجابة ناجحة
    $response = array(
        'status' => 'success',
        'message' => 'Data inserted successfully',
        'data' => $data
    );
    echo json_encode($response);

} catch (Exception $e) {
    // استجابة عند حدوث خطأ
    $response = array(
        'status' => 'error',
        'message' => 'Failed to insert data: ' . $e->getMessage()
    );
    echo json_encode($response);
}
?>
