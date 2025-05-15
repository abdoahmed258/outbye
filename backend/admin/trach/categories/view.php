<?php
include '../../../connect.php';

$deletedCategories = getAllData("categories", "is_deleted = 1");
