function build_cef_uploader(id, extensions) { 
    $(function(){
        $("#" + id).swfupload({
            upload_url: plugin_url + "cef-upload.php",
                    file_size_limit : "10240",
                    file_types : extensions, //"*.*",//
                    file_types_description : "Allowed Files",
                    file_upload_limit : "0",
                    flash_url : plugin_url + "scripts/swfupload.swf",
                    button_image_url : plugin_url + "scripts/cef-button.png",
                    button_width : 150,
                    button_height : 40,
                    button_placeholder : $("#button" + id)[0],
                    debug: false,
                    post_params : {cef_id : id}
            })
            .bind('swfuploadLoaded', function(event){
// do something?
		})
		.bind('fileQueued', function(event, file){
			$(this).swfupload('startUpload');
		})
		.bind('fileQueueError', function(event, file, errorCode, message){
// do something?
		})
		.bind('fileDialogStart', function(event){
// do something?
		})
		.bind('fileDialogComplete', function(event, numFilesSelected, numFilesQueued){
// do something?
		})
		.bind('uploadStart', function(event, file){
// do something?
		})
		.bind('uploadProgress', function(event, file, bytesLoaded){
// do something?
		})
                .bind('uploadSuccess', function(event, file, serverData){
                    var aux = serverData.split('|');
                    $('#log_' + id).html(aux[0]);
                    $('#cef_uploaded_file_' + id).val(aux[1]);
                })
                .bind('uploadComplete', function(event, file){
                    // upload has completed, lets try the next one in the queue
                    $(this).swfupload('startUpload');
		})
                .bind('uploadError', function(event, file, errorCode, message){
                    $('#log_' + id).html(message);
                });
    });
}