<?php
/**
 * Author: Bozhidar Slaveykov
 */
require_once 'src/MultilanguageApi.php';
require_once 'src/TranslateManager.php';
require_once 'api_exposes.php';
require_once 'event_binds_general.php';


// Check multilanguage is active
if (get_option('is_active', 'multilanguage_settings') !== 'y') {
    return;
}

$currentLocale = mw()->lang_helper->current_lang();
if (is_lang_supported($currentLocale)) {

    $translate = new TranslateManager();
    $translate->run();

    require_once 'event_binds.php';
}

function get_supported_locale_by_id($id)
{
    return db_get('multilanguage_supported_locales', 'id=' . $id . '&single=1');
}

function get_supported_locale_by_locale($locale)
{
    return db_get('multilanguage_supported_locales', 'locale=' . $locale . '&single=1');
}

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
    if (!is_cli()) {
        setcookie('lang', $locale, time() + (86400 * 30), "/");
    }

    return mw()->lang_helper->set_current_lang($locale);
}

function add_supported_language($locale, $language)
{

    $get = array();
    $get['locale'] = $locale;
    $get['single'] = true;
    $get['no_cache'] = true;
    $find = db_get('multilanguage_supported_locales', $get);

    if (!$find) {

        $position = 1;
        $last_language = db_get('multilanguage_supported_locales', 'no_cache=true&order_by=position desc&single=1');
        if ($last_language) {
            $position = $last_language['position'] + 1;
        }

        $save = array();
        $save['position'] = $position;
        $save['locale'] = $locale;
        $save['language'] = $language;
        return db_save('multilanguage_supported_locales', $save);
    }

    return $find['id'];
}

function get_default_language()
{
    $langs = mw()->lang_helper->get_all_lang_codes();

    $defaultLang = mw()->lang_helper->default_lang();
    $defaultLang = strtolower($defaultLang);

    if (isset($langs[$defaultLang])) {
        return array('locale' => $defaultLang, 'language' => $langs[$defaultLang]);
    }

    $locale = app()->getLocale();
    $locale = strtolower($locale);
    if (isset($langs[$locale])) {
        return array('locale' => $locale, 'language' => $langs[$locale]);
    }

    return false;
}

function insert_default_language()
{
    $defaultLang = get_default_language();
    if ($defaultLang) {
        return add_supported_language($defaultLang['locale'], $defaultLang['language']);
    }

    return false;
}

function is_lang_supported($lang)
{

    if (!is_lang_correct($lang)) {
        return false;
    }

    $supportedLanguagesMap = array();
    $supportedLanguages = get_supported_languages();
    foreach ($supportedLanguages as $language) {
        $supportedLanguagesMap[] = $language['locale'];
    }

    return in_array($lang, $supportedLanguagesMap);
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

function is_lang_correct_by_display_locale($locale)
{
    $localeSettings = db_get('multilanguage_supported_locales', 'display_locale=' . $locale . '&single=1');
    if ($localeSettings) {
        return true;
    }

    return false;
}

function detect_lang_from_url($targetUrl)
{
    $targetLang = mw()->lang_helper->current_lang();
    $findedLangAbr = 0;
    $segments = explode('/', $targetUrl);
    if (count($segments) < 1) {
        array('target_lang' => $targetLang, 'target_url' => $targetUrl);
    }

    // Find target lang in segments
    foreach ($segments as $segment) {
        if (is_lang_correct($segment)) {
            $findedLangAbr++;
        }

        // display locale
        if (is_lang_correct_by_display_locale($segment)) {
            $findedLangAbr++;
        }
    }

    if ($findedLangAbr == 0) {
        return array(
            'target_lang' => $targetLang,
            'target_url' => $targetUrl
        );
    }
    $targetUrlSegments = array();
    foreach ($segments as $key => $segment) {
        if ($key == 0) {
            $targetLang = $segment; // This is the lang abr
        } else {
            $targetUrlSegments[] = $segment;
        }
    }

    $targetUrl = implode('/', $targetUrlSegments);

    return array('target_lang' => $targetLang, 'target_url' => $targetUrl);
}

function get_supported_languages($only_active = false)
{
    $get_filter = 'no_cache=true&order_by=position asc';
    if ($only_active) {
        $get_filter .= '&is_active=y';
    }

    $languages = db_get('multilanguage_supported_locales', $get_filter);

    if ($languages) {
        $locales = array();
        foreach ($languages as &$language) {
            $language['icon'] = get_flag_icon($language['locale']);
            $locales[] = strtolower($language['locale']);
        }

        // Check default language exists on supported locales
        $default_lang = get_default_language();
        if (!in_array($default_lang['locale'], $locales)) {
            $insert = insert_default_language();
            if ($insert) {
                $languages = get_supported_languages();
            }
        }
    } else {
        $insert = insert_default_language();
        if ($insert) {
            $languages = get_supported_languages();
        }
    }

    return $languages;
}

function get_country_language_by_country_code($country_code)
{
    // Locale list taken from:
    // http://stackoverflow.com/questions/3191664/
    // list-of-all-locales-and-their-short-codes
    $locales = array(
        'af-ZA',
        'am-ET',
        'ar-AE',
        'ar-BH',
        'ar-DZ',
        'ar-EG',
        'ar-IQ',
        'ar-JO',
        'ar-KW',
        'ar-LB',
        'ar-LY',
        'ar-MA',
        'arn-CL',
        'ar-OM',
        'ar-QA',
        'ar-SA',
        'ar-SY',
        'ar-TN',
        'ar-YE',
        'as-IN',
        'az-Cyrl-AZ',
        'az-Latn-AZ',
        'ba-RU',
        'be-BY',
        'bg-BG',
        'bn-BD',
        'bn-IN',
        'bo-CN',
        'br-FR',
        'bs-Cyrl-BA',
        'bs-Latn-BA',
        'ca-ES',
        'co-FR',
        'cs-CZ',
        'cy-GB',
        'da-DK',
        'de-AT',
        'de-CH',
        'de-DE',
        'de-LI',
        'de-LU',
        'dsb-DE',
        'dv-MV',
        'el-GR',
        'en-029',
        'en-AU',
        'en-BZ',
        'en-CA',
        'en-GB',
        'en-IE',
        'en-IN',
        'en-JM',
        'en-MY',
        'en-NZ',
        'en-PH',
        'en-SG',
        'en-TT',
        'en-US',
        'en-ZA',
        'en-ZW',
        'es-AR',
        'es-BO',
        'es-CL',
        'es-CO',
        'es-CR',
        'es-DO',
        'es-EC',
        'es-ES',
        'es-GT',
        'es-HN',
        'es-MX',
        'es-NI',
        'es-PA',
        'es-PE',
        'es-PR',
        'es-PY',
        'es-SV',
        'es-US',
        'es-UY',
        'es-VE',
        'et-EE',
        'eu-ES',
        'fa-IR',
        'fi-FI',
        'fil-PH',
        'fo-FO',
        'fr-BE',
        'fr-CA',
        'fr-CH',
        'fr-FR',
        'fr-LU',
        'fr-MC',
        'fy-NL',
        'ga-IE',
        'gd-GB',
        'gl-ES',
        'gsw-FR',
        'gu-IN',
        'ha-Latn-NG',
        'he-IL',
        'hi-IN',
        'hr-BA',
        'hr-HR',
        'hsb-DE',
        'hu-HU',
        'hy-AM',
        'id-ID',
        'ig-NG',
        'ii-CN',
        'is-IS',
        'it-CH',
        'it-IT',
        'iu-Cans-CA',
        'iu-Latn-CA',
        'ja-JP',
        'ka-GE',
        'kk-KZ',
        'kl-GL',
        'km-KH',
        'kn-IN',
        'kok-IN',
        'ko-KR',
        'ky-KG',
        'lb-LU',
        'lo-LA',
        'lt-LT',
        'lv-LV',
        'mi-NZ',
        'mk-MK',
        'ml-IN',
        'mn-MN',
        'mn-Mong-CN',
        'moh-CA',
        'mr-IN',
        'ms-BN',
        'ms-MY',
        'mt-MT',
        'nb-NO',
        'ne-NP',
        'nl-BE',
        'nl-NL',
        'nn-NO',
        'nso-ZA',
        'oc-FR',
        'or-IN',
        'pa-IN',
        'pl-PL',
        'prs-AF',
        'ps-AF',
        'pt-BR',
        'pt-PT',
        'qut-GT',
        'quz-BO',
        'quz-EC',
        'quz-PE',
        'rm-CH',
        'ro-RO',
        'ru-RU',
        'rw-RW',
        'sah-RU',
        'sa-IN',
        'se-FI',
        'se-NO',
        'se-SE',
        'si-LK',
        'sk-SK',
        'sl-SI',
        'sma-NO',
        'sma-SE',
        'smj-NO',
        'smj-SE',
        'smn-FI',
        'sms-FI',
        'sq-AL',
        'sr-Cyrl-BA',
        'sr-Cyrl-CS',
        'sr-Cyrl-ME',
        'sr-Cyrl-RS',
        'sr-Latn-BA',
        'sr-Latn-CS',
        'sr-Latn-ME',
        'sr-Latn-RS',
        'sv-FI',
        'sv-SE',
        'sw-KE',
        'syr-SY',
        'ta-IN',
        'te-IN',
        'tg-Cyrl-TJ',
        'th-TH',
        'tk-TM',
        'tn-ZA',
        'tr-TR',
        'tt-RU',
        'tzm-Latn-DZ',
        'ug-CN',
        'uk-UA',
        'ur-PK',
        'uz-Cyrl-UZ',
        'uz-Latn-UZ',
        'vi-VN',
        'wo-SN',
        'xh-ZA',
        'yo-NG',
        'zh-CN',
        'zh-HK',
        'zh-MO',
        'zh-SG',
        'zh-TW',
        'zu-ZA',);

    $country_code = strtolower($country_code);

    foreach ($locales as $locale) {
        $locale = strtolower($locale);
        $locale_exp = explode('-', $locale);

        if ($locale_exp[1] == $country_code) {
            return $locale_exp[0];
        }
    }

    return false;
}

function get_geolocation_detailed()
{
    $ip = user_ip();
    // $ip = '8.8.8.8';

    $geolocationProvider = get_option('geolocation_provider', 'multilanguage_settings');
    $accessKey = get_option('ipstack_api_access_key', 'multilanguage_settings');

    if ($geolocationProvider == 'ipstack_com') {
        $ipInfo = mw()->http->url('http://api.ipstack.com/' . $ip . '?access_key=' . $accessKey)->get();
    } else if ($geolocationProvider == 'domain_detection') {
        $countryCode = get_country_code_from_domain();
        $ipInfo = json_encode(array(
            'countryCode' => $countryCode
        ));
    } else if ($geolocationProvider == 'browser_detection') {
        $browserCountryCode = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 3, 2);
        $ipInfo = json_encode(array(
            'countryCode' => $browserCountryCode
        ));
    } else if ($geolocationProvider == 'geoip_browser_detection') {

        $defaultWebsiteLanguage = mw()->lang_helper->default_lang();
        $browserCountryCode = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 3, 2);
        $browserLanguage = get_country_language_by_country_code($browserCountryCode);

        if ($defaultWebsiteLanguage == $browserLanguage) {
            $ipInfo = json_encode(array(
                'countryCode' => $browserCountryCode
            ));
        }

        if ($defaultWebsiteLanguage !== $browserLanguage) {
            $ipInfo = mw()->http->url('http://ipinfo.microweberapi.com/?ip=' . $ip)->get();
        }

    } else {
        $ipInfo = mw()->http->url('http://ipinfo.microweberapi.com/?ip=' . $ip)->get();
    }

    $geoLocation = json_decode($ipInfo, true);
    // $geoLocation['provider'] = $geolocationProvider;

    return $geoLocation;
}

function get_country_code_from_domain()
{
    $countryCode = 'EN';
    if (strpos($_SERVER['HTTP_HOST'], '.com') !== false) {
        $countryCode = 'EN';
    }

    if (strpos($_SERVER['HTTP_HOST'], '.bg') !== false) {
        $countryCode = 'BG';
    }

    if (strpos($_SERVER['HTTP_HOST'], '.ru') !== false) {
        $countryCode = 'RU';
    }

    if (strpos($_SERVER['HTTP_HOST'], '.gr') !== false) {
        $countryCode = 'GR';
    }

    if (strpos($_SERVER['HTTP_HOST'], '.ar') !== false) {
        $countryCode = 'AR';
    }

    return $countryCode;
}

function get_geolocation()
{
    $geoLocation = get_geolocation_detailed();

    $countryCode = false;

    if (isset($geoLocation['country_code'])) {
        $countryCode = $geoLocation['country_code'];
    }

    if (isset($geoLocation['countryCode'])) {
        $countryCode = $geoLocation['countryCode'];
    }

    if ($countryCode) {
        return array('countryCode' => $countryCode);
    }

    return false;
}
