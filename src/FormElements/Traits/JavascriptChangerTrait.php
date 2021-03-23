<?php

namespace MicroweberPackages\Multilanguage\FormElements\Traits;

trait JavascriptChangerTrait
{
    public function getJavaScript()
    {
        $html = '  <script>
                    function runMlField' . $this->randId . '() {
                        var defaultLang = "'.$this->defaultLanguage.'";
                        var selectLang = document.getElementById("js-multilanguage-select-lang-' . $this->randId . '");
                        selectLang.value = "'.$this->defaultLanguage.'";
                        selectLang.addEventListener("change", (event) => {
                          var inputText = document.getElementById("js-multilanguage-text-' . $this->randId . '");
                          var currentLangSelected = selectLang.value;
                          var currentTextLang =  document.querySelector(".js-multilanguage-value-lang-' . $this->randId . '[lang="+currentLangSelected+"]");

                          if (defaultLang !== currentLangSelected) {
                            inputText.setAttribute("lang", currentLangSelected);
                          }
                          inputText.value = currentTextLang.value;
                        });
                        var inputText = document.getElementById("js-multilanguage-text-' . $this->randId . '");
                        inputText.addEventListener("change", (event) => {
                            var currentLangSelected = selectLang.value;
                            var currentTextLang =  document.querySelector(".js-multilanguage-value-lang-' . $this->randId . '[lang="+currentLangSelected+"]");
                            currentTextLang.value = inputText.value;
                        });

                        var changeEvent = new Event("change");
                        selectLang.dispatchEvent(changeEvent);
                    }
                    runMlField' . $this->randId . '();
                </script>';

        return $html;
    }
}
