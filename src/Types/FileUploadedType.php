<?php

namespace Atusan\Types;

class FileUploadedType
{
  public string $errorMsg;

  function __construct(public int $error, public string $name, public string $type, public string $size, public string $tmp_name)
  {
    $this->errorMsg = $this->errorMsgCollection[$this->error];
  }

  private array $errorMsgCollection = [
    UPLOAD_ERR_OK => 'El archivo se ha subido correctamente.',
    UPLOAD_ERR_INI_SIZE => 'El archivo subido excede la directiva UPLOAD_MAX_FILESIZE en php.ini.',
    UPLOAD_ERR_FORM_SIZE => 'El archivo subido excede directiva MAX_FILE_SIZE del formulario.',
    UPLOAD_ERR_PARTIAL => 'El archivo solo se ha subido parcialmente.',
    UPLOAD_ERR_NO_FILE => 'No se ha subido ningun archivo.',
    5 => 'Error desconocido (5).',
    UPLOAD_ERR_NO_TMP_DIR => 'Directorio temporal no encontrado.',
    UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo en disco.',
    UPLOAD_ERR_EXTENSION => 'Extensión de PHP detuvo la subida del archivo.'
  ];
}
