<?php 

include '../connect.php';

$table = "address";

$addressid    = filterRequest("addressid"); 
$name         = filterRequest("name");
$city         = filterRequest("city");
$street       = filterRequest("street");
$phone       = filterRequest("phone");
$data = array(  
"address_city" => $city, 
"address_street" => $street,
"address_name"   => $name,
"address_phone" => $phone,
);

updateData($table , $data , "address_id = $addressid");