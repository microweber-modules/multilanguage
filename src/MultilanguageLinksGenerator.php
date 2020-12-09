<?php
/**
 * Created by PhpStorm.
 * User: Bojidar
 * Date: 12/9/2020
 * Time: 4:52 PM
 */

class MultilanguageLinksGenerator
{
    private $_fetched_ml_content;

    public function links($type = 'all')
    {
        $links = [];

        $allActiveLangs = get_supported_languages();
        $defaultLang = current_lang();

        foreach($allActiveLangs as $lang) {
            change_language_by_locale($lang['locale']);
            $this->generate($type, $lang);
        }

        foreach ($this->_fetched_ml_content as $langLocale=>$fetched_contents) {
            foreach ($fetched_contents as $fetched_content) {
                $multilangUrlsMapId[$fetched_content['item']['id']][$langLocale] = $fetched_content;
            }
        }

        foreach ($multilangUrlsMapId as $contentId=>$contentData) {
            $multiCatLangUrls = [];
            foreach ($contentData as $contentDataLang=>$contentDataDetails) {
                $multiCatLangUrls[$contentDataLang] = $contentDataDetails['link'];
            }

            foreach ($contentData as $contentDataLocale=>$contentDataFull) {
                $links[] = [
                    'original_link'=>$contentDataFull['link'],
                    'updated_at'=>$contentDataFull['item']['updated_at'],
                    'multilanguage_links'=>$multiCatLangUrls
                ];
            }
        }

        change_language_by_locale($defaultLang);

        return $links;
    }

    public function contentLinks()
    {
        return $this->links('content');
    }

    public function categoryLinks()
    {

        return $this->links('category');
    }

    private function generate($type ='all', $lang = false)
    {
        $generateContent = true;
        $generateCategories = true;

        if ($type == 'category') {
            $generateContent = false;
            $generateCategories = true;
        }

        if ($type == 'content') {
            $generateContent = true;
            $generateCategories = false;
        }

       if ($generateCategories) {
           $categories = get_categories('no_limit=1');
           foreach ($categories as $category) {
               if (!empty($lang)) {
                   $this->_fetch_link_multilang($category, 'category', $lang);
               }

           }
       }

        if ($generateContent) {
            $cont = get_content('is_active=1&is_deleted=0&limit=2500&fields=id,content_type,url,updated_at&orderby=updated_at desc');
            if (!empty($cont)) {
                foreach ($cont as $item) {
                    if (!empty($item['content_type']) && !empty($item['url']) && in_array($item['content_type'], ['page', 'product', 'post'])) {

                        if (!empty($lang)) {
                            $this->_fetch_link_multilang($item, 'content', $lang);
                        }
                    }
                }
            }
        }
    }

    private function _fetch_link_multilang($item, $type, $lang)
    {
        if($type === 'category') {
            $link = category_link($item['id']);
        } else if($type === 'content') {
            $link = app()->content_manager->link($item['id']);
        }

        $this->_fetched_ml_content[$lang['locale']][] = [
            'id'=>$item['id'],
            'item'=>$item,
            'type'=>$type,
            'link'=>$link,
            'lang'=>$lang
        ];
    }
}