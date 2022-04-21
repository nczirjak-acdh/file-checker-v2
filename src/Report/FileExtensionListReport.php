<?php

namespace OEAW\Report;
/**
 * Description of FileExtensionList
 *
 * @author nczirjak
 */
class FileExtensionListReport {

    private $fileExtensions = [];
    private $list = [];
    private $file;
    
    public function __construct(array $list, string $file) {
        $this->list = $list;
        $this->file = $file;
        $this->createFileExtensionListData();
    }
    
    public function __toJson() {
        $result = array();
        foreach($this->fileExtensions as $k => $v) {
          
            $result[] = array("text" => $k, "children" => 
                array(
                    array("icon" => "jstree-file", "text" => "SumSize: ".$v['sumSize']),
                    array("icon" => "jstree-file", "text" => "fileCount: ".$v['fileCount']." file(s)"),
                    array("icon" => "jstree-file", "text" => "MinSize: ".$v['minSize']),
                    array("icon" => "jstree-file", "text" => "MaxSize: ".$v['maxSize'])
                )
            );
        } 
        //$jsonData = array("data" => $result);
        $json = json_encode($result, JSON_UNESCAPED_SLASHES);
        file_put_contents($this->file, $json);
    }

    public function createFileExtensionListData(): array {

        foreach ($this->list as $k => $v) {
            if (!isset($this->fileExtensions[$v->getExtension()])) {
                $this->fileExtensions[$v->getExtension()]['fileCount'] = 1;
                $this->fileExtensions[$v->getExtension()]['sumSize'] = $v->getSize();
                $this->fileExtensions[$v->getExtension()]['minSize'] = $v->getSize();
                $this->fileExtensions[$v->getExtension()]['maxSize'] = $v->getSize();
            } else {
                $this->fileExtensions[$v->getExtension()]['fileCount'] += 1;
                $this->fileExtensions[$v->getExtension()]['sumSize'] += $v->getSize();
                $this->getMinSize($v);
                $this->getMaxSize($v);
            }
        }

        return $this->fileExtensions;
    }
    
    private function getMinSize(object $v): void {
        if ($this->fileExtensions[$v->getExtension()]['minSize'] > $v->getSize()) {
            $this->fileExtensions[$v->getExtension()]['minSize'] = $v->getSize();
        }
    }
    
    private function getMaxSize(object $v): void {
        if ($this->fileExtensions[$v->getExtension()]['maxSize'] > $v->getSize()) {
            $this->fileExtensions[$v->getExtension()]['maxSize'] = $v->getSize();
        }
    }

}
