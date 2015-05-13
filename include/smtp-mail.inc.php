<?php

class smtpmail {

  function smtpconfig($smtpserver,$smtpport,$smtpuser,$smtppass,$from,$to,$subject,$body) {

    // Define mail variables
    $this->smtpserver=$smtpserver;
    if (strlen($smtpport) > 0) { $this->smtpport=25; }
    else { $this->smtpport=$smtpport; }
    echo $smtpuser;
    if (strlen($smtpuser) > 0) { $this->smtpuser=base64_encode($smtpuser); }
    else { $this->smtpuser = ''; }
    if (strlen($smtppass) > 0) { $this->smtppass=base64_encode($smtppass); }
    else { $this->smtppass = ''; }
    $this->from=$from;
    $this->to=$to;
    $this->subject=$subject;
    $this->body=$body;

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
      fputs($smtpconn,"To: <".$this->to.">\r\nFrom: <".$this->from.">\r\nSubject:".$this->subject."\r\n\r\n\r\n".$this->body."\r\n.\r\n");
      $smtpret["mail"]=fgets($smtpconn,256);

      // Close connection
      fputs($smtpconn,"QUIT\r\n");
      fclose($smtpconn);

      return $smtpret;

    }

  }

}
