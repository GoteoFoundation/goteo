<?php

/*
 * This file is part of ansi-to-html.
 *
 * (c) 2013 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goteo\Util\AnsiConverter\Theme;
use SensioLabs\AnsiConverter\Theme\Theme;

/**
 * Solarized theme.
 *
 * @see http://ethanschoonover.com/solarized
 */
class SolarizedLightTheme extends Theme
{
    public function asArray()
    {
        return array(
            // normal
            'black' => '#ffffff',

            'red' => '#dc322f',
            'green' => '#859900',
            'yellow' => '#b58900',
            'blue' => '#268bd2',
            'magenta' => '#d33682',
            'cyan' => '#2aa198',

            'white' => '#073642',

            // bright
            'brblack' => '#ffffd7',

            'brred' => '#d75f00',
            'brgreen' => '#4e4e4e',
            'bryellow' => '#657b83',
            'brblue' => '#839496',
            'brmagenta' => '#6c71c4',
            'brcyan' => '#93a1a1',

            'brwhite' => '#1c1c1c',
        );
    }
}
