<?php
define("FLICK","c81efc466062c0b12bf5b75f8fd6289a");

function uwe_get_file($uri) {
//uses UWE proxy on file
    $context = stream_context_create(
            array('http' =>
                array('proxy' => 'proxysg.uwe.ac.uk:8080',
                    'header' => 'Cache-Control: no-cache')
            ));
    $contents = file_get_contents($uri, false, $context);
    return $contents;
}

?>
