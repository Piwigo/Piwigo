<?php
// see https://php.watch/versions/8.4/session_set_save_handler-alt-signature-deprecated
// https://github.com/Piwigo/Piwigo/issues/2296
class PwgSession implements SessionHandlerInterface {
  public function open($path, $name)
  {
    return pwg_session_open($path, $name);
  }

  public function close()
  {
    return pwg_session_close();
  }

  public function read($id)
  {
    return pwg_session_read($id);
  }

  public function write($id, $data)
  {
    return pwg_session_write($id, $data);
  }

  public function destroy($id)
  {
    return pwg_session_destroy($id);
  }

  public function gc($max_lifetime)
  {
    return pwg_session_gc();
  }
}