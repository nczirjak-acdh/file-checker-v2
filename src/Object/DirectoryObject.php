<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace OEAW\Object;

/**
 * Description of DirectoryListObject
 *
 * @author nczirjak
 */
class DirectoryObject {
    
    private $name;
    private $valid = false;
    private $lastmodified;
    private $checkDir;
    
    public function __construct(object $obj, string $checkDir) {
        $this->name = $obj->getPathName();
        $this->checkDir = $checkDir;
        $this->valid = $this->checkDirectoryNameValidity();
        $this->lastmodified = gmdate("Y-m-d\TH:i:s\Z", $obj->getMTime());
    }
    public function getName() {
        return $this->name;
    }

    public function getValid() {
        return $this->valid;
    }

    public function getLastmodified() {
        return $this->lastmodified;
    }
    
    public function toJsonFile(): array {
        return array("name" => $this->getName(), "valid" => $this->getValid(), "lastmodified" => $this->getLastmodified());
    }
    
     /**
     * 
     * Checks the Directory Name validation
     * 
     * @param string $dir
     * @return bool
     */
    private function checkDirectoryNameValidity(): bool {
        //remove the main dir from the dir url
        if (preg_match("#^(?:[a-zA-Z]:|\.\.?)?(?:[\\\/][a-zA-Z0-9_.\'\"-]*)+$#", str_replace($this->checkDir, "", $this->name)) !== 1) {
            return false;
        } else {
            return true;
        }
    }
}
