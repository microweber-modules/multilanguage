<?php
/**
 * Author: Bozhidar Slaveykov
 */

require_once 'src/TranslateManager.php';

$translate = new TranslateManager();
$translate->run();

function get_flag_icon($locale)
{
    if ($locale == 'en') {
        $icon_key = 'us';
    } else {
        $icon_key = $locale;
    }

    return $icon_key;
}

function change_language_by_locale($locale) {

    $langs = mw()->lang_helper->get_all_lang_codes();

    if (!is_string($locale) || !array_key_exists($locale, $langs)) {
        return false;
    }

    $_COOKIE['lang'] = $locale;

    return mw()->lang_helper->set_current_lang($locale);
}

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

        $current_lang = mw()->lang_helper->current_lang();

        $new_url = str_replace(site_url(), site_url() . $current_lang . '/', $menu['url']);

        $menu['url'] = $new_url;
    }

    return $menu;

});