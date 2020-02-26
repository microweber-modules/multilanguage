<?php
/**
 * Created by PhpStorm.
 * User: Bojidar
 * Date: 12/12/2019
 * Time: 1:52 PM
 */


api_expose_admin('multilanguage/edit_locale', function ($params) {

    if (isset($params['locale_id'])) {

        $getLocale = get_supported_locale_by_id($params['locale_id']);
        if ($getLocale) {

            $localeUpdate = [];
            $localeUpdate['id'] = $getLocale['id'];
            $localeUpdate['display_name'] = $params['display_name'];
            $localeUpdate['display_icon'] = $params['display_icon'];

            $save = db_save('multilanguage_supported_locales', $localeUpdate);
            if ($save) {
                return ['success'=>true];
            }
        }

    }

    return ['error'=>true];
});


api_expose_admin('multilanguage/delete_language', function ($params) {

    $api = new MultilanguageApi();
    return $api->deleteLanguage($params);

});

api_expose_admin('multilanguage/sort_language', function ($params) {

    $api = new MultilanguageApi();
    return $api->sortLanguage($params);

});

api_expose_admin('multilanguage/add_language', function ($params) {

    $api = new MultilanguageApi();
    return $api->addLanguage($params);

});

api_expose('multilanguage/change_language', function ($params) {

    $api = new MultilanguageApi();
    return $api->changeLanguage($params);

});

api_expose('multilanguage/geolocaiton_test', function ($params) {

    $geo = get_geolocation_detailed();

    echo json_encode($geo,JSON_PRETTY_PRINT);

});