<?php
class ImageResultsProvider
{
  private $connection;

  public function __construct($connection)
  {
    $this->connection = $connection;
  }

  public function getNumResults($query)
  {
    $query = mysqli_query($this->connection, "SELECT COUNT(*) AS total FROM images WHERE title LIKE '%$query%' OR alt LIKE '%$query%' AND broken=0");

    $row = mysqli_fetch_assoc($query);
    return $row['total'];
  }

  public function getResultsHtml($page, $pageSize, $query)
  {

    // pag 1: (1 - 1) * 20 = 0;
    // pag 2: (2 - 1) * 20 = 20;
    // pag 1: (3 - 1) * 20 =4 0;
    $fromLimit = ($page - 1) * $pageSize;

    $sql = mysqli_query($this->connection, "SELECT * FROM images WHERE title LIKE '%$query%' OR alt LIKE '%$query%' OR imageUrl LIKE '%$query%' AND broken=0 ORDER BY clicks DESC LIMIT $fromLimit,$pageSize");

    $resultsHtml = "<div class='imageResults'>";

    $count = 0;
    while ($row = mysqli_fetch_assoc($sql)) {
      $count++;
      $title = $row['title'];
      $imageUrl = $row['imageUrl'];
      $siteUrl = $row['siteUrl'];
      $id = $row['id'];
      $alt = $row['alt'];

      if ($title) {
        $displayText = $title;
      } elseif ($alt) {
        $displayText = $alt;
      } else {
        $displayText = $imageUrl;
      }

      $resultsHtml .= "
        <div class='griditem image$count'>
          <a href='$imageUrl' data-fancybox data-caption='$displayText' data-siteurl='$siteUrl'>
            <script>$(document).ready(function(){
              loadImage(\"$imageUrl\",\"image$count\");
            });</script>
            <span class='details'>$displayText</span>
          </a>
        </div>";
    }

    $resultsHtml .= "</div>";
    return $resultsHtml;
  }
}
