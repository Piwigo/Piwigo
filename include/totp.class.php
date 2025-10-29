<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

require_once(PHPWG_ROOT_PATH . 'include/base32.class.php');
require_once(PHPWG_ROOT_PATH . 'include/phpqrcode.php');

class PwgTOTP
{

  /**
   * Generate a Base32 secret for TOTP
   *
   * @param string $secret Base32-encoded secret
   * @param int $timestamp 30s intervasl since 1970
   * @return string TOTP Code
   */
  private static function generateCodeFromTimestamp($secret, $timestamp)
  {
    $key = PwgBase32::decode($secret);

    $msg = pack('N*', 0) . pack('N*', $timestamp); // hash_hmac need this form
    $hash = hash_hmac('sha1', $msg, $key, true);

    // RFC 4226, section 5.3
    $offset = ord(substr($hash, -1)) & 0x0F;
    $part = substr($hash, $offset, 4);
    $number = unpack('N', $part)[1] & 0x7FFFFFFF;

    $code = $number % 1000000; // code 6 digits $number % 10^6
    return str_pad((string)$code, 6, '0', STR_PAD_LEFT); // 123 become 000123
  }

  /**
   * Generate a Base32 secret for TOTP
   *
   * @param int $length Length in bytes (default: 20)
   * @return string Base32-encoded secret
   */
  public static function generateSecret($length = 20)
  {
    $random = random_bytes($length);
    return PwgBase32::encode($random, false);
  }

  /**
   * Get Otp auth url
   *
   * @param string $secret Encoded base32 secret
   * @return string otpauth://totp/ url
   */
  public static function getOtpAuthUrl($secret)
  {
    global $user;
    $url = substr(get_absolute_root_url(), 0, -1);
    return 'otpauth://totp/'.$user['username'].':'.$url.'?secret='.$secret.'&issuer=Piwigo&algorithm=sha1&digits=6&period=30';
  }

  /**
   * Get Qr Code
   *
   * @param string $secret Encoded base32 secret
   * @return string data:image/png;base64..
   */
  public static function getQrCode($secret)
  {
    $otp_url = self::getOtpAuthUrl($secret);
    
    ob_start();
    QRcode::png($otp_url);    
    $qrcode_image = ob_get_clean();
    $base64_qrcode = base64_encode($qrcode_image);
    return 'data:image/png;base64,' . $base64_qrcode;
  }

  /**
   * Generate a TOTP Code
   *
   * @param string $secret Encoded base32 secret
   * @param int $timestamp timestamp used in second (default: 30)
   * @return string 6 digits TOTP code
   */
  public static function generateCode($secret, $timestamp = 30)
  {
    $timestamp = floor(time() / $timestamp); // e.g 58338889 > 30-second intervals since 1970 at the moment T
    return self::generateCodeFromTimestamp($secret, $timestamp);
  }

  /**
   * Verify TOTP Code
   *
   * @param string $code Digits 6 TOTP Code
   * @param string $secret Encoded base32 secret
   * @param int $timestamp timestamp used in second (default: 30)
   * @param int $check_interval Number of 30s steps to check before/after current (default: 1)
   * @return bool
   */
  public static function verifyCode($code, $secret, $timestamp = 30, $check_interval = 1)
  {
    $timestamp = floor(time() / $timestamp);

    // generate a totp code for 30s intervals
    // following or preceding the current one and check it
    for ($i=-$check_interval; $i <= $check_interval; $i++)
    {
      $interval_timestamp = $timestamp + $i;
      $generated_code = self::generateCodeFromTimestamp($secret, $interval_timestamp);
      if (hash_equals($generated_code, $code))
      {
        return true;
      }
    }

    return false;
  }
}
