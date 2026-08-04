<?php
// see https://php.watch/versions/8.4/session_set_save_handler-alt-signature-deprecated
class PwgSession implements SessionHandlerInterface {
  public function open(string $path, string $name): bool
  {
    return pwg_session_open($path, $name);
  }

  public function close(): bool
  {
    return pwg_session_close();
  }

  public function read(string $id): string
  {
    return pwg_session_read($id);
  }

  public function write(string $id, string $data): bool
  {
    return pwg_session_write($id, $data);
  }

  public function destroy(string $id): bool
  {
    return pwg_session_destroy($id);
  }

  public function gc(int $max_lifetime): int
  {
    return pwg_session_gc();
  }
}