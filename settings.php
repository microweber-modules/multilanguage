<script type="text/javascript">
    $(document).ready(function () {
        mw.options.form('.module-settings-group-multilanguage', function () {
            mw.notification.success("All changes are saved.");
        });
    });
</script>
<div class="module-settings-group-multilanguage">

    <div class="mw-ui-box-no-bg">
        <b style="margin-right: 10px;">Multilanguage is active?</b>
        <label class="mw-switch mw-switch-action">
            <input class="mw_option_field" type="checkbox" autocomplete="off" name="is_active" <?php if (get_option('is_active','multilanguage_settings') == 'y'):?>checked="checked"<?php endif;?> option-group="multilanguage_settings" data-value-checked="y" data-value-unchecked="n">
            <span class="mw-switch-off">No</span>
            <span class="mw-switch-on">Yes</span>
            <span class="mw-switcher"></span>
        </label>
    </div>

    <div class="mw-ui-box-no-bg" style="margin-top:20px;">
        <b style="margin-right: 10px;">Switch language by IP Geolocation</b>
        <label class="mw-switch mw-switch-action">
            <input class="mw_option_field" type="checkbox" autocomplete="off" name="use_geolocation" <?php if (get_option('use_geolocation','multilanguage_settings') == 'y'):?>checked="checked"<?php endif;?> option-group="multilanguage_settings" data-value-checked="y" data-value-unchecked="n">
            <span class="mw-switch-off">No</span>
            <span class="mw-switch-on">Yes</span>
            <span class="mw-switcher"></span>
        </label>
    </div>

    <div class="mw-ui-box" style="margin-top:20px;">
        <div class="mw-ui-box-header">
            <span class="mw-icon-gear"></span><span>IpStack.com Integration</span>
        </div>
        <div class="mw-ui-box-content">

            <div class="demobox">
                <label class="mw-ui-label">API Access Key</label>
                <input name="ipstack_api_access_key" style="width: 100%;" option-group="multilanguage_settings" value="<?php echo get_option('ipstack_api_access_key','multilanguage_settings');?>" class="mw_option_field mw-ui-field mw-options-form-binded" type="text">
            </div>

        </div>
    </div>
</div>