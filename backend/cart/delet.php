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

// ✅ التحقق نجح

$usersid = filterRequest("usersid");
$itemsid = filterRequest("itemsid");
$type    = filterRequest("type"); // 📌 النوع: item أو offer

if (!$type || !in_array($type, ['item', 'offer'])) {
    echo json_encode(["success" => false, "message" => "Invalid or missing type"]);
    exit;
}

// جلب الكمية الحالية من السلة
$stmt = $con->prepare("
    SELECT cart_quantity FROM cart 
    WHERE cart_usersid = ? 
    AND cart_itemsid = ? 
    AND cart_type = ?
    AND cart_orders = 0
");
$stmt->execute([$usersid, $itemsid, $type]);
$cart_quantity = $stmt->fetchColumn();

if ($cart_quantity !== false) {
    if ($cart_quantity > 1) {
        // 👈 تقليل الكمية بمقدار 1
        $updateStmt = $con->prepare("
            UPDATE cart 
            SET cart_quantity = cart_quantity - 1 
            WHERE cart_usersid = ? 
            AND cart_itemsid = ? 
            AND cart_type = ?
            AND cart_orders = 0
        ");
        $result = $updateStmt->execute([$usersid, $itemsid, $type]);

        if ($result) {
            echo json_encode(["success" => true, "message" => "Quantity decreased by 1"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to decrease quantity"]);
        }
    } else {
        // 👈 حذف السطر بالكامل
        $deleteStmt = $con->prepare("
            DELETE FROM cart 
            WHERE cart_usersid = ? 
            AND cart_itemsid = ? 
            AND cart_type = ?
            AND cart_orders = 0
        ");
        $result = $deleteStmt->execute([$usersid, $itemsid, $type]);

        if ($result) {
            echo json_encode(["success" => true, "message" => "Item/Offer removed from cart"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to remove item/offer"]);
        }
    }
} else {
    echo json_encode(["success" => false, "message" => "Item/Offer not found in cart"]);
}



