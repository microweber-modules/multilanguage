<?php
namespace MicroweberPackages\Multilanguage\FormElements;

class Text extends \MicroweberPackages\Form\Elements\Text
{
    public function render()
    {

        $inputName = $this->getAttribute('name');

        $supportedLanguages = get_supported_languages(true);

        $attributes = [];
        $model = $this->getModel();
        if ($model) {
            $attributes = $model->getAttributes();
        }

       $randId = random_int(111,999).time();

        //fieldValue'.$randId.".value = "MANQK LI SI";
        // inputText'.$randId.'.value = '';

        $html = '
                <script>
                    const selectLang'.$randId.' = document.getElementById("js-multilanguage-select-lang-'.$randId.'");
                    selectLang'.$randId.'.addEventListener("change", (event) => {
                      const inputText'.$randId.' = document.getElementById("js-multilanguage-text-'.$randId.'");

                    });
                </script>

                <div class="input-group mb-3 append-transparent">
                ';

            /*    if (isset($attributes['multilanguage'])) {
                    foreach($attributes['multilanguage'] as $locale=>$multilanguageFields) {
                        foreach($multilanguageFields as $fieldName=>$value) {
                            $html .= '<input type="text" class="form-control" id="js-multilanguage-text-' . $randId . '" value="' . $value . '">';
                        }
                    }
                }*/

                foreach($supportedLanguages as $language) {

                    $this->setAttribute('lang', $language['locale']);
                    $this->setAttribute('name', $inputName);

                    $html .= '<input type="text" '.$this->renderAttributes().'
                            id="js-multilanguage-text-' . $randId . '" value="">';
                }

                $html .= '
                <div class="input-group-append">
                    <span style="width:70px;">
                        <select class="selectpicker"  id="js-multilanguage-select-lang-'.$randId.'" data-width="100%">';

                    foreach($supportedLanguages as $language) {
                        $flagIcon = "<i class='flag-icon flag-icon-".$language['icon']."' style='font-size:18px'></i>";
                        $html .= '<option data-content="'.$flagIcon.'" value=""></option>';
                    }

       $html .= '</select>
                   </span>
                </div>
            </div>';

       return $html;
    }

}
