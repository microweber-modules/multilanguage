<?php

class MultilanguagePermalinkManager extends \Microweber\Providers\PermalinkManager
{

    public function __construct($app = null)
    {
        parent::__construct();
        
        $this->structureMapPrefix[] = 'locale';

        $getLinkAfter = $this->__getLinkAfter();
        if ($getLinkAfter) {
            $this->linkAfter[] = $getLinkAfter;
        }
    }
    
    private function __getLinkAfter()
    {
        $rewriteUrl = false;
        $defaultLang = get_option('language', 'website');
        $currentLang = mw()->lang_helper->current_lang();

        $prefixForAll = get_option('add_prefix_for_all_languages','multilanguage_settings');

        if ($defaultLang !== $currentLang) {
            $rewriteUrl = true;
        }

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