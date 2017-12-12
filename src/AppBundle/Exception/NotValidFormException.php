<?php

namespace AppBundle\Exception;

use Symfony\Component\Form\Form;

/**
 * Thrown to handle Symfony 2 form validation failures.
 */
class NotValidFormException extends \Exception
{
    /**
     * @var Form
     */
    private $form;

    /**
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->form = $form;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }
}
