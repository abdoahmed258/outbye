<?php 
include '../../../connect.php';
$deletedServices = getAllData("services", "is_deleted = 1");
