<?php
function cef_debug($data, $clr = 'red')
{
	echo "<pre style='color:{$clr};'>"; print_r($data); echo "</pre>";
}

function cef_save_comment_extra_field($data, $on_edit)
{
    $validations = cef_comment_valid_data($data, $on_edit);
    if($validations === true) {
        cef_save_valid_comment_extra_field($data, $on_edit);
    }
    return $validations;
}

/**
 * check if received infro is valid
 * 1. all required info is not empty and order is digits
 * 2. if field of type db select if table and column names actually exist 
 * 3. check if a field with same id already exists
 * @param type $data - array with POST info
 */
function cef_comment_valid_data($data, $on_edit)
{
    $data_keys  = array_keys($data);
    $errors     = array();
    /* 1. */
    if(isset($data['cef_field_label']) && empty($data['cef_field_label']))
        $errors[] = __('Label is required.');
    if(isset($data['cef_field_id']) && empty($data['cef_field_id']))
        $errors[] = __('Id/Name is required.');
    if(isset($data['cef_field_type']) && empty($data['cef_field_type']))
        $errors[] = __('Type is required.');
    if(isset($data['cef_field_order']) && empty($data['cef_field_order']))
        $errors[] = __('Order is required.');
    if(isset($data['cef_field_order']) && !is_numeric($data['cef_field_order']))
        $errors[] = __('Order must be a number.');
    
    // if no errors, check step 2
    if(empty($errors)) {
        /* 2. */
        if($data['cef_field_type'] == CEF_INPUT_DB_SELECT) {
            if(empty($data['cef_db_selects_table']) || empty($data['cef_db_selects_id']) || empty($data['cef_db_selects_value']))
                $errors[] = __('Please fill in the database information.');
            else {
                if(!cef_is_valid_table_name($data['cef_db_selects_table'])) 
                    $errors[] = __('The table <b>' . cef_validate_table_name($data['cef_db_selects_table']) . '</b> does not exist.');
                else {
                    if(!cef_is_valid_column_name($data['cef_db_selects_table'], $data['cef_db_selects_id']))
                        $errors[] = __('The column <b>' . $data['cef_db_selects_id'] . '</b> does not exist.');
                    if(!cef_is_valid_column_name($data['cef_db_selects_table'], $data['cef_db_selects_value']))
                        $errors[] = __('The column <b>' . $data['cef_db_selects_value'] . '</b> does not exist.');
                }
            }
        }
    }
    // if no errors, check step 3
    if(empty($errors) && !$on_edit) {
        /* 3. */
        if(!cef_is_unique_extra_field_id($data['cef_field_id']))
            $errors[] = __('A field with this id <b>' . $data['cef_field_id'] . '</b> already exists for the comment form.');
    }
    return (empty($errors)) ? true : implode('<br />', $errors);
}
/**
 * Check if a table name exists in the Wordpress Database
 * @param type $table - name of table from user input
 */
function cef_is_valid_table_name($table)
{
    global $wpdb;
    if(defined('DB_NAME')) {
        $col_name   = 'Tables_in_' . DB_NAME;
        $table_name = cef_validate_table_name($table);
        $sql        = "SHOW TABLES FROM `" . DB_NAME . "` WHERE `{$col_name}`=%s";
        $data       = $wpdb->get_row($wpdb->prepare($sql, $table_name));
        return !(empty($data));
    }
    return false;
}

function cef_validate_table_name($table)
{
    global $wpdb;
    return (stripos($table, $wpdb->prefix) === 0) ? $table : $wpdb->prefix . $table; 
}
/**
 * Check for a given table name if a column exists
 * @param type $table - name of table from user input
 * @param type $column - name of column
 */
function cef_is_valid_column_name($table, $column)
{
    global $wpdb;
    $sql    = "SHOW COLUMNS FROM `" . cef_validate_table_name($table) . "` WHERE Field=%s";
    $data   = $wpdb->get_row($wpdb->prepare($sql, $column));
    return !(empty($data));
}
/**
 * Check if an extra/default comment field with this id already exists
 * @param type $id - extra field id from user input
 */
function cef_is_unique_extra_field_id($id)
{
    if(is_null($id) || empty($id))
        return false;
    
    if(in_array($id, unserialize(CEF_COMMENT_DEFAULT_FIELDS)))
        return false;
    $opt_name   = CEF_OPTION_PREFIX . CEF_POSTFIX_ID;
    $data       = cef_get_extra_field_ids($opt_name);
    return (!in_array($id, $data));
}
/**
 * Save informations about comment extra field
 * @param type $data user input
 */
function cef_save_valid_comment_extra_field($data, $on_edit)
{
    $cef_id     = $data['cef_field_id'];
    $cef_prefix = CEF_OPTION_PREFIX . $cef_id;
    if(!$on_edit) {
        $UID        = md5(time());
        $opt_id     = CEF_OPTION_PREFIX . CEF_POSTFIX_ID . $UID;
        if(!(in_array($cef_id, cef_get_extra_field_ids($opt_id))))
            add_option($opt_id, $cef_id);
    } else 
        update_option($opt_id, $cef_id);
    update_option($cef_prefix . CEF_POSTFIX_LABEL, $data['cef_field_label']);
    update_option($cef_prefix . CEF_POSTFIX_TYPE, $data['cef_field_type']);
    update_option($cef_prefix . CEF_POSTFIX_ORDER, $data['cef_field_order']);
    switch($data['cef_field_type']) {
        case CEF_INPUT_CHECKBOX :
            update_option($cef_prefix . CEF_POSTFIX_CHECKBOXES, $data['cef_checkboxes']);
            break;
        case CEF_INPUT_RADIO :
            update_option($cef_prefix . CEF_POSTFIX_RADIOS, $data['cef_radios']);
            break;
        case CEF_INPUT_SELECT :
            update_option($cef_prefix . CEF_POSTFIX_SELECTS, $data['cef_selects']);
            break;
        case CEF_INPUT_FILE :
            update_option($cef_prefix . CEF_POSTFIX_UPLOAD_DIR, $data['cef_files']);
            update_option($cef_prefix . CEF_POSTFIX_ALLOWED_EXT, $data['cef_file_extensions']);
            break;
        case CEF_INPUT_DB_SELECT :
            update_option($cef_prefix . CEF_POSTFIX_DB_TABLE, $data['cef_db_selects_table']);
            update_option($cef_prefix . CEF_POSTFIX_DB_ID_COL, $data['cef_db_selects_id']);
            update_option($cef_prefix . CEF_POSTFIX_DB_VALUE_COL, $data['cef_db_selects_value']);
            break;
    }
    update_option($cef_prefix . CEF_POSTFIX_HTML_CODE, $data['cef_html_code']);
    update_option($cef_prefix . CEF_POSTFIX_WHERE, $data['cef_field_where']);
    update_option($cef_prefix . CEF_POSTFIX_WHO, $data['cef_field_who']);
}

/**
 * List all extra fields
 * @param type $order_by - order fields criteria
 * @param type $order_dir - order direction
 * @return type - sorted extra fields array
 */
function cef_list_comment_extra_fields($order_by = 'order', $order_dir = SORT_ASC)
{
    $ids            = cef_get_extra_field_ids();
    $extra_fields   = array();
    if($ids) {
        foreach($ids as $id) {
            $extra_fields[] = cef_load_comment_extra_field($id);
        }
    }
    return cef_order_complex_array($extra_fields, $order_by, $order_dir);
}

function cef_get_extra_field_ids()
{
    $final = array();
    global $wpdb;
    $partial_opt_name = CEF_OPTION_PREFIX . CEF_POSTFIX_ID;
    $sql = "SELECT option_value FROM `{$wpdb->options}` WHERE `option_name` LIKE ('%" . $partial_opt_name . "%')";
    $data = $wpdb->get_results($sql);
    $final = cef_map_array($data, 'option_value');
    
    return $final;
}

function cef_map_array($array, $col_name)
{
    if(is_null($array) || empty($array) || !is_array($array))
        return array();
    $final = array();
    foreach($array as $a)
        $final[] = $a->{$col_name};
    return $final;
}

function cef_load_comment_extra_field($id)
{
    $label          = get_option(CEF_OPTION_PREFIX . $id . CEF_POSTFIX_LABEL);
    $type           = cef_translate_type(get_option(CEF_OPTION_PREFIX . $id . CEF_POSTFIX_TYPE));
    $final = array(
        'id'            => $id,
        'label'         => $label,
        'type'          => $type,
        'order'         => get_option(CEF_OPTION_PREFIX . $id . CEF_POSTFIX_ORDER),
        'where'         => get_option(CEF_OPTION_PREFIX . $id . CEF_POSTFIX_WHERE),
        'who'           => get_option(CEF_OPTION_PREFIX . $id . CEF_POSTFIX_WHO),
        'html_code'     => cef_parse_html_code($id, $label, $type)
    );
    return $final;
}

function cef_translate_type($type, $reversed = false)
{
    if($reversed) {
        switch($type) {
            case 'text' :
                return CEF_INPUT_TEXT;
            case 'textarea' :
                return CEF_INPUT_TEXTAREA;
            case 'checkbox' :
                return CEF_INPUT_CHECKBOX;
            case 'radio' :
                return CEF_INPUT_RADIO;
            case 'drop-down' :
                return CEF_INPUT_SELECT;
            case 'file' :
                return CEF_INPUT_FILE;
            case 'db list' :
                return CEF_INPUT_DB_SELECT;
            default :
                // unchange data
                 return $type;
        }
    } else {
        switch($type) {
            case CEF_INPUT_TEXT :
                return 'text';
            case CEF_INPUT_TEXTAREA :
                return 'textarea';
            case CEF_INPUT_CHECKBOX :
                return 'checkbox';
            case CEF_INPUT_RADIO :
                return 'radio';
            case CEF_INPUT_SELECT :
                return 'drop-down';
            case CEF_INPUT_FILE :
                return 'file';
            case CEF_INPUT_DB_SELECT :
                return 'db list';
            default:
                // unchange data
                return $type;
        }
    }
}

function cef_parse_html_code($id, $label, $type, $value = null)
{
    $type           = cef_translate_type($type, true);
    $final_string   = '';
    $html_code      = get_option(CEF_OPTION_PREFIX . $id . CEF_POSTFIX_HTML_CODE);
    if(empty($html_code) || ($html_code == addslashes(CEF_DEFAULT_HTML_CODE))) {
        switch($type) {
            case CEF_INPUT_TEXT :
                $final_string = str_replace(array('[field_label]', '[field_id]', '[field_type]', '[field_value]'), array($label, $id, cef_translate_type($type), esc_attr($value)), CEF_DEFAULT_HTML_CODE);
                break;
            case CEF_INPUT_TEXTAREA :
                $final_string = "<p class='comment-form-comment'><label for='" . esc_attr($id) . "'>" . 
                                    esc_attr($label) . "</label>" .
                                    "<textarea id='" . esc_attr($id) . "' name='" . esc_attr($id) . "' rows='8' cols='45'>" . esc_attr($value) . "</textarea></p>";
                break;
            case CEF_INPUT_CHECKBOX :
            case CEF_INPUT_RADIO :
            case CEF_INPUT_SELECT :
            case CEF_INPUT_DB_SELECT :
                $is_select              = ($type == CEF_INPUT_SELECT || $type == CEF_INPUT_DB_SELECT);
                $field_default_class    = (!$is_select) ? "comment-form-checkbox" : "comment-form-select";
                $final_string =  "<div class='{$field_default_class}'>" . (($is_select) ? "<label for='" . esc_attr($id) . "'>" .
                                    esc_attr($label) . "</label>" : '') .
                                    cef_display_extra_options($id, $type, $value) . "</div>";
                break;
            case CEF_INPUT_FILE :
                $extensions     = get_option(CEF_OPTION_PREFIX . $id . CEF_POSTFIX_ALLOWED_EXT);
                $allowed_ext    = cef_build_allowed_extensions($extensions);
                $final_string   .= '<div class="comment-form-file">
                                        <label for="' . esc_attr($id) . '">' . esc_attr($label) . '</label>' . 
                                        cef_build_uploader_field($id, $allowed_ext) .
                                    '</div>';
                break;
        }
    } else {
        $final_string = str_replace(array('[field_label]', '[field_id]', '[field_type]', '[field_value]'), array($label, $id, cef_translate_type($type), cef_display_extra_options($id, cef_translate_type($type, true), $value)), stripslashes($html_code));
    }
    return $final_string;
}

function cef_build_uploader_field($id, $extensions)
{
    return '<span id="' . esc_attr($id) . '">
                <div id="log_' . esc_attr($id) . '" class="cef-display-upload-info"></div>
                <input type="button" id="button' . esc_attr($id) . '" />
                <input type="hidden" name="cef_uploaded_file_' . esc_attr($id) . '" id="cef_uploaded_file_'. esc_attr($id) . '" />
            </span>
            <script type="text/javascript">
                build_cef_uploader("' . esc_attr($id) . '", "' . esc_attr($extensions) . '");
            </script>';
}
function cef_build_allowed_extensions($extensions = 'jpg, gif, png')
{
    $temp   = explode(',', $extensions);
    $final  = array();
    foreach($temp as $ext) {
        $processed_ext  = trim(str_replace('.', '', $ext));
        $final[]        = "*." . $processed_ext;
    }
    return implode('; ', $final);
}


function cef_order_complex_array($array, $order, $dir)
{
    if(is_null($array) || empty($array) || !is_array($array))
        return array();
    
    $column = array();
    foreach($array as $key => $value)
        $column[$key] = $value[$order];
    
    array_multisort($column, $dir, $array);
    return $array;
}

function cef_display_extra_options($id, $type, $value)
{
    $final_string = '';
    switch($type) {
        case CEF_INPUT_RADIO :
        case CEF_INPUT_CHECKBOX :
            $final_option   = ($type == CEF_INPUT_CHECKBOX) ? CEF_POSTFIX_CHECKBOXES : CEF_POSTFIX_RADIOS;
            $options        = get_option(CEF_OPTION_PREFIX . $id . $final_option);
            
            $final_options  = cef_process_extra_options($options); 
            $counter = 1;
            foreach($final_options as $opt) {
                $value = (!empty($value) && !is_array($value)) ? array($value) : ((empty($value)) ? array() : $value);
                $checked = (!is_null($value) && in_array(esc_attr($opt['id']), $value)) ? "checked" : "";
                $final_string .= "<input type='" . cef_translate_type($type) . "' name='" . esc_attr($id) . "[]' id='" . esc_attr($id) . 
                                    "_{$counter}' value='" . esc_attr($opt['id']) . "' {$checked} />" .
                                    "<label for='" . esc_attr($id) . "_{$counter}'>" . esc_attr($opt['value']) . "</label>" .
                                    "<div class='clear'><!-- --></div>";
                $counter++;
            }
            break;
        case CEF_INPUT_SELECT :
            $options        = get_option(CEF_OPTION_PREFIX . $id . CEF_POSTFIX_SELECTS);
            $final_options  = cef_process_extra_options($options);
            $final_string   .= "<select name='" . esc_attr($id) . "' id='" . esc_attr($id) . "'>";
            foreach($final_options as $opt) {
                $selected = (!is_null($value) && $value == esc_attr($opt['id'])) ? "selected" : "";
                $final_string .= "<option value='" . esc_attr($opt['id']) . "' {$selected}>" . esc_attr($opt['value']) . "</option>";
            }
            $final_string .= "</select>";
            break;
        case CEF_INPUT_DB_SELECT :
            $table_name     = cef_validate_table_name(get_option(CEF_OPTION_PREFIX . $id . CEF_POSTFIX_DB_TABLE));
            $col_id         = get_option(CEF_OPTION_PREFIX . $id . CEF_POSTFIX_DB_ID_COL);
            $col_value      = get_option(CEF_OPTION_PREFIX . $id . CEF_POSTFIX_DB_VALUE_COL);
            $final_options  = cef_get_database_select_info($table_name, $col_id, $col_value);
            $final_string   .= "<select name='" . esc_attr($id) . "' id='" . esc_attr($id) . "'>";
            foreach($final_options as $opt) {
                $selected = (!is_null($value) && $value == esc_attr($opt['id'])) ? "selected='selected'" : "";
                $final_string .= "<option value='" . esc_attr($opt['id']) . "' {$selected}>" . esc_attr($opt['value']) . "</option>";
            }
            $final_string .= "</select>";
            break;
        default:
            $final_string = stripslashes($value);
    }
    return $final_string;
}

function cef_process_extra_options($options) 
{
    $option_array   = explode(CEF_OPTION_SEPARATOR, $options);
    $final          = array();
    if($option_array) {
        foreach($option_array as $o) {
            $value_array = explode(CEF_VALUE_SEPARATOR, $o);
            $final[] = (count($value_array) == 2) ? 
                        array('id' => trim($value_array[0]), 'value' => trim($value_array[1])) : 
                        array('id' => trim($value_array[0]), 'value' => trim($value_array[0]));
        }
    }
    return $final;
}

function cef_get_database_select_info($table, $col_id, $col_value)
{
    $final = array('id' => '', 'value' => '');
    if(is_null($table) || empty($table) || is_null($col_id) || empty($col_id) || is_null($col_value) || empty($col_value))
        return $final;
    global $wpdb;
    $final  = array();
    $sql = "SELECT `" . $col_id. "` AS id, `" . $col_value . "` AS value FROM `" . $table . "` ORDER BY value";
    $data = $wpdb->get_results($wpdb->prepare($sql), ARRAY_A);
    return ($data) ? $data : $final;
}

function cef_load_comment_extra_fields($c_id)
{
    $fields         = cef_list_comment_extra_fields();
    $comment_values = array();
    foreach($fields as $f) {
        $type = cef_translate_type($f['type'], true);
        switch($type)
        {
            case CEF_INPUT_FILE :
            $comment_values[CEF_USER_FILE . $f['id']] = get_comment_meta($c_id, CEF_USER_FILE . $f['id'], true);
                break;
            case CEF_INPUT_CHECKBOX :
            $comment_values[$f['id']] = get_comment_meta($c_id, $f['id'], true);
                break;
            case CEF_INPUT_RADIO :
                $value = get_comment_meta($c_id, $f['id'], true);
                $comment_values[$f['id']] = $value[0];
                break;
            default:
                $comment_values[$f['id']] = get_comment_meta($c_id, $f['id'], true);
                break;
        }
    }
    
    return $comment_values;
}


function cef_save_comment_meta($c_id, $data)
{
    if(is_null($c_id) || empty($c_id))
        return;

    $fields = cef_list_comment_extra_fields();
    foreach($fields as $f)
    {
        $type = cef_translate_type($f['type'], true);
        if($type == CEF_INPUT_FILE && isset($data[CEF_USER_FILE . $f['id']]))
            update_comment_meta($c_id, CEF_USER_FILE . $f['id'], $data[CEF_USER_FILE . $f['id']]);
        else if(isset($data[$f['id']]))
            update_comment_meta($c_id, $f['id'], $data[$f['id']]);
    }
}

function cef_comment_meta_delete($comment)
{
    if(is_null($comment) || empty($comment))
        return;

    $fields = cef_list_comment_extra_fields();
    foreach($fields as $f)
    {
        $type = cef_translate_type($f['type'], true);
        if($type == CEF_INPUT_FILE)
            delete_metadata('comment', $comment, CEF_USER_FILE . $f['id']);
        else
            delete_metadata('comment', $comment, $f['id']);
    }
}

function cef_delete_comment_extra_field($cef_uid)
{
    if(is_null($cef_uid) || empty($cef_uid))
        return;

    $id = cef_get_id_from_UID($cef_uid);
    cef_delete_extra_field_all_options($id, $cef_uid);
}

function cef_get_unique_ID($cef_id)
{
    if(is_null($cef_id) || empty($cef_id))
        return '';

    global $wpdb;
    $sql    = "SELECT option_name FROM {$wpdb->options} WHERE option_value='" . $cef_id . "' AND option_name LIKE ('" . CEF_OPTION_PREFIX . CEF_POSTFIX_ID . "%')";
    $data   = $wpdb->get_var($sql);
    return ($data) ? str_replace(CEF_OPTION_PREFIX . CEF_POSTFIX_ID, '', $data) : '';
}

function cef_get_id_from_UID($cef_uid)
{
    return get_option(CEF_OPTION_PREFIX . CEF_POSTFIX_ID . $cef_uid, $cef_uid);
}

function cef_delete_extra_field_all_options($id, $cef_uid)
{
    if(is_null($id) || empty($id))
        return;

    global $wpdb;
    delete_option(CEF_OPTION_PREFIX . CEF_POSTFIX_ID . $cef_uid);
    $sql = "DELETE FROM {$wpdb->options} WHERE option_name LIKE ('" . CEF_OPTION_PREFIX . $id . "%')";
    $wpdb->query($sql);
}

function cef_get_comment_extra_field($cef_uid, $is_uid = true)
{
    if(is_null($cef_uid) || empty($cef_uid))
        return;

    $id     = ($is_uid) ? cef_get_id_from_UID($cef_uid) : $cef_uid;
    $type   = get_option(CEF_OPTION_PREFIX . $id . CEF_POSTFIX_TYPE);
    $final  = array(
        'label'         => get_option(CEF_OPTION_PREFIX . $id . CEF_POSTFIX_LABEL),
        'id'            => $id,
        'type'          => $type,
        'order'         => get_option(CEF_OPTION_PREFIX . $id . CEF_POSTFIX_ORDER),
        'where'         => get_option(CEF_OPTION_PREFIX . $id . CEF_POSTFIX_WHERE),
        'who'           => get_option(CEF_OPTION_PREFIX . $id . CEF_POSTFIX_WHO),
        'html_code'     => get_option(CEF_OPTION_PREFIX . $id . CEF_POSTFIX_HTML_CODE)
    );

    switch($type) {
        case CEF_INPUT_CHECKBOX :
            $final  = array_merge($final, array('check_values' => get_option(CEF_OPTION_PREFIX . $id . CEF_POSTFIX_CHECKBOXES)));
            break;
        case CEF_INPUT_RADIO :
            $final  = array_merge($final, array('radio_values' => get_option(CEF_OPTION_PREFIX . $id . CEF_POSTFIX_RADIOS)));
            break;
        case CEF_INPUT_SELECT :
            $final  = array_merge($final, array('select_values' => get_option(CEF_OPTION_PREFIX . $id . CEF_POSTFIX_SELECTS)));
            break;
        case CEF_INPUT_FILE :
            $final  = array_merge($final, array('allowed_ext' => get_option(CEF_OPTION_PREFIX . $id . CEF_POSTFIX_ALLOWED_EXT)));
            break;
        case CEF_INPUT_DB_SELECT :
            $final  = array_merge($final, array(
                'db_table'  => get_option(CEF_OPTION_PREFIX . $id . CEF_POSTFIX_DB_TABLE),
                'db_id'     => get_option(CEF_OPTION_PREFIX . $id . CEF_POSTFIX_DB_ID_COL),
                'db_value'  => get_option(CEF_OPTION_PREFIX . $id . CEF_POSTFIX_DB_VALUE_COL))
            );
            break;
        default : //don't change the $final array if not valid info
    }
    return $final;
}

function cef_get_all_extra_field_values($comment_id)
{
    if(is_null($comment_id) || empty($comment_id))
        return;
    return cef_load_comment_extra_fields($comment_id);
}

function cef_get_extra_field_value($comment_id, $field_id)
{
    if(is_null($comment_id) || empty($comment_id))
        return;
    $data = cef_load_comment_extra_fields($comment_id);
    $keys = array_keys($data);
    if(in_array($field_id, $keys))
        return $data[$field_id];
    
    return;
}
?>