<?php 

include "../connect.php"; 

$couponName = filterRequest("couponname"); 
$now = date("Y-m-d H:i:s");

// جلب بيانات الكوبون
$stmt = $con->prepare("SELECT * FROM coupon WHERE coupon_name = ? AND coupon_expiredate > ? AND coupon_count > 0");
$stmt->execute([$couponName, $now]);
$couponData = $stmt->fetch(PDO::FETCH_ASSOC);

if ($couponData) {
    // تحديث العدد بعد استخدام الكوبون
    $updateStmt = $con->prepare("UPDATE coupon SET coupon_count = coupon_count - 1 WHERE coupon_name = ? AND coupon_count > 0");
    $updateStmt->execute([$couponName]);

    if ($updateStmt->rowCount() > 0) {
        echo json_encode(["status" => "success", "message" => "Coupon applied successfully", "data" => $couponData]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update coupon count"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid or expired coupon"]);
}

?>
