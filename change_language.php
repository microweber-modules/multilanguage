<?php
$supportedLanguages = db_get('supported_locales', array());
$currentLanguage = mw()->lang_helper->current_lang();
$currentLanguage = get_short_abr($currentLanguage);
?>
<style>
    .module-multilanguage-change-language .flag-icon{
        margin-right: 7px;
    }
    .module-multilanguage-change-language{
        display: inline-block;
    }
</style>
<?php if (!empty($supportedLanguages)): ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#switch_language_ul li').on('click', function () {
            var selected = $(this).data('value');
            var is_admin = <?php if (defined('MW_FRONTEND')) { echo 0; } else { echo 1; } ?>;
            $.post(mw.settings.api_url + "change_language", { locale: selected, is_admin: is_admin })
                .done(function(data) {
                    if (data.refresh) {
                        if (data.location) {
                            window.location.href = data.location;
                        } else {
                            location.reload();
                        }
                    }
                });
        });
        mw.dropdown();
    });
</script>

<script>
    mw.lib.require('flag_icons');
</script>

<div class="mw-dropdown mw-dropdown-default">
    <span class="mw-dropdown-value mw-ui-btn mw-ui-btn-medium mw-ui-btn-info mw-dropdown-val">
        <span class="flag-icon flag-icon-<?php echo get_flag_icon($currentLanguage); ?> m-r-10"></span> <?php echo strtoupper($currentLanguage); ?>
    </span>
    <div class="mw-dropdown-content">
        <ul id="switch_language_ul">
            <?php foreach($supportedLanguages as $language): ?>
                <li <?php if ($currentLanguage == get_short_abr($language['locale'])): ?> selected="" <?php endif; ?> data-value="<?php print $language['locale'] ?>" style="color:#000;">
				<span class="flag-icon flag-icon-<?php echo get_flag_icon($language['locale']); ?> m-r-10"></span> <?php echo strtoupper($language['locale']) ?>
				</li>
            <?php endforeach; ?>
            <li style="color:#000;text-align: center;" onclick="window.location.href = '<?php echo admin_url() ?>view:modules/load_module:multilanguage';">
                <?php _e('Settings'); ?>
            </li>
        </ul>
    </div>
</div>
<?php endif; ?>
