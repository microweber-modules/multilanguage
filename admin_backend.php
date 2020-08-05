<?php only_admin_access(); ?>

<?php if (MW_VERSION < '1.2.0'): ?>
    <script>mw.lib.require('bootstrap4');</script>
<?php endif; ?>
<?php
$from_live_edit = false;
if (isset($params["live_edit"]) and $params["live_edit"]) {
    $from_live_edit = $params["live_edit"];
}
?>

<?php if (isset($params['backend'])): ?>
    <module type="admin/modules/info"/>
<?php endif; ?>

<div class="card style-1 mb-3 <?php if ($from_live_edit): ?>card-in-live-edit<?php endif; ?>">
    <div class="card-header">
        <?php $module_info = module_info($params['module']); ?>
        <h5>
            <img src="<?php echo $module_info['icon']; ?>" class="module-icon-svg-fill"/> <strong><?php echo $module_info['name']; ?></strong>
        </h5>
    </div>

    <div class="card-body pt-3">
        <div class="mw-modules-tabs">

            <div class="mw-accordion-item">
                <div class="mw-ui-box-header mw-accordion-title">
                    <div class="header-holder">
                        <i class="mw-icon-navicon-round"></i> Languages
                    </div>
                </div>
                <div class="mw-accordion-content mw-ui-box mw-ui-box-content">
                    <module type="multilanguage/language_settings"/>
                </div>
            </div>

            <div class="mw-accordion-item">
                <div class="mw-ui-box-header mw-accordion-title">
                    <div class="header-holder">
                        <i class="mw-icon-beaker"></i> Settings
                    </div>
                </div>
                <div class="mw-accordion-content mw-ui-box mw-ui-box-content">

                    <module type="multilanguage/settings"/>

                </div>
            </div>

        </div>
        <br/>
        <module type="help/modal_with_button" for_module="multilanguage"/>
    </div>
</div>





