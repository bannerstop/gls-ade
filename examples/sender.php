<?php
/** @var \Bannerstop\GlsAde\Api\Factory\ApiFactory $apiFactory */
$apiFactory = require 'bootstrap.php';

/** @var array $config */

$senderApi = $apiFactory->createSenderAddressApi($account);
$status = $senderApi->getSenderAddressStatus();

printf("Status of sender address: %d\n", $status);

$senderAddresses = $senderApi->getSenderAddressDictionary();
if ($senderAddresses->count() > 0) {
    echo "Sender Dictionary:\n";
    /** @var \Bannerstop\GlsAde\Model\SenderAddress $senderAddress */
    foreach ($senderAddresses as $senderAddress) {
        printf("Address: %s\n", $senderAddress->getName1());
    }
} else {
    echo "No addresses in Sender Dictionary\n";
}
