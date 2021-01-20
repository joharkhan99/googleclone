<?php
include "../config.php";

if (isset($_POST['url'])) {

  $query = $connection->prepare("UPDATE images SET clicks=clicks+1 WHERE imageUrl=?");
  $query->bind_param('s', $_POST['url']);
  $query->execute();
} else {
  echo 'Not sent';
}
