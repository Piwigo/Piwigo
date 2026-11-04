<?php
// +-----------------------------------------------------------------------+
// | This file is part of Piwigo.                                          |
// |                                                                       |
// | For copyright and license information, please view the COPYING.txt    |
// | file that was distributed with this source code.                      |
// +-----------------------------------------------------------------------+

require_once __DIR__ . '/../include/Logger.class.php';

$directory = sys_get_temp_dir() . '/piwigo-logger-test-' . uniqid();
if (!mkdir($directory))
{
  throw new RuntimeException('Could not create the test directory.');
}

$log_file = $directory . '/log.txt';

try
{
  if (file_put_contents($log_file, 'existing log') === false || !chmod($log_file, 0444))
  {
    throw new RuntimeException('Could not make the log file read-only.');
  }

  if (is_writable($log_file))
  {
    throw new RuntimeException('The test environment does not honor read-only file permissions.');
  }

  $logger = new Logger(array(
    'directory' => $directory,
    'filename' => 'log.txt',
  ));

  $logger->info('The first write must not fail the request.');
  if ($logger->status() !== Logger::STATUS_OPEN_FAILED)
  {
    throw new RuntimeException('Logger did not record the failed open.');
  }

  $logger->info('Later writes must also be ignored after the open failure.');
}
finally
{
  if (file_exists($log_file))
  {
    chmod($log_file, 0644);
    unlink($log_file);
  }
  rmdir($directory);
}

echo "Logger write failure test passed.\n";
