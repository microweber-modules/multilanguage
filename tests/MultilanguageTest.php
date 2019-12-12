<?php

class MultilanguageTest extends \Microweber\tests\TestCase
{
    public function testSupportedLanguages()
    {
        // Set default lang
        $lang = 'en';
        $option = array();
        $option['option_value'] = $lang;
        $option['option_key'] = 'language';
        $option['option_group'] = 'website';
        save_option($option);

        mw()->lang_helper->set_current_lang($lang);

        $languages = get_supported_languages();

        // Check default lang is exists on supported languages
        $locales = array();
        foreach ($languages as $language) {
            $locales[] = strtolower($language['locale']);
        }
        $default_lang = mw()->lang_helper->default_lang();
        $default_lang = strtolower($default_lang);
        $check = false;
        if (in_array($default_lang, $locales)) {
            $check = true;
        }

        $this->assertEquals(true, $check);

    }

    public function testAddNewLanguage()
    {

        $locale = 'bg';
        $language = 'Bulgarian';

        // Add language
        add_supported_language($locale, $language);

        $languages = get_supported_languages();

        $locales = array();
        foreach ($languages as $language) {
            $locales[] = strtolower($language['locale']);
        }

        $check = false;
        if (in_array($locale, $locales)) {
            $check = true;
        }

        $this->assertEquals(true, $check);

    }

    public function testSwitchLanguage()
    {

        mw()->lang_helper->set_current_lang('bg');

        var_dump(mw()->lang_helper->current_lang());
        die();
    }

    public function testTranslateNewOption()
    {
        // Switch to english language
        mw()->lang_helper->set_current_lang('en');

        $option_group = 'new_option_test';

        // Add new english option
        $option = array();
        $option['option_value'] = 'Apple';
        $option['option_key'] = 'fruit';
        $option['option_group'] = $option_group;
        save_option($option);

        // Get option
        $fruit = get_option('fruit', $option_group);
        $this->assertEquals('Apple', $fruit);

        // Switch to bulgarian language
        mw()->lang_helper->set_current_lang('bg');

        // Update english option
        $option = array();
        $option['option_value'] = 'Ябълка';
        $option['option_key'] = 'fruit';
        $option['option_group'] = $option_group;
        save_option($option);


        var_dump(db_get('translations', array()));

        die();
        // Get option
        $fruit = get_option('fruit', $option_group);
        $this->assertEquals('Ябълка', $fruit);

        // Switch to english language
        mw()->lang_helper->set_current_lang('en');

        // Get option
        $fruit = get_option('fruit', $option_group);

        var_dump($fruit);
        die();
        $this->assertEquals('Apple', $fruit);


       // var_dump(mw()->lang_helper->default_lang());
        //var_dump(mw()->lang_helper->current_lang());
        var_dump($fruit);

    }

}
