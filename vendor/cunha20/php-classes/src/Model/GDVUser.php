<?php

namespace Cunha20\Model;

use Cunha20\DB\Sql;
use Cunha20\GDVModel;
use Cunha20\GDVMailer;

class GDVUser extends GDVModel {

   const SESSION = "User";
   const SECRET = "GoldenView@13011";

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
         throw new \Exception("Usuário inexistente ou senha inválida0.");
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

   public static function listAll() {
      $oSql = new Sql();
      return $oSql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");
   }

   public function save() {
      $oSql = new Sql;
      $aResults = $oSql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
          ":desperson" => $this->getdesperson(),
          ":deslogin" => $this->getdeslogin(),
          ":despassword" => $this->getdespassword(),
          ":desemail" => $this->getdesemail(),
          ":nrphone" => $this->getnrphone(),
          ":inadmin" => $this->getinadmin()
      ));
      $this->setData($aResults[0]);
   }

   public function get($xIdUser) {
      $oSql = new Sql();
      $aResults = $oSql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :IDUSER", array(
          ":IDUSER" => $xIdUser
      ));
      $this->setData($aResults[0]);
   }

   public function update() {
      $oSql = new Sql;
      $aResults = $oSql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
          ":iduser" => $this->getiduser(),
          ":desperson" => $this->getdesperson(),
          ":deslogin" => $this->getdeslogin(),
          ":despassword" => $this->getdespassword(),
          ":desemail" => $this->getdesemail(),
          ":nrphone" => $this->getnrphone(),
          ":inadmin" => $this->getinadmin()
      ));
      $this->setData($aResults[0]);
   }

   public function delete() {
      $oSql = new Sql;
      $oSql->select("CALL sp_users_delete(:iduser)", array(
          ":iduser" => $this->getiduser()
      ));
   }

   public static function getForgot($xEmail) {
      $oSql = new Sql();
      $aRes1 = $oSql->select("SELECT * FROM tb_persons a INNER JOIN tb_users b USING(idperson) WHERE a.desemail = :XEMAIL", array(
          ":XEMAIL" => $xEmail
      ));
      if (count($aRes1) === 0):
         throw new \Exception("Não foi possível recurperar a senha1.");
      else:
         $aDat1 = $aRes1[0];
         $aRes2 = $oSql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
             ":iduser" => $aDat1["iduser"],
             ":desip" => $_SERVER["REMOTE_ADDR"]
         ));
         if (count($aRes2) === 0):
            throw new \Exception("Não foi possível recurperar a senha2.");
         else:
            $aDat2 = $aRes2[0];
            $cCode = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, GDVUser::SECRET, $aDat2["idrecovery"], MCRYPT_MODE_ECB));
            $cLink = "http://www.cunha20ecommerce.com.br/admin/forgot/reset?code={$cCode}";
            $oMail = new GDVMailer($aDat1["desemail"], $aDat1["desperson"], "Redefinir Senha da Cunha20 - Store", "forgot", array(
                "name" => $aDat1["desperson"],
                "link" => $cLink
            ));
            $oMail->send();
            return $aDat1;
         endif;
      endif;
   }

   public static function validForgotDecrypt($xCode) {
      $nIdRecovery = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, GDVUser::SECRET, base64_decode($xCode), MCRYPT_MODE_ECB);
      $cQryTmp = "SELECT * FROM tb_userspasswordsrecoveries a ";
      $cQryTmp .= "INNER JOIN tb_users b USING(iduser) ";
      $cQryTmp .= "INNER JOIN tb_persons c USING(idperson) ";
      $cQryTmp .= "WHERE a.idrecovery = :XIDRECOVERY ";
      $cQryTmp .= "AND a.dtrecovery IS NULL ";
      $cQryTmp .= "AND DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW() ";
      $oSql = new Sql();
      $aRes1 = $oSql->select($cQryTmp, array(":XIDRECOVERY" => $nIdRecovery));
      if (count($aRes1) === 0):
         throw new \Exception("Não foi possível recuperar a senha3.");
      else:
         return $aRes1[0];
      endif;
   }

   public static function setForgotUsed($xIdRecovery) {
      $oSql = New Sql();
      $oSql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :XIDRECOVERY", array(
          ":XIDRECOVERY" => $xIdRecovery
      ));
   }

   public function setPassword($xPassword) {
      $oSql = new Sql();
      $oSql->query("UPDATE tb_users SET despassword = :XPASSWORD WHERE iduser = :XIDUSER", array(
          ":XPASSWORD" => $xPassword,
          ":XIDUSER" => $this->getiduser()
      ));
   }

}
