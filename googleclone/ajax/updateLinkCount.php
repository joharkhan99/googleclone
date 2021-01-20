<?php
include "../config.php";

if (isset($_POST['id'])) {

  $query = $connection->prepare("UPDATE sites SET clicks=clicks+1 WHERE id=?");
  $query->bind_param('i', $_POST['id']);
  $query->execute();
} else {
  echo 'Not sent';
}
