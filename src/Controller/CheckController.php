<?php

namespace OEAW\Controller;

/**
 * Description of CheckController
 *
 * @author nczirjak
 */
class CheckController {
    
    private $settings;
    private $helper;
    
    public function __construct(array $args) {
        $this->settings = new \OEAW\Object\SettingsObject($args);
        $this->helper = new \OEAW\Helper\Helper();
    }
    
    public function start() {
       
        if(!$this->settings->isReportDirReadable() || !$this->settings->isTmpDirReadable()) {
            throw new \Exception('Report Dir / Temp Dir does not exists! Please check your settings in the config.ini file');
        }
        
        //import mimeTypes
        $this->settings->setMimeTypes($this->helper->getMimeFromPronom($this->settings->getSignatureDir()));
        
        //create the reportDir for the actual report
        $this->settings->createReportDirForActualReport();
        
        $this->runChecks();
        
        
    }
    
    private function runChecks() {
        $checks = new \OEAW\Helper\Checks();
        $checks->start($this->settings);       
    }
    
           
    
}
