<?php
include "config.php";
include "classes/siteResultsProvider.php";
include "classes/imageResultsProvider.php";

$query = isset($_GET['query']) ? $_GET['query'] :  exit("Something went wrong");
$type = isset($_GET['type']) ? $_GET['type'] : 'sites';
$page = isset($_GET['page']) ? $_GET['page'] : 1;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="description" content="Search engine for sites and images">
  <meta name="keywords" content="engine,search engine,search,site">
  <meta name="author" content="Johar Khan">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Gogle</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
  <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time() ?>">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>

<body>

  <div class="wrapper">

    <div class="header">
      <div class="headerContent">

        <div class="logoContainer">
          <a href="index.php">
            <img src="assets/img/logo.PNG" alt="Gugle">
          </a>
        </div>

        <div class="searchContainer">
          <form action="search.php" method="GET">
            <div class="searchBarContainer">
              <input type="hidden" name="type" value="<?php echo $type; ?>">
              <input type="text" class="searchBox" name="query" value="<?php echo isset($_GET['query']) ? $_GET['query'] : '' ?>">
              <button class="glass">&#128269;</button>
            </div>
          </form>
        </div>

      </div>

      <div class="tabsContainer">
        <ul class="tabList">
          <li class="<?php echo $type == 'sites' ? 'active' : '' ?>">
            <a href='<?php echo "search.php?query=$query&type=sites"; ?>'>Sites</a>
          </li>
          <li class="<?php echo $type == 'images' ? 'active' : '' ?>">
            <a href='<?php echo "search.php?query=$query&type=images"; ?>'>Images</a>
          </li>
        </ul>
      </div>

    </div>


    <div class="mainResultsSection">
      <?php
      if ($type == 'sites') {
        $resultProvider = new SiteResultsProvider($connection);
        $pageSize = 20;
      }
      if ($type == 'images') {
        $resultProvider = new ImageResultsProvider($connection);
        $totalImages = $resultProvider->getNumResults($query);
        $pageSize = 30;
      }

      $totalRes = $resultProvider->getNumResults($query);
      ?>
      <p class="result-count"><?php echo ($type == 'sites') ? $totalRes : $totalImages; ?> results found.</p>

      <?php
      echo $resultProvider->getResultsHtml($page, $pageSize, $query);
      ?>
    </div>

    <div class="paginationContainer">

      <div class="pageButtons">
        <div class="pageNumberContainer">
          <img src="assets/img/pagestart.PNG" alt="">
        </div>

        <?php
        $pagesToShow = 10;
        $numPages = ceil($totalRes / $pageSize);
        $pagesLeft = min($pagesToShow, $numPages);

        $currentPage = $page - floor($pagesToShow / 2);    //5-5 on each side

        if ($currentPage < 1) {
          $currentPage = 1;
        }

        if ($currentPage + $pagesLeft > $numPages + 1) {
          $currentPage = $numPages + 1 - $pagesLeft;
        }

        while ($pagesLeft != 0 && $currentPage <= $numPages + 1) {
          if ($currentPage == $page) {
            echo "<div class='pageNumberContainer'>
                  <img src='assets/img/pageselected.png' class='selected' alt=''>
                  <span class='pageNumber'>$currentPage</span>    
                </div>
            ";
          } else {
            echo "<div class='pageNumberContainer'>
                    <a href='search.php?query=$query&type=$type&page=$currentPage'>
                      <img src='assets/img/page.png' alt=''>
                      <span class='pageNumber'>$currentPage</span>    
                    </a>
                  </div>
            ";
          }
          $currentPage++;
          $pagesLeft--;
        }
        ?>

        <div class="pageNumberContainer">
          <img src="assets/img/pageend.PNG" alt="">
        </div>

      </div>
    </div>


  </div>

  <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>

  <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>

  <script src="assets/js/script.js"></script>
</body>

</html>