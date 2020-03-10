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
     * @var string
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
     * @var string
     */
    private $currency;

    /**
     * @var DateTime
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
     * @var int
     */
    private $specificSymbol;

    /**
     * @var string
     */
    private $payerReference;

    /**
     * @var string
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
     * @var string
     */
    private $payeeName;

    /**
     * @var string
     */
    private $payeeAddressLine1;

    /**
     * @var string
     */
    private $payeeAddressLine2;

    public function __construct(array $rawData, int $version)
    {
        $this->version = $version;

        // the first 4 bytes are the crc32 sum
        $this->paymentId = substr($rawData[0] ?? '', 4);
        $this->paymentsCount = (int) $rawData[1] ?? 0;
        $this->regularPayment = ($rawData[2] ?? false) === '1';
        $this->amount = (float) $rawData[3] ?? 0;
        $this->currency = $rawData[4] ?? '';
        $this->dueDate = DateTime::createFromFormat('Ymd', $rawData[5] ?? '1970-01-01');
        // todo assign symbols from payer reference and vice-versa if one information is available and other is not
        $this->variableSymbol = is_numeric($rawData[6] ?? '') ? (int) $rawData[6] : null;
        $this->constantSymbol = is_numeric($rawData[7] ?? '') ? (int) $rawData[7] : null;
        $this->specificSymbol = is_numeric($rawData[8] ?? '') ? (int) $rawData[8] : null;
        $this->payerReference = $rawData[9] ?? '';
        $this->note = $rawData[10] ?? '';
        $this->ibanCount = (int) $rawData[11] ?? 0;
        for ($index = 12; $index < 12 + $this->ibanCount * 2; $index += 2) {
            $this->ibans[] = new IBAN($rawData[$index] ?? '', $rawData[$index + 1] ?? '');
        }
        $this->standingOrder = ($rawData[$index] ?? false) === '1';
        $this->directDebit = ($rawData[$index + 1] ?? false) === '1';
        $this->payeeName = $rawData[$index + 2] ?? '';
        $this->payeeAddressLine1 = $rawData[$index + 3] ?? '';
        $this->payeeAddressLine2 = $rawData[$index + 4] ?? '';
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getPaymentId(): string
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
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return DateTime
     */
    public function getDueDate(): DateTime
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
     * @return int
     */
    public function getSpecificSymbol(): int
    {
        return $this->specificSymbol;
    }

    /**
     * @return string
     */
    public function getPayerReference(): string
    {
        return $this->payerReference;
    }

    /**
     * @return string
     */
    public function getNote(): string
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
     * @return string
     */
    public function getPayeeName(): string
    {
        return $this->payeeName;
    }

    /**
     * @return string
     */
    public function getPayeeAddressLine1(): string
    {
        return $this->payeeAddressLine1;
    }

    /**
     * @return string
     */
    public function getPayeeAddressLine2(): string
    {
        return $this->payeeAddressLine2;
    }
}
