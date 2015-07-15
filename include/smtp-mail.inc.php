<?php

/************************************************************
* FILENAME:    smtp-mail.inc.php
* DESCRIPTION: This script contains a simple class to send
*              SMTP e-mails for alerts.
* AUTHOR:      Patrick K. Ryon (Slashdoom)
* LICENSE:     BSD 3-clause (see LICENSE file)
************************************************************/

class smtpmail {

  function smtpconfig($smtpserver,$smtpport,$smtpuser,$smtppass,$from,$to,$subject,$body,$attachment) {

    // Define mail variables
    $this->smtpserver=$smtpserver;
    $this->random_hash = md5(date('r', time()));
    if (strlen($smtpport) > 0) { $this->smtpport = 25; }
    else { $this->smtpport=$smtpport; }
    echo $smtpuser;
    if (strlen($smtpuser) > 0) { $this->smtpuser = base64_encode($smtpuser); }
    else { $this->smtpuser = ''; }
    if (strlen($smtppass) > 0) { $this->smtppass = base64_encode($smtppass); }
    else { $this->smtppass = ''; }
    $this->from = $from;
    $this->to = $to;
    $this->subject = $subject;
    $this->body = $body;
    if (strlen($attachment) > 0) { 
      $this->attachment  = "--PHP-mixed-".$this->random_hash."\r\n".
      $this->attachment .= $attachment;
    }
    else { $this->attachment = ''; }

  }

  function smtpsend() {

    // Open smtp connection
    $smtpconn = fsockopen($this->smtpserver,$this->smtpport);

    if($smtpconn) {

      $smtpret["server"]=fgets($smtpconn,1024);

      // Send hello
      fputs($smtpconn,"HELO ".$this->smtpserver."\r\n");
      $smtpret["hello"]=fgets($smtpconn,1024);


      // Check for authentication
      if(strlen($this->smtpuser) > 0) {

        // Send auth login
        fputs($smtpconn,"auth login\r\n");
        $smtpret["auth"]=fgets($smtpconn,1024);

        // Send user
        fputs($smtpconn,$this->smtpuser."\r\n");
        $smtpret["user"]=fgets($smtpconn,1024);

        // Send password
        fputs($smtpconn,$this->smtppass."\r\n");
        $smtpret["pass"]=fgets($smtpconn,256);
      }

      // Send mail from info
      fputs($smtpconn,"MAIL FROM: <".$this->from.">\r\n");
      $smtpret["from"]=fgets($smtpconn,1024);

      // Send mail to info
      fputs($smtpconn,"RCPT TO: <".$this->to.">\r\n");
      $smtpret["to"]=fgets($smtpconn,1024);

      // Send data
      fputs($smtpconn,"DATA\r\n");
      $smtpret["data"]=fgets($smtpconn,1024);

      // Send message info
      fputs($smtpconn,"To: <".$this->to.">\r\n".
                      "From: <".$this->from.">\r\n".
                      "Subject: ".$this->subject."\r\n".
                      "MIME-Version: 1.0"."\r\n".
                      'Content-Type: multipart/mixed; boundary = "PHP-mixed-'.$this->random_hash.'"\r\n'.
                      "--PHP-mixed-".$this->random_hash."\r\n".
                      "Content-Type: text/plain; charset = 'ISO-8859-1'"."\r\n".
                      "\r\n\r\n".$this->body."\r\n".
                      $this->attachment."\r\n".
                      "--PHP-mixed-".$this->random_hash."--\r\n"."\r\n.\r\n");
      $smtpret["mail"]=fgets($smtpconn,256);

      // Close connection
      fputs($smtpconn,"QUIT\r\n");
      fclose($smtpconn);

      return $smtpret;

    }

  }

}
