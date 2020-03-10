<?php

namespace Rikudou\BySquare\VO;

class IBAN
{
    /**
     * @var string
     */
    private $iban;

    /**
     * @var string
     */
    private $bic;

    public function __construct(string $iban, string $bic)
    {
        $this->iban = $iban;
        $this->bic = $bic;
    }

    /**
     * @return string
     */
    public function getIban(): string
    {
        return $this->iban;
    }

    /**
     * @return string
     */
    public function getBic(): string
    {
        return $this->bic;
    }
}
