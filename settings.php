<script type="text/javascript">
    $(document).ready(function () {
        mw.options.form('.module-settings-group-multilanguage', function () {
            mw.notification.success("All changes are saved.");
        });
    });
</script>
<div class="module-settings-group-multilanguage">

    <?php
    $langs = array();
    foreach (get_supported_languages(1) as $supported_language) {
        $langs[$supported_language['locale']] = $supported_language['language'];
    }
    ?>

    <?php if ($langs): ?>
    <div class="mw-ui-field-holder">
        <label class="mw-ui-label" style="float:left;margin-right: 5px;">
            <?php _e("Default Website Language"); ?>
            <br>
            <small>
                <?php _e("You can set the default language for your website."); ?>
            </small>
        </label>
        <?php
        $def_language = get_option('language', 'website');

        if ($def_language == false) {
            $def_language = 'en';
        }
        ?>
        <?php if($langs) : ?>
            <select id="user_lang" name="language" class="mw-ui-field mw_option_field" option-group="website">
                <option disabled="disabled"><?php _e('Select Language...'); ?></option>
                <?php foreach($langs as $key=>$lang): ?>
                    <option <?php if ($def_language == $key): ?> selected="" <?php endif; ?> value="<?php print $key ?>" ><?php print $lang ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($langs): ?>
    <div class="mw-ui-field-holder">
        <label class="mw-ui-label" style="float:left;margin-right: 5px;">
            <?php _e("Homepage Language"); ?>
        </label>
        <?php
        $def_language = get_option('homepage_language', 'website');

        if ($def_language == false) {
            $def_language = 'en';
        }
        ?>
        <?php if($langs) : ?>
            <select id="user_homepage_lang" name="homepage_language" class="mw-ui-field mw_option_field" option-group="website">
                <option disabled="disabled"><?php _e('Select Language...'); ?></option>
                <option value="none">None</option>
                <?php foreach($langs as $key=>$lang): ?>
                    <option <?php if ($def_language == $key): ?> selected="" <?php endif; ?> value="<?php print $key ?>" ><?php print $lang ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="mw-ui-box-no-bg" style="margin-top: 15px;">
        <b style="margin-right: 10px;">Multilanguage is active?</b>
        <label class="mw-switch mw-switch-action">
            <input class="mw_option_field" type="checkbox" autocomplete="off" name="is_active" <?php if (get_option('is_active','multilanguage_settings') == 'y'):?>checked="checked"<?php endif;?> option-group="multilanguage_settings" data-value-checked="y" data-value-unchecked="n">
            <span class="mw-switch-off">No</span>
            <span class="mw-switch-on">Yes</span>
            <span class="mw-switcher"></span>
        </label>
    </div>

    <div class="mw-ui-box-no-bg" style="margin-top: 15px;">
        <b style="margin-right: 10px;">Add prefix for all languages</b>
        <label class="mw-switch mw-switch-action">
            <input class="mw_option_field" type="checkbox" autocomplete="off" name="add_prefix_for_all_languages" <?php if (get_option('add_prefix_for_all_languages','multilanguage_settings') == 'y'):?>checked="checked"<?php endif;?> option-group="multilanguage_settings" data-value-checked="y" data-value-unchecked="n">
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

    <div class="mw-ui-field-holder" style="margin-top: 15px;">
        <label class="mw-ui-label" style="float:left;margin-right: 5px;padding-top: 10px;">
            <?php _e("Geolocation Provider"); ?>
        </label>
        <select name="geolocation_provider" class="mw-ui-field mw_option_field js-geolocation-provider" style="width: 200px;" option-group="multilanguage_settings">
            <option value="browser_detection">Browser Detection</option>
            <option value="domain_detection">Domain Detection</option>
            <option value="geoip_browser_detection">GEO-IP + Browser Detection</option>
            <option value="microweber">Microweber Geo Api</option>
            <option value="ipstack_com">IpStack.com</option>
        </select>
        <a class="mw-ui-btn mw-ui-btn-outline mw-ui-btn-info" onclick="testGeoApi();" style="margin-top: -4px">
            <span class="mw-icon-beaker"></span> Test Geo Api
        </a>
    </div>

    <script>
        $(document).ready(function () {
            $('.js-geolocation-provider').change(function () {
                    if ($(this).val() == 'ipstack_com') {
                        $('.js-ipstack-com').fadeIn();
                    } else {
                        $('.js-ipstack-com').fadeOut();
                    }
            });
        });
        function testGeoApi() {
            var client_details = {}
            // client_details.ip = $('#ip').val();

            $.post("<?php print site_url('api/multilanguage/geolocaiton_test'); ?>", client_details, function (msg) {
                mw.tools.modal.init({
                    html: "<pre>" + msg + "</pre>",
                    title: "<?php _e('Geo API Results...'); ?>"
                });
            });
        }
    </script>

    <?php
    $displayIstack = 'display:none;';
    if (get_option('geolocation_provider','multilanguage_settings') == 'ipstack_com') {
        $displayIstack = '';
    }
    ?>

    <div class="mw-ui-box js-ipstack-com" style="<?php echo $displayIstack; ?> margin-top:20px;">
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
