<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Expediente extends Model
{
  protected $table = 'expedientes';

  protected $fillable = [
    'expediente', //  VARCHAR (125)
    'expedientestipos_id', // INT
    'organismos_id', // INT
    'expediente_num', // INT
    'fecha_inicio', // INT
    'importe', // decimal(10,2)
    'usuario_inicio', // INT
    'sector_inicio' // INT
  ];
  public function organismos()
  {
    return $this->belongsTo(Organismo::class, 'organismos_id');
  }

  public function expedientetipo()
  {
    return $this->belongsTo(Expedientestipo::class, 'expedientestipos_id');
  }

  public function expedientesestados()
  {
    return $this->hasMany(Expedienteestado::class, 'expedientes_id');
  }

  public function organismosetiquetas()
  {
    return $this->belongsToMany(Organismosetiqueta::class);
  }

  public function expedienteRequisitos()
  {
    return $this->belongsToMany(Expedientesrutasrequisitos::class, 'expediente_expedientesrequisito','expedientes_id','expedientesrequisito_id')->withPivot('estado')->withTimestamps();
  }

  //Recursividad Documentos
  public function documentosVinculados()
  {
    return $this->belongsToMany(
      Expediente::class,
      'expediente_expediente',
      'expediente_id1',
      'expediente_id2'
    )->withPivot('tipo');
  }

  public function documentosEnlazados()
  {
    return $this->belongsToMany(
      Expediente::class,
      'expediente_expediente',
      'expediente_id1',
      'expediente_id2'
    )->wherePivot('tipo',"Enlace");
  }

  public function documentosFusionados()
  {
    return $this->belongsToMany(
      Expediente::class,
      'expediente_expediente',
      'expediente_id1',
      'expediente_id2'
    )->wherePivot('tipo',"Fusion");
  }


  /**
   * makeHashFromFile
   *
   * @param  string $filePath La ruta del archivo a calcular
   * @return String con algoritmo sha256
   */
  public function makeHashFromFile($filePath)
  {
    return hash_file("sha256", $filePath);
  }

  /**
   * makeHashFromText
   *
   * @param  mixed $plainText Un valor de tipo texto a calcular
   * @return hash con algoritmo sha256
   */
  public function makeHashFromText($plainText)
  {
    return hash("sha256", $plainText);
  }

  /**
   * compareFileHash
   * Compara el hash calculado anteriormente con el esatdoa ctual de un archivo
   *
   * @param  string $hashAnterior El hash calculado anteriormente
   * @param  string $currentFilePath La ruta actual del storage del archivo
   * @return void
   */
  public function compareFileHash($hashAnterior, $currentFilePath)
  {
    return hash_equals($hashAnterior, $this->makeHashFromFile($currentFilePath));
  }



  /**
   * comparePlainTextHash
   * Compara un texto plano con el valor anterior calculado de un texto
   * 
   * @param  mixed $hashAnterior
   * @param  mixed $currentText
   * @return void
   */
  public function comparePlainTextHash($hashAnterior, $currentText)
  {
    return hash_equals($hashAnterior, $this->makeHashFromText($currentText));
  }

  public function fojas()
  {
    return $this->hasMany(Foja::class, 'expedientes_id');
  }

  // Un expediente puede tener muchas personas vinculadas
  public function personas()
  {
    return $this->belongsToMany(Persona::class);
  }

  // Este metodo permite traer los datos de los tipos de vinculo de las personas asociadas a un expediente, agregando como pivot los id de tipo de vinculo y persona
  public function personasVinculo() {
    return $this->belongsToMany(Organismostiposvinculo::class, 'expediente_persona', 'expediente_id')->withPivot('organismostiposvinculo_id', 'persona_id');
  }

  public function adjuntos() {
    return $this->hasMany(Expedientesadjunto::class, 'expedientes_id');
  }

  // Global Query Scopes  
  /**
   * Consultar solo los expedientes del organismo actual
   *
   * @param  mixed $query
   * @param  mixed $organismo_id
   * @return void
   */
  public function scopeEsteorganismo($query)
  {
    $user = Auth::user();
    $organismo = $user->organismo;
    return $query->where('organismos_id', $organismo->id);
  }


  /**
   * Consultar solo los expedientes del organismo actual.
   * Excepto un array de uno o mas id de expedientes dados
   *  
   * @param  mixed $query
   * @param  mixed $array_expedientes_ids Un solo id o un array de ids de expedientes
   * @return void
   */
  public function scopeEsteorganismoExcepto($query, $array_expedientes_ids)
  {
    if (!is_array($array_expedientes_ids)) {
      $array_expedientes_ids = array($array_expedientes_ids);
    }
    $user = Auth::user();
    $organismo = $user->organismo;
    $resultado = $query->where('organismos_id', $organismo->id)->whereNotIn('id', $array_expedientes_ids);
    return $resultado;
  }
}
