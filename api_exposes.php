<?php
/**
 * Created by PhpStorm.
 * User: Bojidar
 * Date: 12/12/2019
 * Time: 1:52 PM
 */

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
    if (isset($_POST['ids'])) {
        if (is_array($_POST['ids']) && !empty($_POST['ids'])) {
            foreach ($_POST['ids'] as $id) {
                if (isset($id['id']) && isset($id['position']) && !empty($id['id']) && !empty($id['position'])) {
                    $save = array();
                    $save['id'] = $id['id'];
                    $save['position'] = $id['position'];
                    $saved = db_save('supported_locales', $save);
                }
            }
        }
    }
});

api_expose('add_language', function () {
    if (isset($_POST['locale']) && isset($_POST['language'])) {

        $locale = $_POST['locale'];
        $language = $_POST['language'];

        return add_supported_language($locale, $language);
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