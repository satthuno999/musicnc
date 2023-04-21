<?php
namespace OCA\musicnc\AppInfo;

$app = new Application();
$c = $app->getContainer();
$userManager = $c->getServer()->getUserManager();

$application->add(new \OCA\musicnc\Command\Scan(
	$userManager, 
	$c->query(\OCA\musicnc\Controller\ScannerController::class)
));

$application->add(new \OCA\musicnc\Command\Reset(
	$userManager,
    $c->query(\OCA\musicnc\Controller\DbController::class)
));

