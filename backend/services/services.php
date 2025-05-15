<?php


include "../connect.php";

// ✅ إعداد ترويسات CORS للسماح بجميع الطلبات
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

// ✅ معالجة طلبات `OPTIONS` (مهم لحل مشكلة CORS)
if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    http_response_code(200);
    exit();
}

// ✅ استقبال `id` من `POST` أو `GET`
$categoryid = $_POST['id'] ?? $_GET['id'] ?? 0;
$categoryid = intval($categoryid);

// ✅ التحقق من صحة `categoryid`
if ($categoryid <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid category ID"]);
    exit();
}

try {
    // ✅ استعلام SQL محسّن
    $stmt = $con->prepare("
        SELECT 
            s.service_id,
            s.service_name,
            s.service_name_ar,
            s.service_description,
            s.service_description_ar,
            s.service_image,
            s.service_location,
            s.service_rating,
            s.service_phone,
            s.service_email,
            s.service_website,
            s.service_type,
            s.service_cat,
            s.service_active,
            s.service_created,
            c.categories_id,
            c.categories_name,
            c.categories_name_ar,
            c.categories_image,
            c.categories_datetime
        FROM services s
        LEFT JOIN categories c ON s.service_cat = c.categories_id
        WHERE c.categories_id = ?
    ");

    $stmt->execute([$categoryid]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ✅ التحقق مما إذا كانت هناك بيانات وإرجاعها
    if ($results) {
        echo json_encode(["status" => "success", "data" => $results]);
    } else {
        echo json_encode(["status" => "error", "message" => "No services found"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
?>


