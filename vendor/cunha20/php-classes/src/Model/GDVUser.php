<?php

namespace Cunha20\Model;

use \Cunha20\DB\Sql;
use \Cunha20\GDVModel;

class GDVUser extends GDVModel {

   const SESSION = "User";

   public static function login($xLogin, $xPassword) {
      $oSql = new Sql;
      $aResults = $oSql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
          ":LOGIN" => $xLogin
      ));
      if (count($aResults) === 0):
         throw new \Exception("Usuário inexistente ou senha inválida.");
      endif;
      $aData = $aResults[0];
      if (password_verify($xPassword, $aData["despassword"]) === true):
         $oUser = new GDVUser();
         $oUser->setData($aData);
         $_SESSION[GDVUser::SESSION] = $oUser->getData();
         return $oUser;
      else:
         throw new \Exception("Usuário inexistente ou senha inválida.");
      endif;
   }

   public static function logout() {
      $_SESSION[GDVUser::SESSION] = null;
   }

   public static function verifyLogin($xInAdmin = true) {
      if (!isset($_SESSION[GDVUser::SESSION]) || empty($_SESSION[GDVUser::SESSION]) || empty($_SESSION[GDVUser::SESSION]["iduser"])):
         header("Location: /admin/login");
         exit;
      endif;
   }

}
