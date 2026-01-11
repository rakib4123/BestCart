<?php
// views/admin/file_handler.php
// Safer upload helper (works from both views and controllers)

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
    // Prevent empty filenames
    if ($out === '' || $out === '.' || $out === '..') {
        $out = 'file';
    }
    return $out;
}

// Upload image and return stored filename.
// - If no file selected: returns $fallback (keeps existing image on edit).
// - If invalid upload: returns $fallback.
function uploadImage($file, $fallback = 'default.png') {

    // 0) No file selected
    if (!is_array($file) || !isset($file['name']) || trim((string)$file['name']) === '') {
        return $fallback;
    }

    // 1) Basic upload error handling
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return $fallback;
    }

    // 2) Uploads directory (project_root/uploads)
    $uploadDir = __DIR__ . "/../../uploads";
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0777, true);
    }

    // 2.1) Prevent PHP execution in uploads on Apache (XAMPP)
    $ht = $uploadDir . DIRECTORY_SEPARATOR . ".htaccess";
    if (!file_exists($ht)) {
        @file_put_contents($ht, "php_flag engine off\nOptions -Indexes\n<FilesMatch \"\.(php|phtml|php3|php4|php5|phar)$\">\n  Deny from all\n</FilesMatch>\n");
    }

    // 3) Validate file type (extension + real image check)
    $allowedExt = ['jpg','jpeg','png','webp','gif'];
    $safeName = sanitizeFileNameSimple($file['name']);
    $ext = strtolower(pathinfo($safeName, PATHINFO_EXTENSION));
    if ($ext === '' || !in_array($ext, $allowedExt, true)) {
        return $fallback;
    }

    // 4) Size limit (2MB)
    $maxBytes = 2 * 1024 * 1024;
    if (isset($file['size']) && (int)$file['size'] > $maxBytes) {
        return $fallback;
    }

    // 5) Verify it is an actual image
    if (!isset($file['tmp_name']) || $file['tmp_name'] === '') {
        return $fallback;
    }
    $imgInfo = @getimagesize($file['tmp_name']);
    if ($imgInfo === false) {
        return $fallback;
    }

    // 6) Unique filename
    $fileName = time() . "_" . $safeName;
    $targetFile = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

    // 7) Move upload (suppress warnings to avoid breaking JSON)
    if (@move_uploaded_file($file['tmp_name'], $targetFile)) {
        return $fileName;
    }

    return $fallback;
}
?>
