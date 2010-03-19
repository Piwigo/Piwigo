<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based picture gallery                                  |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2010 Piwigo Team                  http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team    http://phpwebgallery.net |
// | Copyright(C) 2002-2003 Pierrick LE GALL   http://le-gall.net/pierrick |
// +-----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify  |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation                                          |
// |                                                                       |
// | This program is distributed in the hope that it will be useful, but   |
// | WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU      |
// | General Public License for more details.                              |
// |                                                                       |
// | You should have received a copy of the GNU General Public License     |
// | along with this program; if not, write to the Free Software           |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, |
// | USA.                                                                  |
// +-----------------------------------------------------------------------+

// These function were originally a part of the punBB.

class smtp_mail
{
  var $socket;
  var $no_error;
  var $host;
  var $user;
  var $password;
  var $email_webmaster;

  function smtp_mail($host, $user, $password, $email_webmaster)
  {
    $this->host = $host;
    $this->user = $user;
    $this->password = $password;
    $this->email_webmaster = $email_webmaster;
  }

  // Adaptation of server_parse
  function server_parse($expected_response)
  {
    if ($this->no_error)
    {
      $server_response = '';
      while (substr($server_response, 3, 1) != ' ')
      {
        if (!($server_response = fgets($this->socket, 256)))
        {
          trigger_error('Couldn\'t get mail server response codes.', E_USER_WARNING);
          $this->no_error = false;
        }
      }
    }

    if ($this->no_error)
    {
      if (!(substr($server_response, 0, 3) == $expected_response))
      {
        trigger_error('Unable to send e-mail. Error message reported by the SMTP server: "'.$server_response.'"', E_USER_WARNING);
        $this->no_error = false;
      }
    }
    return $this->no_error;
  }

  function server_write($s)
  {
    $this->no_error = $this->no_error && (fwrite($this->socket, $s) !== false);
    return $this->no_error;
  }

  function add_recipients(&$recipients, $headers, $type_header)
  {
    if (preg_match('/^\s*'.$type_header.'\s*:.*/mi', $headers, $matches) != 0)
    {
      $list = explode(',', $matches[0]);
      foreach ($list as $email)
      {
        if (strpos($email, '<') !== false)
        {
           $email = preg_replace('/.*<(.*)>.*/i', '$1', $email);
        }
        $recipients[] = trim($email);
      }
    }
  }

  // Adaptation of pun_mail
  function mail($to, $subject, $message, $headers = '')
  {
    $this->no_error = true;

    // Are we using port 25 or a custom port?
    if (strpos($this->host, ':') !== false)
    {
      list($smtp_host, $smtp_port) = explode(':', $this->host);
    }
    else
    {
      $smtp_host = $this->host;
      $smtp_port = 25;
    }

    if ($this->socket = fsockopen($smtp_host, $smtp_port, $errno, $errstr, 15))
    {
      $this->server_parse('220');

      if (!empty($this->user) && !empty($this->password))
      {
        $this->server_write('EHLO '.$smtp_host."\r\n");
        $this->server_parse('250');

        $this->server_write('AUTH LOGIN'."\r\n");
        $this->server_parse('334');

        $this->server_write(base64_encode($this->user)."\r\n");
        $this->no_error = $this->no_error && $this->no_error = $this->server_parse('334');

        $this->server_write(base64_encode($this->password)."\r\n");
        $this->server_parse('235');
      }
      else
      {
        $this->server_write('HELO '.$smtp_host."\r\n");
        $this->server_parse('250');
      }

      $this->server_write('MAIL FROM:<'.$this->email_webmaster.'>'."\r\n");
      $this->server_parse('250');

      // Add "To:" on headers if there are included
      if ((preg_match('/^\s*to\s*:.*/mi', $headers) === 0) and !empty($to))
      {
        $to_header = 'To:'.implode(',', array_map(create_function('$email','return "<".$email.">";'), explode(',', $to)));
      }
      else
      {
        $to_header = '';
      }

      if (!empty($to))
      {
        $recipients = explode(',', $to);
      }
      else
      {
        $recipients = array();
      }

      $this->add_recipients($recipients, $headers, 'Cc');
      $this->add_recipients($recipients, $headers, 'Bcc');

      @reset($recipients);
      while (list(, $email) = @each($recipients))
      {
        $this->server_write('RCPT TO:<'.$email.'>'."\r\n");
        $this->server_parse('250');
      }

      $this->server_write('DATA'."\r\n");
      $this->server_parse('354');

      $this->server_write('Subject:'.$subject."\r\n".(empty($to_header) ? "" : $to_header."\r\n").$headers."\r\n\r\n".$message."\r\n");
      $this->server_write('.'."\r\n");
      $this->server_parse('250');

      $this->server_write('QUIT'."\r\n");
      fclose($this->socket);
    }
    else
    {
      trigger_error('Could not connect to smtp host "'.$this->host.'" ('.$errno.') ('.$errstr.')', E_USER_WARNING);
      $this->no_error = false;;
    }

    return $this->no_error;
  }
}

?>
