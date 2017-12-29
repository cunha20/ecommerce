<?php

namespace Cunha20;

use Rain\Tpl;

class GDVMailer {

   const USERNAME = "<YOUR@EMAIL.COM>";
   const PASSWORD = "<PASSWORD>";
   const NAMEFROM = "<NAMEFROM>";

   private $oMail;

   public function __construct($xToAddr, $xToName, $xSubject, $xTplName, $xData = array()) {

      $aConfig = array(
          "tpl_dir" => $_SERVER["DOCUMENT_ROOT"] . "/views/email/",
          "cache_dir" => $_SERVER["DOCUMENT_ROOT"] . "/views-cache/"
      );
      Tpl::configure($aConfig);
      $oTpl = new Tpl;
      foreach ($xData as $key => $value):
         $oTpl->assign($key, $value);
      endforeach;
      $cHtml = $oTpl->draw($xTplName, true);

      //Create a new PHPMailer instance
      $this->oMail = new \PHPMailer;
      //Tell PHPMailer to use SMTP
      $this->oMail->isSMTP();
      //Enable SMTP debugging
      // 0 = off (for production use)
      // 1 = client messages
      // 2 = client and server messages
      $this->oMail->SMTPDebug = 0;
      //Ask for HTML-friendly debug output
      $this->oMail->Debugoutput = "html";
      //Set the hostname of the mail server
      $this->oMail->Host = "<smtp.domain.com>";
      //Set the SMTP port number - likely to be 25, 465 or 587
      $this->oMail->Port = 587;
      //Set the encryption system to use - ssl (deprecated) or tls
      $this->oMail->SMTPSecure = false;
      //Whether to use SMTP authentication
      $this->oMail->SMTPAuth = true;
      //Username to use for SMTP authentication
      $this->oMail->Username = GDVMailer::USERNAME;
      //Password to use for SMTP authentication
      $this->oMail->Password = GDVMailer::PASSWORD;
      //Set who the message is to be sent from
      $this->oMail->setFrom(GDVMailer::USERNAME, GDVMailer::NAMEFROM);
      //Set who the message is to be sent to
      $this->oMail->addAddress($xToAddr, $xToName);
      //Set the subject line
      $this->oMail->Subject = $xSubject;
      //Read an HTML message body from an external file, convert referenced images to embedded,
      //convert HTML into a basic plain-text alternative body
      $this->oMail->msgHTML($cHtml);
      //SMTP Options
      $this->oMail->SMTPOptions = array(
          'ssl' => array(
              'verify_peer' => false,
              'verify_peer_name' => false,
              'allow_self_signed' => true
      ));
   }

   public function send() {
      return $this->oMail->send();
   }

}
