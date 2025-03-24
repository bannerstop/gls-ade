<?php

namespace Bannerstop\GlsAde\Model;

use JMS\Serializer\Annotation as JMS;

/**
 * Class MaxParcelWeight
 * @package Bannerstop\GlsAde\Model
 */
class MaxParcelWeight
{
    /**
     * @JMS\Type("string")
     * @JMS\SerializedName("weight_max_national")
     *
     * @var string
     */
    private $national;

    /**
     * @JMS\Type("string")
     * @JMS\SerializedName("weight_max_international")
     *
     * @var string
     */
    private $international;

    /**
     * @return string
     */
    public function getNational()
    {
        return $this->national;
    }

    /**
     * @return string
     */
    public function getInternational()
    {
        return $this->international;
    }
}
