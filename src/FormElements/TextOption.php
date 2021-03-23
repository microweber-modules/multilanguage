<?php
namespace MicroweberPackages\Multilanguage\FormElements;

class TextOption extends \MicroweberPackages\Form\Elements\TextOption
{
    public function render()
    {
        $inputValue = '';

        $defaultLanguage = mw()->lang_helper->default_lang();
        $supportedLanguages = get_supported_languages(true);

        $modelAttributes = [];
        $model = \MicroweberPackages\Option\Models\ModuleOption::where('option_key', $this->optionKey)->where('option_group', $this->optionGroup)->first();
        if ($model) {
            $modelAttributes = $model->getAttributes();
            $inputValue = $model->option_value;
            $this->setValue($inputValue);
        }

       $randId = random_int(111,999).time();

        $html = '
                <script>
                    function runMlField' . $randId . '() {
                        var selectLang = document.getElementById("js-multilanguage-select-lang-' . $randId . '");
                        selectLang.value = "'.$defaultLanguage.'";
                        selectLang.addEventListener("change", (event) => {
                          var inputText = document.getElementById("js-multilanguage-text-' . $randId . '");
                          var currentLangSelected = selectLang.value;
                          var currentTextLang =  document.querySelector(".js-multilanguage-value-lang-' . $randId . '[lang="+currentLangSelected+"]");

                          inputText.setAttribute("lang", currentLangSelected);
                          inputText.value = currentTextLang.value;
                        });
                        var inputText = document.getElementById("js-multilanguage-text-' . $randId . '");
                        inputText.addEventListener("change", (event) => {
                            var currentLangSelected = selectLang.value;
                            var currentTextLang =  document.querySelector(".js-multilanguage-value-lang-' . $randId . '[lang="+currentLangSelected+"]");
                            currentTextLang.value = inputText.value;
                        });
                    }
                    runMlField' . $randId . '();
                </script>

                <div class="input-group mb-3 append-transparent">
                ';

                $html .= '<input type="text" '.$this->renderAttributes().' id="js-multilanguage-text-' . $randId . '">';

                foreach($supportedLanguages as $language) {
                    $value = $inputValue;
                    if (isset($modelAttributes['multilanguage'])) {
                        foreach ($modelAttributes['multilanguage'] as $locale => $multilanguageFields) {
                            if ($locale == $language['locale']) {
                                $value = $multilanguageFields[key($multilanguageFields)];
                                break;
                            }
                        }
                    }
                    $html .= '<input type="hidden" class="js-multilanguage-value-lang-'.$randId.'"  lang="'.$language['locale'].'" value="'.$value.'">';
                }

                $html .= '
                <div class="input-group-append">
                    <span>
                        <select class="selectpicker"  id="js-multilanguage-select-lang-'.$randId.'" data-width="100%">';

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
