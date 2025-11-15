<?php

/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Util\Foil\Extension;

use Foil\Contracts\ExtensionInterface;
use Goteo\Util\Jalali\JalaliDate;
use Goteo\Application\Lang;

/**
 * Jalali (Persian) Date utilities for Foil templates
 */
class JalaliUtils implements ExtensionInterface
{
    private $args;

    public function setup(array $args = [])
    {
        $this->args = $args;
    }

    public function provideFilters()
    {
        return [
            'jalali' => [$this, 'jalali'],
            'jalali_format' => [$this, 'jalaliFormat'],
        ];
    }

    public function provideFunctions()
    {
        return [
            'jalali_date' => [$this, 'jalali'],
            'jalali_format' => [$this, 'jalaliFormat'],
            'is_jalali_lang' => [$this, 'isJalaliLang'],
        ];
    }

    /**
     * Convert date to Jalali format
     *
     * @param mixed $date Date to convert
     * @param string $format Format string
     * @return string
     */
    public function jalali($date, $format = 'Y/m/d')
    {
        if (empty($date)) {
            return '';
        }

        return JalaliDate::format($date, $format);
    }

    /**
     * Format date based on current language
     * Uses Jalali for Persian (fa), Gregorian for others
     *
     * @param mixed $date Date to format
     * @param string $jalaliFormat Format for Jalali dates
     * @param string $gregorianFormat Format for Gregorian dates
     * @return string
     */
    public function jalaliFormat($date, $jalaliFormat = 'Y/m/d', $gregorianFormat = 'd/m/Y')
    {
        if (empty($date)) {
            return '';
        }

        if ($this->isJalaliLang()) {
            return JalaliDate::format($date, $jalaliFormat);
        }

        if ($date instanceof \DateTime) {
            return $date->format($gregorianFormat);
        }

        $timestamp = is_numeric($date) ? $date : strtotime($date);
        return date($gregorianFormat, $timestamp);
    }

    /**
     * Check if current language uses Jalali calendar
     *
     * @return bool
     */
    public function isJalaliLang()
    {
        $lang = Lang::current();
        return in_array($lang, ['fa']);
    }
}
