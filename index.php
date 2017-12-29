<?php

session_start();
require_once("vendor/autoload.php");

use Slim\Slim;
use Cunha20\GDVPage;
use Cunha20\GDVPageAdmin;
use Cunha20\Model\GDVUser;
use Cunha20\Model\GDVCategory;

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
   $cPstLogin = trim(strip_tags($_POST["login"]));
   $cPstPass = trim(strip_tags($_POST["password"]));
   GDVUser::login($cPstLogin,$cPstPass);
   header("Location: /admin");
   exit;
});

$oApp->get('/admin/logout', function() {
   GDVUser::logout();
   header("Location: /admin/login");
   exit;
});

$oApp->get('/admin/users/create', function() {
   GDVUser::verifyLogin();
   $oGDVPageAdmin = new GDVPageAdmin();
   $oGDVPageAdmin->setTpl("users-create");
});

$oApp->post("/admin/users/create", function() {
   GDVUser::verifyLogin();
   $oUser = new GDVUser();
   $_POST["inadmin"] = (isset($_POST["inadmin"]) ? 1 : 0);
   $oUser->setData($_POST);
   $oUser->save();
   header("Location: /admin/users");
   exit;
});

$oApp->get("/admin/users/:iduser/delete", function($xIdUser) {
   GDVUser::verifyLogin();
   $oUser = new GDVUser();
   $oUser->get((int) $xIdUser);
   $oUser->delete();
   header("Location: /admin/users");
   exit;
});

$oApp->get('/admin/users/:iduser', function($xIdUser) {
   GDVUser::verifyLogin();
   $oUser = new GDVUser();
   $oUser->get((int) $xIdUser);
   $oGDVPageAdmin = new GDVPageAdmin();
   $oGDVPageAdmin->setTpl("users-update", array(
       "user" => $oUser->getData()
   ));
});

$oApp->post("/admin/users/:iduser", function($xIdUser) {
   GDVUser::verifyLogin();
   $oUser = new GDVUser();
   $oUser->get((int) $xIdUser);
   $_POST["inadmin"] = (isset($_POST["inadmin"]) ? 1 : 0);
   $oUser->setData($_POST);
   $oUser->update();
   header("Location: /admin/users");
   exit;
});

$oApp->get('/admin/users', function() {
   GDVUser::verifyLogin();
   $aUsers = GDVUser::listAll();
   $oGDVPageAdmin = new GDVPageAdmin();
   $oGDVPageAdmin->setTpl("users", array(
       "users" => $aUsers
   ));
});

$oApp->get('/admin/forgot', function() {
   $oGDVPageAdmin = new GDVPageAdmin([
       "header" => false,
       "footer" => false
   ]);
   $oGDVPageAdmin->setTpl("forgot");
});

$oApp->post('/admin/forgot', function() {
   $cPstEmail = trim(strip_tags($_POST['email']));
   GDVUser::getForgot($cPstEmail);
   header("Location: /admin/forgot/sent");
   exit;
});

$oApp->get("/admin/forgot/sent", function() {
   $oGDVPageAdmin = new GDVPageAdmin([
       "header" => false,
       "footer" => false
   ]);
   $oGDVPageAdmin->setTpl("forgot-sent");
});

$oApp->get("/admin/forgot/reset", function () {
   $cGetCode = trim(strip_tags($_GET["code"]));
   $aUser = GDVUser::validForgotDecrypt($cGetCode);
   $oGDVPageAdmin = new GDVPageAdmin([
       "header" => false,
       "footer" => false
   ]);
   $oGDVPageAdmin->setTpl("forgot-reset", array(
       "name" => $aUser["desperson"],
       "code" => $cGetCode
   ));
});

$oApp->post("/admin/forgot/reset", function () {
   $cPstCode = trim(strip_tags($_POST["code"]));
   $cPstPass = trim(strip_tags($_POST["password"]));
   $aUser = GDVUser::validForgotDecrypt($cPstCode);
   GDVUser::setForgotUsed($aUser["idrecovery"]);
   $oUser = new GDVUser();
   $oUser->get($aUser["iduser"]);
   $cPass = password_hash($cPstPass, PASSWORD_DEFAULT, ["cost" => 14]);
   $oUser->setPassword($cPass);
   $oGDVPageAdmin = new GDVPageAdmin([
       "header" => false,
       "footer" => false
   ]);
   $oGDVPageAdmin->setTpl("forgot-reset-success");
});

$oApp->get("/admin/categories", function() {
   GDVUser::verifyLogin();
   $aCategories = GDVCategory::listAll();
   $oGDVPageAdmin = new GDVPageAdmin();
   $oGDVPageAdmin->setTpl("categories", array(
       "categories" => $aCategories
   ));   
});

$oApp->get("/admin/categories/create", function() {
   GDVUser::verifyLogin();
   $oGDVPageAdmin = new GDVPageAdmin();
   $oGDVPageAdmin->setTpl("categories-create");   
});

$oApp->post("/admin/categories/create", function() {
   GDVUser::verifyLogin();
   $oCategory = new GDVCategory();
   $oCategory->setData($_POST);
   $oCategory->save();
   header("Location: /admin/categories");
   exit;
});

$oApp->get("/admin/categories/:idcategory/delete", function($xIdCategory) {
   GDVUser::verifyLogin();
   $oCategory = new GDVCategory();
   $oCategory->get((int)$xIdCategory);
   $oCategory->delete();
   header("Location: /admin/categories");
   exit;
});

$oApp->get("/admin/categories/:idcategory", function($xIdCategory) {
   GDVUser::verifyLogin();
   $oCategory = new GDVCategory();
   $oCategory->get((int)$xIdCategory);
   $oGDVPageAdmin = new GDVPageAdmin();
   $oGDVPageAdmin->setTpl("categories-update", array(
       "category" => $oCategory->getData()
   ));   
});

$oApp->post("/admin/categories/:idcategory", function($xIdCategory) {
   GDVUser::verifyLogin();
   $oCategory = new GDVCategory();
   $oCategory->get((int)$xIdCategory);
   $oCategory->setData($_POST);
   $oCategory->save();
   header("Location: /admin/categories");
   exit;
});

$oApp->get("/categories/:idcategory", function($xIdCategory) {
   $oCategory = new GDVCategory();
   $oCategory->get((int)$xIdCategory);
   $oGDVPage = new GDVPage();
   $oGDVPage->setTpl("category", array(
       "category" => $oCategory->getData(),
       "products" => []
   ));   
});

$oApp->run();
