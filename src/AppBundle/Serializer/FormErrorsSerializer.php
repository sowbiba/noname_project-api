<?php

namespace AppBundle\Serializer;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;

/**
 * Symfony 2 Form Error Serializer. Allows tree and flat array styles for errors.
 *
 * @see https://github.com/temenb/FormErrorsSerializer/blob/master/src/FormErrorsSerializer/FormErrorsSerializer.php
 * Some parts of the code as been improved.
 */
final class FormErrorsSerializer
{
    /**
     * @param Form   $form
     * @param bool   $flatArray
     * @param bool   $addFormName
     * @param string $glueKeys
     *
     * @return array
     */
    public static function serializeFormErrors(Form $form, $flatArray = false, $addFormName = false, $glueKeys = '_')
    {
        $errors = [];
        $errors['global'] = [];
        $errors['fields'] = [];

        foreach ($form->getErrors() as $error) {
            $errors['global'][] = [
                'code' => $error->getCause()->getCode(),
                'message' => $error->getMessage(),
            ];
        }

        $errors['fields'] = static::serialize($form);

        if ($flatArray) {
            $errors['fields'] = static::arrayFlatten($errors['fields'], $glueKeys, $addFormName ? $form->getName() : '');
        }

        return $errors;
    }

    /**
     * @param Form $form
     *
     * @return array
     */
    private static function serialize(Form $form)
    {
        $localErrors = [];

        foreach ($form->getIterator() as $key => $child) {
            foreach ($child->getErrors() as $error) {
                $localErrors[$key] = [
                    'code' => $error->getCause()->getCode(),
                    'message' => $error->getMessage(),
                ];
            }

            if ($child instanceof Form && 0 < count($child->getIterator())) {
                if ($error = static::serialize($child) instanceof FormError) {
                    $localErrors[$key] = [
                        'code' => $error->getCause()->getCode(),
                        'message' => $error->getMessage(),
                    ];
                }
            }
        }

        return $localErrors;
    }

    /**
     * @param $array
     * @param string $separator
     * @param string $flattenedKey
     *
     * @return array
     */
    private static function arrayFlatten($array, $separator = '_', $flattenedKey = '')
    {
        $flattenedArray = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $flattenedArray = array_merge(
                    $flattenedArray,
                    static::arrayFlatten(
                        $value,
                        $separator,
                        strlen($flattenedKey) > 0 ? $flattenedKey.$separator : ''
                    ).$key
                );
            } else {
                $flattenedArray[(strlen($flattenedKey) > 0 ? $flattenedKey.$separator : '').$key] = $value;
            }
        }

        return $flattenedArray;
    }
}
