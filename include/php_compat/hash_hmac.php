<?php
//(hash) - enabled by default as of PHP 5.1.2
function hash_hmac($algo, $data, $key, $raw_output=false)
{
  /* md5 and sha1 only */
  $algo=strtolower($algo);
  $p=array('md5'=>'H32','sha1'=>'H40');
  if ( !isset($p[$algo]) or !function_exists($algo) )
  {
    $algo = 'md5';
  }
  if(strlen($key)>64) $key=pack($p[$algo],$algo($key));
  if(strlen($key)<64) $key=str_pad($key,64,chr(0));

  $ipad=substr($key,0,64) ^ str_repeat(chr(0x36),64);
  $opad=substr($key,0,64) ^ str_repeat(chr(0x5C),64);

  $ret = $algo($opad.pack($p[$algo],$algo($ipad.$data)));
  if ($raw_output)
  {
    $ret = pack('H*', $ret);
  }
  return $ret;
}
?>