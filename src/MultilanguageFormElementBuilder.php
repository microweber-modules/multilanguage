<?php

namespace MicroweberPackages\Multilanguage;

use MicroweberPackages\Form\FormElementBuilder;
use MicroweberPackages\Multilanguage\FormElements\Text;

class MultilanguageFormElementBuilder extends FormElementBuilder
{

    protected $formElementsClasses = [
        'Text'=>Text::class
    ];
}
