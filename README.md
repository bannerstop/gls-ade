# GLS ADE WebAPI Implementation

Fork of [webit/gls-ade](https://github.com/dbojdo/gls-ade) with updated dependencies.
So far we have only updated the dependencies and fixed the code to work with the new versions.
There is still some work to be done, like restoring the tests and implementing strict typing.

The repository provides a client to communicate with GLS SOAP APIs.

## Installation

Add the **bannerstop/gls-ade** into **composer.json**

```json
{
    "require": {
        "bannerstop/gls-ade": "^3.0.0"
    }
}
```

## Usage

```php
use \Webit\GlsAde\Model\AdeAccount;
use \Webit\GlsAde\Api\Factory\ApiFactory;

$adeAccount = new AdeAccount('your-login', 'your-password', 'is-test-env' ? true : false);

$apiFactory = ApiFactory::create();

/** @var \Webit\GlsAde\Api\AuthApi $authApi */
$authApi = $apiFactory->createAuthApi();

/** @var \Webit\GlsAde\Api\ConsignmentPrepareApi $consignemntPrepareApi */
$consignemntPrepareApi = $apiFactory->createConsignmentPrepareApi($adeAccount);

/** @var \Webit\GlsAde\Api\MpkApi $mpkApi */
$mpkApi = $apiFactory->createMpkApi($adeAccount);

/** @var \Webit\GlsAde\Api\PickupApi $pickupApi */
$pickupApi = $apiFactory->createPickupApi($adeAccount);

/** @var \Webit\GlsAde\Api\PostalCodeApi $postalCodeApi */
$postalCodeApi = $apiFactory->createPostalCodeApi($adeAccount);

/** @var \Webit\GlsAde\Api\ProfileApi $profileApi */
$profileApi = $apiFactory->createProfileApi($adeAccount);

/** @var \Webit\GlsAde\Api\SenderAddressApi $senderAddressApi */
$senderAddressApi = $apiFactory->createSenderAddressApi($adeAccount);

/** @var \Webit\GlsAde\Api\ServiceApi $serviceApi */
$serviceApi = $apiFactory->createServiceApi($adeAccount);
```

## Running examples

For real life example see **examples** directory.

```sh
cd examples
cp config.php.dist config.php
```

Set your account details in **config.php** then run examples

``sh
php auth.php
php mpk.php
php post-codes.php
php profile.php
php sender.php
php services.php
``

## TODO

- [ ] Implement strict typing
- [ ] Restore tests
