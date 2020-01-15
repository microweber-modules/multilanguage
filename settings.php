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
            <input class="mw_option_field" type="checkbox" name="is_active" value="<?php echo get_option('use_geolocation','multilanguage');?>" option-group="multilanguage" data-value-checked="1" data-value-unchecked="0">
            <span class="mw-switch-off">No</span>
            <span class="mw-switch-on">Yes</span>
            <span class="mw-switcher"></span>
        </label>
    </div>

    <div class="mw-ui-box-no-bg" style="margin-top:20px;">
        <b style="margin-right: 10px;">Switch language by IP Geolocation</b>
        <label class="mw-switch mw-switch-action">
            <input class="mw_option_field" type="checkbox" name="use_geolocation" value="<?php echo get_option('use_geolocation','multilanguage');?>" option-group="multilanguage" data-value-checked="1" data-value-unchecked="0">
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
                <input name="ipstack_api_access_key" style="width: 100%;" option-group="multilanguage" value="<?php echo get_option('ipstack_api_access_key','multilanguage');?>" class="mw_option_field mw-ui-field mw-options-form-binded" type="text">
            </div>

        </div>
    </div>
</div>