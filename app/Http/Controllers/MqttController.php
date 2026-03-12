<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpMqtt\Client\Facades\MQTT;

class MqttController extends Controller
{
    public function startIA($name)
    {
        $ias = [
            'yolov5'     => 'run:yolov5',
            'yolobestpt' => 'run:yolobestpt',
            'RTKfinal'   => 'run:RTKfinal',
        ];

        if (!isset($ias[$name])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'IA inconnue',
            ]);
        }

        MQTT::publish('raspberry/cmd', $ias[$name]);

        return response()->json([
            'status'  => 'ok',
            'message' => "IA $name lancee",
        ]);
    }

    public function stopIA($name)
    {
        $ias = [
            'yolov5'     => 'stop:yolov5',
            'yolobestpt' => 'stop:yolobestpt',
            'RTKfinal'   => 'stop:RTKfinal',
        ];

        if (!isset($ias[$name])) {
            return response()->json([
                'status'  => 'error',
                'message' => 'IA inconnue',
            ]);
        }

        MQTT::publish('raspberry/cmd', $ias[$name]);

        return response()->json([
            'status'  => 'ok',
            'message' => "IA $name arretee",
        ]);
    }
}
