<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use Printful\Cache;
use Printful\PrintfulApi;

final class PrintfulAPITest extends TestCase
{
    private $api;
    private $cache;
    private $token;

    protected function setUp(): void
    {
        $this->token = 'T4JC7mlBCO3bYq7X3fZFpiqS2Y2Z4yiNi4WAVmAu';

        $cache = new Cache('cache');
        $this->api = new PrintfulApi($cache, $this->token);
    }

    public function testGetData(): void
    {
        $productId = 12;

        $data = $this->api->getData($productId);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('colors', $data);
        $this->assertArrayHasKey('sizes', $data);
        var_dump($data);
    }

    public function testGetDataInvalidProductId(): void
    {
        $productId = -1;
        $data = $this->api->getData($productId);

        $this->assertNull($data);
    }

    public function testCacheReset()
    {
        $cache = new Cache('cache');
        $api = new PrintfulAPI($cache, $this->token);

        $cache->set('test_key', 'test_data', 10);
        sleep(8);

        $this->assertEquals('test_data', $cache->get('test_key'));
        sleep(3);

        $this->assertNull($cache->get('test_key'));
    }
}