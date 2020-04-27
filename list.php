<?php
only_admin_access();
?>
<script type="text/javascript">
    $(document).ready(function () {

        $('.js-option-field-change-is-active').change(function (e) {

            is_checked = $(this).is(':checked');
            supported_language_id = $(this).attr('supported_language_id');

            $.post("<?php print site_url('api/multilanguage/supported_locale/set_active'); ?>", {is_active:is_checked, id:supported_language_id}, function (msg) {
                if (is_checked === true) {
                    mw.notification.success('<?php _e('Language is enabled!');?>');
                } else {
                    mw.notification.error('<?php _e('Language is disabled!');?>');
                }
            });
        });

    });
</script>
<table class="mw-ui-table mw-full-width mw-ui-table-basic" style="margin-top: 30px;">
    <thead>
    <tr>
        <th style="width: 3%;"></th>
        <th style="width: 10%;"><?php echo _e('Locale');?></th>
        <th style="width:25%;"><?php echo _e('Language');?></th>
        <th style="width: 20%;"><?php echo _e('Dispaly Locale');?></th>
        <th style="width: 20%;"><?php echo _e('Dispaly Name');?></th>
        <th style="width: 15%;"><?php echo _e('Dispaly Icon');?></th>
        <th style="width: 7%;"></th>
        <th style="width: 20%;"></th>
        <th style="width: 10%;">Active</th>
    </tr>
    </thead>
    <tbody class="js-tbody-supported-locales">
    <?php
    $defaultLang = mw()->lang_helper->default_lang();
    $supportedLanguages = get_supported_languages();
    if (!empty($supportedLanguages)):
        $isl=1;
        foreach($supportedLanguages as $language):
            ?>
            <tr style="background: #fff;" class="js-browser-redirect-tr-<?php echo $language['locale']; ?>">
                <td><span class="mw-icon-drag show-on-hover"></span></td>
                <td><?php echo strtoupper($language['locale']); ?></td>
                <td>
                    <span class="flag-icon flag-icon-<?php echo get_flag_icon($language['locale']); ?> m-r-10"></span> <?php echo $language['language']; ?>

                    <?php if (strtolower($defaultLang) == strtolower($language['locale'])): ?>
                    (<?php _e('Default'); ?>)
                    <?php endif; ?>
                </td>
                <td><?php echo $language['display_locale']; ?></td>
                <td><?php echo $language['display_name']; ?></td>
                <td><img src="<?php echo $language['display_icon']; ?>" style="max-width:22px;max-height: 22px;" /></td>
                <td>
                    <input class="js-supported-language-order-numbers js-supported-language-order-number-<?php echo $language['id']; ?>" name="<?php echo $language['id']; ?>" data-initial-value="<?php echo $isl; ?>" value="<?php echo $isl; ?>" type="number" style="display:none;font-size:22px;border: 0px;width: 35px;" min="1">
                    <a href="#" class="show-on-hover" onclick="updateOrderNumber(<?php echo $language['id']; ?>, 'down')"><span class="mw-icon-arrow-up-a js-update-order-number"></span></a>
                    <a href="#" class="show-on-hover" onclick="updateOrderNumber(<?php echo $language['id']; ?>, 'up')"><span class="mw-icon-arrow-down-a js-update-order-number"></span></a>
                </td>
                <td>
                    <a href="javascript:;" onClick="editSuportedLanguage('<?php echo $language['id']; ?>')" class="mw-ui-btn mw-ui-btn-medium show-on-hover">
                        <?php echo _e('Edit');?>
                    </a>
                    <?php
                    if ($defaultLang !== $language['locale']):
                        ?>
                        <a href="javascript:;" onClick="deleteSuportedLanguage('<?php echo $language['id']; ?>')" class="mw-ui-btn mw-ui-btn-medium show-on-hover">
                            <?php echo _e('Delete');?>
                        </a>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="mw-ui-box-no-bg">
                        <label class="mw-switch mw-switch-action">
                            <input class="mw_option_field js-option-field-change-is-active" type="checkbox" supported_language_id="<?php echo $language['id']; ?>" autocomplete="off" value="y" name="is_active" <?php if ($language['is_active'] == 'y'):?>checked="checked"<?php endif;?> data-value-checked="y" data-value-unchecked="n">
                            <span class="mw-switch-off">No</span>
                            <span class="mw-switch-on">Yes</span>
                            <span class="mw-switcher"></span>
                        </label>
                    </div>
                </td>
            </tr>
        <?php $isl++; endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="5"><?php _e('No supported languages found.'); ?></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
