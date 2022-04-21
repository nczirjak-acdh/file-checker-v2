<?php

namespace OEAW\Helper;

/**
 * Description of DirectoryChecks
 *
 * @author nczirjak
 */
class DirectoryChecks {

    private $errors = array();

    public function checkValidDirectories(array $dirList): array {

        foreach ($dirList as $k => $v) {
            if ($v->getValid() === false) {
                $this->errors[] = array("errorType" => "Directory name is invalid", "filename" => $v->getName(), "dir" => $v->getName());
            }
        }
        return $this->errors;
    }

}
