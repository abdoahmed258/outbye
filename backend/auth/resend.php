<?php 

include "../connect.php"  ;

$email = filterRequest("email");

$verfiycode     = rand(10000 , 99999);

$data = array(
"users_verfiycode" => $verfiycode
) ; 

updateData("users" ,  $data  , "users_email = '$email'" ) ; 


$subject = "Welcome to Outbye – Verify Your Account";
$body = '
<div style="font-family: Arial, sans-serif; background-color: #f9f9f9; color: #333; padding: 30px; border-radius: 10px;">
    <div style="text-align: center;">
        <img src="https://abdulrahmanantar.com/outbye/outbye-logo.png" alt="Outbye Logo" style="width: 120px; margin-bottom: 20px;">
        <h2 style="color: #00bcd4;">Verify Your Outbye Account</h2>
    </div>

    <p>Hi there 👋,</p>

    <p>Here is your verification code to continue with your Outbye experience:</p>

    <div style="text-align: center; margin: 20px 0;">
        <span style="font-size: 24px; font-weight: bold; color: #007bff; background-color: #e6f2ff; padding: 12px 24px; border-radius: 8px; display: inline-block;">
            ' . htmlspecialchars($verfiycode) . '
        </span>
    </div>

    <p>If you didn\'t request this, please ignore the message. Otherwise, enter the code to complete your sign-up.</p>

  

    <p>Thanks for choosing Outbye 🌍<br><strong>The Outbye Team</strong></p>
</div>
';

sendEmail($email, $subject, $body);

