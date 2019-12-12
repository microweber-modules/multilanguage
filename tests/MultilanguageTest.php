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

    public function testTranslateNewPost()
    {
        // Add new english post

        

    }

}
