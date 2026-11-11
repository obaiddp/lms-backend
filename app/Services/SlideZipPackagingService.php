<?php

namespace App\Services;

class SlideZipPackagingService
{
    public function zipSlideFolder(string $sourceFolderFsPath, string $destinationZipFsPath): bool
    {
        $source = realpath($sourceFolderFsPath);
        if (! $source || ! is_dir($source)) {
            return false;
        }

        $zip = new \ZipArchive();
        if ($zip->open($destinationZipFsPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return false;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if ($file->isDir()) {
                continue;
            }

            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($source) + 1);
            $relativePath = str_replace('\\', '/', $relativePath);

            if (basename($relativePath) === 'index.html') {
                $zip->addFile($filePath, 'index.html');
                continue;
            }

            if (str_starts_with($relativePath, 'data/')) {
                $zip->addFile($filePath, $relativePath);
            } else {
                $zip->addFile($filePath, 'data/' . $relativePath);
            }
        }

        return (bool) $zip->close();
    }
}
