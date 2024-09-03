<?php
function create_zip($year,$upload_dir) {
    // Get real path for our folder
//    $upload_dir = wp_upload_dir();
    $src = $upload_dir  . "/bic-$year/";
    $dist = $upload_dir . "/bic-$year.zip";

    // Initialize archive object
    $zip = new ZipArchive();
    if ($zip->open($dist, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
        exit("Cannot open <$dist>\n");
    }

    // Create recursive directory iterator
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($src),
        RecursiveIteratorIterator::SELF_FIRST
    );

    $count = 0;
    foreach ($files as $file) {
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($src));

        if ($file->isDir()) {
            // Add current directory to archive
            $zip->addEmptyDir($relativePath);
        } else {
            // Skip adding files that don't match the condition
            if (str_contains($file->getFilename(), ").")) {
                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
                $count++;
            }
        }
    }

    // Zip archive will be created only after closing object
    $zip->close();
    file_put_contents( "/bic-$year.zip.count", $count);
//    return $upload_dir["baseurl"] . "/bic-$year.zip";
}

// Ensure the script is called via CLI
if (php_sapi_name() == "cli") {
    if ($argc != 2) {
        echo "Usage: php zip_creator.php <year> <uploade_dir>\n";
        exit(1);
    }
    $year = $argv[1];
    $dir = $argv[2];
    create_zip($year,$dir);
}
