<?php
namespace MicroweberPackages\Multilanguage\FormElements;

class TextAreaOption extends \MicroweberPackages\Form\Elements\TextAreaOption
{
    public $randId;
    public $defaultLanguage;

    public function render()
    {
        $this->defaultLanguage = mw()->lang_helper->default_lang();
        $this->randId = random_int(111,999).time();

        $supportedLanguages = get_supported_languages(true);

        $html = ' <div class="bs-component">
                <nav class="nav nav-pills nav-justified btn-group btn-group-toggle btn-hover-style-1">
                ';

                foreach($supportedLanguages as $language) {

                    $showTab= '';
                    if ($this->defaultLanguage == $language['locale']) {
                        $showTab = 'active';
                    }

                    $langData = \MicroweberPackages\Translation\LanguageHelper::getLangData($language['locale']);
                    $flagIcon = "<i class='flag-icon flag-icon-".$language['icon']."'></i> " . strtoupper($langData['language']);
                    $html .= '<a class="btn btn-outline-secondary btn-sm justify-content-center '.$showTab.'" data-toggle="tab" href="#' . $this->randId . $language['locale'] . '">'.$flagIcon.'</a>';
                }

                $html .='</nav>
                <div id="js-multilanguage-tab-'.$this->randId.'" class="tab-content py-3">
                ';
                    foreach($supportedLanguages as $language) {
                        $showTab= '';
                        if ($this->defaultLanguage == $language['locale']) {
                            $showTab = 'show active';
                        }
                        $html .= '<div class="tab-pane fade '.$showTab.'" id="' . $this->randId . $language['locale'] . '">
                                   <textarea onchange="applyMlFieldChanges(this)" lang="'.$language['locale'] . '" class="form-control">'.$language['locale'] . '</textarea>
                                   </div>';
                    }

                    $html .= '
                    <script>
                        function applyMlFieldChanges(element) {
                            var applyToElement = document.getElementById("js-multilanguage-textarea-' . $this->randId . '");
                            applyToElement.value = element.value
                            applyToElement.setAttribute("lang", element.getAttribute("lang"));

                            var changeEvent = new Event("change");
                            applyToElement.dispatchEvent(changeEvent);
                        }
                   </script>
                    ';

                    $html .= '<textarea '.$this->renderAttributes().' style="display:none" id="js-multilanguage-textarea-' . $this->randId . '"></textarea>';
                    $html .= '
                    </div>
                  </div>';

        return $html;
    }

}
