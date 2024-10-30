<?php
require_once('../../../wp-load.php');
if(isset($_FILES))
{
    $field_id = $_POST['cef_id'];
    // try to upload files
    $file_ids = array_keys($_FILES);
    foreach($file_ids as $f) {
        if(!empty($_FILES[$f]) && !empty($_FILES[$f]['name'])) {
            $source     = $_FILES[$f]['tmp_name'];
            $dest_dir   = THEME_DISK_PATH . 'uploads';
            if(!is_dir($dest_dir)) {
                mkdir($dest_dir);
                chmod($dest_dir, '0777');
            }
            $destination    = $dest_dir .'/' . $field_id . '_' . $_FILES[$f]['name'];
            $save_file      = THEME_RELATIVE_PATH . 'uploads/' . $field_id . '_' . $_FILES[$f]['name'];
            if(!move_uploaded_file($source, $destination)) {
                echo "There was an error when uploading the file.";
            } else {
                echo "The file was successfully uploaded to folder <strong>/uploads/</strong> in the active theme.|{$save_file}";
            }
        }
    }
}
?>
