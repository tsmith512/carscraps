<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class offers API endpoints related to receiving requests and scraping
 * requested URLs. Currently only supports a handful of Slack App events.
 */
class MirrorIndex {
  /**
   * @Route("/", methods={"GET"})
   */
  public function index() {
    ob_start();

    $handle = fopen("index.txt", "r");
    if ($handle) {
      echo "<ul>";
      while (($line = fgets($handle)) !== false) {
        $parts = explode(":", $line);
        $file = str_replace("public/mirror", "", $parts[0]);
        $title = preg_replace("/<\/?title>/", "", trim($parts[1]));
        echo "<li><a href=\"/mirror/{$file}\">{$title}</a></li>";
      }
      fclose($handle);
      echo "</ul>";
    } else {
      echo "Error";
    }

    return new Response(ob_get_clean(), 200);
  }
}
