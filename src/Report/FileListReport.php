<?php

namespace OEAW\Report;
/**
 * Description of FileExtensionList
 *
 * @author nczirjak
 */
class FileListReport {

    private $list = [];
    private $file;
    
    public function __construct(array $list, string $file) {
        $this->list = $list;
        $this->file = $file;
    }
    
    public function __toJson() {
        $result = array();
        foreach($this->list as $k => $v) {
          
            $result[] = array(
                "name" => $v->getFilenameAndDir(),
                "directory" => $v->getDirectory(),
                "type" => $v->getType(),
                "size" => $v->getSize(),
                "lastmod" => $v->getLastmodification(),
                "valid_file" => $v->getValid(),
                "filename" => $v->getFilename(),
                "extension" => $v->getExtension() 
            );
        } 
        $jsonData = array("data" => $result);
        $json = json_encode($jsonData, JSON_UNESCAPED_SLASHES);
        file_put_contents($this->file, $json);
    }

   

}
