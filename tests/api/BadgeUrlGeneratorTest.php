<?php

declare(strict_types=1);

namespace ChernegaSergiy\ChostPulse\Tests\api;

use ChernegaSergiy\ChostPulse\api\BadgeUrlGenerator;
use PHPUnit\Framework\TestCase;

class BadgeUrlGeneratorTest extends TestCase
{
    private BadgeUrlGenerator $generator;
    private string $baseUrl = 'https://mon.chost.pp.ua/api/badge';
    private string $publicId = 'srv_pub_f8a92b3c4d5e';

    protected function setUp(): void
    {
        $this->generator = new BadgeUrlGenerator($this->baseUrl, $this->publicId);
    }

    public function testGetStatusBadge(): void
    {
        $expected = 'https://mon.chost.pp.ua/api/badge?id=srv_pub_f8a92b3c4d5e&type=status';
        $actual = $this->generator->getStatusBadge();
        
        $this->assertEquals($expected, $actual);
    }

    public function testGetPlayersBadge(): void
    {
        $expected = 'https://mon.chost.pp.ua/api/badge?id=srv_pub_f8a92b3c4d5e&type=players';
        $actual = $this->generator->getPlayersBadge();
        
        $this->assertEquals($expected, $actual);
    }

    public function testGetTpsBadge(): void
    {
        $expected = 'https://mon.chost.pp.ua/api/badge?id=srv_pub_f8a92b3c4d5e&type=tps';
        $actual = $this->generator->getTpsBadge();
        
        $this->assertEquals($expected, $actual);
    }

    public function testGetSoftwareBadge(): void
    {
        $expected = 'https://mon.chost.pp.ua/api/badge?id=srv_pub_f8a92b3c4d5e&type=software';
        $actual = $this->generator->getSoftwareBadge();
        
        $this->assertEquals($expected, $actual);
    }

    public function testGetCustomBadge(): void
    {
        $expected = 'https://mon.chost.pp.ua/api/badge?id=srv_pub_f8a92b3c4d5e&type=custom';
        $actual = $this->generator->getCustomBadge('custom');
        
        $this->assertEquals($expected, $actual);
    }

    public function testGetCustomBadgeWithVersion(): void
    {
        $expected = 'https://mon.chost.pp.ua/api/badge?id=srv_pub_f8a92b3c4d5e&type=version';
        $actual = $this->generator->getCustomBadge('version');
        
        $this->assertEquals($expected, $actual);
    }

    public function testTrailingSlashIsRemoved(): void
    {
        $generatorWithSlash = new BadgeUrlGenerator(
            'https://mon.chost.pp.ua/api/badge/',
            $this->publicId
        );
        
        $expected = 'https://mon.chost.pp.ua/api/badge?id=srv_pub_f8a92b3c4d5e&type=status';
        $actual = $generatorWithSlash->getStatusBadge();
        
        $this->assertEquals($expected, $actual);
    }

    public function testMultipleTrailingSlashesAreRemoved(): void
    {
        $generatorWithSlashes = new BadgeUrlGenerator(
            'https://mon.chost.pp.ua/api/badge///',
            $this->publicId
        );
        
        $expected = 'https://mon.chost.pp.ua/api/badge?id=srv_pub_f8a92b3c4d5e&type=players';
        $actual = $generatorWithSlashes->getPlayersBadge();
        
        $this->assertEquals($expected, $actual);
    }

    public function testDifferentPublicIds(): void
    {
        $publicId1 = 'srv_pub_abc123';
        $publicId2 = 'srv_pub_xyz789';
        
        $generator1 = new BadgeUrlGenerator($this->baseUrl, $publicId1);
        $generator2 = new BadgeUrlGenerator($this->baseUrl, $publicId2);
        
        $url1 = $generator1->getStatusBadge();
        $url2 = $generator2->getStatusBadge();
        
        $this->assertStringContainsString($publicId1, $url1);
        $this->assertStringContainsString($publicId2, $url2);
        $this->assertNotEquals($url1, $url2);
    }

    public function testDifferentBaseUrls(): void
    {
        $baseUrl1 = 'https://example.com/badge';
        $baseUrl2 = 'https://different.com/api';
        
        $generator1 = new BadgeUrlGenerator($baseUrl1, $this->publicId);
        $generator2 = new BadgeUrlGenerator($baseUrl2, $this->publicId);
        
        $url1 = $generator1->getStatusBadge();
        $url2 = $generator2->getStatusBadge();
        
        $this->assertStringStartsWith($baseUrl1, $url1);
        $this->assertStringStartsWith($baseUrl2, $url2);
        $this->assertNotEquals($url1, $url2);
    }

    public function testUrlStructureContainsAllParts(): void
    {
        $url = $this->generator->getStatusBadge();
        
        // Check that URL contains base URL
        $this->assertStringContainsString('mon.chost.pp.ua/api/badge', $url);
        
        // Check that URL contains public ID
        $this->assertStringContainsString('id=srv_pub_f8a92b3c4d5e', $url);
        
        // Check that URL contains type parameter
        $this->assertStringContainsString('type=status', $url);
        
        // Check that URL has proper query string format
        $this->assertStringContainsString('?', $url);
        $this->assertStringContainsString('&', $url);
    }

    public function testAllBadgeTypesAreUnique(): void
    {
        $urls = [
            $this->generator->getStatusBadge(),
            $this->generator->getPlayersBadge(),
            $this->generator->getTpsBadge(),
            $this->generator->getSoftwareBadge(),
            $this->generator->getCustomBadge('custom'),
        ];
        
        $uniqueUrls = array_unique($urls);
        $this->assertCount(count($urls), $uniqueUrls, 'All badge URLs should be unique');
    }
}
