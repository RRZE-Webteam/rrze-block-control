<?php

namespace RRZE\BlockControl\Settings;

defined('ABSPATH') || exit;

use RRZE\BlockControl\Blocks\BlockRegistry;


/**
 * SettingsPage
 *
 * Input & Rendering
 * Shows Tabs, full block lists, Blacklist with activated check boxes
 * saves changes via Settings.php (setOption())
 */
class SettingsPage
{
    private Settings $settings;
    private BlockRegistry $registry;

    /**
     * SettingsPage constructor.
     *
     * Stores the shared Settings and BlockRegistry instances and registers
     * the admin menu + styles hooks so the settings UI becomes available.
     */
    public function __construct(Settings $settings, BlockRegistry $registry)
    {
        $this->settings = $settings;
        $this->registry = $registry;
        add_action('admin_menu', [$this, 'registerSettingsPage']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminStyles']);

    }

    /**
     * Enqueue admin CSS for the settings page.
     *
     * This method loads the admin stylesheet only in the WordPress backend.
     * It should NOT be used for frontend styles.
     *
     * @param string $hook Current admin page hook suffix.
     * @return void
     */
    public function enqueueAdminStyles($hook): void
    {
        if ($hook !== 'settings_page_rrze-block-control') {
            return;
        }

        wp_enqueue_style(
            'rrze-block-control-admin',
            plugins_url('assets/css/admin.css', dirname(__DIR__, 2) . '/rrze-block-control.php',
            ),
            [],
            '1.0.0'
        );
    }


    /**
     *Adds a sub menu settings page to Options
     *
     * @return void
     */
    public function registerSettingsPage(): void
    {
        add_submenu_page(
            'options-general.php',
            'RRZE Block Control',
            'RRZE Block Control',
            'manage_options',
            'rrze-block-control',
            [$this, 'renderSettingsPage']
        );

    }


    /**
     * Renders the Block Control settings page.
     *
     * Acts as the central controller for the settings view:
     * - determines the selected role
     * - processes form submissions
     * - loads required data
     * - delegates rendering to specialized methods
     *
     * @return void
     */

    public function renderSettingsPage(): void
    {
        // 1. Determine current role context
        $selectedRole = $this->getSelectedRole();

        // 2. Handle form submission (if any)
        $this->handleFormSubmit($selectedRole);

        // 3. Load data required for rendering
        $blocksByCategory = $this->getRegisteredBlocksByCategory();
        $restrictedBlockSlugs = $this->getRestrictedBlockSlugsForRole($selectedRole);
        $newBlockSlugs = $this->registry->getNewBlockSlugs();

        // 4. Render page wrapper
        echo '<div class="wrap">';
        echo '<h1>' . esc_html(__('RRZE Block Control', 'rrze-block-control')) . '</h1>';
        echo '<p>' . esc_html(__('Select which blocks should be restricted for a specific user role. These blocks will be hidden in the block editor.', 'rrze-block-control')) . '</p>';

        // 5. Render settings form
        echo '<form method="post">';

        // Security nonce
        wp_nonce_field('rrze_block_control_save', 'rrze_block_control_nonce');

        // Role selector
        $this->renderRoleSelector($selectedRole);

        // Block list
        $this->renderBlockSlugList($blocksByCategory, $restrictedBlockSlugs, $newBlockSlugs);

        // Submit button
        echo '<p class="bc-submit">';
        echo '<input type="submit" name="rrze_block_control_submit" class="button button-primary" value="' . esc_attr__('Save changes', 'rrze-block-control') . '">';
        echo '</p>';

        //Reset Button
        echo '<hr>';
        echo '<p class="bc-reset-role-button">';
        echo '<p>' . esc_html(__('Reset role to default.', 'rrze-block-control')) . '</p>';
        echo '<button type="submit" name="rrze_block_control_reset_role" class="button button-secondary">';
        echo esc_html__('Reset Block Choice', 'rrze-block-control');
        echo '</button>';
        echo '</p>';

        echo '</form>';
        echo '</div>';

    }

    /**
     * Returns the user role currently selected on the Block Control settings page.
     *
     * The settings page is only accessible to administrators and always operates
     * on a single role context at a time. If a valid role has been submitted via
     * the settings form, it is returned. Otherwise, the default role "author" is used.
     *
     * @return string WordPress role slug used as the current settings context.
     */
    public function getSelectedRole(): string
    {
        $availableRoles = array_keys(get_editable_roles());

        if (isset ($_POST['role'])) {
            $selectedRole = sanitize_text_field($_POST['role']);
            if (in_array($selectedRole, $availableRoles, true)) {
                return $selectedRole;
            }
        }
        //default
        if (in_array('author', $availableRoles, true)) {

            return 'author';
        }
        return 'author';
    }

    /**
     * Handles the submission of the Block Control settings form.
     *
     * This method processes the submitted block selection for the currently
     * selected role. It performs basic request checks, verifies user permissions
     * and nonce validity, sanitizes the submitted block slugs and persists the
     * selection via the Settings class.
     *
     * If the form was not submitted or any security check fails, the method
     * returns early without performing any action.
     *
     * @param string $selectedRole The role for which the block selection is saved.
     * @return void
     */
    public function handleFormSubmit($selectedRole): void
    {
        $isSave = isset($_POST['rrze_block_control_submit']);
        $isReset = isset($_POST['rrze_block_control_reset_role']);

        if (!$isSave && !$isReset) {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        //Nonce check
        if (!isset($_POST['rrze_block_control_nonce']) ||
            !wp_verify_nonce($_POST['rrze_block_control_nonce'], 'rrze_block_control_save')
        ) {
            return;
        }

        if ($isReset) {
            $this->settings->resetRole($selectedRole);
            return;
        }

        // Read submitted block slugs
        $submittedBlockSlugs = $_POST['blocks'] ?? [];
        if (!is_array($submittedBlockSlugs)) {
            $submittedBlockSlugs = [];
        }

        $sanitizedBlockSlugs = array_map('sanitize_text_field', $submittedBlockSlugs);

        //Persist selection for the selected role
        $this->settings->saveRestrictedBlockSlugsForRole($selectedRole, $sanitizedBlockSlugs);
        // Mark newly detected blocks as reviewed
        $this->registry->markNewBlocksAsSeen();

    }


    /**
     * Returns all registered Gutenberg blocks grouped by category.
     *
     * This method retrieves the current block list from the BlockRegistry
     * and exposes it to the settings page for rendering purposes.
     *
     * @return array Blocks grouped by category, including slug and title.
     */
    public function getRegisteredBlocksByCategory(): array
    {
        return $this->registry->getBlockSlugsByCategory();
    }


    /**
     * Returns all restricted block slugs for the given role.
     *
     * This method acts as a thin wrapper around the Settings class and is used
     * by the settings page to retrieve the currently stored block blacklist
     * for a specific user role.
     *
     * @param string $role Role identifier (e.g. "author", "editor").
     * @return array List of restricted block slugs for the role.
     */
    public function getRestrictedBlockSlugsForRole(string $role): array
    {
        return $this->settings->getBlockSlugsForRole($role);
    }


    /**
     * Renders the role selection dropdown.
     *
     * @param string $selectedRole Currently selected role.
     * @return void
     */
    public function renderRoleSelector(string $selectedRole): void
    {
        $roles = get_editable_roles();

        echo '<div class="bc-user-role">';
        echo '<h2>' . esc_html(__('User role', 'rrze-block-control')) . '</h2>';

        echo '<p>';
        echo '<label for="rrze-block-control-role">';
        echo esc_html(__('Select the role you want to configure:', 'rrze-block-control'));
        echo '</label>';
        echo '</p>';
        echo '</div>';


        echo '<div class="bc-role-selector">';

        echo '<select name="role" id="rrze-block-control-role">';
        foreach ($roles as $roleSlug => $roleData) {
            $selected = ($roleSlug === $selectedRole) ? 'selected' : '';

            echo '<option value="' . esc_attr($roleSlug) . '" ' . $selected . '>';
            echo esc_html(translate_user_role($roleData['name'])); //shows correct language in select field
            echo '</option>';
        }
        echo '</select>';

        // Submit button
        echo '<p class="bc-load-role-button">';
        echo '<input type="submit" name="rrze_block_control_change_role" class="button button-primary" value="' . esc_attr__('Load Role', 'rrze-block-control') . '">';
        echo '</p>';

        echo '</div>';

    }


    /**
     * Renders the list of registered blocks grouped by category.
     *
     * @param array $blocksByCategory Blocks grouped by category.
     * @param array $restrictedBlockSlugs Restricted block slugs for the selected role.
     * @param array $newBlockSlugs Newly detected Blog Slugs
     * @return void
     */
    public function renderBlockSlugList(array $blocksByCategory, array $restrictedBlockSlugs, array $newBlockSlugs): void
    {
        echo '<h2>' . esc_html(__('Available blocks', 'rrze-block-control')) . '</h2>';

        $customLabels = [
            'rrze-plugins' => 'RRZE Plugins',
            'rrze_elements' => 'RRZE Elements',
            'rrze' => 'RRZE',
            'fau-elemental/FAU' => 'FAU Elemental/FAU',
        ];

        foreach ($blocksByCategory as $category => $blocks) {

            $label = $customLabels[$category] ?? ucfirst($category);

            echo '<fieldset  class="bc-block-category">';
            echo '<legend><strong>' . esc_html($label) . '</strong></legend>';
            echo '<div class="bc-block-grid">';

            foreach ($blocks as $block) {

                $slug = $block['slug'];
                $title = $block['title'];

                $isChecked = in_array($block['slug'], $restrictedBlockSlugs, true);
                $isNew = in_array($slug, $newBlockSlugs, true);

                $classes = 'bc-block-item';
                if ($isNew) {
                    $classes .= ' bc-block-is-new';
                }

                echo '<label class=" ' . esc_attr($classes) . '">';

                echo '<input type="checkbox" name="blocks[]" value="' . esc_attr($block['slug']) . '" ' . checked($isChecked, true, false) . '>';

                echo ' ' . esc_html($title);

                if ($isNew) {
                    echo ' <span class="bc-block-new-label">' . esc_html__('New', 'rrze-block-control') . '</span>';
                }

                echo '</label>';

            }
            echo '</div>';
            echo '</fieldset>';

        }
    }
}


