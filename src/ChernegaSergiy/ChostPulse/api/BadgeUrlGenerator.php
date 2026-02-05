<?php

declare(strict_types=1);

namespace ChernegaSergiy\ChostPulse\api;

class BadgeUrlGenerator {
    
    private string $baseUrl;
    private string $publicId;
    
    public function __construct(string $baseUrl, string $publicId) {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->publicId = $publicId;
    }
    
    public function getStatusBadge(): string {
        return "{$this->baseUrl}?id={$this->publicId}&type=status";
    }
    
    public function getPlayersBadge(): string {
        return "{$this->baseUrl}?id={$this->publicId}&type=players";
    }
    
    public function getTpsBadge(): string {
        return "{$this->baseUrl}?id={$this->publicId}&type=tps";
    }
    
    public function getSoftwareBadge(): string {
        return "{$this->baseUrl}?id={$this->publicId}&type=software";
    }

    public function getVersionBadge(): string {
        return "{$this->baseUrl}?id={$this->publicId}&type=version";
    }
    
    public function getCustomBadge(string $type): string {
        return "{$this->baseUrl}?id={$this->publicId}&type={$type}";
    }
}
