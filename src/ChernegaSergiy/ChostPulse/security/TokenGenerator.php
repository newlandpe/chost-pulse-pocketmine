<?php

declare(strict_types=1);

namespace ChernegaSergiy\ChostPulse\security;

class TokenGenerator {
    
    private const PREFIX_SECRET = "sk_live_";
    private const PREFIX_PUBLIC = "srv_pub_";
    
    public function generateSecretKey(): string {
        // Generate UUIDv4-compatible random string
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // Version 4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // Variant
        
        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        
        return self::PREFIX_SECRET . $uuid;
    }
    
    public function derivePublicId(string $secretToken): string {
        // Remove prefix before hashing
        $cleanToken = str_replace(self::PREFIX_SECRET, "", $secretToken);
        
        // SHA-256 hash (first 12 chars for brevity)
        $hash = hash("sha256", $cleanToken);
        
        return self::PREFIX_PUBLIC . substr($hash, 0, 12);
    }
}
