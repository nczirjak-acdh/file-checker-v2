<?php

namespace OEAW\Helper;

/**
 * Description of Checks
 *
 * @author nczirjak
 */
class Checks {

    private $settings;
    private $files = [];
    private $numberOfFiles = 0;
    private $progressBar;
    private $directoryList = [];
    private $fileList = [];
    private $errors = [];
    private $fileObj;

    public function start(\OEAW\Object\SettingsObject $settings) {
        $this->settings = $settings;

        echo "\n File check started...\n";
        //check the directories and files and run dir and file checks
        $this->iterateDirectories();

        //check the duplications
        $this->checkDuplications();

        return array("errors" => $this->errors, "fileList" => $this->fileList, "dirList" => $this->directoryList);
    }

    private function checkDirectory(): void {
         try {
           @dir($this->settings->getDirectoryToCheck()); 
        } catch (\Exception $ex) {
            throw new Exception("Failed opening directory" . $this->settings->getDirectoryToCheck() . " for reading");
        }
    }
    
    
    private function iterateDirectories() {
        
        //check if the directory exists/writable or not
        $this->checkDirectory();
       
        //nem biztos h kell
        $this->files = scandir($this->settings->getDirectoryToCheck());
      
        $this->countFiles();

        $this->progressBar = new \ProgressBar\Manager(0, $this->numberOfFiles);

        $iter = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($this->settings->getDirectoryToCheck(), \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST,
                \RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
        );
        
        foreach ($iter as $path => $dir) {            
            if ($dir->isDir()) {
                $this->runDirectoryChecks($dir->getPathName(), $dir);
            } else {
                echo $dir->getPathName()."\n";
                $this->progressBar->advance();
                echo "\n";
                $this->createFileObject($dir->getPathName(), $dir);
                $this->runFileChecks($path);
            }
        }
    }

    private function countFiles(): void {
        $this->numberOfFiles = count($this->files) - 2;
        if ($this->numberOfFiles == 0) {
            throw new \Exception('Directory empty');
        }
    }

    private function runDirectoryChecks(string $filename, object $file) {
        $obj = new \OEAW\Helper\DirectoryChecks();
        $dirObj = $obj->start($filename, $file, $this->settings->getDirectoryToCheck());
        $this->directoryList[] = $dirObj;
        if ($dirObj->getValid() === false) {
            $this->errors[] = array("errorType" => "Directory name is invalid", "filename" => $dirObj->getName(), "dir" => $dirObj->getName());
        }
    }

    private function runFileChecks(string $actualDirectory) {
        //run the filechecks
        $obj = new \OEAW\Helper\FileChecks();
        $file = $obj->start($actualDirectory, $this->fileObj, $this->settings);
        $this->fileList[] = $file['fileObj'];

        if (count($file['errors']) > 0) {
            $this->errors = $file['errors'];
        }
    }

    private function createFileObject(string $filename, object $file) {
        $this->fileObj = new \OEAW\Object\FileListObject(
                $file->getFileName(),
                str_replace($file->getFileName(), "", $filename),
                $file->getSize(),
                $file->getExtension(),
                mime_content_type($filename),
                $this->checkFileNameValidity($file->getFileName()),
                gmdate("Y-m-d\TH:i:s\Z", $file->getMTime())
        );
    }

    private function checkDuplications() {

        $new = array();
        //we get the filenames
        foreach ($this->fileList as $k => $value) {
            $new[$k] = serialize(strtolower($value->getFileName()));
        }

        //remove the duplicates
        $sorted = array_unique($new);

        foreach ($this->fileList as $k => $v) {
            //check which key is missing from the list
            if (!isset($sorted[$k])) {
                $this->errors[] = array("errorType" => "Duplicated Filename!", "errorCode" => 1, "filename" => $v->getFileName(), "dir" => $v->getDirectory());
            }
        }
    }

    /**
     * 
     * Checks the filename validation
     * 
     * @param string $filename
     * @return bool : true
     */
    private function checkFileNameValidity(string $filename): bool {
        if (preg_match('/[^A-Za-z0-9\_\(\)\-\.]/', $filename)) {
            $this->errors[] = array("errorType" => "File name contains invalid characters", "errorCode" => 0, "dir" => $this->actualDirectory, "filename" => $filename);
            return false;
        }
        return true;
    }

}
