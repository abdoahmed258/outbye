<?php
include "../connect.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");


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
$user_id_from_request = filterRequest("userid");

if ($user_id_from_token != $user_id_from_request) {
    http_response_code(403);
    echo json_encode(["message" => "Access denied. This token is not for this user."]);
    exit;
}

// ✅ التوكن يخص نفس المستخدم.. كمل تنفيذ العملية

// تحديد الطريقة المستخدمة في الطلب (GET أو POST)
$serviceid = isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : null);
$userid = isset($_GET['userid']) ? $_GET['userid'] : (isset($_POST['userid']) ? $_POST['userid'] : null);

// تحديد إذا كان المستخدم زائرًا (إذا كانت القيمة userid = 0 أو غير موجودة)
if ($userid === null || $userid == 0) {
    // هنا يتم تعيين قيمة للمستخدم كـ "زائر" وتخصيص سلوك معين (مثلاً، عدم تمكين المفضلة)
    $userid = 0;  // أو يمكنك تعيين قيمة أخرى لتمييز الزوار
    $isVisitor = true;
} else {
    $isVisitor = false;
}

// التحقق من أن المعرفات موجودة
if ($serviceid !== null && $userid !== null) {
    // استعلام محسّن باستخدام LEFT JOIN بدلاً من UNION ALL
    $stmt = $con->prepare("
        SELECT s.*, 
              CASE 
                  WHEN f.favorite_itemsid IS NOT NULL THEN 1 
                  ELSE 0 
              END AS favorite
        FROM services_items_view s
        LEFT JOIN favorite f 
            ON s.items_id = f.favorite_itemsid 
            AND f.favorite_usersid = ?
        WHERE s.service_id = ?
    ");

    // تنفيذ الاستعلام
    $stmt->execute([$userid, $serviceid]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count = $stmt->rowCount();

    // إرجاع البيانات بتنسيق JSON
    $response = $count > 0 ? ["status" => "success", "data" => $data] : ["status" => "failure"];
} else {
    $response = ["status" => "error", "message" => "Missing parameters"];
}

echo json_encode($response);

?>


