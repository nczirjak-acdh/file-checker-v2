<?php

namespace OEAW\Object;

/**
 * Description of FilesListObject
 *
 * @author nczirjak
 */
class FileObject {

    private $filename;
    private $filenameAndDir;
    private $directory;
    private $size = 0;
    private $extension;
    private $type;
    private $valid = false;
    private $lastmodification;
    private $errors = [];

    public function __construct(object $file) {
        $this->filename = $file->getFileName();
        $this->directory = str_replace($file->getFileName(), "", $file->getPathName());
        $this->filenameAndDir = $file->getPathName();
        $this->size = $file->getSize();
        $this->extension = $file->getExtension();
        $this->type = mime_content_type($this->filenameAndDir);
        $this->valid = $this->checkFileNameValidity($file->getFileName());
        $this->lastmodification = gmdate("Y-m-d\TH:i:s\Z", $file->getMTime());
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
    
    /**
     * 
     * Checks the filename validation
     * 
     * @param string $filename
     * @return bool : true
     */
    private function checkFileNameValidity(): bool {
        if (preg_match('/[^A-Za-z0-9\_\(\)\-\.]/', $this->filename)) {
            $this->errors[] = array("errorType" => "File name contains invalid characters", "errorCode" => 0, "dir" => $this->directory, "filename" => $this->filename);
            return false;
        }
        return true;
    }


}
