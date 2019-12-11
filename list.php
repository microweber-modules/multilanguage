<table class="mw-ui-table mw-full-width mw-ui-table-basic" style="margin-top: 30px;">
    <thead>
    <tr>
        <th style="width: 200px;"><?php echo _e('Locale');?></th>
        <th><?php echo _e('Language');?></th>
        <th style="width:190px;"><?php echo _e('Action');?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $supportedLanguages = db_get('supported_locales', 'no_cache=true');
    if (!empty($supportedLanguages)):
        foreach($supportedLanguages as $language):
            ?>
            <tr class="js-browser-redirect-tr-<?php echo $language['locale']; ?>">
                <td><?php echo $language['locale']; ?></td>
                <td><span class="flag-icon flag-icon-<?php echo get_flag_icon($language['locale']); ?> m-r-10"></span> <?php echo $language['language']; ?></td>
                <td>
                    <a href="javascript:;" onClick="deleteSuportedLanguage('<?php echo $language['locale']; ?>')" class="mw-ui-btn mw-ui-btn-medium"><?php echo _e('Delete');?></a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="5"><?php e_('No supported languages found.'); ?></td>
        </tr>
    <?php endif; ?>

    </tbody>
</table>