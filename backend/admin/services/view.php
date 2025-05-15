<?php
include "../../connect.php";

// الاستعلام SQL لعرض الخدمات غير المحذوفة فقط
$query = "SELECT s.*, c.categories_name, c.categories_name_ar 
          FROM services s 
          JOIN categories c ON s.service_cat = c.categories_id
          WHERE s.is_deleted = 0";

try {
    // تحضير وتنفيذ الاستعلام
    $stmt = $con->prepare($query);
    $stmt->execute();
    
    // جلب جميع البيانات
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // تحويل البيانات إلى JSON
    echo json_encode($data);
} catch (PDOException $e) {
    echo "خطأ: " . $e->getMessage();
}
?>
