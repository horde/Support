<?php
/**
 * Copyright 2010-2017 Horde LLC (http://www.horde.org/)
 *
 * @author   Chuck Hagenbuch <chuck@horde.org>
 * @license  http://www.horde.org/licenses/bsd BSD
 * @category Horde
 * @package  Support
 */

namespace Horde\Support;

use Horde_String;

/**
 * @author   Chuck Hagenbuch <chuck@horde.org>
 * @license  http://www.horde.org/licenses/bsd BSD
 * @category Horde
 * @package  Support
 */
class Numerizer
{
    public static function numerize($string, $args = [])
    {
        return self::factory($args)->numerize($string);
    }

    public static function factory($args = [])
    {
        $locale = $args['locale'] ?? null;
        if ($locale && Horde_String::lower($locale) != 'base') {
            $locale = str_replace(' ', '_', Horde_String::ucwords(str_replace('_', ' ', Horde_String::lower($locale))));
            $class = '\Horde\Support\Numerizer\Locale\\' . $locale;
            if (class_exists($class)) {
                return new $class($args);
            }

            [$language, ] = explode('_', $locale);
            if ($language != $locale) {
                $class = 'Horde\Support\Numerizer\Locale\\' . $language;
                if (class_exists($class)) {
                    return new $class($args);
                }
            }
        }

        return new \Horde\Support\Numerizer\Locale\Base($args);
    }
}
