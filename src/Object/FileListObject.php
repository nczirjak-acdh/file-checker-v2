<?php

namespace OEAW\Object;

/**
 * Description of FilesListObject
 *
 * @author nczirjak
 */
class FileListObject {

    private $filename;
    private $filenameAndDir;
    private $directory;
    private $size = 0;
    private $extension;
    private $type;
    private $valid = false;
    private $lastmodification;
    private $errors = [];

    public function __construct(string $filename, string $directory, int $size,
            string $extension, string $type, bool $valid, string $lastmodification) {
        $this->filename = $filename;
        $this->directory = $directory;
        $this->filenameAndDir = $directory . $filename;
        $this->size = $size;
        $this->extension = $extension;
        $this->type = $type;
        $this->valid = $valid;
        $this->lastmodification = $lastmodification;
    }

    public function getFilename() {
        return $this->filename;
    }

    public function getFilenameAndDir() {
        return $this->filenameAndDir;
    }

    public function getDirectory() {
        return $this->directory;
    }

    public function getSize() {
        return $this->size;
    }

    public function getDir() {
        return $this->dir;
    }

    public function getExtension() {
        return $this->extension;
    }

    public function getType() {
        return $this->type;
    }

    public function getValid() {
        return $this->valid;
    }

    public function getLastmodification() {
        return $this->lastmodification;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function setErrors(array $errors): void {
        $this->errors[] = $errors;
    }
    
    public function getFileDirectory(): string {
        return str_replace($this->getFilename(), "", $this->getFilenameAndDir());
    }

    public function isZipFile(): bool {
        if ($this->getExtension() == "zip" || $this->getType() == "application/zip" ||
                $this->getExtension() == "gzip" || $this->getType() == "application/gzip" ||
                $this->getExtension() == "7zip" || $this->getType() == "application/7zip") {
            return true;
        }
        return false;
    }

    public function isPdfFile(): bool {
        if ($this->getExtension() == "pdf" || $this->getType() == "application/pdf") {
            return true;
        }
        return false;
    }
    
    public function isBagItFile(): bool {
        if ($this->getExtension() == "tgz" || $this->getExtension() == "zip" || $this->getExtension() == "bz2") {
            return true;
        }
        return false;
    }
    
    public function toJsonFile(): array {
        return array(
            "name" => $this->getFilenameAndDir(), "directory" => $this->getDirectory(), 
            "type" => $this->getType(), "size" => $this->getSize(),
            "lastmod"=> $this->getLastmodification(), "valid_file" => $this->getValid(),
            "filename" => $this->getFilename(), "extension" => $this->getExtension()
            );
    }
    
    public function toFileTypeJsonFile(): array {
        return array(
            "name" => $this->getFilenameAndDir(), "directory" => $this->getDirectory(), 
            "type" => $this->getType(), "size" => $this->getSize(),
            "lastmod"=> $this->getLastmodified(), "valid_file" => $this->getValid(),
            "filename" => $this->getFilename(), "extension" => $this->getExtension()
            );
    }

}
