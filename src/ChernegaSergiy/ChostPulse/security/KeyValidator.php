<?php

declare(strict_types=1);

namespace ChernegaSergiy\ChostPulse\security;

class KeyValidator {
    
    private const PREFIX_SECRET = "sk_live_";
    private const MIN_LENGTH = 40;
    
    public static function isValidSecretToken(string $token): bool {
        // Check prefix
        if (!str_starts_with($token, self::PREFIX_SECRET)) {
            return false;
        }
        
        // Check minimum length
        if (strlen($token) < self::MIN_LENGTH) {
            return false;
        }
        
        // Check format (should be sk_live_ + UUID)
        $uuid = substr($token, strlen(self::PREFIX_SECRET));
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';
        
        return preg_match($pattern, $uuid) === 1;
    }
}
