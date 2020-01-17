<?php
require_once __DIR__ . '/TranslateTable.php';
require_once __DIR__ . '/TranslateTables/TranslateMenu.php';
require_once __DIR__ . '/TranslateTables/TranslateOption.php';
require_once __DIR__ . '/TranslateTables/TranslateCategory.php';
require_once __DIR__ . '/TranslateTables/TranslateContent.php';
require_once __DIR__ . '/TranslateTables/TranslateContentFields.php';
require_once __DIR__ . '/TranslateTables/TranslateCustomFields.php';
require_once __DIR__ . '/TranslateTables/TranslateCustomFieldsValues.php';
require_once __DIR__ . '/TranslateTables/TranslateTestimonials.php';

class TranslateManager
{

    public $translateProviders = [
        'TranslateMenu',
        'TranslateOption',
        'TranslateCategory',
        'TranslateContent',
        'TranslateContentFields',
        'TranslateCustomFields',
        'TranslateCustomFieldsValues',
        'TranslateTestimonials'
    ];

    public function run()
    {
        if (!empty($this->translateProviders)) {
            foreach ($this->translateProviders as $provider) {

                $providerInstance = new $provider();
                $providerTable = $providerInstance->getRelType();

                // BIND GET TABLES
                $currentLocale = mw()->lang_helper->current_lang();
                $defaultLocale = mw()->lang_helper->default_lang();

                if ($currentLocale != $defaultLocale) {

                    event_bind('mw.database.' . $providerTable . '.get', function ($get) use ($providerTable, $providerInstance) {
                        if (is_array($get) && !empty($get)) {
                            foreach ($get as &$item) {

                                // Exclude for language option
                                if (isset($item['option_key']) && $item['option_key'] == 'language') {
                                    continue;
                                }

                                if (isset($item['option_key']) && $item['option_key'] == 'multilanguage_settings') {
                                    continue;
                                }

                                $item = $providerInstance->getTranslate($item);
                            }
                        }
                        return $get;
                    });

                    // BIND SAVE TABLES
                    event_bind('mw.database.' . $providerTable . '.save.params', function ($saveData) use ($providerInstance) {

                        // Exclude for language option
                        if (isset($saveData['option_key']) && $saveData['option_key'] == 'language') {
                            return false;
                        }

                        if (isset($saveData['option_key']) && $saveData['option_key'] == 'multilanguage_settings') {
                            return false;
                        }

                        if ($providerInstance->getRelType() == 'options') {
                            $saveData['__option_value'] = $saveData['option_value'];
                            unset($saveData['option_value']);
                            return $saveData;
                        }

                        if ($providerInstance->getRelType() == 'content_fields') {
                            $saveData['__value'] = $saveData['value'];
                            unset($saveData['value']);
                            return $saveData;
                        }

                        if (!empty($providerInstance->getColumns())) {
                            $dataForTranslate = $saveData;
                            foreach ($providerInstance->getColumns() as $column) {

                                if (!isset($saveData['id'])) {
                                    continue;
                                }

                                if (intval($saveData['id']) !== 0) {
                                    if (isset($saveData[$column])) {
                                        unset($saveData[$column]);
                                    }
                                }
                            }

                            if (!empty($dataForTranslate)) {
                                $providerInstance->saveOrUpdate($dataForTranslate);
                            }
                        }


                        return $saveData;
                    });

                    event_bind('mw.database.' . $providerTable . '.save.after', function ($saveData) use ($providerInstance) {

                        $currentLocale = mw()->lang_helper->current_lang();
                        $defaultLocale = mw()->lang_helper->default_lang();

                        if ($currentLocale != $defaultLocale) {
                            if (!empty($providerInstance->getColumns())) {

                                if ($providerInstance->getRelType() == 'content_fields' && isset($saveData['__value'])) {
                                    $saveData['value'] = $saveData['__value'];
                                    unset($saveData['__value']);
                                    $providerInstance->saveOrUpdate($saveData);
                                }

                                if ($providerInstance->getRelType() == 'options' && isset($saveData['__option_value'])) {
                                    $saveData['option_value'] = $saveData['__option_value'];
                                    unset($saveData['__option_value']);
                                    $providerInstance->saveOrUpdate($saveData);
                                }

                            }
                        }

                    });
                }
            }
        }

    }

}