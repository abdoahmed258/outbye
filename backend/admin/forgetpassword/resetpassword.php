<?php

include "../../connect.php";

$email = filterRequest("email");
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
$data = array("admin_password" => $password);
updateData("admin", $data, "admin_email = '$email'");