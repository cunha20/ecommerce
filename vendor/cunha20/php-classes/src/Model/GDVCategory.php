<?php

namespace Cunha20\Model;

use Cunha20\DB\Sql;
use Cunha20\GDVModel;
use Cunha20\GDVMailer;

class GDVCategory extends GDVModel {

   public static function listAll() {
      $oSql = new Sql();
      return $oSql->select("SELECT * FROM tb_categories a ORDER BY a.descategory");
   }

   public function save() {
      $oSql = new Sql;
      $aResults = $oSql->select("CALL sp_categories_save(:idcategory, :descategory)", array(
          ":idcategory" => $this->getidcategory(),
          ":descategory" => $this->getdescategory()
      ));
      $this->setData($aResults[0]);
      GDVCategory::updateFile();
   }

   public function get($xIdCategory) {
      $oSql = new Sql();
      $aResults = $oSql->select("SELECT * FROM tb_categories a WHERE a.idcategory = :XIDCATEGORY", array(
          ":XIDCATEGORY" => $xIdCategory
      ));
      $this->setData($aResults[0]);
   }

   public function delete() {
      $oSql = new Sql;
      $oSql->query("DELETE FROM tb_categories WHERE idcategory = :XIDCATEGORY", array(
          ":XIDCATEGORY" => $this->getidcategory()
      ));
      GDVCategory::updateFile();
   }
   
   public static function updateFile() {
      $aCategories = GDVCategory::listAll();
      $aHtml = [];
      foreach ($aCategories as $row):
         array_push($aHtml, "<li><a href='/categories/{$row['idcategory']}'>{$row['descategory']}</a></li>");
      endforeach;
      file_put_contents($_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "views" .DIRECTORY_SEPARATOR . "categories-menu.html", implode($aHtml));
   }

}
