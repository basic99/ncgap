<?php

ini_set("log_errors", 1);
ini_set("display_errors", 0);
ini_set("error_log", "/var/www/html/ncgap/logs/php-error.log");

error_log("running map_ajax.php");

  // Create a unique image name every time through

  $image_name = sprintf("phpms-hello%0.6d",rand(0,999999)).".png";

  // Create a new instance of a map object

  $map = ms_newMapObj("/var/www/html/ncgap/test/hello.map");

  // Create an image of the map and save it to disk

  $image=$map->draw();
  $image->saveImage("/var/www/html/ncgap/test/".$image_name);
?>

<html>
<head><title>PHP MapScript Hello World</title></head>
<body>
  <form action="phpms_hello.php" method="POST">
    <input type="image" name="img"
          src="/ncgap/test/<?php echo $image_name; ?>">
  </form>
</body>
</html>
