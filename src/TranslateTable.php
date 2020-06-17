<?php

class TranslateTable
{

    protected $columns = array();
    protected $relId = false;
    protected $relType = false;
    protected $locale = false;

    public function getRelType()
    {
        return $this->relType;
    }

    public function getRelId()
    {
        return $this->relId;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function saveOrUpdate($data)
    {

        foreach ($this->columns as $column) {
            if (isset($data[$column]) && !empty($data[$column])) {

                $saveTranslation = array();

                if ($this->locale) {
                    $saveTranslation['locale'] = $this->locale;
                } else {
                    $saveTranslation['locale'] = $this->getCurrentLocale();
                }

                $saveTranslation['rel_id'] = $data[$this->relId];
                $saveTranslation['rel_type'] = $this->relType;
                $saveTranslation['field_name'] = $column;
                $saveTranslation['field_value'] = $data[$column];

                $findTranslation = $this->findTranslate($saveTranslation);
                if ($findTranslation) {
                    $saveTranslation['id'] = $findTranslation['id'];
                }

                $saveTranslation['allow_html'] = 1;
                $saveTranslation['allow_scripts'] = 1;

                db_save('multilanguage_translations', $saveTranslation);
            }
        }
    }

    public function findTranslate($filter)
    {

        if (!isset($filter['locale']) || empty($filter['locale'])) {
            $filter['locale'] = $this->getCurrentLocale();
        }

        $filter['single'] = 1;

        unset($filter['field_value']);

        return db_get('multilanguage_translations', $filter);
    }


    public function getTranslate($data)
    {
        if (!isset($data[$this->relId])) {
            return $data;
        }

        $filter = array();
        $filter['no_limit'] = 1;
        $filter['locale'] = $this->getCurrentLocale();
        $filter['rel_type'] = $this->relType;
        $filter['enable_triggers'] = false;

        $translates = db_get('multilanguage_translations', $filter);
        if ($translates) {
            foreach ($translates as $translate_item) {
                if (isset($translate_item['rel_type']) and $translate_item['rel_type'] == $this->relType) {
                    if (isset($translate_item['rel_id']) and $translate_item['rel_id'] == $data[$this->relId]) {
                        foreach ($this->columns as $column) {
                            if (isset($translate_item['field_name']) and $translate_item['field_name'] == $column) {
                                if (!empty($translate_item['field_value'])) {
                                    $data[$column] = $translate_item['field_value'];
                                }
                            }
                        }
                    }
                }
                $data['item_lang'] = $filter['locale'];
            }
        }

        return $data;
    }

    /* public function getTranslate($data) {

         echo 'Get Translation..<br />';

         if (!isset($data[$this->relId])) {
             return $data;
         }

         foreach ($this->columns as $column) {

             $filter = array();
             $filter['single'] = 1;
             $filter['limit'] = 1;
             $filter['locale'] = $this->getCurrentLocale();
             $filter['rel_type'] = $this->relType;
             $filter['rel_id'] = $data[$this->relId];
             $filter['field_name'] = $column;
             $filter['enable_triggers'] = false;
            // $filter['no_cache'] = true;

             $translate = db_get('multilanguage_translations', $filter);

             if (!empty($translate['field_value'])) {
                 $data[$column] = $translate['field_value'];
             }

             $data['item_lang'] = $filter['locale'];

         }

         return $data;
     }*/

    public function getCurrentLocale()
    {
        return mw()->lang_helper->current_lang();
    }
}
