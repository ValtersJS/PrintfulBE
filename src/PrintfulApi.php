<?php
namespace Printful;

require 'vendor/autoload.php';

use Printful\CacheInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class PrintfulApi
{
    private Client $client;
    private CacheInterface $cache;
    private string $token;
    public function __construct(CacheInterface $cache, string $token)
    {
        $this->cache = $cache;
        $this->token = $token;
        $this->client = new Client([
            'base_uri' => 'https://api.printful.com/v2/',
            'headers' => [
                'Authorization' => "Bearer {$this->token}",
            ],
        ]);
    }

    public function getData(int $productId)
    {
        $cacheKey = $this->generateCacheKey($productId);

        // if data is aleady cached, return cache
        $cachedData = $this->cache->get($cacheKey);
        if ($cachedData !== null)
            return $cachedData;

        return $this->getCatalogProductVariants($productId, $cacheKey);
    }

    public function getCatalogProductVariants(int $productId, string $cacheKey)
    {
        try {
            $response = $this->client->get("catalog-products/{$productId}/catalog-variants");
            $data = json_decode($response->getBody(), true);

        } catch (ClientException $e) {
            echo "Error: " . $e->getMessage() . "\n";
            return null;
        }

        $filteredData = $this->filterData($data, $cacheKey);
        return $filteredData;
    }

    public function filterData($data, $cacheKey)
    {
        if ($data === null)
            return null;

        $colors = [];
        $sizes = [];

        if (isset($data['data']) && is_array($data['data'])) {
            foreach ($data['data'] as $variant) {
                if (!in_array($variant['color'], $colors)) {
                    array_push($colors, $variant['color']);
                }
                if (!in_array($variant['size'], $sizes)) {
                    array_push($sizes, $variant['size']);

                }
            }
        }

        $result = [
            'colors' => $colors,
            'sizes' => $sizes,
        ];

        echo "Caching result...\n";
        $this->cache->set($cacheKey, $result, 300); // Cache for 5 minutes

        return $result;
    }

    private function generateCacheKey(int $productId)
    {
        return "product_{$productId}_variants";
    }

}