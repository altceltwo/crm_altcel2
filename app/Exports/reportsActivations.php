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
            'Status',
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

        $response = [];

        if ($type == 'general') {
            $data = DB::table('users')
                        ->join('activations','activations.client_id','=','users.id')
                        ->join('numbers','numbers.id','=','activations.numbers_id')
                        ->join('rates','rates.id','=','activations.rate_id')
                        ->leftJoin('devices','devices.id','=','activations.devices_id')
                        ->leftJoin('clients','clients.user_id','=','users.id')
                        ->whereBetween('activations.date_activation', [$dateStart, $dateEnd])
                        ->select('users.name AS name', 'users.lastname','clients.cellphone AS cellphone','numbers.MSISDN AS MSISDN','devices.no_serie_imei AS imei','numbers.icc_id AS icc','rates.name AS rate_name','numbers.producto AS service','numbers.traffic_outbound_incoming AS status_one','numbers.traffic_outbound AS status_two','numbers.status_altan AS status_three','rates.price_subsequent AS amount_rate','activations.amount_device AS amount_device','activations.date_activation AS date_activation')
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
                    ->select('users.name AS name', 'users.lastname','clients.cellphone AS cellphone','numbers.MSISDN AS MSISDN','devices.no_serie_imei AS imei','numbers.icc_id AS icc','rates.name AS rate_name','numbers.producto AS service','numbers.traffic_outbound_incoming AS status_one','numbers.traffic_outbound AS status_two','numbers.status_altan AS status_three','rates.price_subsequent AS amount_rate','activations.amount_device AS amount_device','activations.date_activation AS date_activation')
                    ->get(); 
        }

        foreach ($data as $row) {
            $status = '';
            $service = $row->service;
            $service = trim($service);
            if($row->status_three == 'activo'){
                if($service == 'MIFI' || $service == 'HBB'){
                    $status = $row->status_one;
                }else if($service == 'MOV'){
                    $status = $row->status_two;
                }
            }else{
                $status = $row->status_three;
            }
            array_push($response,array(
                'name' => $row->name,
                'lastname' => $row->lastname,
                'cellphone' => $row->cellphone,
                'msisdn' => $row->MSISDN,
                'imei' => $row->imei,
                'icc' => $row->icc,
                'rate_name' => $row->rate_name,
                'service' => $row->service,
                'status' => $status,
                'amount_rate' => $row->amount_rate,
                'amount_device' => $row->amount_device,
                'date_activation' => $row->date_activation,
            ));
        }
        return collect($response);
    }
}
