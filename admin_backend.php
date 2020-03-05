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

<script>
    mw.lib.require('bootstrap4');
</script>

<div id="mw-admin-content" class="admin-side-content">
    <div class="mw-modules-tabs">

        <div class="mw-accordion-item">
            <div class="mw-ui-box-header mw-accordion-title">
                <div class="header-holder">
                    <i class="mw-icon-navicon-round"></i> Languages
                </div>
            </div>
            <div class="mw-accordion-content mw-ui-box mw-ui-box-content">
                <module type="multilanguage/language_settings" />
            </div>
        </div>

        <div class="mw-accordion-item">
            <div class="mw-ui-box-header mw-accordion-title">
                <div class="header-holder">
                    <i class="mw-icon-beaker"></i> Settings
                </div>
            </div>
            <div class="mw-accordion-content mw-ui-box mw-ui-box-content">

                <module type="multilanguage/settings" />

            </div>
        </div>

    </div>
    <br />
    <module type="help/modal_with_button" for_module="multilanguage" />
</div>