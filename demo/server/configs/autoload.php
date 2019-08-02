<?php
spl_autoload_register(function ($class) {
    $nameSpace = explode('\\', $class);
    $className = array_pop($nameSpace);
    $nameSpace = array_map("lcfirst", $nameSpace);
    $filename = ROOT_PATH.(implode('/', $nameSpace).DIRECTORY_SEPARATOR.$className.".php");
    if (file_exists($filename)) {
        include_once $filename;
    }
});
