<?php
/**
 * Created by PhpStorm.
 * User: Bojidar
 * Date: 12/13/2019
 * Time: 2:29 PM
 */

class MultilanguageApi
{
    public function deleteLanguage($params)
    {
        if (isset($params['id'])) {

            $get = array();
            $get['id'] = intval($params['id']);
            $get['single'] = true;
            $get['no_cache'] = true;

            $find = db_get('multilanguage_supported_locales', $get);

            if ($find) {
                return db_delete('multilanguage_supported_locales', $find['id']);
            }
        }
    }

    public function sortLanguage($params) {
        if (isset($params['ids'])) {
            if (is_array($params['ids']) && !empty($params['ids'])) {
                foreach ($params['ids'] as $id) {
                    if (isset($id['id']) && isset($id['position']) && !empty($id['id']) && !empty($id['position'])) {
                        $save = array();
                        $save['id'] = $id['id'];
                        $save['position'] = $id['position'];
                        $saved = db_save('multilanguage_supported_locales', $save);
                    }
                }
            }
        }
    }

    public function addLanguage($params) {
        if (isset($params['locale']) && isset($params['language'])) {

            $locale = $params['locale'];
            $language = $params['language'];

            return add_supported_language($locale, $language);
        }
        return false;
    }

    public function changeLanguage($params) {

        if (!isset($params['locale'])) {
            return;
        }

        $json = array();
        $locale = $params['locale'];

        if (!is_lang_correct($locale)) {
            return array('error' => _e('Locale is not supported', true));
        }

        change_language_by_locale($locale);

        if (isset($params['is_admin']) && $params['is_admin'] == 1) {
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
    }
}