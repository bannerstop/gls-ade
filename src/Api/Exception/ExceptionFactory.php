<?php
/**
 * File: ExceptionFactory.php
 * Created at: 2014-11-29 01:09
 */
 
namespace Webit\GlsAde\Api\Exception;

use Webit\SoapApi\Exception\ExceptionFactoryInterface;

/**
 * Class ExceptionFactory
 * @author Daniel Bojdo <daniel.bojdo@web-it.eu>
 */
class ExceptionFactory implements ExceptionFactoryInterface
{
    /**
     * Wraps exception to API's type
     *
     * @param \Exception $e
     * @param string $soapFunction
     * @param $input
     * @return \Exception
     */
    public function createException(\Exception $e, $soapFunction, $input)
    {
        if ($e instanceof \SoapFault) {
            throw new InvalidInputDataException(
                sprintf('%s (%s) - %s', $e->faultcode, $e->faultstring, isset($e->faultactor) ? $e->faultactor : 'unknown'),
                null,
                $e
            );
        }
        return $e;
    }
}
