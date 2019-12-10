<?php

class TranslateContent extends TranslateTable {

    protected $relId = 'id';
    protected $relType = 'content';

    protected $columns = [
        'title',
        'url',
        'description',
        'content',
        'content_body',
        'content_meta_title',
        'content_meta_keywords'
    ];

}
