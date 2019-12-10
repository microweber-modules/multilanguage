<?php

class TranslateCustomFields extends TranslateTable {

    protected $relId = 'id';
    protected $relType = 'custom_fields';

    protected $columns = [
        'name',
        'placeholder'
    ];

}