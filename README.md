# Pay By Square decoder

This library decodes the string encoded according the the Slovakian
Pay By Square standard.

> This package can be used as a standalone library or a Symfony bundle

> The package is currently in alpha state and public api will
> likely change slightly

## Installing

`composer require rikudou/pay-by-square-decoder`

## Requirements

You must have the `xz` binary from `xz-utils` in your system.

## Usage

The class `\Rikudou\BySquare\Decoder\PayBySquareDecoder` has a
public method called `decode` which accepts a string argument with
the encoded data.

Example:

```php
<?php

use Rikudou\BySquare\Decoder\PayBySquareDecoder;

$encodedData = '0006Q0000UAT63HVES6GL5A5A0O9NSPEEHUHIEP70EG9LM6LU6EBNQ8KG6RB2N2LUIHMVTV51KQ77DGFC25KM2S9V46EQSN5GSD9J1N4BKT1L9ASVOOT1LPOMAO66IS2BHJDCNA4D9LFKG9MTFLISBD36O5CQQNJIBB2TJILQVVN684000';
$decoder = new PayBySquareDecoder();

$decodedData = $decoder->decode($encodedData);
// $decodedData is now an instance of \Rikudou\BySquare\VO\DecodedBySquareData
```

If the `xz` binary is not in your `PATH` you must set the path
first:

```php
<?php

use Rikudou\BySquare\Decoder\PayBySquareDecoder;

$decoder = new PayBySquareDecoder();
$decoder->setXzBinary('/path/to/xz');

$decodedData = $decoder->decode($encodedData);
// $decodedData is now an instance of \Rikudou\BySquare\VO\DecodedBySquareData
```

## Usage in Symfony

If you use Symfony flex, the package should be configured
automatically, if not, just add `\Rikudou\BySquare\RikudouPayBySquareDecoderBundle`
to your `config/bundles.php`.

You can now use `Rikudou\BySquare\Decoder\PayBySquareDecoder` as
a service:

```php
<?php
use Rikudou\BySquare\Decoder\PayBySquareDecoder;

class MyService
{
    /**
     * @var PayBySquareDecoder
     */
    private $decoder;
    
    public function __construct(PayBySquareDecoder $decoder)
    {   
        $this->decoder = $decoder;
    }
}
```

If your `xz` binary is not in your path, you can create a config
file in `config/packages/rikudou_pay_by_square_decoder.yaml`
and change the path, here is the default config (generated using
`config:dump` command):

```yaml
# Default configuration for "RikudouPayBySquareDecoderBundle"
rikudou_pay_by_square_decoder:

    # The path to the xz binary, null means auto detect
    xz_path:              null

```

## Return values description

The `decode` method returns an instance of `\Rikudou\BySquare\VO\DecodedBySquareData`
which is a value object. The method names are self-explanatory
in most cases.

- `getVersion(): int` - returns the Pay By Square version, currently
only version 0 is supported
- `getPaymentId(): string` - internal payment id
- `getPaymentsCount(): int` - the count of payments the encoded
string contains
- `isRegularPayment(): bool` - returns true if the payment is
a standard one-off payment
- `getAmount(): float` - the payment amount
- `getCurrency(): string` - the three-letter ISO currency code
- `getDueDate(): DateTime` - returns the due date, if no due
date was given returns the date *1970-01-01*
- `getVariableSymbol(): ?int` - returns variable symbol if present,
 null otherwise
- `getConstantSymbol(): ?int` - returns constant symbol if present,
null otherwise
- `getSpecificSymbol(): ?int` - returns specific symbol if present,
null otherwise
- `getPayerReference(): string` - returns the payer reference,
e.g. a variable, constant and specific symbol as a single string
- `getNote(): string` - returns the note/comment
- `getIbanCount(): int` - returns the count of IBANs present
in encoded string
- `getIban(): IBAN` - returns the first IBAN - if no IBANs are
present it throws an exception
- `getIbans(): iterable<IBAN>` - returns an iterable of all IBANs
present
- `isStandingOrder(): bool` - whether the payment is a standing
order
- `isDirectDebit(): bool` - whether the payment is a direct debit
- `getPayeeName(): string` - returns the name of the payee
- `getPayeeAddressLine1(): string` - returns the address line 1
of the payee
- `getPayeeAddressLine2(): string` - returns the address line 2
of the payee

The methods `getIban` and `getIbans` return `\Rikudou\BySquare\VO\IBAN`
(or iterable of it) which is a really simple value object with
two methods:

- `getIban(): string` - returns the IBAN
- `getBic(): string` - returns the BIC/SWIFT code

## Exception handling

The only exception thrown by this library is 
`\Rikudou\BySquare\Exception\PayBySquareException`