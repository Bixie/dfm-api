<?php

/** $var array $config */
include 'main.php';

//check previewimages dir
$fp = __DIR__ . '\\' .$config['lime']['temppath.previewimages'];
$previewPath = normalizePath(__DIR__ . '\\' .$config['lime']['temppath.previewimages']);
if ($previewPath && !is_dir($previewPath)) {
    mkdir($previewPath, 755, true);
}


/**
 * Normalizes the given path
 * @param  string $path
 * @return string
 */
function normalizePath ($path) {
    $path = str_replace(['\\', '//'], '/', $path);
    $parts = array_filter(explode('/', $path), 'strlen');
    $tokens = [];
    foreach ($parts as $part) {
        if ('..' === $part) {
            array_pop($tokens);
        } elseif ('.' !== $part) {
            array_push($tokens, $part);
        }
    }

    return implode('/', $tokens);
}

