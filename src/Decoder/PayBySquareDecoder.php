<?php

namespace Rikudou\BySquare\Decoder;

use Rikudou\BySquare\Exception\PayBySquareException;
use Rikudou\BySquare\VO\DecodedBySquareData;

class PayBySquareDecoder
{
    /**
     * The path to xz binary, null means autodetect
     * @var string|null
     */
    private $xzBinary = null;

    /**
     * @param string $encodedString
     * @return DecodedBySquareData
     * @throws PayBySquareException
     */
    public function decode(string $encodedString): DecodedBySquareData
    {
        // every character must be converted from decimal to binary (using the by square table of characters, see
        // numerifyCharacter() method below) and must be exactly 5 characters in length (e.g. prepending 0)
        $expandedString = '';
        for ($i = 0, $length = strlen($encodedString); $i < $length; ++$i) {
            $char = $encodedString[$i];
            $expandedString .= str_pad(
                decbin($this->numerifyCharacter($char)),
                5,
                '0',
                STR_PAD_LEFT
            );
        }

        // header is the first 16 bits, it contains information about the document such as:
        // - type - first 4 bits
        // - version - next 4 bits
        // - subtype - next 4 bits
        // - reserved 4 bits for future use
        $type = substr($expandedString, 0, 4);
        $version = substr($expandedString, 4, 4);

        // 0000 means the pay by square standard
        if ($type !== '0000') {
            throw new PayBySquareException('This library can only handle Pay By Square standard');
        }

        if ($version > 0) {
            throw new PayBySquareException(
                sprintf("This library currently supports only version 0 of standard, '%s' given", bindec($version))
            );
        }

        // remove the padding bits that were necessary during encoding
        // as per documentation, the last bit should be removed until the length is divisible by 8
        // todo: rewrite this to not replace the string in every iteration
        while (strlen($expandedString) % 8 !== 0) {
            $expandedString = substr($expandedString, 0, -1);
        }

        // every 4 bits should be transformed from binary to hexadecimal
        $base16Transformed = '';
        for ($i = 0, $length = strlen($expandedString) / 4; $i < $length; ++$i) {
            $base16Transformed .= base_convert(
                substr($expandedString, $i * 4, 4),
                2,
                16
            );
        }

        // convert the result to binary data and remove the header to get the body
        $binaryData = hex2bin($base16Transformed);
        $binaryBody = substr($binaryData, 4);

        // decode the binary body using lzma1
        // there are two possible options at handling this - creating temporary file with the content or providing the
        // data in STDIN which needs proc_open or similar instead of simple shell_exec/exec
        $xzProcess = proc_open("'{$this->getXzBinary()}' '--format=raw' '--lzma1=lc=3,lp=0,pb=2,dict=128KiB' '-c' '-d' '-'", [
            0 => [
                'pipe',
                'r',
            ],
            1 => [
                'pipe',
                'w',
            ],
            2 => [
                'pipe',
                'w',
            ]
        ], $xzProcessPipes);
        fwrite($xzProcessPipes[0], $binaryBody);
        fclose($xzProcessPipes[0]);

        $error = stream_get_contents($xzProcessPipes[2]);
        $lzDecoded = stream_get_contents($xzProcessPipes[1]);

        fclose($xzProcessPipes[1]);
        fclose($xzProcessPipes[2]);

        $exitCode = proc_close($xzProcess);
        if($exitCode !== 0) {
            throw new PayBySquareException(sprintf('There was an error decoding data: %s', $error));
        }

        // the data are separated by TAB
        return new DecodedBySquareData(explode("\t", $lzDecoded), bindec($version));
    }

    /**
     * By Square standard defines its own character table where the characters are a combination of [0-9] and [A-Z].
     * The characters A-Z are pretty much a continuation of the numbers, e.g. A = 10, B = 11 ... Z = 35
     *
     * @param string $character
     * @return string
     */
    private function numerifyCharacter(string $character): string
    {
        if (is_numeric($character)) {
            return $character;
        }

        return strval(ord(strtoupper($character)) - ord('A') + 10);
    }

    /**
     * @return string
     * @throws PayBySquareException
     */
    public function getXzBinary(): string
    {
        if ($this->xzBinary === null) {
            exec('which xz', $output, $exitCode);
            if ($exitCode !== 0 || !isset($output[0])) {
                throw new PayBySquareException("The 'xz' binary not found in PATH, specify it using setXzBinary()");
            }
            $this->xzBinary = $output[0];
        }
        return $this->xzBinary;
    }

    /**
     * @param null|string $xzBinary
     * @return PayBySquareDecoder
     */
    public function setXzBinary(?string $xzBinary)
    {
        $this->xzBinary = $xzBinary;

        return $this;
    }
}