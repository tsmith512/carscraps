<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Document</title>
</head>
<body>

<?php

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
?>

</body>
</html>
