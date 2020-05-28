<?php
/**
 * Author: Bozhidar Slaveykov
 */

template_head(function () {

    $content_link = content_link(CONTENT_ID);
    $link = '<link rel="canonical" href="' . $content_link . '"/>';

    $supportedLanguages = get_supported_languages();
    foreach ($supportedLanguages as $locale) {
        $hrefLang = $locale['locale'];
        if (mb_strlen($hrefLang) > 2) {
            $hrefLang = mb_substr($hrefLang, 0, 2);
        }
        $link .= '<link rel="alternate" href="' . $content_link . '" hreflang="' . $hrefLang . '" />';
    }

    return $link;
});

event_bind('app.permalink.structure_map_prefix', function () {
    return 'locale';
});

event_bind('app.category.get_category_id_from_url', function ($slug) {

    $relId = get_rel_id_by_multilanguage_url($slug, 'categories');
    if ($relId) {
        return $relId;
    }

    return false;
});


event_bind('app.permalink.link.after', function(){

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

});




/*
event_bind('app.permalink.slug.before', function ($params) {

    //var_dump($params);

    $relType = 'post';
    if ($params['type'] == 'category') {
        $relType = 'categories';
    }

    $filter = array();
    $filter['field_name'] = 'url';
    $filter['field_value'] = $params['slug'];
    $filter['single'] = 1;
    if ($relType) {
        $filter['rel_type'] = $relType;
    }

    $get = db_get('multilanguage_translations', $filter);
    if ($get) {
        if ($relType == 'categories') {
            $category = get_categories('id=' . $get['rel_id'] . '&single=1');
            if ($category) {
                return $category['url'];
            }
        }
    }

    if ($relType == 'categories') {
        $category = get_categories('url=' . $params['slug'] . '&single=1');
        if ($category) {
            return $category['url'];
        }
    }

    return false;
});*/

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


event_bind('mw.controller.index', function () {

    $targetUrl = mw()->url_manager->string();
    $detect = detect_lang_from_url($targetUrl);

    $useGeolocation = get_option('use_geolocation', 'multilanguage_settings');
    if ($useGeolocation && $useGeolocation == 'y') {
//        if (!isset($_COOKIE['autodetected_lang']) and !isset($_COOKIE['lang'])) {
//            $geoLocation = get_geolocation();
//
//            if ($geoLocation && isset($geoLocation['countryCode'])) {
//                $language = get_country_language_by_country_code($geoLocation['countryCode']);
//
//               // var_dump($geoLocation);
//
//                if ($language && is_lang_supported($language)) {
//                    change_language_by_locale($language);
//                    setcookie('autodetected_lang', 1);
//                    return;
//                }
//            }
//
//        }
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
