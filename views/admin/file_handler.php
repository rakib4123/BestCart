<?php


function sanitizeFileNameSimple($name) {
    $name = basename($name);
    $out = "";
    $len = strlen($name);
    for ($i = 0; $i < $len; $i++) {
        $ch = $name[$i];
        if (ctype_alnum($ch) || $ch === '.' || $ch === '_' || $ch === '-') {
            $out .= $ch;
        } else {
            $out .= '_';
        }
    }
    
    if ($out === '' || $out === '.' || $out === '..') {
        $out = 'file';
    }
    return $out;
}


function uploadImage($file, $fallback = 'default.png') {

    
    if (!is_array($file) || !isset($file['name']) || trim((string)$file['name']) === '') {
        return $fallback;
    }

    
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return $fallback;
    }

    
    $uploadDir = __DIR__ . "/../../uploads";
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0777, true);
    }

    
    $ht = $uploadDir . DIRECTORY_SEPARATOR . ".htaccess";
    if (!file_exists($ht)) {
        @file_put_contents($ht, "php_flag engine off\nOptions -Indexes\n<FilesMatch \"\.(php|phtml|php3|php4|php5|phar)$\">\n  Deny from all\n</FilesMatch>\n");
    }

    
    $allowedExt = ['jpg','jpeg','png','webp','gif'];
    $safeName = sanitizeFileNameSimple($file['name']);
    $ext = strtolower(pathinfo($safeName, PATHINFO_EXTENSION));
    if ($ext === '' || !in_array($ext, $allowedExt, true)) {
        return $fallback;
    }

    
    $maxBytes = 2 * 1024 * 1024;
    if (isset($file['size']) && (int)$file['size'] > $maxBytes) {
        return $fallback;
    }

    
    if (!isset($file['tmp_name']) || $file['tmp_name'] === '') {
        return $fallback;
    }
    $imgInfo = @getimagesize($file['tmp_name']);
    if ($imgInfo === false) {
        return $fallback;
    }

   
    $fileName = time() . "_" . $safeName;
    $targetFile = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

    
    if (@move_uploaded_file($file['tmp_name'], $targetFile)) {
        return $fileName;
    }

    return $fallback;
}
?>
