<?php

namespace Cunha20;

use \Cunha20\GDVPage;

class GDVPageAdmin extends GDVPage {

   public function __construct($xOptions = array(), $xTplDir = "/views/admin/") {
      parent::__construct($xOptions, $xTplDir);
   }

}
