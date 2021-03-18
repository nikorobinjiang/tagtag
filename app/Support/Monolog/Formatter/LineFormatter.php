<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Support\Monolog\Formatter;

use Exception;
use Monolog\Utils;
use Monolog\Formatter\LineFormatter as MonologLineFormatter;

/**
 * Normalizes incoming records to remove objects/resources so it's easier to dump to various targets
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class LineFormatter extends MonologLineFormatter
{
    protected function normalize($data, $depth = 0)
    {
        if ($depth > 40) {
            return 'Over 20 levels deep, aborting normalization';
        }

        if (null === $data || is_scalar($data)) {
            if (is_float($data)) {
                if (is_infinite($data)) {
                    return ($data > 0 ? '' : '-') . 'INF';
                }
                if (is_nan($data)) {
                    return 'NaN';
                }
            }

            return $data;
        }

        if (is_array($data)) {
            $normalized = array();

            $count = 1;
            foreach ($data as $key => $value) {
                if ($count++ > 1000) {
                    $normalized['...'] = 'Over 1000 items (' . count($data) . ' total), aborting normalization';
                    break;
                }

                $normalized[$key] = $this->normalize($value, $depth + 1);
            }

            return $normalized;
        }

        if ($data instanceof \DateTime) {
            return $data->format($this->dateFormat);
        }

        if (is_object($data)) {
            // TODO 2.0 only check for Throwable
            if ($data instanceof Exception || (PHP_VERSION_ID > 70000 && $data instanceof \Throwable)) {
                return $this->normalizeException($data);
            }

            // non-serializable objects that implement __toString stringified
            if (method_exists($data, '__toString') && !$data instanceof \JsonSerializable) {
                $value = $data->__toString();
            } else {
                // the rest is json-serialized in some way
                $value = $this->toJson($data, true);
            }

            return sprintf("[object] (%s: %s)", Utils::getClass($data), $value);
        }

        if (is_resource($data)) {
            return sprintf('[resource] (%s)', get_resource_type($data));
        }

        return '[unknown(' . gettype($data) . ')]';
    }
}
