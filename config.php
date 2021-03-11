<?php
$config = array();
$config['name'] = "Multilanguage";
$config['author'] = "Bozhidar Slaveykov";
$config['ui'] = true; //if set to true, module will be visible in the toolbar
$config['ui_admin'] = true; //if set to true, module will be visible in the admin panel
$config['categories'] = "content";
$config['position'] = 99;
$config['version'] = 3.3;
$config['require'] = [
    'microweber/microweber'=>'1.2'    
];
$config['settings']['service_provider'] = [
    \MicroweberPackages\Multilanguage\MultilanguageServiceProvider::class
];

$config['tables'] = array(
    'multilanguage_translations' => array(
        'id' => 'integer',
        'rel_id' => 'string',
        'rel_type' => 'string',
        'field_name' => 'string',
        'field_value' => 'text',
        'locale' => 'string',
    ),
    'multilanguage_supported_locales' => array(
        'id' => 'integer',
        'locale' => 'string',
        'language' => 'string',
        'display_locale'=> 'string',
        'display_name'=> 'string',
        'display_icon'=> 'string',
        'position'=> 'integer',
        'is_active'=> 'string'
    )
);
