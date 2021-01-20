<?php ob_start() ?>
<?php

define("DB_NAME", "googleclone");
define("HOST", "localhost");
define("DB_PASS", "");
define("USERNAME", "root");

$connection = mysqli_connect(HOST, USERNAME, DB_PASS, DB_NAME);

if (mysqli_connect_errno()) {
  die("CONNECTION FAILED: " . mysqli_connect_errno());
}



?>