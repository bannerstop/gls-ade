<?php
/** @var \Bannerstop\GlsAde\Api\Factory\ApiFactory $apiFactory */
$apiFactory = require 'bootstrap.php';

/** @var array $config */

$profileApi = $apiFactory->createProfileApi($account);
$profiles = $profileApi->getProfiles();

echo "Profiles:\n";
/** @var \Bannerstop\GlsAde\Model\Profile $profile */
foreach ($profiles as $profile) {
    echo sprintf("%d: %s\n", $profile->getId(), $profile->getDescription());
}
echo "\n";

$profile = $profileApi->changeProfile($profiles->first()->getId());
echo sprintf("Profile has been changed to \"%s\"\n", $profile->getId());
