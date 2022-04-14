<?php

namespace OEAW\Helper;

/**
 * Description of DirectoryChecks
 *
 * @author nczirjak
 */
class DirectoryChecks {
    
    public function start(string $actualDirectory, object $fileObj, string $checkDir): \OEAW\Object\DirectoryListObject {
        return new \OEAW\Object\DirectoryListObject($actualDirectory, $this->checkDirectoryNameValidity($actualDirectory, $checkDir), gmdate("Y-m-d\TH:i:s\Z", $fileObj->getMTime()));
    }
    
    /**
     * 
     * Checks the Directory Name validation
     * 
     * @param string $dir
     * @return bool
     */
    public function checkDirectoryNameValidity(string $actualDir, string $mainDirectory): bool {
        //remove the main dir from the dir url
        if (preg_match("#^(?:[a-zA-Z]:|\.\.?)?(?:[\\\/][a-zA-Z0-9_.\'\"-]*)+$#", str_replace($mainDirectory, "", $actualDir)) !== 1) {
            return false;
        } else {
            return true;
        }
    }
}
