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
    private $reportHelper;
    private $result = array();
    
    public function __construct(array $args) {
        $this->settings = new \OEAW\Object\SettingsObject($args);
        $this->helper = new \OEAW\Helper\Helper();
        $this->reportHelper = new \OEAW\Helper\ReportFileHelper();        
    }
    
    public function start() {
       
        if(!$this->settings->isReportDirReadable() || !$this->settings->isTmpDirReadable()) {
            throw new \Exception('Report Dir / Temp Dir does not exists! Please check your settings in the config.ini file');
        }
        
        //import mimeTypes
        $this->settings->setMimeTypes($this->helper->getMimeFromPronom($this->settings->getSignatureDir()));
        
        //create the reportDir for the actual report
        $this->settings->createReportDirForActualReport();
        
        $this->result = $this->runChecks();
        
        if(count($this->result) == 0) {
            throw new \Exception('There are no files! runChecks failed!');
        }
        
        echo "\n Generating reports...\n";
        $this->createReports();
        echo "Check is done!";
    }
    
    private function runChecks(): array {
        $checks = new \OEAW\Helper\Checks();
        return $checks->start($this->settings);       
    }
    
    private function createReports() {
        return $this->reportHelper->createReportFiles($this->settings, $this->result);        
    }        
    
}
