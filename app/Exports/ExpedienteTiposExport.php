<?php

namespace App\Exports;

use App\Expedientestipo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Illuminate\Support\Facades\DB;

class ExpedienteTiposExport implements FromCollection, WithHeadings, WithColumnWidths
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if ($this->data['fecha_desde'] !== NULL && $this->data['fecha_hasta'] !== NULL)
        {
            return DB::table('expedientes')
                ->select('expedientes.expediente_num', 'expedientes.expediente', 'expedientestipos.expedientestipo', DB::raw('date_format(expedientes.fecha_inicio, "%d-%m-%Y")'))
                ->join('expedientestipos', 'expedientes.expedientestipos_id', '=', 'expedientestipos.id')
                ->where('expedientes.organismos_id', $this->data['organismos_id'])
                ->where('expedientestipos.id', $this->data['tipo'])
                ->whereBetween('expedientes.created_at', [$this->data['fecha_desde'] .' 00:00:00', $this->data['fecha_hasta'] .' 23:59:59'])
                ->get();
        }
        elseif ($this->data['anio'] !== NULL)
        {
            return DB::table('expedientes')
                ->select('expedientes.expediente_num', 'expedientes.expediente', 'expedientestipos.expedientestipo', DB::raw('date_format(expedientes.fecha_inicio, "%d-%m-%Y")'))
                ->join('expedientestipos', 'expedientes.expedientestipos_id', '=', 'expedientestipos.id')
                ->where('expedientes.organismos_id', $this->data['organismos_id'])
                ->where('expedientestipos.id', $this->data['tipo'])
                ->whereYear('expedientes.created_at', $this->data['anio'])
                ->get();
        }
    }

    public function headings(): array
    {
        return [
            'Nro documento',
            'Extracto',
            'Tipo documento',
            'Fecha Inicio'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 60,
            'C' => 30,
            'D' => 25         
        ];
    }
}
