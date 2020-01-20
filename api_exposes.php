<?php
/**
 * Created by PhpStorm.
 * User: Bojidar
 * Date: 12/12/2019
 * Time: 1:52 PM
 */

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