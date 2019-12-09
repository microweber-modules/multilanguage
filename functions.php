<?php
/**
 * Author: Bozhidar Slaveykov
 */

require_once 'src/TranslateManager.php';

$translate = new TranslateManager();
$translate->run();

function get_short_abr($locale) {

    if(strlen($locale) == 2) {
        return $locale;
    }

    $exp = explode("_", $locale);

    return strtolower($exp[0]);
}

function get_flag_icon($locale)
{
    if(strlen($locale) == 2) {
        return $locale;
    }

    $exp = explode("_", $locale);

    return strtolower($exp[1]);
}

function change_language_by_locale($locale) {

    $locale = get_short_abr($locale);

    $langs = mw()->lang_helper->get_all_lang_codes();

    if (!is_string($locale) || !array_key_exists($locale, $langs)) {
        return false;
    }

    $_COOKIE['lang'] = $locale;

    return mw()->lang_helper->set_current_lang($locale);
}

api_expose('delete_language', function () {
    if (isset($_POST['locale'])) {

        $get = array();
        $get['locale'] = $_POST['locale'];
        $get['single'] = true;
        $get['no_cache'] = true;

        $find = db_get('supported_locales', $get);

        if ($find) {
            return db_delete('supported_locales', $find['id']);
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
    if (isset($_POST['locale'])) {
        return change_language_by_locale($_POST['locale']);
    }
    return false;
});

event_bind('mw.admin.header.toolbar', function () {
    echo '<div class="mw-ui-col pull-right">
         <module type="multilanguage/change_language"></module>
    </div>';
});

event_bind('menu.after.get_item', function ($menu) {

    if (isset($menu['url']) && !empty($menu['url']) && $menu['url'] !== site_url()) {

        $default_lang = get_option('language', 'website');
        $current_lang = mw()->lang_helper->current_lang();

        if ($default_lang !== $current_lang) {
            $new_url = str_replace(site_url(), site_url() . $current_lang . '/', $menu['url']);
            $menu['url'] = $new_url;
        }
    }

    return $menu;

});

event_bind('content.get_posts', function ($posts)  {

    return $posts;
});

event_bind('content.get_by_url', function ($url)  {

    if (!empty($url)) {

        $targetUrl = $url;
        $targetLang = false;
        $segments = explode('/', $url);
        if (count($segments) == 2) {
            $targetLang = $segments[0];
            $targetUrl = $segments[1];
        }

        if (!$targetLang) {
            return;
        }

        change_language_by_locale($targetLang);

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
                // Redirect to target lang & finded content url
                header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
                header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
                header('HTTP/1.1 301');
                header('Location: ' . site_url() . $targetLang .'/' . $content['url']);
                exit;
            }
        } else {
            $get = array();
            $get['url'] = $targetUrl;
            $get['single'] = true;

            $content = mw()->content_manager->get($get);
            if ($content) {

                if ($content['url'] !== $targetUrl) {
                    // Redirect to finded content url
                    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
                    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
                    header('HTTP/1.1 301');
                    header('Location: ' . site_url() . $targetLang .'/' . $content['url']);
                    exit;
                }

                return $content;
            }
        }
    }

    return;
});