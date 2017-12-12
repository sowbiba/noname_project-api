<?php

namespace AppBundle\Utils;

/**
 * Provides a `generate` method which generate an unique hash.
 */
final class HashGenerator
{
    /**
     * @return string
     */
    public static function generate()
    {
        return sha1(base_convert(uniqid(mt_rand(), true), 16, 36));
    }
}
