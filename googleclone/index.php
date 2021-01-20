<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="description" content="Search engine for sites and images">
  <meta name="keywords" content="engine,search engine,search,site">
  <meta name="author" content="Johar Khan">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>PUUG Engine</title>
  <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time() ?>">
</head>

<body>

  <div class="wrapper indexPage">
    <div class="mainSection">

      <div class="logoContainer">
        <img src="assets/img/logo.PNG" alt="Gugle">
      </div>

      <div class="searchContainer">
        <form action="search.php" method="GET">
          <input type="text" class="searchBox" name="query">
          <input type="submit" class="searchBtn" value="Search">
        </form>
      </div>

    </div>
  </div>

</body>

</html>