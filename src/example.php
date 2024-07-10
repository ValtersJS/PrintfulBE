<?php

require_once 'vendor/autoload.php';

use Printful\Cache;
use Printful\PrintfulApi;

$token = 'T4JC7mlBCO3bYq7X3fZFpiqS2Y2Z4yiNi4WAVmAu';

$cache = new Cache('cache');
$service = new PrintfulApi($cache, $token);

$productId = 12;

$result = $service->getCatalogProductVariants($productId);

print_r($result);