<?php

namespace MicroweberPackages\Multilanguage;

use MicroweberPackages\Form\FormElementBuilder;
use MicroweberPackages\Multilanguage\FormElements\TextOption;

class MultilanguageFormElementBuilder extends FormElementBuilder
{
    protected $formElementsClasses = [
        'TextOption'=>TextOption::class
    ];
}
