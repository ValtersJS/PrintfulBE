<?php
namespace Printful;

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
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function getCatalogProductVariants(int $productId)
    {
        $cacheKey = "catalog_product_{$productId}";
        $cachedData = $this->cache->get($cacheKey);

        // if data is aleady cached, return cache
        if ($cachedData !== null) {
            return $cachedData;
        }

        try {
            $response = $this->client->get("catalog-products/{$productId}/catalog-variants");
            $data = json_decode($response->getBody(), true);

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
        } catch (ClientException $e) {
            echo "Error: " . $e->getMessage() . "\n";
            return ['error' => $e->getMessage()];
        }
    }

}