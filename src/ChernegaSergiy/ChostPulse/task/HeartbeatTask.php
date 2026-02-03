<?php

declare(strict_types=1);

namespace ChernegaSergiy\ChostPulse\task;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class HeartbeatTask extends AsyncTask {
    
    private string $url;
    private string $payload;
    
    public function __construct(string $url, array $data) {
        $this->url = $url;
        $this->payload = json_encode($data);
    }
    
    public function onRun(): void {
        $ch = curl_init($this->url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $this->payload,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'ChostPulse-PMMP/1.0.0'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        $this->setResult([
            "success" => $httpCode === 200,
            "code" => $httpCode,
            "response" => $response,
            "error" => $error
        ]);
    }
    
    public function onCompletion(): void {
        $result = $this->getResult();
        
        if (!$result["success"]) {
            $logger = Server::getInstance()->getLogger();
            $logger->warning("ChostPulse: Heartbeat failed with HTTP {$result['code']}");
            
            if (!empty($result["error"])) {
                $logger->warning("ChostPulse: cURL error: {$result['error']}");
            }
        }
    }
}
