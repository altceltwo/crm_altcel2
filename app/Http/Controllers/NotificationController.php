<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function getData(Request $request){
        $payload = $request['payload'];
        $content = $payload['content'];
        $eventType = $content['eventType'];
        $callback = $content['callback'];
        $event = $content['event'];
        
        switch ($eventType) {
            case "Band28":
                $id = $event['id'];
                $effectiveDate = $event['effectiveDate'];
                $detail = $event['detail'];
                $be = $detail['be'];
                $tagname = $detail['tagname'];
                $imiei = $detail['imiei'];
                $band28 = $detail['band28'];
                $unbarringdate = $detail['unbarringdate'];
                
                break;
            case "EVENT_UNITS":
                $id = $event['id'];
                $effectiveDate = $event['effectiveDate'];
                $detail = $event['detail'];
                $be = $detail['be'];
                $FreeUnitTypeName = $detail['FreeUnitTypeName'];
                $TotalAmount = $detail['TotalAmount'];
                $UnsedAmount = $detail['UnsedAmount'];
                $expireDate = $detail['expireDate'];
                $ThresholdValue = $detail['ThresholdValue'];
                $OfferingID = $detail['OfferingID'];

                break;
            case "NETWORK_FAILURES":
                $effectiveDate = $event['effectiveDate'];
                $detail = $event['detail'];
                $be = $detail['be'];
                $posiblesUsuariosAfectados = $detail['posiblesUsuariosAfectados'];
                $Ciudad = $detail['Ciudad'];
                $Estado = $detail['Estado'];
                $Municipio = $detail['Municipio'];
                $FechaHora = $detail['Fecha/Hora'];

                break;
            case  ($eventType == "SUSPEND_IMEI" || $eventType == "RESUME_IMEI"):
                $id = $event['id'];
                $effectiveDate = $event['effectiveDate'];
                $detail = $event['detail'];
                $be = $detail['BE'];
                $TagName = $detail['TagName'];
                $IMSI = $detail['IMSI'];
                $IMEI_ORIG = $detail['IMEI_ORIG'];
                $IMEI_DET = $detail['IMEI_DET'];
                $DetectionDate = $detail['DetectionDate'];
                $SuspendDate = $detail['SuspendDate'];
                $ResumeDate = $detail['ResumeDate'];

                break;
            case ($eventType == "SUSPEND_MOVILITY" || $eventType ==  "RESUME_MOVILITY"):
                $id = $event['id'];
                $effectiveDate = $event['effectiveDate'];
                $detail = $event['detail'];
                $be = $detail['BE'];
                $MovilityTypeName = $detail['MovilityTypeName'];
                $Home = $detail['Home'];
                $Final = $detail['Final'];
                $ResumeDate = $detail['ResumeDate'];
                $EnodeBHome = $detail['EnodeBHome'];
                $EnodeBFinal = $detail['EnodeBFinal'];

                break;
            case "ACTIVATION":
                $id = $event['id'];
                $effectiveDate = $event['effectiveDate'];
                $detail = $event['detail'];
                $be = $detail['be'];
                $offeringID = $detail['offeringID'];
                $notificationDate = $detail['notificationDate'];

                break;
            case "CAMBIOIMEI":
                $id = $event['id'];
                $detail = $event['detail'];
                $be = $detail['BE'];
                $imei = $detail['imei'];
                $msisdn = $detail['msisdn'];
                $Homologado = $detail['Homologado'];
                $notificationDate = $detail['notifica$notificationDate'];

                break;
        }
    }
}
