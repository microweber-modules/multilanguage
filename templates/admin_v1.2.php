<li class="mx-1 language-selector">
    <?php
    $current_lang = current_lang();
    if ($current_lang == 'en' OR $current_lang == 'undefined') {
        $current_lang_flag = 'gb';
    } else {
        $current_lang_flag = $current_lang;
    } ?>
    <button type="button" class="btn btn-outline-secondary btn-rounded btn-icon" data-toggle="dropdown"><i class="flag-icon flag-icon-<?php print $current_lang_flag; ?>"></i></button>
    <div class="dropdown-menu dropdown-languages">
        <?php
        $langs = get_available_languages();
        $selected_lang = isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'en';
        foreach ($langs as $lang): ?>
            <?php
            if ($lang == 'en') {
                $lang_flag = 'gb';
            } else {
                $lang_flag = $lang;
            }
            ?>
            <button onclick='mw.admin.language("<?php print $lang; ?>");' class="dropdown-item <?php if ($selected_lang == $lang): ?>active<?php endif; ?>">
                <i class="flag-icon flag-icon-<?php print $lang_flag; ?>"></i> <span class="text-uppercase"><?php print $lang ?></span>
            </button>
        <?php endforeach; ?>
    </div>
</li>