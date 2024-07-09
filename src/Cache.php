<?php

namespace Printful;

require 'vendor/autoload.php';

use Printful\CacheInterface;

class Cache implements CacheInterface
{
    private $cacheDir;

    public function __construct($cacheDir)
    {
        $this->cacheDir = $cacheDir;

        // Makes sure cache dir exists
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }


    public function set(string $key, $value, int $duration)
    {
        $cacheFile = $this->getCacheFilePath($key);
        $expiration = time() + $duration;

        $data = [
            'value' => $value,
            'expiration' => $expiration,
        ];

        file_put_contents($cacheFile, json_encode($data));
        chmod($cacheFile, 0777); // Give all permissions to prevent problems with new files
    }

    public function get(string $key)
    {
        $cacheFile = $this->getCacheFilePath($key);

        if (file_exists($cacheFile)) {
            $data = json_decode(file_get_contents($cacheFile), true);
            if ($data && $data['expiration'] > time()) {
                return $data['value'];
            } else {
                unlink($cacheFile);
            }
        }

        return null;
    }

    private function getCacheFilePath($key)
    {
        // Takes ProductId and chosen size from the cache key
        list($productId, $size) = explode('_', $key);

        // Makes cache filename using productId and chosen size
        return $this->cacheDir . '/' . $productId . '_' . $size . '.json';
    }
}