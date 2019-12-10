<?php
only_admin_access();
/**
 * Dev: Bozhidar Slaveykov
 * Emai: bobi@microweber.com
 * Date: 11/18/2019
 * Time: 10:26 AM
 */
?>

<?php if (isset($params['backend'])): ?>
    <module type="admin/modules/info"/>
<?php endif; ?>

<style>
    .js-dropdown-text-language{
        justify-content: start;
    }
    .mw-module-language-settings .mw-ui-btn,
    .mw-module-language-settings .mw-dropdown{
        vertical-align: top;
    }
</style>

<?php
$langs = mw()->lang_helper->get_all_lang_codes();
?>

<script type="text/javascript">
    $(document).ready(function () {

        add_language_key = false;
        add_language_value = false;

        $('.js-add-language').on('click', function () {

            if (add_language_key == false || add_language_value == false) {
                mw.notification.error('<?php _e('Please, select language.'); ?>');
                return;
            }

            $.post(mw.settings.api_url + "add_language", { locale: add_language_key, language: add_language_value })
                .done(function(data) {
                    mw.reload_module_everywhere('multilanguage/list');
                   // mw.reload_module('multilanguage/change_language');
                });
        });


      /*  $('#add_language_ul li').on('click', function () {

            var key = $(this).data('key');
            var value = $(this).data('value');

            add_language_key = key;
            add_language_value = value;

            $('.js-dropdown-text-language').html('<span class="flag-icon flag-icon-'+key+' m-r-10" style=""></span>' + value);

        });*/
    });

    function deleteSuportedLanguage(language_key) {
        $.post(mw.settings.api_url + "delete_language", { locale: language_key })
            .done(function(data) {
                mw.reload_module_everywhere('multilanguage/list');
                // mw.reload_module('multilanguage/change_language');
            });
    }
</script>

<script>
    mw.lib.require('flag_icons');
</script>

<div id="mw-admin-content" class="admin-side-content">
    <div class="mw-module-language-settings">
        <div class="mw-ui-box mw-ui-box-content">
            <div>
                <label class="mw-ui-label"><?php _e('Add new language');?></label>
                <?php if($langs) : ?>

                    <div class="mw-dropdown mw-dropdown-default" style="width:300px;">
                        <span class="mw-dropdown-value mw-ui-btn mw-ui-btn-normal mw-dropdown-val js-dropdown-text-language">
                            <?php _e('Select Language...'); ?>
                        </span>
                        <div class="mw-dropdown-content">
                            <ul id="add_language_ul" style="max-height: 300px;">
                                <?php foreach($langs as $key=>$lang): ?>
                                    <li data-key="<?php print $key ?>" data-value="<?php print $lang ?>" style="color:#000;"><span class="flag-icon flag-icon-<?php echo get_flag_icon($key); ?> m-r-10"></span><?php print $lang ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
                <button class="mw-ui-btn mw-ui-btn-normal mw-ui-btn-notification js-add-language"><span class="mw-icon-plus"></span> &nbsp; <?php _e('Add');?></button>
            </div>
            <module type="multilanguage/list" />
        </div>
    </div>
</div>
