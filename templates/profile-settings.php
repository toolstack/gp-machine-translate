<?php
declare(strict_types=1);

// Prevent direct access
use GpMachineTranslate\Providers\AbstractProvider;

if (! defined('ABSPATH')) {
    exit;
}

if (!isset($templateData['provider'], $templateData['userAuthClientId'], $templateData['userAuthKey'],)) {
    exit;
}
/** @var AbstractProvider $provider */
$provider = $templateData['provider'];
?>
<h3 id="gp-machine-translate"><?php _e('GP Machine Translate', 'gp-machine-translate'); ?></h3>
<table class="form-table">
    <tr>
        <th><label for="gp_machine_translate_user_key"><?php _e('User API Key', 'gp-machine-translate'); ?></label></th>
        <td>
            <input type="text" id="gp_machine_translate_user_key" name="gp_machine_translate_user_key" size="40" value="<?php echo htmlentities($templateData['userAuthKey']); ?>">
            <p class="description"><?php printf(__('Enter the %s API key for this user.', 'gp-machine-translate'), $provider::IDENTIFIER); ?></p>
        </td>
    <tr>
        <th><label for="gp_machine_translate_user_client_id"><?php _e('Client ID', 'gp-machine-translate'); ?></label></th>
        <td>
            <input type="text" id="gp_machine_translate_user_client_id" name="gp_machine_translate_user_client_id" size="40" value="<?php echo htmlentities($templateData['userAuthClientId']); ?>">
            <p class="description"><?php _e('Enter the client ID for this user if using Microsoft Translator.', 'gp-machine-translate'); ?></p>
        </td>
    </tr>
</table>