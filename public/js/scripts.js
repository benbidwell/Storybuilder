jQuery(document).ready(function () {
    jQuery(".alert-success").fadeIn('slow').animate({opacity: 1.0}, 3600).fadeOut('slow'); 
	jQuery('.deleteit').click(function(event) {
        event.preventDefault();
        var currentForm = jQuery(this).closest('form');
        /** Create div element for delete confirmation dialog*/
        var dynamicDialog = jQuery('<div id="conformBox">'+
        '<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;">'+
        '</span>Are you sure to delete the item?</div>');
        dynamicDialog.dialog({
                title : "Are you sure?",
                closeOnEscape: true,
                modal : true,
               	buttons : 
                    [{
                        text : "Yes",
                        click : function() {
                                jQuery(this).dialog("close");
                                currentForm.submit();
                        }
                    },
                    {
                        text : "No",
                        click : function() {
                                jQuery(this).dialog("close");
                        }
                    }]
        });
        return false;
    });
});

//validate image before uploading
jQuery("#uploadedimage").change(function() {

    var val = jQuery(this).val();

    switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
        case 'gif': case 'jpg': case 'png':
           jQuery('#imageerrorwrapper').css('display','none');
            break;
        default:
            $(this).val('');
            // error message here
            jQuery('#imageerrorwrapper').css('display','block');
            jQuery('#imageerror').html('Only supported format for image is .jpg, jpeg, .png')
            break;
    }
});
