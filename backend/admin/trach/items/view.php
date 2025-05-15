<?php
include '../../../connect.php';
$deletedItems = getAllData("items", "is_deleted = 1");
