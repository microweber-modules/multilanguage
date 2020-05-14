<?php
/**
 * Author: Bozhidar Slaveykov
 */

template_head(function () {

    $content_link = content_link(CONTENT_ID);
    $link = '<link rel="canonical" href="' . $content_link . '"/>';

    $supportedLanguages = get_supported_languages();
    foreach ($supportedLanguages as $locale) {
        $link .= '<link rel="alternate" href="' . $content_link . '" hreflang="' . $locale['locale'] . '" />';
    }

    return $link;
});

event_bind('content.link.after', function ($link) {

    if (!defined('MW_API_HTML_OUTPUT') && (defined('MW_FRONTEND') || defined('MW_API_CALL'))) {
        $default_lang = get_option('language', 'website');
        $current_lang = mw()->lang_helper->current_lang();

        if ($default_lang !== $current_lang) {

            // display locale
            $localeSettings = db_get('multilanguage_supported_locales', 'locale=' . $current_lang . '&single=1');
            if ($localeSettings && !empty($localeSettings['display_locale'])) {
                $current_lang = $localeSettings['display_locale'];
            }

            $new_url = str_replace(site_url(), site_url() . $current_lang . '/', $link);
            $link = $new_url;
        }
    }

    return $link;
});


/*event_bind('menu.after.get_item', function ($menu) {

    if (isset($menu['url']) && !empty($menu['url']) && $menu['url'] !== site_url()) {

        $default_lang = get_option('language', 'website');
        $current_lang = mw()->lang_helper->current_lang();

        if ($default_lang !== $current_lang) {
            $new_url = str_replace(site_url(), site_url() . $current_lang . '/', $menu['url']);
            $menu['url'] = $new_url;
        }
    }

    return $menu;

});*/


event_bind('mw.front', function () {

    if (!isset($_COOKIE['lang']) && is_home()) {
        $homepageLanguage = get_option('homepage_language', 'website');
        if ($homepageLanguage) {
            if (is_lang_supported($homepageLanguage)) {
                change_language_by_locale($homepageLanguage);
                $_COOKIE['autodetected_lang'] = 1;
                return;
            }
        }
    }

});

event_bind('mw.controller.index', function () {

    $targetUrl = mw()->url_manager->string();
    $detect = detect_lang_from_url($targetUrl);

    $useGeolocation = get_option('use_geolocation', 'multilanguage_settings');
    if ($useGeolocation && $useGeolocation == 'y') {
        if (!isset($_COOKIE['autodetected_lang'])) {
            $geoLocation = get_geolocation();

            if ($geoLocation && isset($geoLocation['countryCode'])) {
                $language = get_country_language_by_country_code($geoLocation['countryCode']);

                var_dump($geoLocation);

                if ($language && is_lang_supported($language)) {
                    change_language_by_locale($language);
                    setcookie('autodetected_lang', 1);
                    return;
                }
            }

        }
    }

    if ($detect['target_lang']) {
        // display locale
        $localeSettings = db_get('multilanguage_supported_locales', 'display_locale=' . $detect['target_lang'] . '&single=1');
        if ($localeSettings) {
            change_language_by_locale($localeSettings['locale']);
        } else {
            change_language_by_locale($detect['target_lang']);
        }
    }

});

event_bind('mw.front.content_data', function ($content) {

    $redirect = mw_var('should_redirect');
    if ($redirect) {
        $content['original_link'] = $redirect;
    }

    return $content;
});

event_bind('content.get_by_url', function ($url) {

    if (!empty($url)) {

        $detect = detect_lang_from_url($url);
        $targetUrl = $detect['target_url'];
        $targetLang = $detect['target_lang'];

        if (empty($targetUrl)) {
            $homepageGet = mw()->content_manager->homepage();
            if ($homepageGet) {
                mw_var('should_redirect', site_url() . $targetLang . '/' . $homepageGet['url']);
                return;
            }
        }

        if (!$targetUrl || !$targetLang) {
            return;
        }

        if (mw()->lang_helper->default_lang() == $targetLang) {
            $targetLang = '';
        } else {
            $targetLang .= '/';
        }

        $filter = array();
        $filter['single'] = 1;
        $filter['rel_type'] = 'content';
        $filter['field_name'] = 'url';
        $filter['field_value'] = $targetUrl;
        $findTranslate = db_get('multilanguage_translations', $filter);
        if ($findTranslate && intval($findTranslate['rel_id']) !== 0) {

            $get = array();
            $get['id'] = $findTranslate['rel_id'];
            $get['single'] = true;
            $content = mw()->content_manager->get($get);

            if ($content['url'] == $findTranslate['field_value']) {
                return $content;
            } else {
                mw_var('should_redirect', content_link($content['id']));
                //mw_var('should_redirect', site_url() . $targetLang . $content['url']);
                return;
            }
        } else {
            $get = array();
            $get['url'] = $targetUrl;
            $get['single'] = true;

            $content = mw()->content_manager->get($get);
            if ($content) {
                if ($content['url'] !== $targetUrl) {
                    mw_var('should_redirect', content_link($content['id']));
                    //mw_var('should_redirect', site_url() . $targetLang . $content['url']);
                    return;
                }
                return $content;
            }
        }
    }

    return;
});
