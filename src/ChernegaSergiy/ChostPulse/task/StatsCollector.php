<?php

declare(strict_types=1);

namespace ChernegaSergiy\ChostPulse\task;

use pocketmine\Server;
use pocketmine\utils\Config;

class StatsCollector {
    
    public static function collect(Server $server, Config $config): array {
        $sendSoftware = $config->get("send-software", true);
        $interval = (int) $config->get("interval", 60);
        
        $data = [
            "status" => "online",
            "players" => count($server->getOnlinePlayers()),
            "maxPlayers" => $server->getMaxPlayers(),
            "tps" => round($server->getTicksPerSecond(), 2),
            "version" => $server->getMinecraftVersion(),
            "heartbeatIntervalSec" => $interval
        ];
        
        if ($sendSoftware) {
            $data["software"] = $server->getName() . " " . $server->getPocketMineVersion();
        }
        
        return $data;
    }
}
