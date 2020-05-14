<?php

event_bind('mw.admin.header.toolbar', function () {
    echo '<div class="mw-ui-col pull-right">
         <module type="multilanguage/change_language"></module>
    </div>';
});

event_bind('live_edit_toolbar_action_buttons', function () {
    echo '<module type="multilanguage/change_language"></module>';
});
