<?php
/**
 * Author: Bozhidar Slaveykov
 */
require_once 'src/TranslateManager.php';

$translate = new TranslateManager();
$translate->run();

require_once 'event_binds.php';
require_once 'api_exposes.php';

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

    if ($locale == 'ar') {
        return 'sa';
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

function add_supported_language($locale, $language) {

    $get = array();
    $get['locale'] = $locale;
    $get['single'] = true;
    $get['no_cache'] = true;
    $find = db_get('supported_locales', $get);

    if (!$find) {

        $position = 1;
        $last_language = db_get('supported_locales', 'no_cache=true&order_by=position desc&single=1');
        if ($last_language) {
            $position = $last_language['position'] + 1;
        }

        $save = array();
        $save['position'] = $position;
        $save['locale'] = $locale;
        $save['language'] = $language;
        return db_save('supported_locales', $save);
    }

    return $find['id'];
}

function insert_default_language()
{
    $defaultLang = mw()->lang_helper->default_lang();
    $langs = mw()->lang_helper->get_all_lang_codes();

    if (isset($langs[$defaultLang])) {
        return add_supported_language($defaultLang, $langs[$defaultLang]);
    }

    return false;
}

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

function get_supported_languages()
{
    $get_filter = 'no_cache=true&order_by=position asc';
    $languages = db_get('supported_locales', $get_filter);

    if ($languages) {
        $locales = array();
        foreach ($languages as &$language) {
            $language['icon'] = get_flag_icon($language['locale']);
            $locales[] = strtolower($language['locale']);
        }

        // Check default language exists on supported locales
        $default_lang = mw()->lang_helper->default_lang();
        $default_lang = strtolower($default_lang);

        if (!in_array($default_lang, $locales)) {
            insert_default_language();
            $languages = db_get('supported_locales', $get_filter);
        }
    } else {
        insert_default_language();
        $languages = db_get('supported_locales', $get_filter);
    }

    return $languages;
}