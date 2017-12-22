<?php

namespace Cunha20;

use Rain\Tpl;

class GDVPage {

   private $oTpl;
   private $aOptions;
   private $aDefaults = [
       "header" => true,
       "footer" => true,
       "data" => [],
   ];

   private function setData($xData = array()) {
      foreach ($xData as $key->$value):
         $this->oTpl->assign($key, $value);
      endforeach;
   }

   public function __construct($xOptions = array(), $xTplDir = "/views/") {
      $this->aOptions = array_merge($this->aDefaults, $xOptions);
      $aConfig = array(
          "tpl_dir" => $_SERVER["DOCUMENT_ROOT"] . $xTplDir,
          "cache_dir" => $_SERVER["DOCUMENT_ROOT"] . "/views-cache/"
      );
      Tpl::configure($aConfig);
      $this->oTpl = new Tpl;
      $this->setData($this->aOptions["data"]);
      if ($this->aOptions["header"]):
         $this->oTpl->draw("header");
      endif;
   }

   public function setTpl($xName, $xData = array(), $xHtml = false) {
      $this->setData($xData);
      $this->oTpl->draw($xName, $xHtml);
   }

   public function __destruct() {
      if ($this->aOptions["footer"]):
         $this->oTpl->draw("footer");
      endif;
   }

}
