<?php

include "../connect.php";

$email = filterRequest("email");
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
$data = array("users_password" => $password);
updateData("users", $data, "users_email = '$email'");