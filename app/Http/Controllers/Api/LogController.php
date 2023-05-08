<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class LogController extends Controller
{
    use ApiResponseTrait;
    public function index()
    {
        try {
            $logPath = storage_path('logs/laravel.log');
            $logs = $this->parseLogs($logPath, 'WooCommerce Sync');

            if(empty($logs)){
                return $this->successResponse(['data'=>'No logs are found'],204);
            } 

            return $this->successResponse($logs);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
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