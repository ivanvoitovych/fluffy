<?php

namespace Fluffy\Services;

class UtilsService
{
    static function uuidv4($data = null)
    {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    static function randomHex(int $length = 16)
    {
        $data = random_bytes($length);
        return bin2hex($data);
    }

    static function randomInt(int $length = 8)
    {
        $data = random_int(10 ** ($length - 1), (10 ** $length) - 1);
        return $data;
    }

    static function randomString(int $length = 32)
    {
        $bytes = random_bytes($length);
        // var_dump($bytes);
        // echo base64_encode($bytes) . PHP_EOL;
        $randomString = substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $length);
        // echo $randomString . PHP_EOL;
        // echo strlen($randomString) . PHP_EOL;
        return $randomString;
    }

    static function splitName($name)
    {
        $name = trim($name);
        $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        $first_name = trim(preg_replace('#' . preg_quote($last_name, '#') . '#', '', $name));
        return array($first_name, $last_name);
    }

    static function GetMicroTime(): int
    {
        $timeOfDay = gettimeofday();
        return $timeOfDay['sec'] * 1000000 + $timeOfDay['usec'];
    }
}
