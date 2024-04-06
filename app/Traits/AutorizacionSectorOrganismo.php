<?php

namespace App\Traits;

trait AutorizacionSectorOrganismo
{
  public function AutorizacionSectorOrganismo($sector, $organismo)
  {
      if ($sector == $organismo) {
        return true;
      }else{
        return false;
      }
  }
}
