<?php
/** @var \Bannerstop\GlsAde\Api\Factory\ApiFactory $apiFactory */
$apiFactory = require 'bootstrap.php';

/** @var array $config */

$postCodesApi = $apiFactory->createPostCodeApi($account);

$country = 'PL';
$postCode = '30-855';

$city = $postCodesApi->getCity($country, $postCode);
printf("City for country \"%s\" and post code \"%s\": %s\n", $country, $postCode, $city);
