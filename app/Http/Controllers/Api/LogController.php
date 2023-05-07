<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        $logPath = storage_path('logs/laravel.log');
        $logs = $this->parseLogs($logPath, 'WooCommerce Sync');

        return response()->json(['logs' => $logs]);
    }

    private function parseLogs($logPath, $keyword)
    {
        $logs = [];

        if (file_exists($logPath)) {
            $file = fopen($logPath, 'r');

            while (!feof($file)) {
                $line = fgets($file);

                if (strpos($line, $keyword) !== false) {
                    $logs[] = $line;
                }
            }

            fclose($file);
        }

        return $logs;
    }
}