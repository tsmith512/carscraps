<?php

$entityBody = file_get_contents('php://input');

$string = trim(preg_replace('/\s+/', ' ', $entityBody));
$pieces = explode(" ", trim($string));

foreach ($pieces as $word) {
  if (preg_match('/http.+craigslist\.org.+html/', $word)) {
    // Zapier's "Raw" output sure looks a helluva lot like a JSON array which is stupid
    $word = preg_replace('/html.+$/', 'html', $word);
    $word = preg_replace('/^.+http/', 'http', $word);
    file_put_contents('queue.txt', $word.PHP_EOL , FILE_APPEND | LOCK_EX);
  }
}