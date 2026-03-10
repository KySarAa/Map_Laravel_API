<?php

namespace App\Http\Controllers;

use App\Services\MqttService;

class MqttController extends Controller
{
    public function start(MqttService $mqtt)
    {
        $mqtt->send('raspberry/cmd', 'run:RTKfinal');

        return response()->json([
            'status' => 'ok',
            'message' => 'Commande envoyee'
        ]);
    }
}
