<?php


namespace proxy{

    $error = null;
    if(phpversion() <= 7.2){
        $error = "PHP version need to be higher than 7.2";
    }

    $extensions = array('pthreads', 'sockets', 'zlib', 'yaml');

    $missing = '';
    foreach($extensions as $extension){
        if(!extension_loaded($extension)){
            $missing .= $extension . " ";
        }
    }
    if(strlen($missing) > 0)$error = "Missing PHP extensions: " . $missing;

    new Server([]/** CLI ARGUMENTS */);


}