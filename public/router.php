<?php

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve arquivos estáticos reais diretamente
$file = __DIR__ . $uri;
if ($uri !== '/' && is_file($file)) {
    return false;
}

// Serve arquivos de storage contornando o symlink
if (str_starts_with($uri, '/storage/')) {
    $relative = substr($uri, strlen('/storage/'));
    $storagePath = __DIR__ . '/../storage/app/public/' . $relative;
    $realPath = realpath($storagePath);

    if ($realPath && str_starts_with($realPath, realpath(__DIR__ . '/../storage/app/public')) && is_file($realPath)) {
        $mime = mime_content_type($realPath) ?: 'application/octet-stream';
        header('Content-Type: ' . $mime);
        readfile($realPath);
        return true;
    }
}

// Tudo o mais vai para o Laravel
require __DIR__ . '/index.php';
