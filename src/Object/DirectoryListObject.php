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
class DirectoryListObject {
    
    private $name;
    private $valid = false;
    private $lastmodified;
    
    public function __construct(string $name, bool $valid, string $lastmodified) {
        $this->name = $name;
        $this->valid = $valid;
        $this->lastmodified = $lastmodified;
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
}
