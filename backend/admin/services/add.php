<?php 
include '../../connect.php';

$msgError = array();
$table = "services";

$service_name = filterRequest("service_name");
$service_name_ar = filterRequest("service_name_ar");
$service_description = filterRequest("service_description");
$service_description_ar = filterRequest("service_description_ar");
$service_location = filterRequest("service_location");
$service_rating = filterRequest("service_rating");
$service_phone = filterRequest("service_phone");
$service_email = filterRequest("service_email");
$service_website = filterRequest("service_website");
$service_cat = filterRequest("service_cat"); // يجب أن يكون رقمًا يشير إلى فئة موجودة
$service_active = filterRequest("service_active") ?? 1; // افتراضي 1 إذا لم يُحدد
$datenow = date("Y-m-d H:i:s") ;
// رفع الصورة وتخزين اسمها
$imagename = imageUpload("../../upload/services", "files");

// تحديد الرابط الكامل للمجلد
$baseURL = "https://abdulrahmanantar.com/outbye/upload/services/";

// تكوين الرابط الكامل للصورة
$imageFullURL = $baseURL . $imagename;

$data = array( 
    "service_name" => $service_name,
    "service_name_ar" => $service_name_ar,
    "service_description" => $service_description,
    "service_description_ar" => $service_description_ar,
    "service_image" => $imageFullURL,  // تخزين الرابط الكامل للصورة
    "service_location" => $service_location,
    "service_rating"   => $service_rating , 
    "service_phone" => $service_phone,
    "service_email" => $service_email,
    "service_website" => $service_website,
    "service_cat" => $service_cat,
    "service_active" => $service_active,
    "service_created" => $datenow
);

insertData($table, $data);