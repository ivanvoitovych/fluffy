<?php

namespace Fluffy\Domain\CronTab;

use Exception;

class CronTabTest
{
    public static function TEST()
    {
        // test
        $timestamp = time();
        foreach ([
            // '* * * * *',
            // '*/59 * * * *',
            // '*/7 * * * *',
            // '59 * * * *',
            // '7 * * * *',
            // '* * * * * *',
            // '5 * * * * *',
            // '10 * * * * *',
            // '*/5 * * * * *',
            // '*/20 * * * * *',
            // '*/7 * * * * *',
            // // '* * * * *',
            // // '5 * * * *',
            // // '10 * * * *',
            // // '47 * * * *',
            // '*/5 * * * *',
            // // '*/20 * * * *',
            // '*/7 15 * * *',
            // '*/7 5 * * *',
            // '0 6 * * *',
            // '0 5 5 * *',
            // '0 5 */3 * *',
            // '0 5 */3 12 *',
            // '0 5 1 3 *',
            // '0 */3 5 6 *',
            '0 0 28 * *',
            '0 0 31 * *'
        ] as $expression) {
            echo "[TEST] ======================================" . PHP_EOL;
            echo "[TEST] $expression" . PHP_EOL;
            foreach ([
                // $timestamp, // now
                // 1686217500,
                // $timestamp - $timestamp % 60, // 0 seconds
                // $timestamp - $timestamp % 60 + 5, // +5 sec
                // $timestamp - $timestamp % 60 + 33, // +33 sec
                // $timestamp - $timestamp % 60 + 59, // +59 sec
                // $timestamp - (($timestamp / 60) % 60 - 5) * 60, // +5 min
                // $timestamp - (($timestamp / 60) % 60 - 33) * 60, // +33 min
                // $timestamp - (($timestamp / 60) % 60 - 7) * 60, // +7 min

                // $timestamp - (($timestamp / 60) % 60 - 33) * 60 - $timestamp % 60 + 59, // +33 min 59 sec
                // $timestamp - (($timestamp / 60) % 60 - 56) * 60 - $timestamp % 60 + 59, // +56 min 59 sec
                // $timestamp - (($timestamp / 60) % 60 - 59) * 60 - $timestamp % 60 + 59, // +59 min 59 sec
                // $timestamp - (($timestamp / 60) % 60 - 0) * 60 - $timestamp % 60 + 0, // +0 min 0 sec

                // strtotime('2023-06-09 11:59:59 UTC'),
                // strtotime('2023-06-01 11:59:59 UTC'),
                // strtotime('2023-06-04 11:59:59 UTC'),
                // strtotime('2023-06-05 00:00:00 UTC'),
                // strtotime('2024-12-30 00:00:00 UTC'),
                // strtotime('2023-06-09 23:59:59 UTC'),
                // strtotime('2023-06-10 04:59:00 UTC'),
                // strtotime('2023-06-10 04:59:56 UTC'),
                // strtotime('2023-06-10 04:59:59 UTC'),
                // strtotime('2023-06-10 05:00:00 UTC'),
                // strtotime('2023-01-01 05:59:59 UTC'),
                // strtotime('2024-01-01 05:59:59 UTC'),
                // strtotime('2023-12-31 05:59:59 UTC'),
                // strtotime('2024-12-31 05:59:59 UTC'),
                strtotime('2022-02-28 00:00:00 UTC'),
                strtotime('2023-02-28 00:00:00 UTC'),
                strtotime('2024-02-28 00:00:00 UTC'),
                strtotime('2022-01-31 00:00:00 UTC'),
                strtotime('2023-01-31 00:00:00 UTC'),
                strtotime('2024-01-31 00:00:00 UTC'),
                strtotime('2023-03-31 00:00:00 UTC'),

                strtotime('2022-12-31 00:00:00 UTC'),
                strtotime('2023-12-31 00:00:00 UTC'),
                strtotime('2024-12-31 00:00:00 UTC'),

                strtotime('2022-12-28 00:00:00 UTC'),
                strtotime('2023-12-28 00:00:00 UTC'),
                strtotime('2024-12-28 00:00:00 UTC')
            ] as $time) {
                $now = $time;
                $nowDate = gmdate('w Y-m-d H:i:s', $now);
                echo "[TEST] -----------------------------------" . PHP_EOL;
                echo "[TEST] $nowDate $now" . PHP_EOL;
                $nextRunDate = gmdate('w Y-m-d H:i:s', CronTab::getNextRun($expression, $now));
                $secondsMessage = count(explode(' ', $expression)) === 6 ? 'Seconds' : 'Minutes';

                echo "[TEST] $nextRunDate $secondsMessage" . PHP_EOL;
            }
        }

        echo "[TEST] ALL MANUAL PASSED" . PHP_EOL;



        $expression = '0 0 31 * *';
        $expression = '0 0 31 * *';
        foreach ([2022, 2023, 2024] as $year) {
            $time = strtotime($year . '-01-01 00:00:00 UTC');
            for ($i = 0; $i < 20; $i++) {
                $next = CronTab::getNextRun($expression, $time);
                if (gmdate('d H:i:s', $next) !== '31 00:00:00') {
                    throw new Exception("Calculation has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
                }
                $time = $next;
            }
        }
        foreach ([2022, 2023, 2024] as $year) {
            $time = strtotime($year . '-12-31 00:00:00 UTC');
            $next = CronTab::getNextRun($expression, $time);
            if (gmdate('Y-m-d H:i:s', $next) !== ($year + 1) . '-01-31 00:00:00') {
                throw new Exception("Calculation has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
            $time = strtotime($year . '-01-31 00:00:00 UTC');
            $next = CronTab::getNextRun($expression, $time);
            if (gmdate('Y-m-d H:i:s', $next) !== $year . '-03-31 00:00:00') {
                throw new Exception("Calculation has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
        }

        $expression = '0 0 28 * *';
        foreach ([2022, 2023, 2024] as $year) {
            $time = strtotime($year . '-12-28 00:00:00 UTC');
            $next = CronTab::getNextRun($expression, $time);
            if (gmdate('Y-m-d H:i:s', $next) !== ($year + 1) . '-01-28 00:00:00') {
                throw new Exception("Calculation has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
            $time = strtotime($year . '-01-28 00:00:00 UTC');
            $next = CronTab::getNextRun($expression, $time);
            if (gmdate('Y-m-d H:i:s', $next) !== $year . '-02-28 00:00:00') {
                throw new Exception("Calculation has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
            $time = strtotime($year . '-01-28 00:00:01 UTC');
            $next = CronTab::getNextRun($expression, $time);
            if (gmdate('Y-m-d H:i:s', $next) !== $year . '-02-28 00:00:00') {
                throw new Exception("Calculation has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
            $time = strtotime($year . '-01-31 00:00:00 UTC');
            $next = CronTab::getNextRun($expression, $time);
            if (gmdate('Y-m-d H:i:s', $next) !== $year . '-02-28 00:00:00') {
                throw new Exception("Calculation has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
            $time = strtotime($year . '-01-27 00:00:00 UTC');
            $next = CronTab::getNextRun($expression, $time);
            if (gmdate('Y-m-d H:i:s', $next) !== $year . '-01-28 00:00:00') {
                throw new Exception("Calculation has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
            $time = strtotime($year . '-01-27 23:59:59 UTC');
            $next = CronTab::getNextRun($expression, $time);
            if (gmdate('Y-m-d H:i:s', $next) !== $year . '-01-28 00:00:00') {
                throw new Exception("Calculation has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
        }
        $expression = '0 0 29 * *';
        foreach ([2022, 2023, 2024] as $year) {
            $time = strtotime($year . '-12-29 00:00:00 UTC');
            $next = CronTab::getNextRun($expression, $time);
            if (gmdate('Y-m-d H:i:s', $next) !== ($year + 1) . '-01-29 00:00:00') {
                throw new Exception("Calculation has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
            $time = strtotime($year . '-01-29 00:00:00 UTC');
            $next = CronTab::getNextRun($expression, $time);
            if (gmdate('Y-m-d H:i:s', $next) !== ($year % 4 === 0 ? $year . '-02-29 00:00:00' : $year . '-03-29 00:00:00')) {
                throw new Exception("Calculation has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
            $time = strtotime($year . '-01-29 00:00:01 UTC');
            $next = CronTab::getNextRun($expression, $time);
            if (gmdate('Y-m-d H:i:s', $next) !== ($year % 4 === 0 ? $year . '-02-29 00:00:00' : $year . '-03-29 00:00:00')) {
                throw new Exception("Calculation has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
            $time = strtotime($year . '-01-31 00:00:00 UTC');
            $next = CronTab::getNextRun($expression, $time);
            if (gmdate('Y-m-d H:i:s', $next) !== ($year % 4 === 0 ? $year . '-02-29 00:00:00' : $year . '-03-29 00:00:00')) {
                throw new Exception("Calculation has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
            $time = strtotime($year . '-01-27 00:00:00 UTC');
            $next = CronTab::getNextRun($expression, $time);
            if (gmdate('Y-m-d H:i:s', $next) !== $year . '-01-29 00:00:00') {
                throw new Exception("Calculation has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
            $time = strtotime($year . '-01-28 23:59:59 UTC');
            $next = CronTab::getNextRun($expression, $time);
            if (gmdate('Y-m-d H:i:s', $next) !== $year . '-01-29 00:00:00') {
                throw new Exception("Calculation has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
        }
        echo "[TEST] ALL EDGE CASES PASSED" . PHP_EOL;
        // return;
        // test time
        $now = strtotime('2023-01-01 00:00:00 UTC');
        $expression = '0 * * * * *';
        for ($i = 0; $i < 60; $i++) {
            $time = $now + $i;
            $next = CronTab::getNextRun($expression, $time);
            if ($next <= $time) {
                throw new Exception("Calculation has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
            if ($next % 60 !== 0) {
                throw new Exception("Calculation has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
            if ($next !== $time + 60 - $i) {
                throw new Exception("Calculation has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
            // echo "[TEST] " . gmdate('Y-m-d H:i:s', $time) . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL;
        }
        $expression = '0 30 * * * *';
        for ($i = 0; $i < 60; $i++) {
            $time = $now + $i;
            $next = CronTab::getNextRun($expression, $time);
            // echo gmdate('Y-m-d H:i:s', $time) . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL;
            if ($next <= $time) {
                throw new Exception("Calculation has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
            if ($next % 60 !== 0) {
                throw new Exception("Calculation has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
            if (gmdate('Y-m-d H:i:s', $next) !== '2023-01-01 00:30:00') {
                throw new Exception("Calculation has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
        }
        $expression = '* * * * * *';
        for ($i = 0; $i < 60; $i++) {
            $time = $now + $i;
            $next = CronTab::getNextRun($expression, $time);
            // echo gmdate('Y-m-d H:i:s', $time) . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL;
            if ($next <= $time) {
                throw new Exception("Next <= time has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
            if ($next - $time !== 1) {
                throw new Exception("next - now === 1 has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
        }
        $expression = '3 * * * * *';
        for ($i = 0; $i < 60; $i++) {
            $time = $now + $i;
            $next = CronTab::getNextRun($expression, $time);
            // echo gmdate('Y-m-d H:i:s', $time) . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL;
            if ($next <= $time) {
                throw new Exception("Next <= time has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
            if ($next % 60 !== 3) {
                throw new Exception("% 3 has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
            if ($next > $time + 60) {
                throw new Exception("less than 60 sec has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
            if ($i < 3 && $next > $time + 3) {
                throw new Exception("less than 3 sec has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
            if ($i >= 3 && $next / 60 % 60 !== 1) {
                throw new Exception("minute should change has failed $expression $time $next " . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
        }
        $expression = '0 0 31 * *';
        $now = strtotime('2023-01-01 00:00:00 UTC');
        $daysOfMonth = [
            31, 28, 31, 30, 31, 30,
            31, 31, 30, 31, 30, 31
        ];
        for ($i = 0; $i < 60; $i++) {
            $time = $now + $i * 24 * 60 * 60;
            // echo gmdate('Y-m-d H:i:s', $time) . PHP_EOL;
            $next = CronTab::getNextRun($expression, $time);
            // echo gmdate('Y-m-d H:i:s', $next) . PHP_EOL;
            if (gmdate('Y-d H:i:s', $next) !== '2023-31 00:00:00') {
                throw new Exception("Calculation date has failed $expression $time $next i=$i" . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
            $nextM = gmdate('n', $next);
            $expectedM = (intdiv($i, 30) + 1);
            if ($daysOfMonth[$expectedM - 1] < 31) {
                $expectedM++;
            }
            if ($nextM !== $expectedM . '') {
                throw new Exception("Calculation month has failed $expression $time $next i=$i m=$nextM expected m=$expectedM" . PHP_EOL . gmdate('Y-m-d H:i:s', $time)  . PHP_EOL . gmdate('Y-m-d H:i:s', $next) . PHP_EOL);
            }
        }
        echo "[TEST] ALL AUTOMATED PASSED" . PHP_EOL;
    }
}
