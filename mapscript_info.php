<HTML>
<BODY>

<?php
  if (PHP_OS == "WINNT" || PHP_OS == "WIN32")
  {
    dl("php_mapscript.dll");
  }
  else
  {
    dl("php_mapscript.so");
  }
  phpinfo();
?>

</BODY>
</HTML>