<?php

/*CQC: No API check*/
/*CQC: !FLAG__SOMEWHAT_PEDANTIC*/
/*CQC: !FLAG__ESLINT*/

/**
 * @package search
 */

/**
 * @author Philip Burggraf <philip@pburggraf.de>
 */
class CRC24
{
    protected $poly = 0x864cfb;
    protected $lookupTable;
    protected $bitLength = 24;

    public function __construct()
    {
        $this->lookupTable = $this->generateTable($this->poly);
    }

    public function calculate($buffer)
    {
        $bufferLength = strlen($buffer);

        $mask = (((1 << ($this->bitLength - 1)) - 1) << 1) | 1;
        $highBit = 1 << ($this->bitLength - 1);

        $crc = 0xb704ce;

        for ($iterator = 0; $iterator < $bufferLength; ++$iterator) {
            $character = ord($buffer[$iterator]);

            for ($j = 0x80; $j; $j >>= 1) {
                $bit = $crc & $highBit;
                $crc <<= 1;

                if ($character & $j) {
                    $bit ^= $highBit;
                }

                if ($bit) {
                    $crc ^= $this->poly;
                }
            }
        }

        return $crc & $mask;
    }

    protected function generateTable(int $polynomial): array
    {
        $tableSize = 256;

        $mask = (((1 << ($this->bitLength - 1)) - 1) << 1) | 1;
        $highBit = 1 << ($this->bitLength - 1);

        $crctab = [];

        for ($i = 0; $i < $tableSize; ++$i) {
            $crc = $i;

            $crc <<= $this->bitLength - 8;

            for ($j = 0; $j < 8; ++$j) {
                $bit = $crc & $highBit;
                $crc <<= 1;
                if ($bit) {
                    $crc ^= $polynomial;
                }
            }

            $crc &= $mask;
            $crctab[] = $crc;
        }

        return $crctab;
    }
}
