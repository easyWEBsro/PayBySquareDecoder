<?php

namespace Rikudou\BySquare\Config;

class PayBySquareDecoderConfiguration
{
    /**
     * @var bool
     */
    private $allowPartialData = true;

    /**
     * @return bool
     */
    public function isAllowPartialData(): bool
    {
        return $this->allowPartialData;
    }

    /**
     * @param bool $allowPartialData
     *
     * @return PayBySquareDecoderConfiguration
     */
    public function setAllowPartialData(bool $allowPartialData): PayBySquareDecoderConfiguration
    {
        $this->allowPartialData = $allowPartialData;

        return $this;
    }
}
