<?php
/* PATHS INFORMATION */
if(!defined('CEF_PLUGIN_DIR')) define('CEF_PLUGIN_DIR', 'comment-extra-field');
if(!defined('CEF_FULL_PLUGIN_PATH')) define('CEF_FULL_PLUGIN_PATH', WP_PLUGIN_URL . '/' . CEF_PLUGIN_DIR . '/');
/* INPUT TYPES FOR COMMENT EXTRA FIELDS */
if(!defined('CEF_INPUT_TEXT')) define('CEF_INPUT_TEXT', 1);
if(!defined('CEF_INPUT_TEXTAREA')) define('CEF_INPUT_TEXTAREA', 2);
if(!defined('CEF_INPUT_CHECKBOX')) define('CEF_INPUT_CHECKBOX', 3);
if(!defined('CEF_INPUT_RADIO')) define('CEF_INPUT_RADIO', 4);
if(!defined('CEF_INPUT_SELECT')) define('CEF_INPUT_SELECT', 5);
if(!defined('CEF_INPUT_FILE')) define('CEF_INPUT_FILE', 6);
if(!defined('CEF_INPUT_DB_SELECT')) define('CEF_INPUT_DB_SELECT', 7);
/* INPUT DEFAULT VALUE */
if(!defined('CEF_COMMENT_DEFAULT_FIELDS')) define('CEF_COMMENT_DEFAULT_FIELDS', serialize(array('author', 'email', 'url', 'comment')));
$html_code = '<p class="comment-form-author"><label for=[field_id]>[field_label]</label><span class="required">*</span>' .
            '<input type="[field_type]" id="[field_id]" name="[field_id]" size=30 value="[field_value]"/></p>';
if(!defined('CEF_DEFAULT_HTML_CODE')) define('CEF_DEFAULT_HTML_CODE', $html_code);
/* USED FOR COMMENT EXTRA FIELD DATABASE SAVE */
if(!defined('CEF_OPTION_PREFIX')) define('CEF_OPTION_PREFIX', 'comment_extra_field_');
if(!defined('CEF_POSTFIX_ID')) define('CEF_POSTFIX_ID', 'id_');
if(!defined('CEF_POSTFIX_LABEL')) define('CEF_POSTFIX_LABEL', '_label');
if(!defined('CEF_POSTFIX_TYPE')) define('CEF_POSTFIX_TYPE', '_type');
if(!defined('CEF_POSTFIX_ORDER')) define('CEF_POSTFIX_ORDER', '_order');
if(!defined('CEF_POSTFIX_CHECKBOXES')) define('CEF_POSTFIX_CHECKBOXES', '_checkbox_value');
if(!defined('CEF_POSTFIX_RADIOS')) define('CEF_POSTFIX_RADIOS', '_radio_value');
if(!defined('CEF_POSTFIX_SELECTS')) define('CEF_POSTFIX_SELECTS', '_select_value');
if(!defined('CEF_POSTFIX_UPLOAD_DIR')) define('CEF_POSTFIX_UPLOAD_DIR', '_upload_dir');
if(!defined('CEF_POSTFIX_ALLOWED_EXT')) define('CEF_POSTFIX_ALLOWED_EXT', '_allowed_extensions');
if(!defined('CEF_POSTFIX_DB_TABLE')) define('CEF_POSTFIX_DB_TABLE', '_db_table');
if(!defined('CEF_POSTFIX_DB_ID_COL')) define('CEF_POSTFIX_DB_ID_COL', '_col_id');
if(!defined('CEF_POSTFIX_DB_VALUE_COL')) define('CEF_POSTFIX_DB_VALUE_COL', '_col_value');
if(!defined('CEF_POSTFIX_HTML_CODE')) define('CEF_POSTFIX_HTML_CODE', '_html_code');
if(!defined('CEF_POSTFIX_LOCATION')) define('CEF_POSTFIX_LOCATION', '_location');
if(!defined('CEF_POSTFIX_WHERE')) define('CEF_POSTFIX_WHERE', '_where');
if(!defined('CEF_POSTFIX_WHO')) define('CEF_POSTFIX_WHO', '_who');
if(!defined('CEF_EVERYWHERE'))      define('CEF_EVERYWHERE', 'everywhere');

if(!defined('CEF_OPTION_SEPARATOR')) define('CEF_OPTION_SEPARATOR', ';');
if(!defined('CEF_VALUE_SEPARATOR')) define('CEF_VALUE_SEPARATOR', '+');

$current_disk_path      = dirname(__FILE__);
$relative_dir_path      = str_replace(str_replace('\\', '/', get_bloginfo('siteurl')), '',  str_replace('\\', '/', CEF_FULL_PLUGIN_PATH));
$base_disk_path         = str_replace(str_replace('\\', '/', $relative_dir_path), '', str_replace('\\', '/', $current_disk_path) . '/');
if(!defined('BASE_DISK_PATH')) define('BASE_DISK_PATH', $base_disk_path . '/');
$relative_theme_path    = str_replace(get_bloginfo('siteurl'), '', get_bloginfo('template_url'));
if(!defined('THEME_RELATIVE_PATH')) define('THEME_RELATIVE_PATH', $relative_theme_path . '/');
$theme_disk_path        = BASE_DISK_PATH . $relative_theme_path;
if(!defined('THEME_DISK_PATH')) define('THEME_DISK_PATH', $theme_disk_path . '/');
if(!defined('CEF_USER_FILE')) define('CEF_USER_FILE', 'cef_uploaded_file_');

if(!defined('CEF_ADMIN_URL')) define('CEF_ADMIN_URL', get_bloginfo('siteurl') . '/wp-admin/options-general.php?page=comment-extra-fields');

if(!defined('CEF_WHO_BOTH')) define('CEF_WHO_BOTH', 'both');
if(!defined('CEF_WHO_GUEST')) define('CEF_WHO_GUEST', 'guest');
if(!defined('CEF_WHO_LOGGED')) define('CEF_WHO_LOGGED', 'logged');
if(!defined('CEF_MENU_NAV_ITEM'))   define('CEF_MENU_NAV_ITEM', 'nav_menu_item');
?>
