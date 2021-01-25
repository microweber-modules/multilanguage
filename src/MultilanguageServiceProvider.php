<?php
/*
 * This file is part of the Microweber framework.
 *
 * (c) Microweber CMS LTD
 *
 * For full license information see
 * https://github.com/microweber/microweber/blob/master/LICENSE
 *
 */

namespace MicroweberPackages\Multilanguage;

use Illuminate\Support\ServiceProvider;
use MicroweberPackages\Admin\MailTemplates\Models\MailTemplate;
use MicroweberPackages\Category\Models\Category;
use MicroweberPackages\Content\Content;
use MicroweberPackages\Multilanguage\Observers\MultilanguageObserver;
use MicroweberPackages\Option\Models\ModuleOption;
use MicroweberPackages\Page\Models\Page;
use MicroweberPackages\Post\Models\Post;
use MicroweberPackages\Product\Models\Product;

class MultilanguageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        Content::observe(MultilanguageObserver::class);
        Category::observe(MultilanguageObserver::class);
        Post::observe(MultilanguageObserver::class);
        Product::observe(MultilanguageObserver::class);
        Page::observe(MultilanguageObserver::class);
        ModuleOption::observe(MultilanguageObserver::class);
        MailTemplate::observe(MultilanguageObserver::class);

        $this->loadMigrationsFrom(__DIR__ . '/migrations/');
    }
}
