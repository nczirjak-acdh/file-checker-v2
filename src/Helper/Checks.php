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
    private $reportHelper;

    public function __construct() {
            
    }
    
    public function start(\OEAW\Object\SettingsObject $settings) {
        $this->settings = $settings;

        echo "\n File check started...\n";
        //check the directories and files and run dir and file checks
        $this->iterateDirectories();
        
        //run the directory checks
        $this->runDirectoryChecks();
        
        //run the file checks
        $this->runFileChecks();
        
        echo "\n Generating reports...\n";
        $this->createReports();
        
        
       
        //$this->createFileTypeListData();
        if(count($this->fileList) == 0) {
            throw new \Exception('There are no files! runChecks failed!');
        }
        echo "Check is done!";
    }
    
    private function createReports() {
        $this->reportHelper = new \OEAW\Helper\ReportFileHelper(array("errors" => $this->errors, "fileList" => $this->fileList, "dirList" => $this->directoryList), $this->settings);    
        return $this->reportHelper->createReportFiles();        
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
        echo "fetch files/folders\n";
        foreach ($iter as $path => $dir) {            
            if ($dir->isDir()) {
                $this->directoryList[] = new \OEAW\Object\DirectoryObject($dir, $this->settings->getDirectoryToCheck());
            } else {
                echo $dir->getPathName()."\n";
                $this->progressBar->advance();
                echo "\n";
                $this->fileList[] = new \OEAW\Object\FileObject($dir);
            }
        }
    }

    private function countFiles(): void {
        $this->numberOfFiles = count($this->files) - 2;
        if ($this->numberOfFiles == 0) {
            throw new \Exception('Directory empty');
        }
    }

    private function runDirectoryChecks() {
        $dc = new \OEAW\Helper\DirectoryChecks();
        $this->errors = $dc->checkValidDirectories($this->directoryList);
    }

    private function runFileChecks() {
        //run the filechecks
        $obj = new \OEAW\Helper\FileChecks();
        $this->errors = $obj->start($this->fileList, $this->settings);
      
    }

    

}
