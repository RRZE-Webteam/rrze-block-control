# RRZE Block Control

RRZE Block Control helps WordPress administrators keep the block editor focused.  
Select which Gutenberg blocks stay available per user role, hide entire block categories with a single toggle, and receive warnings whenever new blocks are introduced by freshly activated plugins.

## Features
- **Role-aware block blacklist** – Choose a role, mark the blocks that should be hidden, and save the selection. Everything else stays available for that role.
- **Parent/child awareness** – Enabling a child block automatically enables its parents; disabling a parent disables its entire branch, avoiding inconsistent states.
- **Category toggles** – Each block category offers a “Hide all blocks” toggle that checks or unchecks every block inside the group in one click.
- **Reset to defaults** – Clear the stored restrictions for the active role via the “Reset user role” button.
- **New block detection** – When a plugin registers new blocks, an admin notice lists them until you confirm the change. This keeps restrictions up to date after plugin installs.

## Requirements
- WordPress 6.7 or newer
- PHP 8.2 or newer
- A user with `manage_options` capability to manage restrictions


## Usage
1. Navigate to *Settings → RRZE Block Control*.
2. Select the role you want to configure.
3. Use the per-block checkboxes or the category “Hide all blocks” toggle to block specific blocks.
4. Click **Save settings**.
5. Use **Reset user role** to remove the blacklist for the current role.

When you activate a plugin that registers additional Gutenberg blocks, visit any admin page: an admin notice lists the new block names and categories. Use the **Confirm** button in that notice to acknowledge the additions after you have updated the restrictions.


Contributions and bug reports are welcome via pull requests or issues. For support inquiries please reach out to `webmaster@fau.de`.

## License
GNU General Public License v3.0 – see `LICENSE` for details.
