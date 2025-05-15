<?php 

include "../connect.php"  ;

$user_reviewid = filterRequest("id") ; 

deleteData("general_reviews" , "id = $user_reviewid "); 