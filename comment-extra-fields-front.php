<?php
function cef_display_extra_fields($fields)
{
    $data           = cef_list_comment_extra_fields();

    if($data) {
        $post_type = get_post_type();
        foreach($data as $d) {       
            if(
                    ((is_null($d['where']) || empty($d['where'])) || 
                        ( isset($d['where']) && $d['where'] && (
                    in_array(CEF_EVERYWHERE, $d['where']) || in_array($post_type, $d['where'])))) && 
                    ((is_null($d['who']) || empty($d['who'])) || 
                        (isset($d['who']) && $d['who'] && (
                            $d['who'] == CEF_WHO_BOTH || $d['who'] == CEF_WHO_GUEST))
                    )
                )
                $fields[$d['id']] = $d['html_code'];
        }
    }
    return $fields;
}

function cef_display_logged_user_fields($fields)
{
    $data           = cef_list_comment_extra_fields();
    if($data) {
        $post_type = get_post_type();
        foreach ( $data as $d ) {
            if(
                    ((is_null($d['where']) || empty($d['where'])) || 
                        (in_array(CEF_EVERYWHERE, $d['where']) || in_array($post_type, $d['where']))) && 
                    ((is_null($d['who']) || empty($d['who'])) || 
                        ($d['who'] == CEF_WHO_BOTH || $d['who'] == CEF_WHO_LOGGED)
                    )
                )
                echo $d['html_code'];
        }
    }
}
add_filter('comment_form_default_fields', 'cef_display_extra_fields');
add_filter('comment_form_logged_in', 'cef_display_logged_user_fields');

function cef_save_comment_extra_fields($c_id)
{
    $fields     = cef_list_comment_extra_fields();
    foreach($fields as $f)
    {
        if(isset($_POST[$f['id']]))
            update_comment_meta($c_id, $f['id'], $_POST[$f['id']]);
        if(isset($_POST[CEF_USER_FILE . $f['id']]))
            update_comment_meta($c_id, CEF_USER_FILE . $f['id'], $_POST[CEF_USER_FILE . $f['id']]);
    }
    
}

add_action('comment_post', 'cef_save_comment_extra_fields');
?>
