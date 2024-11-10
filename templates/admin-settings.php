<?php
declare(strict_types=1);

// Prevent direct access
use GpMachineTranslate\Providers\AbstractProvider;

if (! defined('ABSPATH')) {
    exit;
}

if (!isset($templateData['provider'], $templateData['providerList'],)) {
    exit;
}
/** @var AbstractProvider $provider */
$provider = $templateData['provider'];
?>
<div class="wrap">
	<h2><?php _e('GP Machine Translate Settings', 'gp-machine-translate'); ?></h2>
    <form method="post" action="options-general.php?page=gp-machine-translate.php" >
        <table class="form-table">
            <tr>
                <th><label for="gp_machine_translate_provider"><?php _e('Translation Provider', 'gp-machine-translate'); ?></label></th>
                <td>
                    <select id="gp_machine_translate_provider" name="gp_machine_translate_provider">
                        <option value=""><?php _e('*Select*', 'gp-machine-translate'); ?></option>
                        <?php
                        foreach ($templateData['providerList'] as $providerIdentifier) {
                            $selected = '';

                            if ($provider::IDENTIFIER == $providerIdentifier) {
                                $selected = ' selected';
                            }

                            echo '<option value="' . $providerIdentifier . '"' . $selected . '>' . $providerIdentifier . '</option>';
                        }
                        ?>
                    </select>
                    <p class="description"><?php _e('Select the translation provider to use.', 'gp-machine-translate'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="gp_machine_translate_key"><?php _e('Global API Key', 'gp-machine-translate'); ?></label></th>
                <td>
                    <input type="text" id="gp_machine_translate_key" name="gp_machine_translate_key" size="40" value="<?php echo htmlentities((string) $provider->getAuthKey()); ?>">
                    <p class="description"><?php _e('Enter the API key for all users (leave blank to disable, per user API keys will still function).', 'gp-machine-translate'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="gp_machine_translate_client_id"><?php _e('Client ID', 'gp-machine-translate'); ?></label></th>
                <td>
                    <input type="text" id="gp_machine_translate_client_id" name="gp_machine_translate_client_id" size="40" value="<?php echo htmlentities((string) $provider->getAuthClientId()); ?>">
                    <p class="description"><?php _e('Enter the client ID if using Microsoft Translator.', 'gp-machine-translate'); ?></p>
                </td>
            </tr>
        </table>
        <?php submit_button(__('Save', 'gp-machine-translate'), 'primary', 'save_gp_machine_translate'); ?>
    </form>
</div>