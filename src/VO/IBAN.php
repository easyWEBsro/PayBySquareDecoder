<?php

namespace Rikudou\BySquare\VO;

class IBAN
{
    /**
     * @var string|null
     */
    private $iban;

    /**
     * @var string|null
     */
    private $bic;

    public function __construct(?string $iban, ?string $bic)
    {
        $this->iban = $iban;
        $this->bic = $bic;
    }

    /**
     * @return string|null
     */
    public function getIban(): ?string
    {
        return $this->iban;
    }

    /**
     * @return string|null
     */
    public function getBic(): ?string
    {
        return $this->bic;
    }
}
