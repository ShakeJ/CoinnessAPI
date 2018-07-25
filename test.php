<?php
require './coinness/apiClient.php';

use Coinness\apiClient;
use const Coinness\COINNESS_LANGUAGE_KO;

$app_id = ""; // Your App id;
$app_sercet = ""; // Your Sercet

$client = new apiClient($app_id, $app_sercet);

$newsflash_list = $client->getNewsflashList(COINNESS_LANGUAGE_KO);

var_dump($newsflash_list);
