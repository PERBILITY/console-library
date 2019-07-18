<?php
namespace Perbility\Console\Util;

/**
 * Unit based helper methods.
 */
class UnitUtils
{
    const BYTE_UNIT_SUFFIXES = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'YB'];
    const BYTE_UNIT_STRING_PATTERN = '#^(\d+)\s*(B|KB|MB|GB|TB|PB|EB|YB)?$#';
    
    const BYTES_ONE_MEGABYTE = 1048576;
    const BYTES_TWO_MEGABYTE = 2097152;
    
    /**
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    public static function formatBytes($bytes, $precision = 2)
    {
        if ($bytes === 0) {
            return "0 B";
        }
        
        if ($bytes < 0) {
            return '-' . self::formatBytes(abs($bytes), $precision);
        }
        
        $exponent = (int) floor(log($bytes, 1024));
        if ($exponent == 0) {
            return sprintf('%d %s', $bytes, self::BYTE_UNIT_SUFFIXES[0]);
        }
        return sprintf("%.{$precision}f %s", $bytes / pow(1024, $exponent), self::BYTE_UNIT_SUFFIXES[$exponent]);
    }
    
    /**
     * Converts the given shorthand byte notation to a representation in bytes (integer)
     *
     * @param string|null $shortHandByteValue
     *
     * @return int|null
     *
     * @throws \InvalidArgumentException
     */
    public static function convertToBytes($shortHandByteValue = null)
    {
        if (null === $shortHandByteValue) {
            return null;
        }
    
        $normalizedString = strtoupper(trim($shortHandByteValue));
        
        if (!preg_match(self::BYTE_UNIT_STRING_PATTERN, $normalizedString, $matches)) {
            throw new \InvalidArgumentException('Invalid byte unit given in string (' . $shortHandByteValue . ')');
        }
        
        // $matches[1] => value
        $value = $matches[1];
        // $matches[2] => unit, if no unit is given use byte as fallback
        $unit = isset($matches[2]) ? $matches[2] : 'B';
        
        return $value * pow(1024, array_flip(self::BYTE_UNIT_SUFFIXES)[$unit]);
    }
    
    /**
     * Interprets a numeric value as seconds and converts it to a string similar to the format H:i:s
     * Note: Excess hours are not converted to days, months etc. and just remain (possibly many) hours
     *
     * @param int|float $value
     * @return string
     */
    public static function formatSeconds($value)
    {
        if (!is_numeric($value)) {
            throw new \InvalidArgumentException(
                __METHOD__ . ' expects a number representing a value in seconds, got "' . gettype($value) . '"."'
            );
        }
        $t = round($value);
        if ($t > 86400) {
            return sprintf(
                '%02d:%02d:%02d:%02d',
                ($t / 86400),
                (($t / 3600) % 24),
                (($t / 60) % 60),
                $t % 60
            );
        } else {
            return sprintf(
                '%02d:%02d:%02d',
                ($t / 3600),
                (($t / 60) % 60),
                $t % 60
            );
        }
    }
}
