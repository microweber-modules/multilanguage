<?php

namespace MicroweberPackages\Multilanguage;

use MicroweberPackages\Form\Elements\TextArea;
use MicroweberPackages\Form\FormElementBuilder;
use MicroweberPackages\Multilanguage\FormElements\Text;
use MicroweberPackages\Multilanguage\FormElements\TextAreaOption;
use MicroweberPackages\Multilanguage\FormElements\TextOption;

class MultilanguageFormElementBuilder extends FormElementBuilder
{
    protected $formElementsClasses = [
        'Text'=>Text::class,
        'TextOption'=>TextOption::class,
        'TextArea'=>TextArea::class,
        'TextAreaOption'=>TextAreaOption::class,
    ];
}
