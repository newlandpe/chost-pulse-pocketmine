<?php

declare(strict_types=1);

namespace ChernegaSergiy\ChostPulse\Tests\security;

use ChernegaSergiy\ChostPulse\security\TokenGenerator;
use PHPUnit\Framework\TestCase;

class TokenGeneratorTest extends TestCase
{
    private TokenGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new TokenGenerator();
    }

    public function testGenerateSecretKeyHasCorrectPrefix(): void
    {
        $token = $this->generator->generateSecretKey();
        $this->assertStringStartsWith('sk_live_', $token);
    }

    public function testGenerateSecretKeyHasCorrectLength(): void
    {
        $token = $this->generator->generateSecretKey();
        // sk_live_ (8 chars) + UUID (36 chars) = 44 chars
        $this->assertEquals(44, strlen($token));
    }

    public function testGenerateSecretKeyFollowsUUIDv4Format(): void
    {
        $token = $this->generator->generateSecretKey();
        $uuid = substr($token, 8); // Remove 'sk_live_' prefix
        
        // UUIDv4 pattern: xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
        // where y is one of [8, 9, a, b]
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';
        $this->assertMatchesRegularExpression($pattern, $uuid);
    }

    public function testGenerateSecretKeyIsUnique(): void
    {
        $tokens = [];
        for ($i = 0; $i < 10; $i++) {
            $tokens[] = $this->generator->generateSecretKey();
        }
        
        // All tokens should be unique
        $uniqueTokens = array_unique($tokens);
        $this->assertCount(10, $uniqueTokens);
    }

    public function testDerivePublicIdHasCorrectPrefix(): void
    {
        $secretToken = 'sk_live_550e8400-e29b-41d4-a716-446655440000';
        $publicId = $this->generator->derivePublicId($secretToken);
        
        $this->assertStringStartsWith('srv_pub_', $publicId);
    }

    public function testDerivePublicIdHasCorrectLength(): void
    {
        $secretToken = 'sk_live_550e8400-e29b-41d4-a716-446655440000';
        $publicId = $this->generator->derivePublicId($secretToken);
        
        // srv_pub_ (8 chars) + 12 hex chars = 20 chars
        $this->assertEquals(20, strlen($publicId));
    }

    public function testDerivePublicIdIsDeterministic(): void
    {
        $secretToken = 'sk_live_550e8400-e29b-41d4-a716-446655440000';
        
        $publicId1 = $this->generator->derivePublicId($secretToken);
        $publicId2 = $this->generator->derivePublicId($secretToken);
        
        $this->assertEquals($publicId1, $publicId2);
    }

    public function testDerivePublicIdIsDifferentForDifferentTokens(): void
    {
        $token1 = 'sk_live_550e8400-e29b-41d4-a716-446655440000';
        $token2 = 'sk_live_660e8400-e29b-41d4-a716-446655440000';
        
        $publicId1 = $this->generator->derivePublicId($token1);
        $publicId2 = $this->generator->derivePublicId($token2);
        
        $this->assertNotEquals($publicId1, $publicId2);
    }

    public function testDerivePublicIdIsOneWayHash(): void
    {
        $secretToken = 'sk_live_550e8400-e29b-41d4-a716-446655440000';
        $publicId = $this->generator->derivePublicId($secretToken);
        
        // Remove prefix to get hash portion
        $hashPortion = substr($publicId, 8);
        
        // Verify it's hexadecimal
        $this->assertMatchesRegularExpression('/^[0-9a-f]{12}$/', $hashPortion);
        
        // Verify it's not the same as the secret token
        $this->assertStringNotContainsString($hashPortion, $secretToken);
    }

    public function testDerivePublicIdUsesOnlyUuidPart(): void
    {
        // Test that the prefix is removed before hashing
        $secretToken = 'sk_live_550e8400-e29b-41d4-a716-446655440000';
        $expectedHash = substr(hash('sha256', '550e8400-e29b-41d4-a716-446655440000'), 0, 12);
        
        $publicId = $this->generator->derivePublicId($secretToken);
        $actualHash = substr($publicId, 8);
        
        $this->assertEquals($expectedHash, $actualHash);
    }
}
