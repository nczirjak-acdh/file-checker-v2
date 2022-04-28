<?php

namespace OEAW\Report;
/**
 * Description of FileErrorList
 *
 * @author nczirjak
 */
class FileErrorListReport {

    private $list = [];
    private $file;
    
    public function __construct(array $list, string $file) {
        $this->list = $list;
        $this->file = $file;
    }
    
    public function __toJson() {
        $result = array();
        foreach($this->list as $k => $v) {
            echo '<pre>';
            var_dump($v);
            echo '</pre>';
            $result[] = array("erroryType" => $v['errorType'], "dir" => $v['dir'], "filename" => $v['filename']);
        } 
        $jsonData = array("data" => $result);
        $json = json_encode($jsonData, JSON_UNESCAPED_SLASHES);
        file_put_contents($this->file, $json);
    }

}
