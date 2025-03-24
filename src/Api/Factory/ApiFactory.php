<?php

namespace Bannerstop\GlsAde\Api\Factory;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Bannerstop\GlsAde\Api\AuthApi;
use Bannerstop\GlsAde\Api\ConsignmentPrepareApi;
use Bannerstop\GlsAde\Api\Exception\ExceptionFactory;
use Bannerstop\GlsAde\Api\Exception\ExceptionWrappingExecutor;
use Bannerstop\GlsAde\Api\MpkApi;
use Bannerstop\GlsAde\Api\PickupApi;
use Bannerstop\GlsAde\Api\PostCodeApi;
use Bannerstop\GlsAde\Api\ProfileApi;
use Bannerstop\GlsAde\Api\SenderAddressApi;
use Bannerstop\GlsAde\Api\ServiceApi;
use Bannerstop\GlsAde\Model\AdeAccount;
use Webit\SoapApi\Executor\SoapApiExecutorBuilder;
use Webit\SoapApi\Hydrator\ArrayHydrator;
use Webit\SoapApi\Hydrator\ChainHydrator;
use Webit\SoapApi\Hydrator\HydratorSerializerBased;
use Webit\SoapApi\Hydrator\Serializer\ResultTypeMap;
use Webit\SoapApi\Input\InputNormaliserSerializerBased;
use Webit\SoapApi\Input\Serializer\StaticSerializationContextFactory;
use Webit\SoapApi\Util\StdClassToArray;

/**
 * Class ApiFactory
 * @author Daniel Bojdo <daniel.bojdo@web-it.eu>
 */
class ApiFactory
{
    const GLS_ADE_WSDL_TEST = 'https://ade-test.gls-poland.com/adeplus/pm1/ade_webapi.php?wsdl';
    const GLS_ADE_WSDL = 'https://adeplus.gls-poland.com/adeplus/pm1/ade_webapi.php?wsdl';

    /** @var array */
    private $executor = array();

    /**
     * @return ApiFactory
     */
    public static function create()
    {
        return new self();
    }

    /**
     * @param bool $testEnvironment
     * @return \Webit\SoapApi\Executor\SoapApiExecutor
     */
    private function getExecutor($testEnvironment = false)
    {
        $key = $testEnvironment ? 'test' : 'prod';
        if (isset($this->executor[$key])) {
            return $this->executor[$key];
        }

        $serializer = SerializerBuilder::create()->build();

        $executorBuilder = new SoapApiExecutorBuilder();
        $executorBuilder->setInputNormaliser(
            new InputNormaliserSerializerBased(
                $serializer,
                new StaticSerializationContextFactory(array(), true)
            )
        );

        $executorBuilder->setHydrator($this->hydrator($serializer));

        $executorBuilder->setWsdl($testEnvironment ? self::GLS_ADE_WSDL_TEST : self::GLS_ADE_WSDL);

        $executor = new ExceptionWrappingExecutor($executorBuilder->build(), new ExceptionFactory());

        $this->executor[$key] = $executor;

        return $executor;
    }

    /**
     * @param bool $testEnvironment
     * @return AuthApi
     */
    public function createAuthApi($testEnvironment = false)
    {
        return new AuthApi(
            $this->getExecutor($testEnvironment)
        );
    }

    /**
     * @param AdeAccount $account
     * @return MpkApi
     */
    public function createMpkApi(AdeAccount $account)
    {
        return new MpkApi(
            $this->getExecutor($account->isTestMode()),
            $this->createAuthApi($account->isTestMode()),
            $account
        );
    }

    /**
     * @param AdeAccount $account
     * @return ConsignmentPrepareApi
     */
    public function createConsignmentPrepareApi(AdeAccount $account)
    {
        return new ConsignmentPrepareApi(
            $this->getExecutor($account->isTestMode()),
            $this->createAuthApi($account->isTestMode()),
            $account
        );
    }

    /**
     * @param AdeAccount $account
     * @return ProfileApi
     */
    public function createProfileApi(AdeAccount $account)
    {
        return new ProfileApi(
            $this->getExecutor($account->isTestMode()),
            $this->createAuthApi($account->isTestMode()),
            $account
        );
    }

    /**
     * @param AdeAccount $account
     * @return ServiceApi
     */
    public function createServiceApi(AdeAccount $account)
    {
        return new ServiceApi(
            $this->getExecutor($account->isTestMode()),
            $this->createAuthApi($account->isTestMode()),
            $account
        );
    }

    /**
     * @param AdeAccount $account
     * @return SenderAddressApi
     */
    public function createSenderAddressApi(AdeAccount $account)
    {
        return new SenderAddressApi(
            $this->getExecutor($account->isTestMode()),
            $this->createAuthApi($account->isTestMode()),
            $account
        );
    }

    /**
     * @param AdeAccount $account
     * @return PostCodeApi
     */
    public function createPostCodeApi(AdeAccount $account)
    {
        return new PostCodeApi(
            $this->getExecutor($account->isTestMode()),
            $this->createAuthApi($account->isTestMode()),
            $account
        );
    }

    /**
     * @param AdeAccount $account
     * @return PickupApi
     */
    public function createPickupApi(AdeAccount $account)
    {
        return new PickupApi(
            $this->getExecutor($account->isTestMode()),
            $this->createAuthApi($account->isTestMode()),
            $account
        );
    }

    /**
     * @param Serializer $serializer
     * @return ChainHydrator
     */
    private function hydrator(Serializer $serializer)
    {
        return new ChainHydrator(
            array(
                new ArrayHydrator(new StdClassToArray()),
                new HydratorSerializerBased(
                    $serializer,
                    new ResultTypeMap(
                        array(
                            'adeProfile_GetIDs' => 'ArrayCollection<Bannerstop\GlsAde\Model\Profile>',
                            'adeProfile_Change' => 'Bannerstop\GlsAde\Model\Profile',
                            'adePreparingBox_GetConsign' => 'Bannerstop\GlsAde\Model\Consignment',
                            'adePickup_Get' => 'Bannerstop\GlsAde\Model\Pickup',
                            'adePickup_GetConsign' => 'Bannerstop\GlsAde\Model\Consignment',
                            'adePickup_ParcelNumberSearch' => 'Bannerstop\GlsAde\Model\Consignment',
                            'adeSendAddr_GetDictionary' => 'ArrayCollection<Bannerstop\GlsAde\Model\SenderAddress>',
                            'adeServices_GetAllowed' => 'Bannerstop\GlsAde\Model\ServiceList',
                            'adeServices_GetMaxParcelWeights' => 'Bannerstop\GlsAde\Model\MaxParcelWeight',
                            'adeServices_GetGuaranteed' => 'Bannerstop\GlsAde\Model\ServiceList'
                        ),
                        'ArrayCollection'
                    )
                )
            )
        );
    }
}
