<?php
namespace MicroweberPackages\Multilanguage\Models;

use Illuminate\Database\Eloquent\Model;
use MicroweberPackages\Database\Traits\CacheableQueryBuilderTrait;

class MultilanguageSupportedLocales extends Model
{

    public $timestamps = false;

    use CacheableQueryBuilderTrait;

}