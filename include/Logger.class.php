<?php
// +-----------------------------------------------------------------------+
// | Piwigo - a PHP based photo gallery                                    |
// +-----------------------------------------------------------------------+
// | Copyright(C) 2008-2016 Piwigo Team          http://piwigo.org |
// | Copyright(C) 2003-2008 PhpWebGallery Team  http://phpwebgallery.net |
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

/**
 * Modified version of KLogger 0.2.0
 *
 * @author  Kenny Katzgrau <katzgrau@gmail.com>
 *
 * @package logger
 */

class Logger
{
  /**
   * Error severity, from low to high. From BSD syslog RFC, section 4.1.1
   * @link http://www.faqs.org/rfcs/rfc3164.html
   */
  const EMERGENCY = 0;  // Emergency: system is unusable
  const ALERT     = 1;  // Alert: action must be taken immediately
  const CRITICAL  = 2;  // Critical: critical conditions
  const ERROR     = 3;  // Error: error conditions
  const WARNING   = 4;  // Warning: warning conditions
  const NOTICE    = 5;  // Notice: normal but significant condition
  const INFO      = 6;  // Informational: informational messages
  const DEBUG     = 7;  // Debug: debug messages

  /**
   * Custom "disable" level.
   */
  const OFF       = -1; // Log nothing at all

  /**
   * Internal status codes.
   */
  const STATUS_LOG_OPEN  = 1;
  const STATUS_OPEN_FAILED = 2;
  const STATUS_LOG_CLOSED  = 3;

  /**
   * Disable archive purge.
   */
  const ARCHIVE_NO_PURGE = -1;

  /**
   * Standard messages produced by the class.
   * @var array
   */
  private static $_messages = array(
    'writefail'   => 'The file could not be written to. Check that appropriate permissions have been set.',
    'opensuccess' => 'The log file was opened successfully.',
    'openfail'  => 'The file could not be opened. Check permissions.',
  );

  /**
   * Instance options.
   * @var array
   */
  private $options = array(
    'directory' => null, // Log files directory
    'filename' => null, // Path to the log file
    'globPattern' => 'log_*.txt', // Pattern to select all log files with glob()
    'severity' => self::DEBUG, // Current minimum logging threshold
    'dateFormat' => 'Y-m-d G:i:s', // Date format
    'archiveDays' => self::ARCHIVE_NO_PURGE, // Number of files to keep
    );

  /**
   * Current status of the logger.
   * @var integer
   */
  private $_logStatus = self::STATUS_LOG_CLOSED;
  /**
   * File handle for this instance's log file.
   * @var resource
   */
  private $_fileHandle = null;


  /**
   * Class constructor.
   *
   * @param array $options
   * @return void
   */
  public function __construct($options)
  {
    $this->options = array_merge($this->options, $options);
    
    if (is_string($this->options['severity']))
    {
      $this->options['severity'] = self::codeToLevel($this->options['severity']);
    }

    if ($this->options['severity'] === self::OFF)
    {
      return;
    }

    $this->options['directory'] = rtrim($this->options['directory'], '\\/') . DIRECTORY_SEPARATOR;

    if ($this->options['filename'] == null)
    {
      $this->options['filename'] = 'log_' . date('Y-m-d') . '.txt';
    }

    $this->options['filePath'] = $this->options['directory'] . $this->options['filename'];

    if ($this->options['archiveDays'] != self::ARCHIVE_NO_PURGE && rand() % 97 == 0)
    {
      $this->purge();
    }
  }
  
  /**
   * Open the log file if not already oppenned
   */
  private function open()
  {
    if ($this->status() == self::STATUS_LOG_CLOSED)
    {
      if (!file_exists($this->options['directory']))
      {
        mkgetdir($this->options['directory'], MKGETDIR_DEFAULT|MKGETDIR_PROTECT_HTACCESS);
      }

      if (file_exists($this->options['filePath']) && !is_writable($this->options['filePath']))
      {
        $this->_logStatus = self::STATUS_OPEN_FAILED;
        throw new RuntimeException(self::$_messages['writefail']);
        return;
      }

      if (($this->_fileHandle = fopen($this->options['filePath'], 'a')) != false)
      {
        $this->_logStatus = self::STATUS_LOG_OPEN;
      }
      else
      {
        $this->_logStatus = self::STATUS_OPEN_FAILED;
        throw new RuntimeException(self::$_messages['openfail']);
      }
    }
  }

  /**
   * Class destructor.
   */
  public function __destruct()
  {
    if ($this->_fileHandle)
    {
      fclose($this->_fileHandle);
    }
  }

  /**
   * Returns logger status.
   *
   * @return int
   */
  public function status()
  {
    return $this->_logStatus;
  }

  /**
   * Returns logger severity threshold.
   *
   * @return int
   */
  public function severity()
  {
    return $this->options['severity'];
  }

  /**
   * Writes a $line to the log with a severity level of DEBUG.
   *
   * @param string $line
   * @param string $cat
   * @param array $args
   */
  public function debug($line, $cat = null, $args = array())
  {
    $this->log(self::DEBUG, $line, $cat, $args);
  }

  /**
   * Writes a $line to the log with a severity level of INFO.
   *
   * @param string $line
   * @param string $cat
   * @param array $args
   */
  public function info($line, $cat = null, $args = array())
  {
    $this->log(self::INFO, $line, $cat, $args);
  }

  /**
   * Writes a $line to the log with a severity level of NOTICE.
   *
   * @param string $line
   * @param string $cat
   * @param array $args
   */
  public function notice($line, $cat = null, $args = array())
  {
    $this->log(self::NOTICE, $line, $cat, $args);
  }

  /**
   * Writes a $line to the log with a severity level of WARNING.
   *
   * @param string $line
   * @param string $cat
   * @param array $args
   */
  public function warn($line, $cat = null, $args = array())
  {
    $this->log(self::WARNING, $line, $cat, $args);
  }

  /**
   * Writes a $line to the log with a severity level of ERROR.
   *
   * @param string $line
   * @param string $cat
   * @param array $args
   */
  public function error($line, $cat = null, $args = array())
  {
    $this->log(self::ERROR, $line, $cat, $args);
  }

  /**
   * Writes a $line to the log with a severity level of ALERT.
   *
   * @param string $line
   * @param string $cat
   * @param array $args
   */
  public function alert($line, $cat = null, $args = array())
  {
    $this->log(self::ALERT, $line, $cat, $args);
  }

  /**
   * Writes a $line to the log with a severity level of CRITICAL.
   *
   * @param string $line
   * @param string $cat
   * @param array $args
   */
  public function critical($line, $cat = null, $args = array())
  {
    $this->log(self::CRITICAL, $line, $cat, $args);
  }

  /**
   * Writes a $line to the log with a severity level of EMERGENCY.
   *
   * @param string $line
   * @param string $cat
   * @param array $args
   */
  public function emergency($line, $cat = null, $args = array())
  {
    $this->log(self::EMERGENCY, $line, $cat, $args);
  }

  /**
   * Writes a $line to the log with the given severity.
   *
   * @param integer $severity
   * @param string $line
   * @param string $cat
   * @param array $args
   */
  public function log($severity, $message, $cat = null, $args = array())
  {
    if ($this->severity() >= $severity)
    {
      if (is_array($cat))
      {
        $args = $cat;
        $cat = null;
      }
      $line = $this->formatMessage($severity, $message, $cat, $args);
      $this->write($line);
    }
  }

  /**
   * Directly writes a line to the log without adding level and time.
   *
   * @param string $line
   */
  public function write($line)
  {
    $this->open();
    if ($this->status() == self::STATUS_LOG_OPEN)
    {
      if (fwrite($this->_fileHandle, $line) === false)
      {
        throw new RuntimeException(self::$_messages['writefail']);
      }
    }
  }

  /**
   * Purges files matching 'globPattern' older than 'archiveDays'.
   */
  public function purge()
  {
    $files = glob($this->options['directory'] . $this->options['globPattern']);
    $limit = time() - $this->options['archiveDays'] * 86400;

    foreach ($files as $file)
    {
      if (@filemtime($file) < $limit)
      {
        @unlink($file);
      }
    }
  }

  /**
   * Formats the message for logging.
   *
   * @param  string $level
   * @param  string $message
   * @param  array  $context
   * @return string
   */
  private function formatMessage($level, $message, $cat, $context)
  {
    if (!empty($context))
    {
      $message.= "\n" . $this->indent($this->contextToString($context));
    }
    $line = "[" . $this->getTimestamp() . "]\t[" . self::levelToCode($level) . "]\t";
    if ($cat != null)
    {
      $line.= "[" . $cat . "]\t";
    }
    return $line . $message . "\n";
  }

  /**
   * Gets the formatted Date/Time for the log entry.
   *
   * PHP DateTime is dumb, and you have to resort to trickery to get microseconds
   * to work correctly, so here it is.
   *
   * @return string
   */
  private function getTimestamp()
  {
    $originalTime = microtime(true);
    $micro = sprintf('%06d', ($originalTime - floor($originalTime)) * 1000000);
    $date = new DateTime(date('Y-m-d H:i:s.'.$micro, $originalTime));
    return $date->format($this->options['dateFormat']);
  }

  /**
   * Takes the given context and converts it to a string.
   *
   * @param  array $context
   * @return string
   */
  private function contextToString($context)
  {
    $export = '';
    foreach ($context as $key => $value)
    {
      $export.= $key . ': ';
      $export.= preg_replace(array(
        '/=>\s+([a-zA-Z])/im',
        '/array\(\s+\)/im',
        '/^  |\G  /m'
        ),
        array(
        '=> $1',
        'array()',
        '  '
        ),
        str_replace('array (', 'array(', var_export($value, true))
        );
      $export.= PHP_EOL;
    }
    return str_replace(array('\\\\', '\\\''), array('\\', '\''), rtrim($export));
  }

  /**
   * Indents the given string with the given indent.
   *
   * @param  string $string The string to indent
   * @param  string $indent What to use as the indent.
   * @return string
   */
  private function indent($string, $indent = '  ')
  {
    return $indent . str_replace("\n", "\n" . $indent, $string);
  }

  /**
   * Converts level constants to string name.
   *
   * @param int $level
   * @return string
   */
  static function levelToCode($level)
  {
    switch ($level)
    {
      case self::EMERGENCY:
        return 'EMERGENCY';
      case self::ALERT:
        return 'ALERT';
      case self::CRITICAL:
        return 'CRITICAL';
      case self::NOTICE:
        return 'NOTICE';
      case self::INFO:
        return 'INFO';
      case self::WARNING:
        return 'WARNING';
      case self::DEBUG:
        return 'DEBUG';
      case self::ERROR:
        return 'ERROR';
      default:
        throw new RuntimeException('Unknown severity level ' . $level);
    }
  }

  /**
   * Converts level names to constant.
   *
   * @param string $code
   * @return int
   */
  static function codeToLevel($code)
  {
    switch (strtoupper($code))
    {
      case 'EMERGENCY':
        return self::EMERGENCY;
      case 'ALERT':
        return self::ALERT;
      case 'CRITICAL':
        return self::CRITICAL;
      case 'NOTICE':
        return self::NOTICE;
      case 'INFO':
        return self::INFO;
      case 'WARNING':
        return self::WARNING;
      case 'DEBUG':
        return self::DEBUG;
      case 'ERROR':
        return self::ERROR;
      default:
        throw new RuntimeException('Unknown severity code ' . $code);
    }
  }
}
?>