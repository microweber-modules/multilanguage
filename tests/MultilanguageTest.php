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
        $this->assertEquals($lang, mw()->lang_helper->current_lang());

        $languages = get_supported_languages();

        // Check default lang is exists on supported languages
        $locales = array();
        foreach ($languages as $language) {
            $locales[] = strtolower($language['locale']);
        }
        $default_lang = mw()->lang_helper->default_lang();
        $default_lang = strtolower($default_lang);

        $this->assertEquals(true, in_array($default_lang, $locales));

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

        $this->assertEquals(true, in_array($locale, $locales));

    }

    public function testSwitchLanguage()
    {
        mw()->lang_helper->set_current_lang('bg');
        $this->assertEquals('bg', mw()->lang_helper->current_lang());
    }

    public function testTranslateNewOption()
    {
        $this->_addNewMultilanguageOption('bozhidar', 'Bozhidar', 'Божидар');
        $this->_addNewMultilanguageOption('slaveykov', 'Slaveykov', 'Славейков');
        $this->_addNewMultilanguageOption('health', 'Health', 'Здраве');
        $this->_addNewMultilanguageOption('love', 'Love', 'Любов');
        $this->_addNewMultilanguageOption('apple', 'Apple', 'Ябълка');
        $this->_addNewMultilanguageOption('car', 'Car', 'Кола');
        $this->_addNewMultilanguageOption('rich', 'Rich', 'Богат');
    }

    private function _addNewMultilanguageOption($option_key, $en_option_value, $bg_option_value)
    {

        // Switch to english language
        mw()->lang_helper->set_current_lang('en');
        $this->assertEquals('en', mw()->lang_helper->current_lang());

        $option_group = 'new_option_test';

        // Add new english option
        $option = array();
        $option['option_value'] = $en_option_value;
        $option['option_key'] = $option_key;
        $option['option_group'] = $option_group;
        save_option($option);
        // Get option
        $this->assertEquals($en_option_value, get_option($option_key, $option_group));

        /**
         * TEST BULGARIAN LANGUAGE
         * Switch to bulgarian language
         */
        mw()->lang_helper->set_current_lang('bg');
        $this->assertEquals('bg', mw()->lang_helper->current_lang());

        // Update english option
        $option = array();
        $option['option_value'] = $bg_option_value;
        $option['option_key'] = $option_key;
        $option['option_group'] = $option_group;
        save_option($option);
        // Get bg option
        $this->assertEquals($bg_option_value, get_option($option_key, $option_group));


        /**
         * TEST ENGLISH LANGUAGE
         * Switch to english language
         */
        mw()->lang_helper->set_current_lang('en');
        $this->assertEquals('en', mw()->lang_helper->current_lang());
        // Get en option
        $this->assertEquals($en_option_value, get_option($option_key, $option_group));


        /**
         * TEST BULGARIAN LANGUAGE
         * Switch to bulgarian language
         */
        mw()->lang_helper->set_current_lang('bg');
        $this->assertEquals('bg', mw()->lang_helper->current_lang());
        // Get bg option
        $this->assertEquals($bg_option_value, get_option($option_key, $option_group));

    }

    public function testTranslateNewMenu()
    {

        // Switch to english language
        mw()->lang_helper->set_current_lang('en');
        $this->assertEquals('en', mw()->lang_helper->current_lang());

        $menu = array();
        $menu['title'] = 'Richest people in the world';
        $menu['url'] = 'richest-people-in-the-world';
        mw()->menu_manager->menu_create($menu);

        $get_menu = mw()->menu_manager->get_menu('url=richest-people-in-the-world&single=1');

        $this->assertEquals($get_menu['title'], $menu['title']);
        $this->assertEquals($get_menu['url'], $menu['url']);


        /**
         * TEST BULGARIAN LANGUAGE
         * Switch to bulgarian language
         */
        mw()->lang_helper->set_current_lang('bg');
        $this->assertEquals('bg', mw()->lang_helper->current_lang());

        $update = array();
        $update['menu_id'] = $get_menu['id'];
        $update['title'] = 'Най-богатите хора в света';
        $update['url'] = 'nai-bogatite-xora-v-sveta';

        mw()->menu_manager->menu_create($update);

        $get_menu = mw()->menu_manager->get_menu('id=' . $get_menu['id'] . '&single=1');

        $this->assertEquals($get_menu['title'], $update['title']);
        $this->assertEquals($get_menu['url'], $update['url']);

    }

    public function testDetectLangFromUrl()
    {
        $url = 'bg/tova-e-strahotniq-post.html';
        $detect = detect_lang_from_url($url);

        $this->assertEquals('bg', $detect['target_lang']);
        $this->assertEquals('tova-e-strahotniq-post.html', $detect['target_url']);


        $url = 'en/tova-e-strahotniq-post.html';
        $detect = detect_lang_from_url($url);

        $this->assertEquals('en', $detect['target_lang']);
        $this->assertEquals('tova-e-strahotniq-post.html', $detect['target_url']);


        $url = 'blqblq/tova-e-strahotniq-post.html';
        $detect = detect_lang_from_url($url);

        $this->assertEquals(false, $detect['target_lang']);
        $this->assertEquals('tova-e-strahotniq-post.html', $detect['target_url']);

    }

    public function testCheckLanguageIsCorrect()
    {
        $check = is_lang_correct('en');
        $this->assertEquals(true, $check);

        $check = is_lang_correct('bg');
        $this->assertEquals(true, $check);

        $check = is_lang_correct('mnogokesh');
        $this->assertEquals(false, $check);
    }

    public function testMultilanguageApi()
    {
        // Ad Greek
        $api = new MultilanguageApi();
        $output = $api->addLanguage([
            'locale' => 'gr',
            'language' => 'Greek'
        ]);

        $this->assertEquals(true, is_int($output));

        $languages = get_supported_languages();

        // Check default lang is exists on supported languages
        $locales = array();
        foreach ($languages as $language) {
            $locales[] = strtolower($language['locale']);
        }

        $this->assertEquals(true, in_array('gr', $locales));

        // Delete greek
        $output = $api->deleteLanguage(['id' => $output]);

        $languages = get_supported_languages();

        // Check default lang is exists on supported languages
        $locales = array();
        foreach ($languages as $language) {
            $locales[] = strtolower($language['locale']);
        }

        $this->assertEquals(false, in_array('gr', $locales));
    }

    public function testChangeLanguageApi()
    {
        $api = new MultilanguageApi();
        $output = $api->changeLanguage([
            'locale'=> 'bobi-money'
        ]);

        $this->assertEquals(true, is_string($output['error']));

        $output = $api->changeLanguage([
            'locale'=> 'bg'
        ]);

        $this->assertEquals(true, $output['refresh']);
    }
}