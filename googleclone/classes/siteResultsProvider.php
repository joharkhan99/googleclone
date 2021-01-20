<?php
class SiteResultsProvider
{
  private $connection;

  public function __construct($connection)
  {
    $this->connection = $connection;
  }

  public function getNumResults($query)
  {
    $query = mysqli_query($this->connection, "SELECT COUNT(*) AS total FROM sites WHERE title LIKE '%$query%' OR url LIKE '%$query%' OR keywords LIKE '%$query%' OR description LIKE '%$query%'");

    $row = mysqli_fetch_assoc($query);
    return $row['total'];
  }

  public function getResultsHtml($page, $pageSize, $query)
  {

    // pag 1: (1 - 1) * 20 = 0;
    // pag 2: (2 - 1) * 20 = 20;
    // pag 1: (3 - 1) * 20 =4 0;
    $fromLimit = ($page - 1) * $pageSize;

    $sql = mysqli_query($this->connection, "SELECT * FROM sites WHERE title LIKE '%$query%' OR url LIKE '%$query%' OR keywords LIKE '%$query%' OR description LIKE '%$query%' ORDER BY clicks DESC LIMIT $fromLimit, $pageSize");

    $resultsHtml = "<div class='siteResults'>";

    while ($row = mysqli_fetch_assoc($sql)) {
      $title = $row['title'];
      $id = $row['id'];
      $description = $row['description'];
      $url = $row['url'];


      $title = $this->trimField($title, 55);
      $description = $this->trimField($description, 230);

      // if (strlen($description) > 90) {
      //   $description = substr($description, 0, 100) . "<br>" . substr($description, 100, strlen($description));
      // }

      $resultsHtml .= "
        <div class='resultContainer'>
          <h3 class='title'>
            <a href='$url' class='result' data-linkId=$id>$title</a>
          </h3>
          <span class='url'>$url</span>
          <span class='description'>$description</span>
        </div>";
    }

    $resultsHtml .= "</div>";
    return $resultsHtml;
  }

  private function trimField($string, $charLimit)
  {
    // if string length greater than character limit then dots will be ...
    $dots = strlen($string) > $charLimit ? "..." : "";
    // return substr of original string
    return substr($string, 0, $charLimit) . $dots;
  }
}
