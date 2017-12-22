<?php

require_once("vendor/autoload.php");

use Slim\Slim;
use Cunha20\GDVPageAdmin;

$oApp = new Slim();

$oApp->config('debug', true);

$oApp->get('/', function() {
   $oGDVPage = new GDVPage();
   $oGDVPage->setTpl("index");
});

$oApp->get('/admin', function() {
   $oGDVPageAdmin = new GDVPageAdmin();
   $oGDVPageAdmin->setTpl("index");
});

$oApp->run();
