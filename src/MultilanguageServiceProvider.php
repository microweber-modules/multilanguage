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
use MicroweberPackages\CustomField\Models\CustomField;
use MicroweberPackages\CustomField\Models\CustomFieldValue;
use MicroweberPackages\Multilanguage\Observers\MultilanguageObserver;
use MicroweberPackages\Option\Models\ModuleOption;
use MicroweberPackages\Page\Models\Page;
use MicroweberPackages\Post\Models\Post;
use MicroweberPackages\Product\Models\Product;
use MicroweberPackages\Form\FormElementBuilder;

class MultilanguageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (defined('MW_DISABLE_MULTILANGUAGE')) {
            return;
        }

        // Check multilanguage is active
        if (is_module('multilanguage') && get_option('is_active', 'multilanguage_settings') !== 'y') {
            return;
        }

        Content::observe(MultilanguageObserver::class);
        Category::observe(MultilanguageObserver::class);
        Post::observe(MultilanguageObserver::class);
        Product::observe(MultilanguageObserver::class);
        Page::observe(MultilanguageObserver::class);
        ModuleOption::observe(MultilanguageObserver::class);
        MailTemplate::observe(MultilanguageObserver::class);
        CustomField::observe(MultilanguageObserver::class);
        CustomFieldValue::observe(MultilanguageObserver::class);

        $this->app->bind(FormElementBuilder::class, function ($app) {
            return new MultilanguageFormElementBuilder();
        });

        $this->loadMigrationsFrom(__DIR__ . '/migrations/');
    }
}
