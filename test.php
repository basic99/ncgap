<?php

try {
    $map = ms_newMapObj('test.map');
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}
