<?php

namespace AppBundle\Utils;

/**
 * Provides methods to check if an object or an array of object of same type implements a specific interface or
 * uses a specific trait.
 */
final class Implementer
{
    private static $interface = 'interface';
    private static $trait = 'trait';

    /**
     * @param $element
     * @param $interface
     *
     * @return bool
     */
    public static function implementsInterface($element, $interface)
    {
        return static::implementsInterfaceOrTrait(static::$interface, $element, $interface);
    }

    /**
     * @param $element
     * @param $trait
     *
     * @return bool
     */
    public static function usesTrait($element, $trait)
    {
        return static::implementsInterfaceOrTrait(static::$trait, $element, $trait);
    }

    /**
     * @param $type
     * @param $element
     * @param $class
     *
     * @return bool
     */
    private static function implementsInterfaceOrTrait($type, $element, $class)
    {
        $implementsOrUses = false;

        if (is_array($element)) {
            if (empty($element)) {
                return false;
            }

            foreach ($element as $el) {
                return static::implementsInterfaceOrTrait($type, $el, $class);
            }
        }

        $classes = [];

        switch ($type) {
            case static::$interface:
                $classes = class_implements($element);
                break;
            case static::$trait:
                $classes = class_uses($element);
                break;
        }

        if (isset($classes[$class])) {
            $implementsOrUses = true;
        }

        return $implementsOrUses;
    }
}
