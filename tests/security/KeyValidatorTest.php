<?php

declare(strict_types=1);

namespace ChernegaSergiy\ChostPulse\Tests\security;

use ChernegaSergiy\ChostPulse\security\KeyValidator;
use PHPUnit\Framework\TestCase;

class KeyValidatorTest extends TestCase
{
    public function testValidSecretTokenIsAccepted(): void
    {
        $validToken = 'sk_live_550e8400-e29b-41d4-a716-446655440000';
        $this->assertTrue(KeyValidator::isValidSecretToken($validToken));
    }

    public function testValidSecretTokenWithUppercaseUUIDIsAccepted(): void
    {
        $validToken = 'sk_live_550E8400-E29B-41D4-A716-446655440000';
        $this->assertTrue(KeyValidator::isValidSecretToken($validToken));
    }

    public function testInvalidPrefixIsRejected(): void
    {
        $invalidToken = 'invalid_550e8400-e29b-41d4-a716-446655440000';
        $this->assertFalse(KeyValidator::isValidSecretToken($invalidToken));
    }

    public function testPublicIdPrefixIsRejected(): void
    {
        $publicId = 'srv_pub_f8a92b3c4d5e';
        $this->assertFalse(KeyValidator::isValidSecretToken($publicId));
    }

    public function testTooShortTokenIsRejected(): void
    {
        $shortToken = 'sk_live_123';
        $this->assertFalse(KeyValidator::isValidSecretToken($shortToken));
    }

    public function testEmptyTokenIsRejected(): void
    {
        $this->assertFalse(KeyValidator::isValidSecretToken(''));
    }

    public function testTokenWithoutUUIDIsRejected(): void
    {
        $invalidToken = 'sk_live_not-a-valid-uuid-format-here-at-all';
        $this->assertFalse(KeyValidator::isValidSecretToken($invalidToken));
    }

    public function testTokenWithInvalidUUIDVersionIsRejected(): void
    {
        // UUIDv3 instead of v4 (third group should start with 4)
        $invalidToken = 'sk_live_550e8400-e29b-31d4-a716-446655440000';
        $this->assertFalse(KeyValidator::isValidSecretToken($invalidToken));
    }

    public function testTokenWithInvalidUUIDVariantIsRejected(): void
    {
        // Invalid variant (fourth group should start with 8, 9, a, or b)
        $invalidToken = 'sk_live_550e8400-e29b-41d4-c716-446655440000';
        $this->assertFalse(KeyValidator::isValidSecretToken($invalidToken));
    }

    public function testTokenWithMissingDashesIsRejected(): void
    {
        // UUID without dashes should be rejected
        $invalidToken = 'sk_live_' . str_replace('-', '', '550e8400-e29b-41d4-a716-446655440000');
        $this->assertFalse(KeyValidator::isValidSecretToken($invalidToken));
    }

    public function testTokenWithExtraCharactersIsRejected(): void
    {
        $invalidToken = 'sk_live_550e8400-e29b-41d4-a716-446655440000-extra';
        $this->assertFalse(KeyValidator::isValidSecretToken($invalidToken));
    }

    public function testTokenWithNonHexCharactersIsRejected(): void
    {
        $invalidToken = 'sk_live_550g8400-e29b-41d4-a716-446655440000';
        $this->assertFalse(KeyValidator::isValidSecretToken($invalidToken));
    }

    public function testMinimumLengthRequirement(): void
    {
        // Test that minimum length is enforced (40 characters)
        $tooShort = 'sk_live_550e8400-e29b-41d4-a716-44665544';
        $this->assertFalse(KeyValidator::isValidSecretToken($tooShort));
        
        $exactLength = 'sk_live_550e8400-e29b-41d4-a716-446655440000';
        $this->assertTrue(KeyValidator::isValidSecretToken($exactLength));
    }

    /**
     * Test various valid UUIDv4 variants (8, 9, a, b in the fourth group)
     */
    public function testAllValidUUIDv4Variants(): void
    {
        $validVariants = [
            'sk_live_550e8400-e29b-41d4-8716-446655440000', // variant 8
            'sk_live_550e8400-e29b-41d4-9716-446655440000', // variant 9
            'sk_live_550e8400-e29b-41d4-a716-446655440000', // variant a
            'sk_live_550e8400-e29b-41d4-b716-446655440000', // variant b
        ];

        foreach ($validVariants as $token) {
            $this->assertTrue(
                KeyValidator::isValidSecretToken($token),
                "Token with variant should be valid: {$token}"
            );
        }
    }
}
