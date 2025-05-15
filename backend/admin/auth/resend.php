<?php 

include "../../connect.php";

$email = filterRequest("email");

$verfiycode     = rand(10000 , 99999);

$data = array(
"admin_verfiycode" => $verfiycode
) ; 

updateData("admin" ,  $data  , "admin_email = '$email'" ) ; 

sendEmail($email, "Verification Code for Outbye App", "Your verification code is: $verfiycode");
