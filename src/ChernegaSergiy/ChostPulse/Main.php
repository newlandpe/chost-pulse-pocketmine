<?php

declare(strict_types=1);

namespace ChernegaSergiy\ChostPulse;

use ChernegaSergiy\ChostPulse\api\BadgeUrlGenerator;
use ChernegaSergiy\ChostPulse\api\HeartbeatClient;
use ChernegaSergiy\ChostPulse\security\KeyValidator;
use ChernegaSergiy\ChostPulse\security\TokenGenerator;
use ChernegaSergiy\ChostPulse\task\HeartbeatTask;
use ChernegaSergiy\ChostPulse\task\StatsCollector;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;

class Main extends PluginBase {
    
    private string $secretToken;
    private string $publicId;
    private HeartbeatClient $client;
    
    public function onEnable(): void {
        $this->saveDefaultConfig();
        
        // Initialize security tokens
        if (!$this->getConfig()->exists("token") || empty($this->getConfig()->get("token", ""))) {
            $this->initializeTokens();
        } else {
            $this->loadTokens();
        }
        
        // Initialize HTTP client
        $apiUrl = $this->getConfig()->get("api_url", "https://your-domain.com/api/heartbeat");
        $this->client = new HeartbeatClient($apiUrl);
        
        // Schedule heartbeat task
        $this->scheduleHeartbeat();
        
        // Display badge URLs on startup
        $this->displayBadgeUrls();
        
        $this->getLogger()->info("ChostPulse enabled successfully!");
    }
    
    private function initializeTokens(): void {
        $generator = new TokenGenerator();
        $this->secretToken = $generator->generateSecretKey();
        $this->publicId = $generator->derivePublicId($this->secretToken);
        
        $this->getConfig()->set("token", $this->secretToken);
        $this->getConfig()->save();
        
        $this->getLogger()->notice("Generated new secret token: " . $this->secretToken);
        $this->getLogger()->notice("Public ID: " . $this->publicId);
        $this->getLogger()->notice("Keep your secret token private! Use the Public ID in your badge URLs.");
    }
    
    private function loadTokens(): void {
        $this->secretToken = $this->getConfig()->get("token", "");
        
        if (!KeyValidator::isValidSecretToken($this->secretToken)) {
            $this->getLogger()->warning("Invalid secret token format in config! Generating new token...");
            $this->initializeTokens();
            return;
        }
        
        $generator = new TokenGenerator();
        $this->publicId = $generator->derivePublicId($this->secretToken);
        
        if ($this->getConfig()->get("debug", false)) {
            $this->getLogger()->info("Loaded token. Public ID: " . $this->publicId);
        }
    }
    
    private function scheduleHeartbeat(): void {
        $interval = $this->getConfig()->get("interval", 60) * 20; // Convert to ticks
        
        $this->getScheduler()->scheduleRepeatingTask(
            new ClosureTask(function(): void {
                $this->sendHeartbeat();
            }),
            $interval
        );
        
        // Send first heartbeat after 5 seconds
        $this->getScheduler()->scheduleDelayedTask(
            new ClosureTask(function(): void {
                $this->sendHeartbeat();
            }),
            100 // 5 seconds
        );
    }
    
    private function sendHeartbeat(): void {
        $data = StatsCollector::collect($this->getServer(), $this->getConfig());
        
        $task = new HeartbeatTask(
            $this->client->getUrl(),
            [
                "token" => $this->secretToken,
                "data" => $data
            ]
        );
        
        $this->getServer()->getAsyncPool()->submitTask($task);
        
        if ($this->getConfig()->get("debug", false)) {
            $this->getLogger()->info("Heartbeat sent: " . json_encode($data));
        }
    }
    
    private function displayBadgeUrls(): void {
        $baseUrl = "https://your-domain.com/api/badge";
        $generator = new BadgeUrlGenerator($baseUrl, $this->publicId);
        
        $this->getLogger()->info("Your Badge URLs:");
        $this->getLogger()->info("Status:   " . $generator->getStatusBadge());
        $this->getLogger()->info("Players:  " . $generator->getPlayersBadge());
        $this->getLogger()->info("TPS:      " . $generator->getTpsBadge());
        $this->getLogger()->info("Software: " . $generator->getSoftwareBadge());
        $this->getLogger()->info("Version:  " . $generator->getVersionBadge());
    }
    
    public function onDisable(): void {
        $this->getLogger()->info("ChostPulse disabled.");
    }
}
