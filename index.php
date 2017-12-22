<?php

session_start();
require_once("vendor/autoload.php");

use Slim\Slim;
use Cunha20\GDVPage;
use Cunha20\GDVPageAdmin;
use Cunha20\Model\GDVUser;

$oApp = new Slim();

$oApp->config('debug', true);

$oApp->get('/', function() {
   $oGDVPage = new GDVPage();
   $oGDVPage->setTpl("index");
});

$oApp->get('/admin', function() {
   GDVUser::verifyLogin();
   $oGDVPageAdmin = new GDVPageAdmin();
   $oGDVPageAdmin->setTpl("index");
});

$oApp->get('/admin/login', function() {
   $oGDVPageAdmin = new GDVPageAdmin([
       "header" => false,
       "footer" => false
   ]);
   $oGDVPageAdmin->setTpl("login");
});

$oApp->post('/admin/login', function() {
   GDVUser::login($_POST["login"], $_POST["password"]);
   header("Location: /admin");
   exit;
});

$oApp->get('/admin/logout', function() {
   GDVUser::logout();
   header("Location: /admin/login");
   exit;
});

$oApp->run();
