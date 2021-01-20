<?php
class DomDocumentParser
{
  private $doc;   //will contain all html of site
  public function __construct($url)
  {
    $options = [
      'http' => ['method' => 'GET', 'header' => 'User-Agent: pugEngine/0.1\n']
    ];
    $context = stream_context_create($options);
    $doc = new DOMDocument();   //built-in php class
    // @ means dont show any warnings/errors
    @$doc->loadHTML(file_get_contents($url, false, $context));

    // $doc contains html of that site
    $this->doc = $doc;
  }

  public function getLinks()
  {
    return $this->doc->getElementsByTagName('a');   //return aray of 'a' tags
  }

  public function getTitleTag()
  {
    return $this->doc->getElementsByTagName('title');   //return aray of 'title' tags
  }

  public function getmetsTags()
  {
    return $this->doc->getElementsByTagName('meta');   //return aray of 'meta' tags
  }

  public function getImageTags()
  {
    return $this->doc->getElementsByTagName('img');   //return aray of 'img' tags
  }
}
