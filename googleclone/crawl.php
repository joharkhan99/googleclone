<?php
include "config.php";
include "classes/DomDocumentParser.php";

$alreadyCrawled = [];
$crawling = [];
$alreadyFoundImages = [];

function linkExists($url)
{
  global $connection;
  $query = mysqli_query($connection, "SELECT * FROM sites WHERE url='$url'");

  // if nog equal to 0 means link exist and returns true
  return $query->num_rows != 0;
}

function insertLinkToDB($url, $title, $description, $keywords)
{
  global $connection;

  // ? are placeholders where vals will be added after
  $query = $connection->prepare("INSERT INTO sites(url,title,description,keywords) VALUES(?,?,?,?)");

  // s: strings. add values to those ?
  $query->bind_param("ssss", $url, $title, $description, $keywords);

  return $query->execute();
}

function insertImageToDB($url, $src, $alt, $title)
{
  global $connection;

  // ? are placeholders where vals will be added after
  $query = $connection->prepare("INSERT INTO images(siteUrl,imageurl,alt,title) VALUES(?,?,?,?)");
  // s: strings. add values to those ?
  $query->bind_param("ssss", $url, $src, $alt, $title);
  $query->execute();
}

// converts relative urls to absolute/full urls
function createLink($src, $url)
{
  $scheme = parse_url($url)['scheme'];    //http/https
  $host = parse_url($url)['host'];        //www.reecekenney.com

  // (//www.reecekenney.com -> http://www.reecekenney.com)
  if (substr($src, 0, 2) == "//")
    $src = $scheme . ":" . $src;

  // (/classes/down -> http://www.reecekenney.com/classes/down)
  elseif (substr($src, 0, 1) == "/")
    $src = $scheme . ":" . $host . $src;

  // (./about/aboutUs.php -> )
  elseif (substr($src, 0, 2) == "./")
    $src = $scheme . "://" . $host . dirname(parse_url($url)['path']) . substr($src, 1);

  // (../about/aboutUs.php -> http://www.reecekenney.com/about/aboutUs.php)
  elseif (substr($src, 0, 3) == "../")
    $src = $scheme . "://" . $host . "/" . $src;

  // (about/aboutUs.php -> http://www.reecekenney.com/about/aboutUs.php)
  elseif (substr($src, 0, 5) != "https" && substr($src, 0, 4) != "http")
    $src = $scheme . "://" . $host . "/" . $src;

  return $src;
}

function getDetails($url)
{

  global $alreadyFoundImages;

  $parser = new DomDocumentParser($url);
  $titleArr = $parser->getTitleTag();

  if (sizeof($titleArr) == 0 || $titleArr[0] == NULL)
    return;

  $title = $titleArr[0]->nodeValue;         //get title tag value
  $title = str_replace("\n", "", $title);     //remove next lines from title

  if ($title == '')
    return;

  $description = "";
  $keywords = "";

  $metasArray = $parser->getmetsTags();
  foreach ($metasArray as $meta) {
    if ($meta->getAttribute('name') == 'description')
      $description = $meta->getAttribute('content');
    if ($meta->getAttribute('name') == 'keywords')
      $keywords = $meta->getAttribute('content');
  }

  $description = str_replace("\n", "", $description);     //remove next lines from descri.. value
  $keywords = str_replace("\n", "", $keywords);     //remove next lines from keywords

  if (linkExists($url)) {
    echo "exists";
  } elseif (insertLinkToDB($url, $title, $description, $keywords)) {
    echo "SUCCESS: $url";
  } else {
    echo "Error: failed to insert";
  }

  $imagesArr = $parser->getImageTags();
  foreach ($imagesArr as $image) {
    $src = $image->getAttribute("src");
    $alt = $image->getAttribute("alt");
    $title = $image->getAttribute("title");

    if (!$title && !$alt)
      continue;

    $src = createLink($src, $url);

    // insert to array if not there in array
    if (!in_array($src, $alreadyFoundImages)) {
      $alreadyFoundImages[] = $src;

      insertImageToDB($url, $src, $alt, $title);
    }
  }
}

function followLinks($url)
{
  global $alreadyCrawled, $crawling;

  $parser = new DomDocumentParser($url);
  $linkList = $parser->getLinks();        //getLinks() returns a tags of site
  foreach ($linkList as $link) {
    $href = $link->getAttribute("href");

    // ignore urls that have # sign or contains javascript
    if (strpos($href, '#') !== false)
      continue;
    elseif (substr($href, 0, 11) == "javascript:")
      continue;

    $href = createLink($href, $url);

    //append to array if not there
    if (!in_array($href, $alreadyCrawled)) {
      $alreadyCrawled[] = $href;
      $crawling[] = $href;
      getDetails($href);
    }
  }

  // get rid of the top item in array
  array_shift($crawling);

  // goto the links inside array
  foreach ($crawling as $site) {
    followLinks($site);
  }
}

$startUrl = "https://www.gamespot.com/";
followLinks($startUrl);
