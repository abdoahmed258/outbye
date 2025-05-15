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
$user_id_from_request = filterRequest("users_id");

if ($user_id_from_token != $user_id_from_request) {
    http_response_code(403);
    echo json_encode(["message" => "Access denied. This token is not for this user."]);
    exit;
}

// ✅ التوكن يخص نفس المستخدم.. كمل تنفيذ العملية

$user_id = filterRequest("users_id");
$new_name = filterRequest("new_name");
$new_email = filterRequest("new_email");
$new_phone = filterRequest("new_phone");

$stmt = $con->prepare("SELECT * FROM users WHERE users_id = ? AND users_approve = 1");
$stmt->execute([$user_id]);
$count = $stmt->rowCount();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($count > 0) {
    $update_data = [];

    if (!empty($new_name)) {
        $update_data["users_name"] = $new_name;
    }

    if (!empty($new_email)) {
        $stmt = $con->prepare("SELECT * FROM users WHERE users_email = ? AND users_id != ?");
        $stmt->execute([$new_email, $user_id]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(["status" => "failure", "message" => "البريد الإلكتروني مستخدم بالفعل"], JSON_UNESCAPED_UNICODE);
            exit;
        }
        $update_data["users_email"] = $new_email;
    }

    if (!empty($new_phone)) {
        $stmt = $con->prepare("SELECT * FROM users WHERE users_phone = ? AND users_id != ?");
        $stmt->execute([$new_phone, $user_id]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(["status" => "failure", "message" => "رقم الهاتف مستخدم بالفعل"], JSON_UNESCAPED_UNICODE);
            exit;
        }
        $update_data["users_phone"] = $new_phone;
    }

    // رفع الصورة الجديدة
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $file_size = $_FILES['image']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        $max_size = 5 * 1024 * 1024;

        if (in_array($file_ext, $allowed_ext) && $file_size <= $max_size) {
            // حذف الصورة القديمة إذا كانت موجودة
            if (!empty($row['users_image'])) {
                $oldImagePath = "../upload/profile/" . basename($row['users_image']);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // رفع الصورة الجديدة
            $new_file_name = uniqid() . '.' . $file_ext;
            $upload_path = "../upload/profile/" . $new_file_name;

            if (move_uploaded_file($file_tmp, $upload_path)) {
                $imageUrl = "https://abdulrahmanantar.com/outbye/upload/profile/" . $new_file_name;
                $update_data["users_image"] = $imageUrl;
            } else {
                echo json_encode(["status" => "failure", "message" => "فشل في رفع الصورة"], JSON_UNESCAPED_UNICODE);
                exit;
            }
        } else {
            echo json_encode(["status" => "failure", "message" => "نوع الصورة غير مدعوم أو الحجم كبير جدًا"], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    if (empty($update_data)) {
        echo json_encode(["status" => "failure", "message" => "يرجى إدخال بيانات للتعديل"], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $updated = updateData("users", $update_data, "users_id = '$user_id'", true);

    if ($updated > 0) {
        getData("users", "users_id = ? AND users_approve = 1", [$user_id], true);
    } else {
        echo json_encode(["status" => "failure", "message" => "فشل في تحديث البيانات"], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode(["status" => "failure", "message" => "المستخدم غير موجود أو غير مفعل"], JSON_UNESCAPED_UNICODE);
}


