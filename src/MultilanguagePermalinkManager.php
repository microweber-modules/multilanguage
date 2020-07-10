<?php

class MultilanguagePermalinkManager extends \MicroweberPackages\App\Managers\PermalinkManager
{

    // public $locale = false;

    public function __construct($app = null)
    {
        parent::__construct();
        
        $this->structureMapPrefix[] = 'locale';

        $getLinkAfter = $this->__getLinkAfter();
        if ($getLinkAfter) {
            $this->linkAfter[] = $getLinkAfter;
        }
    }

 /*   public function setLocale($locale)
    {

    }
    */
    private function __getLinkAfter()
    {
        $rewriteUrl = false;
        $defaultLang = get_option('language', 'website');

      /*  if ($this->locale) {
            mw()->lang_helper->set_current_lang($this->locale);
        }*/

        $currentLang = mw()->lang_helper->current_lang();

        if ($defaultLang !== $currentLang) {
            $rewriteUrl = true;
        }

        $prefixForAll = get_option('add_prefix_for_all_languages','multilanguage_settings');

        // needs fix
        $prefixForAll = 'y';
        if ($prefixForAll == 'y') {
            $rewriteUrl = true;
        }

        if ($rewriteUrl) {
            // display locale
            $localeSettings = db_get('multilanguage_supported_locales', 'locale=' . $currentLang . '&single=1');
            if ($localeSettings && !empty($localeSettings['display_locale'])) {
                $currentLang = $localeSettings['display_locale'];
            }
        }

        if ($rewriteUrl) {
            return $currentLang;
        }
    }
}