<?php
/**
 * Author: Bozhidar Slaveykov
 */

event_bind('mw.admin.header.toolbar', function () {
    echo '<div class="mw-ui-col pull-right">
         <module type="multilanguage/change_language"></module>
    </div>';
});

event_bind('live_edit_toolbar_action_buttons', function () {
    echo '<module type="multilanguage/change_language"></module>';
});

event_bind('content.link.after', function ($link) {

    if (defined('MW_FRONTEND')) {
        $default_lang = get_option('language', 'website');
        $current_lang = mw()->lang_helper->current_lang();

        if ($default_lang !== $current_lang) {
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


event_bind('mw.controller.index', function () {

    $targetUrl = mw()->url_manager->string();
    $detect = detect_lang_from_url($targetUrl);

    $useGeolocation = get_option('use_geolocation','multilanguage');
    if ($useGeolocation && $useGeolocation == 'y') {
        if (!isset($_COOKIE['lang'])) {
            $geoLocation = get_geolocation();
            $geoLocation['countryCode'] = 'us';
            if ($geoLocation && isset($geoLocation['countryCode'])) {
                $language = get_country_language_by_country_code($geoLocation['countryCode']);
                if ($language && is_lang_supported($language)) {
                    change_language_by_locale($language);
                    return;
                }
            }
        }
    }

    if ($detect['target_lang']) {
        change_language_by_locale($detect['target_lang']);
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

        if (!$targetUrl || !$targetLang) {
            return;
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
                mw_var('should_redirect', site_url() . $targetLang . '/' . $content['url']);
                return;
            }
        } else {
            $get = array();
            $get['url'] = $targetUrl;
            $get['single'] = true;

            $content = mw()->content_manager->get($get);
            if ($content) {
                if ($content['url'] !== $targetUrl) {
                    mw_var('should_redirect', site_url() . $targetLang . '/' . $content['url']);
                    return;
                }
                return $content;
            }
        }
    }

    return;
});