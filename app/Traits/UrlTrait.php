<?php

namespace App\Traits;

trait UrlTrait
{
  /**
   * encrypt_decrypt
   *
   * @param  mixed $data La informacion a cifrar o descifrar
   * @param  mixed $action Cifrar o descifrar segun sea la accion deseada
   */
  public function encrypt_decrypt($data, $action)
  {
    // Los valores de key e iv son estaticos para elc aso de uso actual
    $cypherMethod = 'AES-128-CBC';
    $key = '$2y$10$qp10BTVgArdjJa0KhIB7zO4GMEAScL13bqDUUyC515jO0/xPpTare';
    $iv = 8257874035789431;

    if ($action == 'cifrar') {
      return openssl_encrypt($data, $cypherMethod, $key, $options = 0, $iv);
    } else if ($action == 'descifrar') {
      return openssl_decrypt($data, $cypherMethod, $key, $options = 0, $iv);
    }
    // No deberia llegar a retornar lo mismo
    return $data;
  }
}
