<table class="mw-ui-table mw-full-width mw-ui-table-basic" style="margin-top: 30px;">
    <thead>
    <tr>
        <th style="width: 3%;"></th>
        <th style="width: 10%;"><?php echo _e('Locale');?></th>
        <th><?php echo _e('Language');?></th>
        <th style="width: 7%;"></th>
        <th style="width: 10%;"></th>
    </tr>
    </thead>
    <tbody class="js-tbody-supported-locales">
    <?php
    $defaultLang = mw()->lang_helper->default_lang();
    $supportedLanguages = get_supported_languages();
    if (!empty($supportedLanguages)):
        $isl=1;
        foreach($supportedLanguages as $language):
            ?>
            <tr style="background: #fff;" class="js-browser-redirect-tr-<?php echo $language['locale']; ?>">
                <td><span class="mw-icon-drag show-on-hover"></span></td>
                <td><?php echo strtoupper($language['locale']); ?></td>
                <td>
                    <span class="flag-icon flag-icon-<?php echo get_flag_icon($language['locale']); ?> m-r-10"></span> <?php echo $language['language']; ?>

                    <?php if (strtolower($defaultLang) == strtolower($language['locale'])): ?>
                    (<?php _e('Default'); ?>)
                    <?php endif; ?>
                </td>
                <td>
                    <input class="js-supported-language-order-numbers js-supported-language-order-number-<?php echo $language['id']; ?>" name="<?php echo $language['id']; ?>" data-initial-value="<?php echo $isl; ?>" value="<?php echo $isl; ?>" type="number" style="display:none;font-size:22px;border: 0px;width: 35px;" min="1">
                    <a href="#" class="show-on-hover" onclick="updateOrderNumber(<?php echo $language['id']; ?>, 'down')"><span class="mw-icon-arrow-up-a js-update-order-number"></span></a>
                    <a href="#" class="show-on-hover" onclick="updateOrderNumber(<?php echo $language['id']; ?>, 'up')"><span class="mw-icon-arrow-down-a js-update-order-number"></span></a>
                </td>
                <td>
                    <?php
                    if ($defaultLang !== $language['locale']):
                        ?>
                        <a href="javascript:;" onClick="deleteSuportedLanguage('<?php echo $language['id']; ?>')" class="mw-ui-btn mw-ui-btn-medium show-on-hover">
                            <?php echo _e('Delete');?>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php $isl++; endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="5"><?php _e('No supported languages found.'); ?></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>