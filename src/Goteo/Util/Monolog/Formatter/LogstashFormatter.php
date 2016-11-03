<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Util\Monolog\Formatter;

use Monolog\Formatter\LogstashFormatter as MonologLogstashFormatter;

/**
 * Serializes a log message to Logstash Event Format
 * Does exactly the same as the orginal LogstashFormatter withouy throwin an Execption with invalid JSON encodings
 *
 * @see http://logstash.net/
 * @see https://github.com/logstash/logstash/blob/master/lib/logstash/event.rb
 *
 * @author Tim Mower <timothy.mower@gmail.com>
 */
class LogstashFormatter extends MonologLogstashFormatter
{
    /**
     * {@inheritdoc}
     */
    public function format(array $record)
    {
        $record = $this->normalize($record);

        if ($this->version === self::V1) {
            $message = $this->formatV1($record);
        } else {
            $message = $this->formatV0($record);
        }

        return $this->toJson($message, true) . "\n";
    }
}
