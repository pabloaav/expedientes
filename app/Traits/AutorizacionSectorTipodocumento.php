<?php

namespace App\Traits;

trait AutorizacionSectorTipodocumento
{
    public function AutorizacionSectorTipodocumento($sector, $tipodocumento)
    {
        if ($sector == $tipodocumento) {
            return true;
        }else{
            return false;
        }
    }
}
