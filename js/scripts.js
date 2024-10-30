var $ = jQuery.noConflict();

$(document).ready(function(){
   $('#comment_extra_field_add_edit_form').submit(function(){
       // get required fields
       return validate_required_fields() && validate_digits(); 
   });
   // activate tooltip mechanism
   $('.tooltip').click( 
   function() {
       var close_btn = "<a href='javascript:void(0)' id='close_tooltip'><img src='" + cef_plugin_url + "css/images/close.png' alt='Close'></a><div class='clear'><!-- --></div>";
       $('.tooltip-bubble').empty().append(close_btn + $(this).attr('tooltip-body')).show();
       window.location.hash = 'top-tooltip';
   });
   // close tooltip
   $('#close_tooltip').live('click', function(e) {
        $('.tooltip-bubble').empty().hide();
   });
   
   $('#cef_field_type').change(function(){
       reset_extra_field_types();
        var id= $(this).val();
        var el = $('#cef_special_field_extra_' + id);
        $.each(cef_input_types, function(index, value) {
            var all_el = $('#cef_special_field_extra_' + value);
            my_toggle_class(all_el, 'cef-show', 'cef-hide');
        });
        if(el != null) 
            my_toggle_class(el, 'cef-hide', 'cef-show');
   });
   $('#cef-html-code-example').click(function(){
      $('#cef-html-code-example').select(); 
   });
   $('#cef-html-code-hints-toggler').click(function(){
       var hints_div = $('#cef-html-code-hints');
       
      if(hints_div.hasClass('cef-hide'))
          my_toggle_class(hints_div, 'cef-hide', 'cef-show');
      else if(hints_div.hasClass('cef-show'))
          my_toggle_class(hints_div, 'cef-show', 'cef-hide');
   });
   $('#delete-selected-cefs').click(function(){ 
        var selected_ids = new Array();
        $("input[name='cef[]']:checked").each(function(){
            selected_ids.push($(this).val());
        });
        if(selected_ids.length == 0) {
            alert('No fields selected.')
        } else {
            $.ajax({
                url:cef_plugin_url + 'cef-ajax.php',
                data: {cef: selected_ids, action:'delete-bulk'},
                dataType: 'JSON',
                type:'POST',
                success: function(response)
                {
                    $('input[name="cef[]"]').removeAttr('checked');
                    window.location.reload();
                },
                error: function()
                {
                    alert('There was an error. The fields could not be deleted.');
                }
            })
        }
   });
});

reset_extra_field_types = function()
{
    $('#cef_checkboxes').val('');
    $('#cef_radios').val('');
    $('#cef_selects').val('');
    $('#cef_files').val('');
    $('#cef_file_extensions').val('');
    $('#cef_db_selects_table').val('');
    $('#cef_db_selects_id').val('');
    $('#cef_db_selects_value').val('');
}
validate_required_fields = function()
{
    var is_valid = true;
    var display_errors = '';
    $('.input-required').each(function(){
        if($.trim($(this).val()) == '') {
            is_valid = false;
            display_errors = 'Please fill in all mandatory fields';
            my_toggle_class($(this), null, 'input-error');
        } else {
            my_toggle_class($(this), 'input-error');
        }
    });
    if(display_errors != '') {
        $('#cef_error_msgs').html(display_errors);
        my_toggle_class($('#cef_success_msgs'), 'cef-show', 'cef-hide');
        my_toggle_class($('#cef_error_msgs'), 'cef-hide', 'cef-show');
    } else
        my_toggle_class($('#cef_error_msgs'), 'cef-show', 'cef-hide');
    return is_valid;
}

validate_digits = function()
{
    var display_errors = '';
    var is_valid = true;
    var intRegex = /^\d+$/;
    $('.input-digit').each(function() {
       if(!intRegex.test($(this).val())) {
           is_valid = false;
           display_errors = 'Please use only digits for order.'; // TODO: change if other digit only inputs used
           my_toggle_class($(this), null, 'input-error');
       } else 
           my_toggle_class($(this), 'input-error');
    });
    
    if(display_errors != '') {
        $('#cef_error_msgs').html(display_errors);
        my_toggle_class($('#cef_success_msgs'), 'cef-show', 'cef-hide');
        my_toggle_class($('cef_error_msgs'), 'cef-hide', 'cef-show');
    } else
        my_toggle_class($('#cef_error_msgs'), 'cef-show', 'cef-hide');
    return is_valid;
}

my_toggle_class = function(el, old_class, new_class)
{
    if(old_class != null) 
        if(el.hasClass(old_class))
            el.removeClass(old_class);
    if(new_class != null)
        if(!el.hasClass(new_class))
            el.addClass(new_class);
}

delete_comment_extra_field = function(cef_id)
{
    if(confirm('Are you sure you want to delete this field? The values are not deleted, but it will not be visible until an extra field with same id is defined.')) {
        window.location = cef_admin_url + '&action=delete&id=' + cef_id;
    }
}

cef_reset_form = function()
{
    window.location = cef_admin_url;
}