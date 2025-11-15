<?php

/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Util\Jalali;

/**
 * Jalali (Persian) Calendar Date Converter
 * Converts between Gregorian and Jalali dates
 */
class JalaliDate
{
    /**
     * Convert Gregorian date to Jalali
     *
     * @param int $g_y Gregorian year
     * @param int $g_m Gregorian month
     * @param int $g_d Gregorian day
     * @return array [year, month, day]
     */
    public static function gregorianToJalali($g_y, $g_m, $g_d)
    {
        $g_days_in_month = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $j_days_in_month = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];

        $gy = $g_y - 1600;
        $gm = $g_m - 1;
        $gd = $g_d - 1;

        $g_day_no = 365 * $gy + self::div($gy + 3, 4) - self::div($gy + 99, 100) + self::div($gy + 399, 400);

        for ($i = 0; $i < $gm; ++$i) {
            $g_day_no += $g_days_in_month[$i];
        }

        if ($gm > 1 && (($gy % 4 == 0 && $gy % 100 != 0) || ($gy % 400 == 0))) {
            ++$g_day_no;
        }

        $g_day_no += $gd;
        $j_day_no = $g_day_no - 79;
        $j_np = self::div($j_day_no, 12053);
        $j_day_no = $j_day_no % 12053;
        $jy = 979 + 33 * $j_np + 4 * self::div($j_day_no, 1461);
        $j_day_no %= 1461;

        if ($j_day_no >= 366) {
            $jy += self::div($j_day_no - 1, 365);
            $j_day_no = ($j_day_no - 1) % 365;
        }

        if ($j_day_no < 186) {
            $jm = 1 + self::div($j_day_no, 31);
            $jd = 1 + ($j_day_no % 31);
        } else {
            $jm = 7 + self::div($j_day_no - 186, 30);
            $jd = 1 + (($j_day_no - 186) % 30);
        }

        return [$jy, $jm, $jd];
    }

    /**
     * Convert Jalali date to Gregorian
     *
     * @param int $j_y Jalali year
     * @param int $j_m Jalali month
     * @param int $j_d Jalali day
     * @return array [year, month, day]
     */
    public static function jalaliToGregorian($j_y, $j_m, $j_d)
    {
        $g_days_in_month = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $j_days_in_month = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];

        $jy = $j_y - 979;
        $jm = $j_m - 1;
        $jd = $j_d - 1;

        $j_day_no = 365 * $jy + self::div($jy, 33) * 8 + self::div($jy % 33 + 3, 4);

        for ($i = 0; $i < $jm; ++$i) {
            $j_day_no += $j_days_in_month[$i];
        }

        $j_day_no += $jd;
        $g_day_no = $j_day_no + 79;
        $gy = 1600 + 400 * self::div($g_day_no, 146097);
        $g_day_no = $g_day_no % 146097;

        $leap = true;
        if ($g_day_no >= 36525) {
            $g_day_no--;
            $gy += 100 * self::div($g_day_no, 36524);
            $g_day_no = $g_day_no % 36524;

            if ($g_day_no >= 365) {
                $g_day_no++;
            }
            $leap = false;
        }

        $gy += 4 * self::div($g_day_no, 1461);
        $g_day_no %= 1461;

        if ($g_day_no >= 366) {
            $leap = false;
            $g_day_no--;
            $gy += self::div($g_day_no, 365);
            $g_day_no = $g_day_no % 365;
        }

        for ($i = 0; $g_day_no >= $g_days_in_month[$i] + ($i == 1 && $leap ? 1 : 0); $i++) {
            $g_day_no -= $g_days_in_month[$i] + ($i == 1 && $leap ? 1 : 0);
        }

        $gm = $i + 1;
        $gd = $g_day_no + 1;

        return [$gy, $gm, $gd];
    }

    /**
     * Format a date in Jalali format
     *
     * @param string|\DateTime $date Date to format
     * @param string $format Format string (Y = year, m = month, d = day, etc.)
     * @return string Formatted Jalali date
     */
    public static function format($date, $format = 'Y/m/d')
    {
        if ($date instanceof \DateTime) {
            $timestamp = $date->getTimestamp();
        } elseif (is_string($date)) {
            $timestamp = strtotime($date);
        } elseif (is_numeric($date)) {
            $timestamp = $date;
        } else {
            return '';
        }

        if (!$timestamp) {
            return '';
        }

        list($g_y, $g_m, $g_d) = explode('-', date('Y-n-j', $timestamp));
        list($j_y, $j_m, $j_d) = self::gregorianToJalali($g_y, $g_m, $g_d);

        $monthNames = [
            'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
            'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'
        ];

        $dayNames = ['یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنج‌شنبه', 'جمعه', 'شنبه'];

        $result = '';
        $i = 0;
        while ($i < strlen($format)) {
            $char = $format[$i];

            switch ($char) {
                case 'Y':
                    $result .= $j_y;
                    break;
                case 'y':
                    $result .= substr($j_y, 2);
                    break;
                case 'm':
                    $result .= sprintf('%02d', $j_m);
                    break;
                case 'n':
                    $result .= $j_m;
                    break;
                case 'd':
                    $result .= sprintf('%02d', $j_d);
                    break;
                case 'j':
                    $result .= $j_d;
                    break;
                case 'F':
                    $result .= $monthNames[$j_m - 1];
                    break;
                case 'M':
                    $result .= mb_substr($monthNames[$j_m - 1], 0, 3);
                    break;
                case 'l':
                    $result .= $dayNames[date('w', $timestamp)];
                    break;
                case 'D':
                    $result .= mb_substr($dayNames[date('w', $timestamp)], 0, 2);
                    break;
                case 'H':
                    $result .= date('H', $timestamp);
                    break;
                case 'i':
                    $result .= date('i', $timestamp);
                    break;
                case 's':
                    $result .= date('s', $timestamp);
                    break;
                default:
                    $result .= $char;
            }
            $i++;
        }

        return $result;
    }

    /**
     * Parse Jalali date string to Gregorian DateTime
     *
     * @param string $jalaliDate Date in format Y/m/d or Y-m-d
     * @return \DateTime|null
     */
    public static function parse($jalaliDate)
    {
        // Handle both / and - separators
        $parts = preg_split('/[\/\-]/', $jalaliDate);

        if (count($parts) !== 3) {
            return null;
        }

        list($j_y, $j_m, $j_d) = $parts;

        // Validate Jalali date
        if ($j_m < 1 || $j_m > 12 || $j_d < 1 || $j_d > 31) {
            return null;
        }

        list($g_y, $g_m, $g_d) = self::jalaliToGregorian($j_y, $j_m, $j_d);

        try {
            return new \DateTime(sprintf('%04d-%02d-%02d', $g_y, $g_m, $g_d));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Integer division
     */
    private static function div($a, $b)
    {
        return (int) ($a / $b);
    }

    /**
     * Check if a Jalali year is leap year
     *
     * @param int $year Jalali year
     * @return bool
     */
    public static function isLeapYear($year)
    {
        $breaks = [-61, 9, 38, 199, 426, 686, 756, 818, 1111, 1181, 1210, 1635, 2060, 2097, 2192, 2262, 2324, 2394, 2456, 3178];
        $bl = count($breaks);
        $gy = $year + 621;
        $leapJ = -14;
        $jp = $breaks[0];

        for ($i = 1; $i < $bl; $i += 1) {
            $jm = $breaks[$i];
            $jump = $jm - $jp;

            if ($year < $jm) {
                break;
            }

            $leapJ = $leapJ + self::div($jump, 33) * 8 + self::div(($jump % 33), 4);
            $jp = $jm;
        }

        $n = $year - $jp;

        if (($jump % 33) === 4 && ($jump - $n) === 4) {
            $leapJ += 1;
        }

        $leapG = self::div($gy, 4) - self::div((self::div($gy, 100) + 1) * 3, 4) - 150;
        $march = 20 + $leapJ - $leapG;

        if (($jump - $n) < 6) {
            $n = $n - $jump + self::div($jump + 4, 33) * 33;
        }

        $leap = ((($n + 1) % 33) - 1) % 4;

        if ($leap === -1) {
            $leap = 4;
        }

        return $leap === 0;
    }
}
