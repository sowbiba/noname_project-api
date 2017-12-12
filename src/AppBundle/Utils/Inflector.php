<?php

namespace AppBundle\Utils;

final class Inflector
{
    /**
     * Transforms a camelCase string to underscored.
     *
     * @example
     *      myWord -> my_word
     *      MySuperWord -> my_super_word
     *
     * @param string $value
     *
     * @return mixed
     */
    public static function underscore($value)
    {
        $value[0] = strtolower($value[0]);
        $func = create_function('$c', 'return "_" . strtolower($c[1]);');

        return preg_replace_callback('/([A-Z])/', $func, $value);
    }
}
