<?php

namespace App\Http\Controllers;

use App\Services\MqttService;

class MqttController extends Controller
{
    public function startIA($name)
    {
        $ias = [
            'yolov5'     => 'run:yolov5',
            'yolobestpt' => 'run:yolobestpt',
            'RTKfinal'   => 'run:RTKfinal',
			'yolobestpt2' => 'run:Yolobestpt2',
			'light'      => 'run:light',
        ];

        if (!isset($ias[$name])) {
            return response()->json(['status' => 'error', 'message' => 'IA inconnue']);
        }

        app(MqttService::class)->send('raspberry/cmd', $ias[$name]);

        return response()->json(['status' => 'ok', 'message' => "IA $name lancée"]);
    }

    public function stopIA($name)
    {
        $ias = [
            'yolov5'     => 'stop:yolov5',
            'yolobestpt' => 'stop:yolobestpt',
            'RTKfinal'   => 'stop:RTKfinal',
			'yolobestpt2' => 'stop:Yolobestpt2',
			'light'      => 'stop:light',
        ];

        if (!isset($ias[$name])) {
            return response()->json(['status' => 'error', 'message' => 'IA inconnue']);
        }

        app(MqttService::class)->send('raspberry/cmd', $ias[$name]);

        return response()->json(['status' => 'ok', 'message' => "IA $name arrêtée"]);
    }
}
