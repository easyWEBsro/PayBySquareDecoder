<?php

namespace Rikudou\BySquare\VO;

use DateTime;
use Rikudou\BySquare\Exception\PayBySquareException;

class DecodedBySquareData
{
    /**
     * @var int
     */
    private $version;

    /**
     * @var string|null
     */
    private $paymentId;

    /**
     * @var int
     */
    private $paymentsCount;

    /**
     * @var bool
     */
    private $regularPayment;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var string|null
     */
    private $currency;

    /**
     * @var DateTime|null
     */
    private $dueDate;

    /**
     * @var int|null
     */
    private $variableSymbol;

    /**
     * @var int|null
     */
    private $constantSymbol;

    /**
     * @var int|null
     */
    private $specificSymbol;

    /**
     * @var string|null
     */
    private $payerReference;

    /**
     * @var string|null
     */
    private $note;

    /**
     * @var int
     */
    private $ibanCount;

    /**
     * @var IBAN[]
     */
    private $ibans;

    /**
     * @var bool
     */
    private $standingOrder;

    /**
     * @var bool
     */
    private $directDebit;

    /**
     * @var string|null
     */
    private $payeeName;

    /**
     * @var string|null
     */
    private $payeeAddressLine1;

    /**
     * @var string|null
     */
    private $payeeAddressLine2;

    /**
     * DecodedBySquareData constructor.
     *
     * @param array<int, string> $rawData
     * @param int                $version
     */
    public function __construct(array $rawData, int $version)
    {
        $this->version = $version;

        $dueDate = ($rawData[5] ?? null) ? DateTime::createFromFormat('Ymd', $rawData[5]) : null;
        if ($dueDate === false) {
            $dueDate = null;
        }
        $internalId = substr($rawData[0] ?? '', 4);
        if (!$internalId) {
            $internalId = null;
        }

        // the first 4 bytes are the crc32 sum
        $this->paymentId = $internalId;
        $this->paymentsCount = (int) $rawData[1] ?? 0;
        $this->regularPayment = ($rawData[2] ?? false) === '1';
        $this->amount = (float) $rawData[3] ?? null;
        $this->currency = $rawData[4] ?? null;
        $this->dueDate = $dueDate;
        $this->variableSymbol = is_numeric($rawData[6] ?? '') ? (int) $rawData[6] : null;
        $this->constantSymbol = is_numeric($rawData[7] ?? '') ? (int) $rawData[7] : null;
        $this->specificSymbol = is_numeric($rawData[8] ?? '') ? (int) $rawData[8] : null;
        $this->payerReference = ($rawData[9] ?? null) ?: null;
        $this->note = ($rawData[10] ?? null) ?: null;
        $this->ibanCount = (int) $rawData[11] ?? 0;
        for ($index = 12; $index < 12 + $this->ibanCount * 2; $index += 2) {
            $this->ibans[] = new IBAN($rawData[$index] ?? null, $rawData[$index + 1] ?? null);
        }
        $this->standingOrder = ($rawData[$index] ?? false) === '1';
        $this->directDebit = ($rawData[$index + 1] ?? false) === '1';
        $this->payeeName = ($rawData[$index + 2] ?? null) ?: null;
        $this->payeeAddressLine1 = ($rawData[$index + 3] ?? null) ?: null;
        $this->payeeAddressLine2 = ($rawData[$index + 4] ?? null) ?: null;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @return string|null
     */
    public function getPaymentId(): ?string
    {
        return $this->paymentId;
    }

    /**
     * @return int
     */
    public function getPaymentsCount(): int
    {
        return $this->paymentsCount;
    }

    /**
     * @return bool
     */
    public function isRegularPayment(): bool
    {
        return $this->regularPayment;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return string|null
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * @return DateTime|null
     */
    public function getDueDate(): ?DateTime
    {
        return $this->dueDate;
    }

    /**
     * @return int|null
     */
    public function getVariableSymbol(): ?int
    {
        return $this->variableSymbol;
    }

    /**
     * @return int|null
     */
    public function getConstantSymbol(): ?int
    {
        return $this->constantSymbol;
    }

    /**
     * @return int|null
     */
    public function getSpecificSymbol(): ?int
    {
        return $this->specificSymbol;
    }

    /**
     * @return string|null
     */
    public function getPayerReference(): ?string
    {
        return $this->payerReference;
    }

    /**
     * @return string|null
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @return int
     */
    public function getIbanCount(): int
    {
        return $this->ibanCount;
    }

    /**
     * Returns the first IBAN
     *
     * @throws PayBySquareException
     *
     * @return IBAN
     */
    public function getIban(): IBAN
    {
        if ($this->ibanCount <= 0) {
            throw new PayBySquareException('There are no IBANs');
        }

        return $this->ibans[0];
    }

    /**
     * @return IBAN[]
     */
    public function getIbans(): iterable
    {
        return $this->ibans;
    }

    /**
     * @return bool
     */
    public function isStandingOrder(): bool
    {
        return $this->standingOrder;
    }

    /**
     * @return bool
     */
    public function isDirectDebit(): bool
    {
        return $this->directDebit;
    }

    /**
     * @return string|null
     */
    public function getPayeeName(): ?string
    {
        return $this->payeeName;
    }

    /**
     * @return string|null
     */
    public function getPayeeAddressLine1(): ?string
    {
        return $this->payeeAddressLine1;
    }

    /**
     * @return string|null
     */
    public function getPayeeAddressLine2(): ?string
    {
        return $this->payeeAddressLine2;
    }
}
