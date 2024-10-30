<?php
/*
Plugin Name: Comment Extra Fields 
Plugin URI: http://wordpress.org/extend/plugins/comment-extra-field/
Description: Allows administrator to add custom fields to comment form. On admin side let you update/delete the extra info.
Version: 1.7
Author: Simona Ilie	
Author URI: 
License: GPL2

Copyright 2011 Ilie Simona Elena  (email : sysyfina@yahoo.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/**
 * CONSTANTS SECTION
 */
require_once('comment-extra-fields-config.php');
/**
 * FUNCTIONS SECTION
 */
require_once('comment-extra-fields-functions.php');
/**
 * FRONT FUNCTIONS SECTION
 */
require_once('comment-extra-fields-front.php');
wp_enqueue_script( 'jquery' );
if(is_admin()):
    wp_enqueue_script( 'comment-extra-fields-js-scripts', CEF_FULL_PLUGIN_PATH . 'js/scripts.js' );
    
    wp_enqueue_style('comment-extra-fields-style', CEF_FULL_PLUGIN_PATH . 'css/style.css');
else:
    // wp_enqueue_script('jquery-on-front', 'http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js', null, null, null);
    wp_enqueue_script('swf-uploader', CEF_FULL_PLUGIN_PATH . 'scripts/swfupload.js', null, null, null);
    wp_enqueue_script('jquery-swf-uploader', CEF_FULL_PLUGIN_PATH . 'scripts/jquery.swfupload.js', null, null, null);
    wp_enqueue_script('cef-script-uploader', CEF_FULL_PLUGIN_PATH . 'scripts/cef.uploader.js', null, null, null);
    wp_enqueue_style('custom_style', CEF_FULL_PLUGIN_PATH . 'css/front/cef-style.css', null, null);
    if(!isset($_POST['action'])) :
        wp_enqueue_script('comment-extra-fields-js-php-scripts', CEF_FULL_PLUGIN_PATH . 'js/scripts.php?cef_full_plugin_path=' . urlencode(CEF_FULL_PLUGIN_PATH));
    endif;
endif;
/**
 * include a submenu option under Settings Dashboard 
 */
add_action( 'admin_menu', 'comment_extra_fields_admin_menus');

function comment_extra_fields_admin_menus()
{
	add_submenu_page('options-general.php', __('Comment Extra Fields'), __('Comment Extra Fields'), 'manage_options', 'comment-extra-fields', 'comment_extra_fields_inner_box');
}

/**
 * Draw options form
 */
function comment_extra_fields_inner_box()
{
    $cef_error_display      = 'cef-hide';
    $cef_success_display    = 'cef-hide';
    $cef_errors             = '<!-- -->';
    $cef_success            = '<!-- -->';
    $has_errors             = false;
    $checkbox_class = $radio_class = $select_class = $file_class = $db_select_class = 'cef-hide';
    $text_select = $textarea_select = $checkbox_select = $radio_select = $select_select = $file_select = $db_select_select = '';

    if(isset($_POST['cef_submit'])) {
        $on_edit = (isset($_GET['action']) && $_GET['action'] == 'edit');
        $cef_errors = cef_save_comment_extra_field($_POST, $on_edit);
        if($cef_errors !== true) {
            $has_errors         = true;
            $cef_error_display  = 'cef-show';
        }
    } else if(isset($_GET['action'])) {
        if($_GET['action'] == 'delete')
            cef_delete_comment_extra_field($_GET['id']);
        else if ($_GET['action'] == 'edit') {
            $values_in_fields = cef_get_comment_extra_field($_GET['id']);
        }
    }
    $comment_extra_fields = cef_list_comment_extra_fields();
?>
<script type='text/javascript'>
    var cef_admin_url      = '<?php echo CEF_ADMIN_URL;?>';
    var cef_plugin_url     = '<?php echo CEF_FULL_PLUGIN_PATH;?>';
    var cef_input_types    = <?php echo json_encode(array(CEF_INPUT_TEXT, CEF_INPUT_TEXTAREA, CEF_INPUT_CHECKBOX, CEF_INPUT_RADIO, CEF_INPUT_SELECT, CEF_INPUT_FILE, CEF_INPUT_DB_SELECT));?>;
</script>
<div class="wrap">
    <div class="icon32" id="icon-edit-comments"><br></div>
    <h2><?php _e('Comment Extra Fields');?></h2>
    <h3><?php _e('List Fields');?></h3>
    <table cellspacing="0" class="wp-list-table widefat fixed pages">
        <thead>
            <tr>
               <th style="" class="manage-column column-cb check-column" id="cb" scope="col">
                    <input type="checkbox">
                </th>
                <th style="" class="manage-column column-title sortable desc" id="cef_field_name_row" scope="col">
                    <span><a><?php _e('Field Name');?></a></span>
                </th>
                <th style="" class="manage-column column-author sortable desc" id="cef_field_type_row" scope="col">
                    <span><a><?php _e('Field Type');?></a></span>
                </th>
                <th style="" class="manage-column column-author sortable desc" id="cef_field_order_row" scope="col">
                    <span><a><?php _e('Field Order');?></a></span>
                </th>
                <th style="" class="manage-column column-author sortable desc" id="cef_field_location_row" scope="col">
                    <span><a><?php _e('Location');?></a></span>
                </th>
                <th style="" class="manage-column column-author sortable desc" id="cef_field_who_row" scope="col">
                    <span><a><?php _e('Who?');?></a></span>
                </th>
                <th style="" class="manage-column column-comments num sortable desc" id="cef_options_row" scope="col">
                    <span><a><?php _e('Options');?></a></span>
                </th>	
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th style="" class="manage-column column-cb check-column" scope="col">
                    <input type="checkbox">
                </th>
                <th style="" class="manage-column column-title sortable desc" scope="col">
                    <span><a><?php _e('Field Name');?></a></span>
                </th>
                <th style="" class="manage-column column-author sortable desc" scope="col">
                    <span><a><?php _e('Field Type');?></a></span>
                </th>
                <th style="" class="manage-column column-author sortable desc" scope="col">
                    <span><a><?php _e('Field Order');?></a></span>
                </th>
                <th style="" class="manage-column column-author sortable desc" scope="col">
                    <span><a><?php _e('Location');?></a></span>
                </th>                
                <th style="" class="manage-column column-author sortable desc" scope="col">
                    <span><a><?php _e('Who?');?></a></span>
                </th>                
                <th style="" class="manage-column column-comments num sortable desc" scope="col">
                    <span><a><?php _e('Options');?></a></span>
                </th>	
            </tr>
        </tfoot>
        <tbody id="the-list">
            <?php if($comment_extra_fields):
                foreach($comment_extra_fields as $cef):?>
            <tr valign="top" class="alternate author-self status-publish format-default iedit" id="post-9">
                <th class="check-column" scope="row"><input type="checkbox" value="<?php echo $cef['id'];?>" name="cef[]"></th>
                <td class="post-title page-title column-title"><?php echo $cef['label'];?></td>
                <td><?php echo ucwords($cef['type']);?></td>    
                <td><?php echo $cef['order'];?></td>
                <td><?php echo (isset($cef['where']) && $cef['where']) ? implode(', ', $cef['where']) : _e('Everywhere');?></td>
                <td><?php echo (isset($cef['who']) && $cef['who']) ? ucfirst($cef['who']) : ucfirst(CEF_WHO_BOTH);?></td>
                <td><a href="<?php echo CEF_ADMIN_URL;?>&action=edit&id=<?php echo cef_get_unique_ID($cef['id']);?>">Edit</a> | <a href="javascript:void(0)" onclick="delete_comment_extra_field('<?php echo cef_get_unique_ID($cef['id']);?>');return false;">Delete</a></td>
            </tr>
            <?php endforeach;
            else: ?>
            <tr>
                <td colspan="5"><?php _e('No comment extra fields defined yet.');?></td>
            </tr>
            <?php endif;?>
        </tbody>
    </table>
    <div class="cef-horizonat-space"><!-- --></div>
    <input type="button" class="cef-btn" id="delete-selected-cefs" value="Delete Selected Fields" />
    <div class="cef-horizontal-line"><!-- --></div>
    <h3><?php _e('Add/Edit Field');?></h3>
    <div id="cef_error_msgs" class="<?php echo $cef_error_display;?>"><?php echo $cef_errors;?></div>
    <div id="cef_success_msgs" class="<?php echo $cef_success_display;?>"><?php echo $cef_success;?></div>
    <div id="cef_field_edit">
        <a name="top-tooltip"><!-- --></a>
        <div class="tooltip-bubble" style="display:none;"><!-- --></div>
        <form action="" method="POST" id="comment_extra_field_add_edit_form">
        <table class="cef-edit-form-table">
            <tr>
                <td valign="top" class="label-cell"><span class="cef-required">*</span><label for='cef_field_label'><?php _e('Field Label');?></label></td>
                <td colspan="3"><input type="text" size="90" class="input-required" name="cef_field_label" id="cef_field_label" value="<?php 
                echo ($has_errors && isset($_POST['cef_field_label'])) ? esc_attr($_POST['cef_field_label']) : (isset($values_in_fields) ? $values_in_fields['label'] : '');?>"/></td>
            </tr>
            <tr>
                <td valign="top" class="label-cell"><span class="cef-required">*</span><label for='cef_field_id'><?php echo _e('Field id and name');?></label></td>
                <td colspan="3"><input type="text" size="90" class="input-required" name="cef_field_id" id="cef_field_id" value="<?php 
                echo ($has_errors && isset($_POST['cef_field_id'])) ? esc_attr($_POST['cef_field_id']) : (isset($values_in_fields) ? $values_in_fields['id'] : '');?>"/></td>
            </tr>
            <?php
            if(($has_errors && isset($_POST['cef_field_type'])) || isset($values_in_fields)):
                $text_select        = (isset($_POST) && isset($_POST['cef_field_type']) && $_POST['cef_field_type'] == CEF_INPUT_TEXT) ? 'selected' : (
                    (isset($values_in_fields) && $values_in_fields['type'] == CEF_INPUT_TEXT) ? 'selected' : '');
                $textarea_select    = (isset($_POST) && isset($_POST['cef_field_type']) && $_POST['cef_field_type'] == CEF_INPUT_TEXTAREA) ? 'selected' : (
                    (isset($values_in_fields) && $values_in_fields['type'] == CEF_INPUT_TEXTAREA) ? 'selected' : '');
                $checkbox_select    = (isset($_POST) && isset($_POST['cef_field_type']) && $_POST['cef_field_type'] == CEF_INPUT_CHECKBOX) ? 'selected' : (
                    (isset($values_in_fields) && $values_in_fields['type'] == CEF_INPUT_CHECKBOX) ? 'selected' : '');
                $checkbox_class     = (isset($_POST) && isset($_POST['cef_field_type']) && $_POST['cef_field_type'] == CEF_INPUT_CHECKBOX) ? 'cef-show' : (
                    (isset($values_in_fields) && $values_in_fields['type'] == CEF_INPUT_CHECKBOX) ? 'cef-show' : 'cef-hide');
                $radio_select       = (isset($_POST) && isset($_POST['cef_field_type']) && $_POST['cef_field_type'] == CEF_INPUT_RADIO) ? 'selected' : (
                    (isset($values_in_fields) && $values_in_fields['type'] == CEF_INPUT_RADIO) ? 'selected' : '');
                $radio_class        = (isset($_POST) && isset($_POST['cef_field_type']) && $_POST['cef_field_type'] == CEF_INPUT_RADIO) ? 'cef-show' : (
                    (isset($values_in_fields) && $values_in_fields['type'] == CEF_INPUT_RADIO) ? 'cef-show' : 'cef-hide');
                $select_select      = (isset($_POST) && isset($_POST['cef_field_type']) && $_POST['cef_field_type'] == CEF_INPUT_SELECT) ? 'selected' : (
                    (isset($values_in_fields) && $values_in_fields['type'] == CEF_INPUT_SELECT) ? 'selected' : '');
                $select_class       = (isset($_POST) && isset($_POST['cef_field_type']) && $_POST['cef_field_type'] == CEF_INPUT_SELECT) ? 'cef-show' : (
                    (isset($values_in_fields) && $values_in_fields['type'] == CEF_INPUT_SELECT) ? 'cef-show' : 'cef-hide');
                $file_select        = (isset($_POST) && isset($_POST['cef_field_type']) && $_POST['cef_field_type'] == CEF_INPUT_FILE) ? 'selected' : (
                    (isset($values_in_fields) && $values_in_fields['type'] == CEF_INPUT_FILE) ? 'selected' : '');
                $file_class         = (isset($_POST) && isset($_POST['cef_field_type']) && $_POST['cef_field_type'] == CEF_INPUT_FILE) ? 'cef-show' : (
                    (isset($values_in_fields) && $values_in_fields['type'] == CEF_INPUT_FILE) ? 'cef-show' : 'cef-hide');
                $db_select_select   = (isset($_POST) && isset($_POST['cef_field_type']) && $_POST['cef_field_type'] == CEF_INPUT_DB_SELECT) ? 'selected' : (
                    (isset($values_in_fields) && $values_in_fields['type'] == CEF_INPUT_DB_SELECT) ? 'selected' : '');
                $db_select_class    = (isset($_POST) && isset($_POST['cef_field_type']) && $_POST['cef_field_type'] == CEF_INPUT_DB_SELECT) ? 'cef-show' : (
                    (isset($values_in_fields) && $values_in_fields['type'] == CEF_INPUT_DB_SELECT) ? 'cef-show' : 'cef-hide');
                
            endif;
            ?>
            <tr>    
                <td valign="top" class="label-cell"><span class="cef-required">*</span><label for='cef_field_type'><?php _e('Field Type');?></label></td>
                <td><select name="cef_field_type" class="input-required" id="cef_field_type">
                        <option value=""></option>
                        <option value="<?php echo CEF_INPUT_TEXT;?>" <?php echo $text_select;?>><?php _e('Text');?></option>
                        <option value="<?php echo CEF_INPUT_TEXTAREA;?>" <?php echo $textarea_select;?>><?php _e('Textarea');?></option>
                        <option value="<?php echo CEF_INPUT_CHECKBOX;?>" <?php echo $checkbox_select;?>><?php _e('Checkbox');?></option>
                        <option value="<?php echo CEF_INPUT_RADIO;?>" <?php echo $radio_select;?>><?php _e('Radio Buttons');?></option>
                        <option value="<?php echo CEF_INPUT_SELECT;?>" <?php echo $select_select;?>><?php _e('Drop Down List');?></option>
                        <option value="<?php echo CEF_INPUT_FILE;?>" <?php echo $file_select;?>><?php _e('Upload File');?></option>
                        <option value="<?php echo CEF_INPUT_DB_SELECT;?>" <?php echo $db_select_select;?>><?php _e('DB Select');?></option>
                    </select>
                </td>
                <td valign="top" class="label-cell"><span class="cef-required">*</span><label for="cef_field_order"><?php _e('Field Order');?></label></td>
                <td><input type="text" size="20" class="input-required input-digit" name="cef_field_order" id="cef_field_order" value="<?php 
                echo ($has_errors && isset($_POST['cef_field_order'])) ? esc_attr($_POST['cef_field_order']): (isset($values_in_fields) ? $values_in_fields['order'] : '');?>"/></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan="3">
                    <div id="cef_special_field_extra_<?php echo CEF_INPUT_CHECKBOX;?>" class="<?php echo $checkbox_class;?> cef-extra-fields-row">
                        <label for="cef_checkboxes"><span class="tooltip" tooltip-body="<?php _e('Write the list of checkboxes options separated by semicolon (;).For each option you can include a value. Separate it by a plus (+): e.g. 1 + One; 2 + Two. ');?>"><!-- --></span>
                            <?php _e('Checboxes Values');?>
                        </label>
                        <input type="text" size="30" id="cef_checkboxes" name="cef_checkboxes" value="<?php 
                        echo ($has_errors && isset($_POST['cef_checkboxes'])) ? esc_attr($_POST['cef_checkboxes']) : (
                        (isset($values_in_fields) && isset($values_in_fields['check_values'])) ? $values_in_fields['check_values'] : ''
                                );?>"/>
                    </div>
                    <div id="cef_special_field_extra_<?php echo CEF_INPUT_RADIO;?>" class="<?php echo $radio_class;?> cef-extra-fields-row">
                        <label for="cef_radios"><span class="tooltip" tooltip-body="<?php _e('Write the list of radio buttons labels separated by semicolon (;).For each label you can include a value. Separate it by a plus (+): e.g. 1 + One; 2 + Two');?>"><!-- --></span>
                            <?php _e('Radio Buttons Values');?>
                        </label>
                        <input type="text" size="30" id="cef_radios" name="cef_radios" value="<?php
                        echo ($has_errors && isset($_POST['cef_radios'])) ? esc_attr($_POST['cef_radios']) : (
                        (isset($values_in_fields) && isset($values_in_fields['radio_values'])) ? $values_in_fields['radio_values'] : ''
                                );?>" />
                    </div>
                    <div id="cef_special_field_extra_<?php echo CEF_INPUT_SELECT;?>" class="<?php echo $select_class;?> cef-extra-fields-row">
                        <label for="cef_selects"><span class="tooltip" tooltip-body="<?php _e('Write the list of options separated by semicolon (;).For each option you can include a value. Separate it by a plus (+): e.g. 1 + One; 2 + Two');?>"><!-- --></span>
                            <?php _e('Drop Down Values');?>
                        </label>
                        <input type="text" size="30" id="cef_selects" name="cef_selects" value="<?php 
                        echo ($has_errors && isset($_POST['cef_selects'])) ? esc_attr($_POST['cef_selects']) : (
                        (isset($values_in_fields) && isset($values_in_fields['select_values'])) ? $values_in_fields['select_values'] : ''
                                );?>"/>
                    </div>
                    <div id="cef_special_field_extra_<?php echo CEF_INPUT_FILE;?>" class="<?php echo $file_class;?> cef-extra-fields-row">
                        <table border="0">
                            <tr>
                                <td valign="top" class="label-cell"><label for="cef_files"><span class="tooltip" tooltip-body="<?php _e('All uploads are made in current theme, in folder &quot;UPLOADS&quot;');?>"><!-- --></span><?php _e('Uploads folder');?></label></td>
                                <td class="label-cell"><input type="text" size="30" id="cef_files" name="cef_files" readonly="readonly" value="<?php echo THEME_RELATIVE_PATH . 'uploads'; //($has_errors && isset($_POST['cef_files'])) ? esc_attr($_POST['cef_files']) : '';?>" /></td>
                            </tr>
                            <tr>
                                <td valign="top" class="label-cell"><label for="cef_file_extensions"><span class="tooltip" tooltip-body="<?php _e('Insert allowed files extensions separated by commas (,).');?>"><!-- --></span><?php _e('Allowed Extensions');?></label></td>
                                <td class="label-cell"><input type="text" size="30" id="cef_file_extensions" name="cef_file_extensions" value="<?php 
                                echo ($has_errors && isset($_POST['cef_file_extensions'])) ? esc_attr($_POST['cef_file_extensions']) : (
                        (isset($values_in_fields) && isset($values_in_fields['allowed_ext'])) ? $values_in_fields['allowed_ext'] : ''
                                );?>" /></td>
                            </tr>
                        </table>
                    </div>  
                    <div id="cef_special_field_extra_<?php echo CEF_INPUT_DB_SELECT;?>" class="<?php echo $db_select_class?> cef-extra-fields-row">
                        <table border="0">
                            <tr>
                                <td valign="top" class="label-cell">
                                    <label for="cef_db_selects_table"><span class="tooltip" tooltip-body="<?php _e('Please insert the table\'s name from which to populate the drop down. If you don\'t know the tables\' prefix, don\'t write it.');?>"><!-- --></span><?php _e('Table to populate list from');?></label>
                                </td>
                                <td><input type="text" size="20" id="cef_db_selects_table" name="cef_db_selects_table" value="<?php 
                                echo ($has_errors && isset($_POST['cef_db_selects_table'])) ? esc_attr($_POST['cef_db_selects_table']): (
                        (isset($values_in_fields) && isset($values_in_fields['db_table'])) ? $values_in_fields['db_table'] : ''
                                );?>"/></td>
                            </tr>
                            <tr>
                                <td valign="top" class="label-cell">
                                    <label for="cef_db_selects_id"><span class="tooltip" tooltip-body="<?php _e('Please insert the column name to populate drop down ids');?>"><!-- --></span><?php _e('Column for ids');?></label>
                                </td>
                                <td><input type="text" size="20" id="cef_db_selects_id" name="cef_db_selects_id" value="<?php 
                                echo ($has_errors && isset($_POST['cef_db_selects_id'])) ? esc_attr($_POST['cef_db_selects_id']): (
                        (isset($values_in_fields) && isset($values_in_fields['db_id'])) ? $values_in_fields['db_id'] : ''
                                );?>"/></td>
                            </tr>
                            <tr>
                                <td valign="top" class="label-cell">
                                    <label for="cef_db_selects_value"><span class="tooltip" tooltip-body="<?php _e('Please insert the column name to populate drop down');?>"><!-- --></span><?php _e('Column for values');?></label>
                                </td>
                                <td><input type="text" size="20" id="cef_db_selects_value" name="cef_db_selects_value" value="<?php 
                                echo ($has_errors && isset($_POST['cef_db_selects_value'])) ? esc_attr($_POST['cef_db_selects_value']): (
                        (isset($values_in_fields) && isset($values_in_fields['db_values'])) ? $values_in_fields['db_values'] : ''
                                );?>"/></td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
            <tr>
                <td valign="top">
                    <label for="cef_html_code"><span class="tooltip" tooltip-body="<?php _e('If you use a different DOM structure than the Wordpress default for comment form, write it here, using the label, id/name and input type defined in the above mandatory fields.');?>"><!-- --></span>
                        <?php _e('Custom HTML Code');?>
                    </label>
                </td>
                <td colspan="3"><textarea cols="80" rows="5" name="cef_html_code" id="cef_html_code"><?php echo ($has_errors && isset($_POST['cef_html_code'])) ? esc_attr($_POST['cef_html_code']): 
                    (isset($values_in_fields['html_code']) ? stripslashes($values_in_fields['html_code']) : '');?></textarea>
                <p><?php _e('Example: ');?></p>
                <textarea id="cef-html-code-example" class="cef-html-code-example" readonly="readonly"><?php echo esc_attr(CEF_DEFAULT_HTML_CODE);?></textarea>
                <div id="cef-html-code-hints-toggler" style="cursor:pointer;"><?php _e('Click to read more HTML Code hints');?></div>
                <div class="cef-hide" id="cef-html-code-hints">
                    <h3><?php _e('Use required fields definitions');?></h3>
                    <code>
                        &lt;p class="my_class"&gt;<br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;&lt;label for=[field_id]&gt;[field_label]&lt;/label&gt;<br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;[field_value]<br/>
                        &lt;/p&gt;
                    </code><br />
                    <?php _e('This can be used for drop downs, checkboxes and radio button fields. When you change the type you will need to redefine the values.');?>
                    <h3><?php _e('Define your own values');?></h3>
                    <code>
                        &lt;p class="my_class"&gt;<br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;&lt;label for=[field_id]&gt;<em><?php _e('Your label');?></em>&lt;/label&gt;<br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;<em><?php _e('Define values and inputs structure');?></em><br/>
                        &lt;/p&gt;
                    </code><br />
                    <?php _e('Be careful to select the same type from the required field as the one you define for the HTML Code. This field is used in the listing of extra fields.<b>Note: The user input will not be managed by the plugin.</b>');?>
                </div>
                </td>
            </tr>
            <tr>
                <td><label for="cef_field_where"><?php _e('Where?');?></label></td>
                <td>
                    <select id="cef_field_where" name="cef_field_where[]" multiple="multiple" size="3">
                        <option value="everywhere"><?php _e('Everywhere');?></option>
                        <?php $post_types = get_post_types(); 
                        if($post_types) :
                            foreach($post_types as $k => $v) :
                                if($k == CEF_MENU_NAV_ITEM) continue;
                                $selected = ( ($has_errors && isset($_POST['cef_field_where']) && in_array($v, $_POST['cef_field_where'])) || (
                                        isset($values_in_fields) && isset($values_in_fields['where']) && in_array($v,$values_in_fields['where'])
                                        )) ? 'selected' : '';
                            ?>
                        <option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo ucfirst($v);?></option>
                        <?php endforeach;
                        endif;
                        ?>
                    </select>
                    
                </td>
                <td><label for="cef_field_who"><?php _e('Who?');?></label></td>
                <td>
                    <select id="cef_field_who" name="cef_field_who">
                        <option value='<?php echo CEF_WHO_BOTH;?>' <?php echo (
                                ($has_errors && isset($_POST['cef_field_who']) && $_POST['cef_field_who'] == CEF_WHO_BOTH) || 
                                (isset($values_in_fields) && isset($values_in_fields['who']) && $values_in_fields['who'] == CEF_WHO_BOTH) ? 
                                'selected' : '');?>><?php _e('All');?></option>
                        <option value='<?php echo CEF_WHO_GUEST;?>' <?php echo (
                                ($has_errors && isset($_POST['cef_field_who']) && $_POST['cef_field_who'] == CEF_WHO_GUEST) || 
                                (isset($values_in_fields) && isset($values_in_fields['who']) && $values_in_fields['who'] == CEF_WHO_GUEST) ? 
                                'selected' : '');?>><?php _e('Only Guests');?></option>
                        <option value='<?php echo CEF_WHO_LOGGED;?>' <?php echo (
                                ($has_errors && isset($_POST['cef_field_who']) && $_POST['cef_field_who'] == CEF_WHO_LOGGED) || 
                                (isset($values_in_fields) && isset($values_in_fields['who']) && $values_in_fields['who'] == CEF_WHO_LOGGED) ? 
                                'selected' : '');?>><?php _e('Only Logged Users');?></option>
                    </select>
                </td> 
          </tr> 
            <tr>
                <td>&nbsp;</td>
                <td colspan="3">
                    <input type="submit" name="cef_submit" id="cef_submit" value="<?php _e('Save');?>" />
                    <input type="reset" id="cef_reset" value="<?php _e('Reset');?>" onclick="cef_reset_form();"/>
                </td>
            </tr>
        </table>
    </div>
    <!-- <div class="cef-horizontal-line"><!-- --><!-- </div>
    <h3><?php _e('General comment form settings');?></h3>
    -->
</div>
<?php }


function cef_comment_options() {
    add_meta_box('cef_option', __('Comment Extra Fields'), 'cef_admin_comment_edit', 'comment', 'normal', 'high');

    // add a callback function to save any data a user enters in
    add_action('comment_save_pre','cef_comments_meta_save');
    // add a callback function to delete extra comment data on comment deletion
    add_action('deleted_comment', 'cef_comment_meta_delete');
}

add_action('admin_init', 'cef_comment_options');

function cef_admin_comment_edit($c) {
    $c_id       = $c->comment_ID;
    $fields     = cef_load_comment_extra_fields($c_id);
    
    $fields_def = cef_list_comment_extra_fields(); 
    
    foreach($fields_def as $field)
    {
        $html_code      = get_option(CEF_OPTION_PREFIX . $field['id'] . CEF_POSTFIX_HTML_CODE);
        if(!is_null($html_code) && !empty($html_code)) {
            echo "User value: " . $fields[$field['id']];
        }
        if($field['type'] != cef_translate_type(CEF_INPUT_FILE))
            echo cef_parse_html_code($field['id'], $field['label'], $field['type'], $fields[$field['id']]);
        else {
            $file_path = $fields[CEF_USER_FILE . $field['id']];
            if(!empty($file_path) && file_exists(BASE_DISK_PATH . $file_path))
                echo "<a href='" . get_bloginfo('siteurl') . $file_path . "' target='_blank'>View uploaded file</a>";
        } 
            
    }
?>

<?php 
}

function cef_comments_meta_save()
{
    cef_save_comment_meta($_POST['comment_ID'], $_POST);
}
?>
