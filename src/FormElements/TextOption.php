<?php
namespace MicroweberPackages\Multilanguage\FormElements;

use MicroweberPackages\Multilanguage\FormElements\Traits\JavascriptChangerTrait;

class TextOption extends \MicroweberPackages\Form\Elements\TextOption
{
    protected $attributes = [
        'type' => 'text',
        'class'=>'form-control mw_option_field '
    ];

    public $randId;
    public $defaultLanguage;

    use JavascriptChangerTrait;

    public function render()
    {
        $inputValue = '';

        $this->defaultLanguage = mw()->lang_helper->default_lang();
        $supportedLanguages = get_supported_languages(true);

        $modelAttributes = [];
        if ($this->model) {
            $modelAttributes = $this->model->getAttributes();
            $inputValue = $this->model->option_value;
            $this->setValue($inputValue);
        }

        $this->randId = random_int(111,999).time();

        $html = $this->getJavaScript();

        $html .= '<div class="input-group mb-3 append-transparent"><input type="text" '.$this->renderAttributes().' id="js-multilanguage-text-' . $this->randId . '">';

        foreach($supportedLanguages as $language) {
            $value = '';
            if (isset($modelAttributes['multilanguage'])) {
                foreach ($modelAttributes['multilanguage'] as $locale => $multilanguageFields) {
                    if ($locale == $language['locale']) {
                        $value = $multilanguageFields['option_value'];
                        break;
                    }
                }
            }
            $html .= '<input type="hidden" class="js-multilanguage-value-lang-'.$this->randId.'"  lang="'.$language['locale'].'" value="'.$value.'">';
        }

        $html .= '
        <div class="input-group-append">
            <span>
                <select class="selectpicker"  id="js-multilanguage-select-lang-'.$this->randId.'" data-width="100%">';

                    foreach($supportedLanguages as $language) {
                        $langData = \MicroweberPackages\Translation\LanguageHelper::getLangData($language['locale']);
                        $flagIcon = "<i class='flag-icon flag-icon-".$language['icon']."'></i> " . strtoupper($langData['language']);
                        $html .= '<option data-content="'.$flagIcon.'" value="'.$language['locale'].'"></option>';
                    }

            $html .= '</select>
           </span>
        </div>
    </div>';

       return $html;
    }

}
