<?php


namespace proxy {

    define("COMPOSER", "vendor/autoload.php");

    require COMPOSER;

    $extensions = ['pthreads', 'sockets', 'zlib', 'yaml'];
    $notLoaded = [];
    foreach ($extensions as $extension) {
        if(!extension_loaded($extension)) {
            $notLoaded[] = $extension;
        }
    }

    if(!empty($notLoaded)) {
        echo "Could not start proxy, " . implode(", ", $notLoaded) . " extension(s) wasn't found.\n";
        sleep(10);
        exit();
    }

    new Server($argv);
}