<?php
/**
 * Created by PhpStorm.
 * User: Bojidar Slaveykov
 * Date: 2/27/2020
 * Time: 12:50 PM
 */

require_once __DIR__ . '/TranslateTable.php';
require_once __DIR__ . '/TranslateTables/TranslateMenu.php';
require_once __DIR__ . '/TranslateTables/TranslateOption.php';
require_once __DIR__ . '/TranslateTables/TranslateCategory.php';
require_once __DIR__ . '/TranslateTables/TranslateContent.php';
require_once __DIR__ . '/TranslateTables/TranslateContentData.php';
require_once __DIR__ . '/TranslateTables/TranslateContentFields.php';
require_once __DIR__ . '/TranslateTables/TranslateCustomFields.php';
require_once __DIR__ . '/TranslateTables/TranslateCustomFieldsValues.php';
require_once __DIR__ . '/TranslateTables/TranslateTestimonials.php';
require_once __DIR__ . '/TranslateTables/TranslateTaggingTags.php';
require_once __DIR__ . '/TranslateTables/TranslateTaggingTagged.php';
require_once __DIR__ . '/TranslateTables/TranslateDynamicTextVariables.php';

class TranslateManager
{

    public $translateProviders = [
        'TranslateMenu',
        'TranslateOption',
        'TranslateCategory',
        'TranslateContent',
        'TranslateContentData',
        'TranslateContentFields',
        'TranslateCustomFields',
        'TranslateCustomFieldsValues',
        'TranslateTestimonials',
        'TranslateTaggingTags',
        'TranslateTaggingTagged',
        'TranslateDynamicTextVariables'
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

                event_bind('mw.database.' . $providerTable . '.get.query_filter', function ($params) use ($providerTable, $providerInstance) {

                    if (isset($params['params']['data-keyword'])) {

                        $keyword = $params['params']['data-keyword'];
                        $contentType = $params['params']['content_type'];
                        $searchInFields = $params['params']['search_in_fields'];

                        $params['query']->orWhereIn('id', function ($subQuery) use ($providerTable, $searchInFields, $keyword) {
                            $subQuery->select('rel_id');
                            $subQuery->from('multilanguage_translations');
                            $subQuery->where('rel_type', '=', $providerTable);
                            /*foreach ($searchInFields as $field) {
                                 $subQuery->orWhere(function($query) use ($field, $keyword) {
                                     $query->where('field_name', $field);
                                     $query->where('field_value', 'LIKE', '%'.$keyword.'%');
                                 });
                             }*/
                            $subQuery->where('field_value', 'LIKE', '%' . $keyword . '%');
                        });
                    }

                    return $params;
                });

                if ($currentLocale != $defaultLocale) {

                    event_bind('mw.database.' . $providerTable . '.get', function ($get) use ($providerTable, $providerInstance) {
                        if (is_array($get) && !empty($get)) {
                            foreach ($get as &$item) {

                                // Exclude for language option
                                if (isset($item['option_key']) && $item['option_key'] == 'language') {
                                    continue;
                                }

                                if (isset($item['option_group']) && $item['option_group'] == 'multilanguage_settings') {
                                    continue;
                                }

                                $itemHash = md5(serialize($item));
                                $cacheGet = cache_get($itemHash, 'multilanguage');
                                if ($cacheGet && is_array($cacheGet) && !empty($cacheGet)) {
                                    $item = $cacheGet;
                                    continue;
                                }

                                $item = $providerInstance->getTranslate($item);

                                $cacheSave = cache_save($item, $itemHash, 'multilanguage', 15);
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

                        if (isset($saveData['option_group']) && $saveData['option_group'] == 'multilanguage_settings') {
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

                            if (!empty($dataForTranslate) && isset($dataForTranslate['id']) && intval($dataForTranslate['id']) !== 0) {
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


                                cache_clear('multilanguage');
                            }
                        }

                    });
                }
            }
        }

    }

}
