<?php
/**
 * Author: Bozhidar Slaveykov
 */


require_once 'src/TranslateManager.php';

$translate = new TranslateManager();
$translate->run();

function get_short_abr($locale)
{

    if (strlen($locale) == 2) {
        return $locale;
    }

    $exp = explode("_", $locale);

    return strtolower($exp[0]);
}

function get_flag_icon($locale)
{
    if ($locale == 'el') {
        return 'gr';
    }

    if ($locale == 'da') {
        return 'dk';
    }

    if ($locale == 'en') {
        return 'us';
    }
    if ($locale == 'en_uk') {
        return 'gb';
    }

    if (strpos($locale, "_")) {
        $exp = explode("_", $locale);
        return strtolower($exp[1]);
    } else {
        return $locale;
    }
}

function change_language_by_locale($locale)
{

    // $locale = get_short_abr($locale);

    setcookie('lang', $locale, time() + (86400 * 30), "/");

    return mw()->lang_helper->set_current_lang($locale);
}

function insert_default_language() {

    $defaultLang = mw()->lang_helper->default_lang();
    $langs = mw()->lang_helper->get_all_lang_codes();

    if (isset($langs[$defaultLang])) {
        $save = array();
        $save['locale'] = $defaultLang;
        $save['language'] = $langs[$defaultLang];
        return db_save('supported_locales', $save);
    }

    return false;
}

api_expose('delete_language', function () {
    if (isset($_POST['id'])) {

        $get = array();
        $get['id'] = intval($_POST['id']);
        $get['single'] = true;
        $get['no_cache'] = true;

        $find = db_get('supported_locales', $get);

        if ($find) {
            return db_delete('supported_locales', $find['id']);
        }
    }
});

api_expose('sort_language', function () {
    if (isset($_POST['id'])) {

        $get = array();
        $get['id'] = intval($_POST['id']);
        $get['single'] = true;
        $get['no_cache'] = true;

        $find = db_get('supported_locales', $get);

        if ($find) {
            $save = array();
            $save['id'] = $find['id'];

            if (isset($_POST['sort']) && $_POST['sort'] == 'up') {
                $save['sort'] = $find['sort'] + 1;
            } else {
                $save['sort'] = $find['sort'] - 1;
            }

            return db_save('supported_locales', $save);
        }

    }
});

api_expose('add_language', function () {
    if (isset($_POST['locale']) && isset($_POST['language'])) {

        $locale = $_POST['locale'];
        $language = $_POST['language'];

        $get = array();
        $get['locale'] = $locale;
        $get['single'] = true;
        $get['no_cache'] = true;
        $find = db_get('supported_locales', $get);

        if (!$find) {
            $save = array();
            $save['locale'] = $locale;
            $save['language'] = $language;
            return db_save('supported_locales', $save);
        }

        return $find['id'];
    }
    return false;
});

api_expose('change_language', function () {

    if (!isset($_POST['locale'])) {
        return;
    }

    $json = array();
    $locale = $_POST['locale'];

    if (!is_lang_correct($locale)) {
        return array('error' => _e('Locale is not supported'));
    }

    change_language_by_locale($locale);

    if (isset($_POST['is_admin']) && $_POST['is_admin'] == 1) {
        $json['refresh'] = true;
    } else {
        $targetUrl = mw()->url_manager->string(true);
        $detect = detect_lang_from_url($targetUrl);

        $json['refresh'] = true;
        if ($detect['target_url']) {
            $json['location'] = site_url($locale . '/' . $detect['target_url']);
        }

    }

    return $json;
});

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

function is_lang_correct($lang)
{
    $correct = false;
    $langs = mw()->lang_helper->get_all_lang_codes();
    if (is_string($lang) && array_key_exists($lang, $langs)) {
        $correct = true;
    }
    return $correct;
}

function detect_lang_from_url($url)
{

    $targetUrl = false;
    $targetLang = false;
    $segments = explode('/', $url);
    if (count($segments) == 2) {
        $targetLang = $segments[0];
        $targetUrl = $segments[1];
    }

    if (!is_lang_correct($targetLang)) {
        $targetLang = false;
    }

    return array('target_lang' => $targetLang, 'target_url' => $targetUrl);
}

event_bind('mw.controller.index', function () {

    $targetUrl = mw()->url_manager->string();
    $detect = detect_lang_from_url($targetUrl);

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
        $findTranslate = db_get('translations', $filter);
        if ($findTranslate) {

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