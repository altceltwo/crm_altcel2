<?php

namespace App\Exports;

use App\User;
use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportsActivations implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    public $info;
    public function __construct($data)
    {
        $this->info = $data;
    }

    public function styles(Worksheet $sheet){
        return [
            1 => ['font' => ['bold' => true,
                             'italic' => true
                             ]
                ]
        ];
    }

    public function headings(): array{
        return [
            'Nombre',
            'Apellido',
            'Contacto',
            'MSISDN',
            'IMEI',
            'ICC',
            'Plan/Paquete',
            'Servicio',
            'Plan',
            'CPE',
            'Fecha de Activación'
        ];
    }

    public function collection()
    {
        $start = $this->info['start_date'];
        $end = $this->info['end_date'];
        $type = $this->info['type'];
        $año = substr($start, -4);
        $mes = substr($start, 0,2);
        $dia = substr($start, 3, -5);
        $dateStart = $año. '-'. $mes.'-'.$dia;
        $añoEnd = substr($end, -4);
        $mesEnd = substr($end, 0,2);
        $diaEnd = substr($end, 3, -5);
        $dateEnd = $añoEnd. '-'. $mesEnd.'-'.$diaEnd;

        if ($type == 'general') {
            $data = DB::table('users')
                        ->join('activations','activations.client_id','=','users.id')
                        ->join('numbers','numbers.id','=','activations.numbers_id')
                        ->join('rates','rates.id','=','activations.rate_id')
                        ->leftJoin('devices','devices.id','=','activations.devices_id')
                        ->leftJoin('clients','clients.user_id','=','users.id')
                        ->whereBetween('activations.date_activation', [$dateStart, $dateEnd])
                        ->select('users.name AS name', 'users.lastname','clients.cellphone AS cellphone','numbers.MSISDN AS MSISDN','devices.no_serie_imei AS imei','numbers.icc_id AS icc','rates.name AS rate_name','numbers.producto AS service','rates.price_subsequent AS amount_rate','activations.amount_device AS amount_device','activations.date_activation AS date_activation')
                        ->get();            
        }else{
            $data = DB::table('users')
                    ->join('activations','activations.client_id','=','users.id')
                    ->join('numbers','numbers.id','=','activations.numbers_id')
                    ->join('rates','rates.id','=','activations.rate_id')
                    ->leftJoin('devices','devices.id','=','activations.devices_id')
                    ->leftJoin('clients','clients.user_id','=','users.id')
                    ->where('numbers.producto', 'like','%'.$type.'%')
                    ->whereBetween('activations.date_activation', [$dateStart, $dateEnd])
                    ->select('users.name AS name', 'users.lastname','clients.cellphone AS cellphone','numbers.MSISDN AS MSISDN','devices.no_serie_imei AS imei','numbers.icc_id AS icc','rates.name AS rate_name','numbers.producto AS service','rates.price_subsequent AS amount_rate','activations.amount_device AS amount_device','activations.date_activation AS date_activation')
                    ->get(); 
        }
        return collect($data);
    }
}
