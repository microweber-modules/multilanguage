<?php
$supportedLanguages = db_get('supported_locales', array());
$currentLanguage = mw()->lang_helper->current_lang();
$currentLanguage = get_short_abr($currentLanguage);
?>
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
    <span class="mw-dropdown-value mw-ui-btn mw-ui-btn-info mw-dropdown-val">
        <span class="flag-icon flag-icon-<?php echo get_flag_icon($currentLanguage); ?> m-r-10"></span> <?php _e('Select Language...'); ?>
    </span>
    <div class="mw-dropdown-content">
        <ul id="switch_language_ul">
            <?php foreach($supportedLanguages as $language): ?>
                <li <?php if ($currentLanguage == get_short_abr($language['locale'])): ?> selected="" <?php endif; ?> data-value="<?php print $language['locale'] ?>" style="color:#000;"><span class="flag-icon flag-icon-<?php echo get_flag_icon($language['locale']); ?> m-r-10"></span><?php print $language['language'] ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>