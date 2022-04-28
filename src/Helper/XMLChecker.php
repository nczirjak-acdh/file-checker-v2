<?php

namespace OEAW\Helper;

/**
 * Description of XMLChecker
 *
 * @author nczirjak
 */
class XMLChecker {
    
    private $xmlFile;
    private $xmlDir;
    private $xml;
    private $xsdFile;
    private $error = [];
    
    public function __construct(string $xmlLocation, string $xmlDir) {
        $this->xmlFile = $xmlLocation;
        $this->xmlDir = $xmlDir;
        $this->createXML();
    }
    
    private function createXML() {
        $this->xml = new \DOMDocument();
        $this->xml->load($this->xmlFile);
        $this->getXSDFromXML();
    }
    
    private function getXSDFromXML(): void {
        $this->xsdFile = $this->xml->documentElement->getAttributeNS($this->xml->lookupNamespaceURI('xsi'), 'schemaLocation');
        
        if ((strpos($this->xsdFile, "http://") !== false) || (strpos($this->xsdFile, "https://") !== false)) {
            //online xsd
            
        }else {
            //localhost xsd
            $this->xsdFile = $this->xmlDir.$this->xsdFile;
        } 
    }
    
    
    
    public function validateXML(): bool {
       
        libxml_use_internal_errors(true);     
        if ($this->xml->schemaValidate($this->xsdFile)) {
            return true;
        } else {
            $errors = libxml_get_errors();
            foreach ($errors as $error) {
                $this->error = array("errorType" => "XML File Invalid: ".$error->message.' line:'.$error->line.' column:'.$error->column,  "errorCode" => 0, "filename" => $this->xmlFile, "dir" => $this->xmlDir);
            }
            libxml_clear_errors();
        }
        libxml_use_internal_errors(false); 
         
        return false;
    }
    
    public function getErrors(): array {
        return $this->error;
    }
}
