<?php
$supportedLanguages = db_get('supported_locales', array());
$currentLanguage = mw()->lang_helper->current_lang();
?>
<?php if (!empty($supportedLanguages)): ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#switch_language_ul li').on('click', function () {
            var selected = $(this).data('value');
            $.post(mw.settings.api_url + "change_language", { locale: selected })
                .done(function(data) {
                    location.reload();
                });
        });
    });
</script>

<script>
    mw.lib.require('flag_icons');
</script>

<div class="mw-dropdown mw-dropdown-default">
    <span class="mw-dropdown-value mw-ui-btn mw-ui-btn-info mw-dropdown-val"><span class="flag-icon flag-icon-<?php echo get_flag_icon($currentLanguage); ?> m-r-10"></span> <?php _e('Select Language...'); ?></span>
    <div class="mw-dropdown-content">
        <ul id="switch_language_ul">
            <?php foreach($supportedLanguages as $language): ?>
                <li <?php if ($currentLanguage == $language['locale']): ?> selected="" <?php endif; ?> data-value="<?php print $language['locale'] ?>" style="color:#000;"><span class="flag-icon flag-icon-<?php echo get_flag_icon($language['locale']); ?> m-r-10"></span><?php print $language['language'] ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>