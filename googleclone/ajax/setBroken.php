<?php
include "../config.php";

if (isset($_POST['src'])) {
  $query = $connection->prepare("UPDATE images SET broken=1 WHERE imageUrl=?");
  $query->bind_param('i', $_POST['src']);
  $query->execute();
} else {
  echo 'Not sent';
}
