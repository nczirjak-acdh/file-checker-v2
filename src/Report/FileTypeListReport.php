<?php

namespace OEAW\Report;
/**
 * Description of FileExtensionList
 *
 * @author nczirjak
 */
class FileTypeListReport {

    private $list = [];
    private $file;
    private $directories = [];
    private $extensions = [];
    private $overallFileCount = 0;
    private $overallFileSize = 0;
    
    public function __construct(array $list, string $file) {
        $this->list = $list;
        $this->file = $file;
    }
    
    public function __toJson() {

        
        
        $jsonData = array("data" => $this->createResult());
        $json = json_encode($jsonData, JSON_UNESCAPED_SLASHES);
        file_put_contents($this->file, $json);
    }

    private function createResult(): array {
        
        $data = $this->createDataSet();
        return $data;
    }
    
    private function createDataSet(): array {
        
        
        
        foreach($this->list as $k => $v) {
           
            $this->fillDirectoriesArray($v);
            //$this->fillExtensionsArray($v);
            $this->overallFileCount++;
            $this->overallFileSize += $v->getSize();
        } 
        
        return array();
    }
    
    private function fillDirectoriesArray(object $v) {
        
        if(!isset($this->directories[$v->getDirectory()])) {
            $this->directories[$v->getDirectory()] = array();
            
        } 
        if(!isset($this->directories[$v->getDirectory()][$v->getExtension()])) {
            $this->directories[$v->getDirectory()][$v->getExtension()]['fileCount'] = 1;
            $this->directories[$v->getDirectory()][$v->getExtension()]['minSize'] = $v->getSize();
            $this->directories[$v->getDirectory()][$v->getExtension()]['maxSize'] = $v->getSize();
            $this->directories[$v->getDirectory()][$v->getExtension()]['sumSize'] = $v->getSize();
        } else {
            $this->directories[$v->getDirectory()][$v->getExtension()]['fileCount'] += 1;
            $this->getMinSize($v);
            $this->getMaxSize($v);
        }
        /*
        if(!isset($this->directories[$v->getDirectory()][$v->getExtension()])) {
            $this->directories[$v->getDirectory()][$v->getExtension()]['minSize'] = $v->getSize();
            $this->directories[$v->getDirectory()][$v->getExtension()]['maxSize'] = $v->getSize();
            $this->directories[$v->getDirectory()][$v->getExtension()]['sumSize'] = $v->getSize();
            $this->directories[$v->getDirectory()][$v->getExtension()]['fileCount'] = 1;
        }else {
            $this->directories[$v->getDirectory()][$v->getExtension()]['sumSize'] += $v->getSize();
            $this->directories[$v->getDirectory()][$v->getExtension()]['fileCount'] += 1;
            $this->getMinSize($v);
            $this->getMaxSize($v);
        }
         * 
         */
    }
    
    private function getMinSize(object $v): void {
        if ($this->directories[$v->getDirectory()][$v->getExtension()]['minSize'] > $v->getSize()) {
            $this->directories[$v->getDirectory()][$v->getExtension()]['minSize'] = $v->getSize();
        }
    }
    
    private function getMaxSize(object $v): void {
        if ($this->directories[$v->getDirectory()][$v->getExtension()]['maxSize'] > $v->getSize()) {
            $this->directories[$v->getDirectory()][$v->getExtension()]['maxSize'] = $v->getSize();
        }
    }
    
    private function fillExtensionsArray(object $v) {
        
    }
    
    /**
     * data => 
     *  directories =>
     *      'directory name' =>
     *          "extension" => 
     *              'txt' => 
     *                  'sumsize' => sum: 1947,
     *                  'fileCount' => fileCount: 3,
     *                  'minSize' => min: 640,
     *                  'maxSize' => man: 654    
     *          dirSumSize => 
     *              sumSize: 36649
     *          dirSumFiles => 
     *              sumFileCount : 8
     * 
     *  'extensions' =>
     *      'txt' =>
     *          'sumSize' :2601
     *          'fileCount' => fileCount: 3,
     *          'min' => min: 640,
     *          'max' => man: 654
     * 
     *  'summary' =>
     *      'overallFileCount : 9,
     *      'overallFileSize' : 366377
     * 
     */

}
