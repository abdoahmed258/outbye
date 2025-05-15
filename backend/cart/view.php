<?php




include "../connect.php";

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

$userid = filterRequest("usersid");

// ✅ جلب كل بيانات السلة مع اسم الفئة
$stmt = $con->prepare("
    SELECT 
        cart.cart_id, 
        cart.cart_usersid, 
        cart.cart_itemsid, 
        cart.cart_orders, 
        cart.cart_quantity, 
        cart.cart_type,
        CASE 
            WHEN cart.cart_type = 'item' THEN items.items_name
            WHEN cart.cart_type = 'offer' THEN offers.title
        END AS name,
        CASE 
            WHEN cart.cart_type = 'item' THEN items.items_price
            WHEN cart.cart_type = 'offer' THEN offers.price
        END AS price,
        CASE 
            WHEN cart.cart_type = 'item' THEN items.items_image
            WHEN cart.cart_type = 'offer' THEN offers.image
        END AS image,
        CASE 
            WHEN cart.cart_type = 'item' THEN items.items_cat
            WHEN cart.cart_type = 'offer' THEN 0
        END AS cat_id,
        CASE 
            WHEN cart.cart_type = 'item' THEN items.items_discount
            WHEN cart.cart_type = 'offer' THEN 0
        END AS discount,
        categories.categories_id,
        categories.categories_name,
        categories.categories_name_ar,
        CASE 
            WHEN cart.cart_type = 'item' THEN (items.items_price * cart.cart_quantity)
            WHEN cart.cart_type = 'offer' THEN (offers.price * cart.cart_quantity)
        END AS total_price
    FROM cart
    LEFT JOIN items ON cart.cart_itemsid = items.items_id AND cart.cart_type = 'item'
    LEFT JOIN offers ON cart.cart_itemsid = offers.id AND cart.cart_type = 'offer'
    LEFT JOIN categories ON (cart.cart_type = 'item' AND items.items_cat = categories.categories_id)
    WHERE cart.cart_usersid = ? AND cart.cart_orders = 0
");
$stmt->execute([$userid]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ التجميع
$rest_cafe_cart = [];
$hotel_tourist_cart = [];
$other_categories = [];
$offers_cart = []; // 🆕 هنا هنحط العروض

foreach ($data as $item) {
    if ($item['cart_type'] === 'offer') {
        // 🆕 لو العنصر عرض نضيفه في offers فقط
        $offers_cart[] = $item;
        continue; // نكمل بدون معالجة اضافية
    }

    $cat_id = $item['cat_id'] ?? 0;
    $cat_name = $item['categories_name'] ?? "Unknown";

    if (in_array($cat_id, [4, 5])) {
        $rest_cafe_cart[] = $item;
    } elseif (in_array($cat_id, [6, 7])) {
        $hotel_tourist_cart[] = $item;
    } else {
        if (!isset($other_categories[$cat_id])) {
            $other_categories[$cat_id] = [
                "cat_name" => $cat_name,
                "countprice" => [
                    "totalprice" => 0,
                    "totalcount" => 0
                ],
                "datacart" => []
            ];
        }

        $other_categories[$cat_id]["datacart"][] = $item;
        $other_categories[$cat_id]["countprice"]["totalprice"] += $item['total_price'];
        $other_categories[$cat_id]["countprice"]["totalcount"] += $item['cart_quantity'];
    }
}

// ✅ حساب الإجمالي للمطاعم والكافيهات
$stmt_rest_cafe = $con->prepare("
    SELECT 
        COALESCE(SUM(CASE 
            WHEN cart.cart_type = 'item' THEN items.items_price * cart.cart_quantity
            ELSE 0
        END), 0) AS totalprice, 
        COALESCE(SUM(cart.cart_quantity), 0) AS totalcount 
    FROM cart
    LEFT JOIN items ON cart.cart_itemsid = items.items_id AND cart.cart_type = 'item'
    LEFT JOIN offers ON cart.cart_itemsid = offers.id AND cart.cart_type = 'offer'
    WHERE cart.cart_usersid = ? AND cart.cart_orders = 0 
    AND (cart.cart_type = 'offer' OR items.items_cat IN (4, 5))
");
$stmt_rest_cafe->execute([$userid]);
$rest_cafe_countprice = $stmt_rest_cafe->fetch(PDO::FETCH_ASSOC);

// ✅ حساب الإجمالي للفنادق والأماكن السياحية
$stmt_hotel_tourist = $con->prepare("
    SELECT 
        COALESCE(SUM(CASE 
            WHEN cart.cart_type = 'item' THEN items.items_price * cart.cart_quantity
            ELSE 0
        END), 0) AS totalprice, 
        COALESCE(SUM(cart.cart_quantity), 0) AS totalcount 
    FROM cart
    LEFT JOIN items ON cart.cart_itemsid = items.items_id AND cart.cart_type = 'item'
    LEFT JOIN offers ON cart.cart_itemsid = offers.id AND cart.cart_type = 'offer'
    WHERE cart.cart_usersid = ? AND cart.cart_orders = 0 
    AND (cart.cart_type = 'offer' OR items.items_cat IN (6, 7))
");
$stmt_hotel_tourist->execute([$userid]);
$hotel_tourist_countprice = $stmt_hotel_tourist->fetch(PDO::FETCH_ASSOC);

// ✅ الإرجاع النهائي
echo json_encode([
    "status" => "success",
    "rest_cafe" => [
        "countprice" => $rest_cafe_countprice,
        "datacart" => $rest_cafe_cart
    ],
    "hotel_tourist" => [
        "countprice" => $hotel_tourist_countprice,
        "datacart" => $hotel_tourist_cart
    ],
    "other_categories" => $other_categories,
    "offers" => $offers_cart // 🆕 العروض هنا مستقلة
], JSON_UNESCAPED_UNICODE);



