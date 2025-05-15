<?php



header("Content-Type: application/json");

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

$usersid = isset($_POST['usersid']) ? intval($_POST['usersid']) : 0;
$itemsid = isset($_POST['itemsid']) ? intval($_POST['itemsid']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1; 
$type    = isset($_POST['type']) ? $_POST['type'] : 'item'; 

if ($usersid == 0 || $itemsid == 0 || $quantity <= 0 || !in_array($type, ['item', 'offer'])) {
    echo json_encode(["success" => false, "message" => "Missing or invalid parameters"]);
    exit();
}

// التحقق مما إذا كان موجودًا بالفعل
$stmt = $con->prepare("
    SELECT cart_quantity FROM cart 
    WHERE cart_itemsid = ? 
    AND cart_usersid = ? 
    AND cart_type = ? 
    AND cart_orders = 0
");
$stmt->execute([$itemsid, $usersid, $type]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $newQuantity = intval($row['cart_quantity']) + $quantity;
    $stmt = $con->prepare("
        UPDATE cart 
        SET cart_quantity = ? 
        WHERE cart_itemsid = ? AND cart_usersid = ? AND cart_type = ? AND cart_orders = 0
    ");
    $result = $stmt->execute([$newQuantity, $itemsid, $usersid, $type]);
} else {
    $stmt = $con->prepare("
        INSERT INTO cart (cart_usersid, cart_itemsid, cart_quantity, cart_type) 
        VALUES (?, ?, ?, ?)
    ");
    $result = $stmt->execute([$usersid, $itemsid, $quantity, $type]);
}

if ($result) {
    echo json_encode([
        "success" => true,
        "message" => "Item/Offer added or updated in cart",
        "quantity" => $newQuantity ?? $quantity
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to add/update"
    ]);
}
















