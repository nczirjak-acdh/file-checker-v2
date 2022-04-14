<?php

namespace OEAW;

require_once "./vendor/autoload.php";

use zozlak\argparse\ArgumentParser;

$parser = new ArgumentParser();
$parser->addArgument('--signatureDir', default: __DIR__ . '/signatures', help: "Directory containing the DROID_SignatureFile XML file (default: %(default)s)");
$parser->addArgument('--tmpDir', required: true);
$parser->addArgument('--reportDir', required: true);
$parser->addArgument('--blackList', nargs: ArgumentParser::NARGS_REQ, default: ['app', 'apk', 'cfg'], help: "Extenstions of files to be skipped (default: [%(default)s])");
$parser->addArgument('--pdfSize', type: ArgumentParser::TYPE_INT, default: 80000000, help: "Maximum PDF file size in bytes (default: %(default)s)");
$parser->addArgument('--zipSize', type: ArgumentParser::TYPE_INT, default: 100000000, help: "Maximum ZIP file size in bytes (default: %(default)s)");
$parser->addArgument('--checkDuplicates', choices: ['yes', 'no'], help: "Disable the duplicate file checks");
$parser->addArgument('directoryToCheck');
$parser->addArgument('outputMode', choices: [0, 1, 2], type: ArgumentParser::TYPE_INT, help:"0 -json output; 1 - Html output; 2 - NDJSON output;");
$args   = $parser->parseArgs();

$controller = new \OEAW\Controller\CheckController((array) $args);
try {
    $ret = $controller->start();
} catch (\Exception $ex) {
    exit($ex->getMessage());
}

//$ch = new CH((array) $args);
//$ret = $ch->startChecking($args->directoryToCheck, $args->outputMode);
exit($ret ? 0 : 2);
