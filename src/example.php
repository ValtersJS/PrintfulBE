<?php

require_once 'vendor/autoload.php';

use Printful\Cache;
use Printful\PrintfulApi;

// Token is static and exposed freely for the sake of the test,
// should be in an .env file that is apart of .gitignore
$token = 'T4JC7mlBCO3bYq7X3fZFpiqS2Y2Z4yiNi4WAVmAu';

$cache = new Cache('cache');
$service = new PrintfulApi($cache, $token);

$productId = 12;

$result = $service->getData($productId);

print_r($result);