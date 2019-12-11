<?php
$supported_languages = db_get('supported_locales', array());

if (empty($supported_languages)) {
    insert_default_language();
    $supported_languages = db_get('supported_locales', 'no_cache=true');
}

$current_language = array();

// Current language
$current_language_abr = mw()->lang_helper->current_lang();
$current_language_abr = get_short_abr($current_language_abr);

$current_language['locale'] = $current_language_abr;
$current_language['language'] = $current_language_abr;

// Current language icon
$current_language['icon'] = get_flag_icon($current_language_abr);

// Current language full text
$langs = mw()->lang_helper->get_all_lang_codes();
if (isset($langs[$current_language_abr])) {
    $current_language['language'] = $langs[$current_language_abr];
}

foreach ($supported_languages as &$supported_language) {
    $supported_language['icon'] = get_flag_icon($supported_language['locale']);
}

$module_template = get_option('data-template', $params['id']);

if ($module_template == false and isset($params['template'])) {
    $module_template = $params['template'];
}

if ($module_template != false) {
    $template_file = module_templates($config['module'], $module_template);
} else {
    $template_file = module_templates($config['module'], 'default');
}

if (is_file($template_file)) {
    include($template_file);
}