<?php
$supportedLanguages = db_get('supported_locales', array());
$currentLanguage = mw()->lang_helper->current_lang();
$currentLanguage = get_short_abr($currentLanguage);

$moduleTemplate = get_option('data-template', $params['id']);

if ($moduleTemplate == false and isset($params['template'])) {
    $moduleTemplate = $params['template'];
}

if ($moduleTemplate != false) {
    $templateFile = module_templates($config['module'], $moduleTemplate);
} else {
    $templateFile = module_templates($config['module'], 'default');
}

if (is_file($templateFile)) {
    include($templateFile);
}