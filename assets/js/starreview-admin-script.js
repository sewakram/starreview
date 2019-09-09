jQuery(document).ready(function(){

	jQuery(document).on('click', 'a.verifybutton', function() {
		var valueon = jQuery(this).data('value');
		var requests = jQuery(this).data('request');
		var id = this.id;
		jQuery.ajax({
			url: requests,
			type: 'post',
			datatype: 'html',
			data: { action: 'isverified_comment', commentid: id, value: valueon },
			success: function(data){
				if(data == 0)
				{
					jQuery('#'+id).html('Verification Pending');
					jQuery('#'+id).attr('data-value', '0');
				}
				else
				{
					jQuery('#'+id).html('Verified');
					jQuery('#'+id).attr('data-value', '1');
				}
			}
		});
	});

});