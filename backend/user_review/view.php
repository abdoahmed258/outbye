<?php
include "../connect.php";

try {
    $stmt = $con->prepare("
        SELECT 
            general_reviews.*, 
            users.users_name,
            users.users_image
        FROM general_reviews 
        LEFT JOIN users ON general_reviews.user_id = users.users_id 
        ORDER BY general_reviews.created_at DESC
    ");

    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($reviews);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>
