<?php
/**
 * Dev: Bozhidar Slaveykov
 * Emai: bobi@microweber.com
 * Date: 11/18/2019
 * Time: 10:26 AM
 */
?>

<style>
    .js-dropdown-text-language{
        justify-content: start;
    }
    .mw-module-language-settings .mw-ui-btn,
    .mw-module-language-settings .mw-dropdown{
        vertical-align: top;
    }
    .mw-icon-drag {
        cursor: grab;
        font-size: 20px;
    }
    .js-update-order-number {
        font-size: 18px;
        margin-right: 5px;
        color: #3b3b3b85;
    }
</style>

<?php
$langs = mw()->lang_helper->get_all_lang_codes();
?>

<script type="text/javascript">
    $(document).ready(function () {

        add_language_key = false;
        add_language_value = false;

        $('.js-add-language').on('click', function () {

            if (add_language_key == false || add_language_value == false) {
                mw.notification.error('<?php _e('Please, select language.'); ?>');
                return;
            }

            $.post(mw.settings.api_url + "add_language", { locale: add_language_key, language: add_language_value })
                .done(function(data) {
                    mw.reload_module_everywhere('multilanguage/list');
                    // mw.reload_module('multilanguage/change_language');
                    $('.js-dropdown-text-language').html("<?php _e('Select Language...'); ?>");
                    add_language_key = false;
                    add_language_value = false;
                });
        });


        $('#add_language_ul li').on('click', function () {

            var key = $(this).data('key');
            var value = $(this).data('value');

            add_language_key = key;
            add_language_value = value;

            $('.js-dropdown-text-language').html('<span class="flag-icon flag-icon-'+key+' m-r-10" style=""></span>' + value);

        });

       $('.js-tbody-supported-locales').sortable({
           distance: 40,
           onDrop: function(item) {
               $(item).removeClass("dragged").removeAttr("style");
               $("body").removeClass("dragging");
               getInitialOrder('.js-tbody-supported-locales tr');
           }
        });

        getInitialOrder('.js-tbody-supported-locales tr');

        //bind stuff to number inputs
        $('.js-tbody-supported-locales tr input[type="number"]').focus(function(){
            $(this).select();
        }).change(function(){
            updateAllNumbers($(this), '.js-tbody-supported-locales input');
            reorderItems('.js-tbody-supported-locales tr', '.js-tbody-supported-locales');
        }).keyup(function(){
            updateAllNumbers($(this), '.js-tbody-supported-locales input');
        });

    });

    function submitNewOrderNumbers() {

        var languages = [];
        $('.js-supported-language-order-numbers').each(function () {
            languages.push({
                id: $(this).attr('name'),
                position: $(this).attr('data-initial-value')
            });
        });

        $.post(mw.settings.api_url + "sort_language", {ids: languages})
            .done(function (data) {
                // Done
            });

    }

    function updateOrderNumber(id, direct) {
        var new_val = parseInt($('.js-supported-language-order-number-' + id).val());
        if (direct == 'up') {
            new_val = new_val + 1;
        } else {
            new_val = new_val - 1;
        }
        if (new_val < 1) {
            new_val = 1;
        }
        $('.js-supported-language-order-number-' + id).val(new_val);
        updateAllNumbers($('.js-supported-language-order-number-' + id), '.js-tbody-supported-locales input');
        reorderItems('.js-tbody-supported-locales tr', '.js-tbody-supported-locales');
    }

    function getInitialOrder(obj){
        var num = 1;
        $(obj).each(function(){
            //set object initial order data based on order in DOM
            $(this).find('input[type="number"]').val(num).attr('data-initial-value', num);
            num++;
        });
        $(obj).find('input[type="number"]').attr('max', $(obj).length); //give it an html5 max attr based on num of objects
    }

    function updateAllNumbers(currObj, targets){
        var delta = currObj.val() - currObj.attr('data-initial-value'), //if positive, the object went down in order. If negative, it went up.
            c = parseInt(currObj.val(), 10), //value just entered by user
            cI = parseInt(currObj.attr('data-initial-value'), 10), //original object val before change
            top = $(targets).length;

        //if the user enters a number too high or low, cap it
        if(c > top){
            currObj.val(top);
        }else if(c < 1){
            currObj.val(1);
        }

        $(targets).not($(currObj)).each(function(){ //change all the other objects
            var v = parseInt($(this).val(), 10); //value of object changed

            if (v >= c && v < cI && delta < 0){ //object going up in order pushes same-numbered and in-between objects down
                $(this).val(v + 1);
            } else if (v <= c && v > cI && delta > 0){ //object going down in order pushes same-numbered and in-between objects up
                $(this).val(v - 1);
            }
        }).promise().done(function(){
            //after all the fields update based on new val, set their data element so further changes can be tracked
            //(but ignore if no value given yet)
            $(targets).each(function(){
                if($(this).val() !== ""){
                    $(this).attr('data-initial-value', $(this).val());
                }
            });
        });
    }

    function reorderItems(things, parent) {
        for (var i = 1; i <= $(things).length; i++) {
            $(things).each(function () {
                var x = parseInt($(this).find('input').val(), 10);
                if (x === i) {
                    $(this).appendTo(parent);
                }
            });
        }

        submitNewOrderNumbers();
    }

    function deleteSuportedLanguage(language_id) {
        mw.tools.confirm('<?php _e('Are you sure you want to delete?'); ?>', function () {
            $.post(mw.settings.api_url + "delete_language", { id:language_id })
                .done(function(data) {
                    mw.reload_module_everywhere('multilanguage/list');
                    // mw.reload_module('multilanguage/change_language');
                });
        });
    }
</script>

<script>
    mw.lib.require('flag_icons');
</script>
<div class="mw-ui-box mw-ui-box-content">
    <div class="mw-module-language-settings">
        <label class="mw-ui-label"><?php _e('Add new language');?></label>
        <?php if($langs) : ?>
            <div class="mw-dropdown mw-dropdown-default" style="width:300px;">
                        <span class="mw-dropdown-value mw-ui-btn mw-ui-btn-normal mw-dropdown-val js-dropdown-text-language">
                            <?php _e('Select Language...'); ?>
                        </span>
                <div class="mw-dropdown-content">
                    <ul id="add_language_ul" style="max-height: 300px;">
                        <?php foreach($langs as $key=>$lang): ?>
                            <li data-key="<?php print $key ?>" data-value="<?php print $lang ?>" style="color:#000;">
                                <span class="flag-icon flag-icon-<?php echo get_flag_icon($key); ?> m-r-10"></span> <?php echo $lang; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
        <button class="mw-ui-btn mw-ui-btn-normal mw-ui-btn-notification js-add-language"><span class="mw-icon-plus"></span> &nbsp; <?php _e('Add');?></button>

        <module type="multilanguage/list" />

    </div>
</div>