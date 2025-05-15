<?php
include './connect.php';

$stmt = $con->prepare("
    SELECT 
        offers.id,
        offers.title,
        offers.description,
        offers.price,
        offers.image,
        offers.start_date,
        offers.end_date,
        services.service_id,
        services.service_name,
        services.service_name_ar
    FROM 
        offers
    INNER JOIN 
        services 
    ON 
        offers.service_id = services.service_id
    WHERE 
        offers.start_date <= CURDATE()
        AND offers.end_date >= CURDATE()
    ORDER BY 
        offers.start_date ASC
");

$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
?>
