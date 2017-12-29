<?php

namespace Cunha20;

class GDVModel {

   private $aData = [];

   public function __call($xName, $xArgs) {
      $cMethod = substr($xName, 0, 3);
      $cFieldName = substr($xName, 3, strlen($xName));
      switch ($cMethod):
         case "get":
            return (isset($this->aData[$cFieldName]) ? $this->aData[$cFieldName] : NULL);
         case "set":
            $this->aData[$cFieldName] = $xArgs[0];
            break;
      endswitch;
   }

   public function setData($xData = array()) {
      foreach ($xData as $key => $value):
         $this->{"set" . $key}($value);
      endforeach;
   }

   public function getData() {
      return $this->aData;
   }

}
