<?php
define("MB", 1048576);
date_default_timezone_set("Africa/Cairo");



require_once __DIR__ . "/jwt.php";



function checkAuthenticate() {
    // ✅ 1. حدد هنا أسماء API التي تريدها أن تكون عامة (Public)
    $publicEndpoints = [
        "home.php",
        "offers.php",
        "categories.php",
        "topselling.php",
        "services.php",
        "items.php",
        "search.php",
        "resetpassword.php",
        "verifycode.php",
        "checkemail.php",
        "resend.php",
        "verfiycode.php",
        "google_callback.php",
        "delete_unapproved_users.php"
        
        
        
    ];

    // ✅ 2. استخراج اسم الملف الحالي
    $currentFile = basename($_SERVER['PHP_SELF']);

    // ✅ 3. إذا كان API عام، لا تحتاج إلى توثيق
    if (in_array($currentFile, $publicEndpoints)) {
        define("USER_ID", 0);
        return;
    }

    // ✅ 4. جلب التوكن من الهيدر
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        sendUnauthorizedResponse();
    }

    $authHeader = $headers['Authorization'];
    if (strpos($authHeader, 'Bearer ') !== 0) {
        sendUnauthorizedResponse();
    }

    // ✅ 5. استخراج التوكن والتحقق منه
    $token = substr($authHeader, 7);
    $decoded = verifyJWT($token);

    if (!$decoded) {
        sendUnauthorizedResponse();
    }

    // ✅ 6. حفظ معرف المستخدم في `USER_ID`
    define("USER_ID", $decoded->user_id);
}

// ✅ 7. دالة لإرجاع خطأ عند فشل التحقق
function sendUnauthorizedResponse() {
    header('HTTP/1.0 401 Unauthorized');
    echo json_encode(["error" => "Unauthorized access"]);
    exit;
}


function filterRequest($requestname) {
    return htmlspecialchars(strip_tags($_POST[$requestname]));
}

function getAllData($table, $where = null, $values = null, $json = true) {
    global $con;
    $data = array();
    if ($where == null) {
        $stmt = $con->prepare("SELECT * FROM $table");
    } else {
        $stmt = $con->prepare("SELECT * FROM $table WHERE $where");
    }
    $stmt->execute($values);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count = $stmt->rowCount();
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success", "data" => $data), JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(array("status" => "failure"), JSON_UNESCAPED_UNICODE);
        }
        return $count;
    } else {
        if ($count > 0) {
            return  array("status" => "success", "data" => $data);
        } else {
            return  array("status" => "failure", JSON_UNESCAPED_UNICODE);
        }
    }
}

function getData($table, $where = null, $values = null, $json = true)   
{
    global $con;
    $data = array();
    $stmt = $con->prepare("SELECT  * FROM $table WHERE   $where ");
    $stmt->execute($values);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $count  = $stmt->rowCount();
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success", "data" => $data));
        } else {
            echo json_encode(array("status" => "failure"));
        }
    } else {
        return $count;
    }
}


function insertData($table, $data, $json = true) {
    global $con;
    foreach ($data as $field => $v)
        $ins[] = ':' . $field;
    $ins = implode(',', $ins);
    $fields = implode(',', array_keys($data));
    $sql = "INSERT INTO $table ($fields) VALUES ($ins)";

    $stmt = $con->prepare($sql);
    foreach ($data as $f => $v) {
        $stmt->bindValue(':' . $f, $v);
    }
    $stmt->execute();
    $count = $stmt->rowCount();
    return $count > 0; // إرجاع true أو false فقط
}

function updateData($table, $data, $where, $json = true) {
    global $con;
    $cols = array();
    $vals = array();

    foreach ($data as $key => $val) {
        $vals[] = "$val";
        $cols[] = "`$key` = ?";
    }
    $sql = "UPDATE $table SET " . implode(', ', $cols) . " WHERE $where";

    $stmt = $con->prepare($sql);
    $stmt->execute($vals);
    $count = $stmt->rowCount();
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success"), JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(array("status" => "failure"), JSON_UNESCAPED_UNICODE);
        }
    }
    return $count;
}

function deleteData($table, $where, $json = true) {
    global $con;
    $stmt = $con->prepare("DELETE FROM $table WHERE $where");
    $stmt->execute();
    $count = $stmt->rowCount();
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success"), JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(array("status" => "failure"), JSON_UNESCAPED_UNICODE);
        }
    }
    return $count;
}


function imageUpload($dir, $imageRequest)
{
    global $msgError;
    if (isset($_FILES[$imageRequest])) {
        $imagename  = rand(1000, 10000) . $_FILES[$imageRequest]['name'];
        $imagetmp   = $_FILES[$imageRequest]['tmp_name'];
        $imagesize  = $_FILES[$imageRequest]['size'];
        $allowExt   = array("jpg", "png", "gif", "mp3", "pdf" , "svg");
        $strToArray = explode(".", $imagename);
        $ext        = end($strToArray);
        $ext        = strtolower($ext);

        if (!empty($imagename) && !in_array($ext, $allowExt)) {
            $msgError = "EXT";
        }
        if ($imagesize > 2 * MB) {
            $msgError = "size";
        }
        if (empty($msgError)) {
            move_uploaded_file($imagetmp,  $dir . "/" . $imagename);
            return $imagename;
        } else {
            return "fail";
        }
    }else {
        return 'empty' ; 
    }
}


function deleteFile($dir, $imagename) {
    if (file_exists($dir . "/" . $imagename)) {
        unlink($dir . "/" . $imagename);
    }
}



function printSuccess($message = "none") {
    echo json_encode(array("status" => "success", "message" => $message), JSON_UNESCAPED_UNICODE);
    exit; // إيقاف التنفيذ بعد إرجاع الاستجابة
}

function printFailure($message = "none") {
    echo json_encode(array("status" => "failure", "message" => $message), JSON_UNESCAPED_UNICODE);
    exit; // إيقاف التنفيذ بعد إرجاع الاستجابة
}

function result($count) {
    if ($count > 0) {
        printSuccess();
    } else {
        printFailure();
    }
}



function getServicesByCategory($category_id) {
    // تحقق من أن category_id هو رقم صحيح
    $category_id = intval($category_id);

    // استدعاء الدالة getAllData مع تحديد الفئة
    $where = "service_cat = ?";
    $values = [$category_id];

    // جلب البيانات
    getAllData("services", $where, $values, true);
}





   









