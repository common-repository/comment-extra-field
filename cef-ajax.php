<?php
require_once('../../../wp-load.php');
require_once('comment-extra-fields-config.php');
require_once('comment-extra-fields-functions.php');
$action = (isset($_POST['action'])) ? $_POST['action'] : null;
if($action)
{
    switch($action) {
        case 'delete-bulk' :
            $ids = $_POST['cef'];
            if($ids) {
                foreach($ids as $id) {
                    $cef_UID = cef_get_unique_ID($id);
                    cef_delete_comment_extra_field($cef_UID);
                }
            }
            return array('success' => true);
            break;
        default:
            return array('success' => false);
    }
}
?>
