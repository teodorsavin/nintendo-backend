<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use DirectoryIterator;

class FileSystemService
{
    public static $basePath = __DIR__ . '/../../var/data/';
    public $fileSystem;

    public function __construct(Filesystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;

        if (!$this->fileSystem->exists(self::$basePath)) {
            try {
                $this->fileSystem->mkdir(self::$basePath, 0777);
            } catch (\Exception $e) {
                throw new \Exception('Unable to create directory ' . self::$basePath);
            }
        }
    }

    public function read($filename)
    {
        $path = self::$basePath;

        if (!$this->fileSystem->exists($path . $filename)) {
            return 0;
        }

        $result = file_get_contents($path . $filename);
        if ($result === false) {
            throw new \Exception("Unable to read file: {$filename}");
        }

        return $result;
    }

    public function write($filename, $value)
    {
        $path = self::$basePath;

        $result = file_put_contents($path . $filename, $value);
        if ($result === false) {
            throw new \Exception("Unable to write to the file {$path}{$filename}. Value: {$value}");
        }
        return $result;
    }

    public static function cleanUp()
    {
        if (file_exists(self::$basePath)) {
            self::deleteFolder(self::$basePath);
        }
    }

    private static function deleteFolder($path)
    {
        foreach (new DirectoryIterator($path) as $file) {
            if ($file->isDot()) {
                continue;
            }
            if ($file->isFile()) {
                unlink($file->getPathName());
            }
            if ($file->isDir()) {
                self::deleteFolder($file->getPathName());
            }
        }
        rmdir($path);
    }

    public function getVotes()
    {
        $array = [];
        foreach (new DirectoryIterator(self::$basePath) as $file) {
            if ($file->isDot()) {
                continue;
            }
            if ($file->isFile()) {
                $array[$file->getBasename()] = $this->read($file->getBasename());
            }
        }

        return $array;
    }
}
