<?php
only_admin_access();

$localeId = $params['locale_id'];
$getSupportedLocale = get_supported_locale_by_id($localeId);
if (!$getSupportedLocale) {
    return;
}
$displayName = $getSupportedLocale['display_name'];
$displayIcon = $getSupportedLocale['display_icon'];

if (empty($displayName)) {
    $displayName = $getSupportedLocale['language'];
}
?>

<script>
    $(document).ready(function () {

        mw.lib.require('bootstrap4');

        var uploader = mw.uploader({
            filetypes:"images,videos",
            multiple:false,
            element:"#mw_uploader"
        });

        $(uploader).bind("FileUploaded", function(event, data){
            mw.$("#mw_uploader_loading").hide();
            mw.$("#mw_uploader").show();
            mw.$("#upload_info").html("");
            $('.js-display-icon').attr('src', data.src);
            $('.js-display-icon-url').attr('value', data.src);
            $('.js-display-icon-remove').show();
        });

        $(uploader).bind('progress', function(up, file) {
            mw.$("#mw_uploader").hide();
            mw.$("#mw_uploader_loading").show();
            mw.$("#upload_info").html(file.percent + "%");
        });

        $(uploader).bind('error', function(up, file) {
            mw.notification.error("The file is not uploaded.");
        });


        $('.js-display-icon-remove').click(function (e) {
            $('.js-display-icon').attr('src', '');
            $('.js-display-icon-url').attr('value', '');
            $('.js-admin-supported-locale-edit-form').submit();
            $('.js-display-icon-remove').hide();
        });

        $('.js-admin-supported-locale-edit-form').submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: mw.settings.api_url + 'multilanguage/edit_locale',
                type: 'post',
                data: $(this).serialize(),
                success: function(data) {
                    if (data.success) {
                        mw.notification.success('<?php _e('Supported locale is saved!');?>');
                        $('.js-admin-supported-locale-edit-messages').html('<div class="alert alert-success"><?php _e('Supported locale is saved!'); ?></div>');
                    } else if (data.message) {
                        mw.notification.error(data.message);
                        $('.js-admin-supported-locale-edit-messages').html('<div class="alert alert-danger">' + data.message + '</div>');
                    } else {
                        mw.notification.error('<?php _e('Please, fill all fields.'); ?>');
                        $('.js-admin-supported-locale-edit-messages').html('<div class="alert alert-danger"><?php _e('Please, fill all fields.'); ?></div>');
                    }
                    mw.reload_module_everywhere('multilanguage/list');
                    mw.reload_module_everywhere('multilanguage/change_language');
                }
            });
        });
    });
</script>
<form method="post" class="js-admin-supported-locale-edit-form">

    <div class="form-group">
    Display Name:
    <input type="text" name="display_name" value="<?php echo $displayName; ?>" class="form-control" />
    </div>

    Display Icon: <br />
    <img src="<?php echo $displayIcon; ?>" class="js-display-icon" style="max-width:100px;max-height: 100px;" >
    <span id="mw_uploader" class="mw-ui-btn">
    <span class="fa fa-upload"></span>
    <span style="padding-left: 5px"> Upload file <span id="upload_info"></span>
    </span>
    </span>
    <div class="mw-ui-btn js-display-icon-remove" <?php if (empty($displayIcon)): ?>style="display: none;" <?php endif; ?>><span class="fa fa-times"></span> <span style="padding-left: 5px">Remove</span></div>

    <input type="hidden" name="display_icon" value="<?php echo $displayIcon; ?>" class="form-control js-display-icon-url" />

    <div class="form-group"  style="margin-top:15px">
    <input type="hidden" name="locale_id" value="<?php echo $localeId; ?>">
    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
    </div>

</form>
<div class="js-admin-supported-locale-edit-messages" style="margin-top:15px"></div>
