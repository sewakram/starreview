jQuery(document).ready(function()
{
	 jQuery('#commentform')[0].encoding = 'multipart/form-data';
	 jQuery('.comment-notes').remove();
     jQuery('#bottom_line-error').remove();
     jQuery(".therating label").mouseover( function(){ jQuery(this).parents(".comment-form").find(".big-rating-description").html(jQuery(this).attr("msg")); });
     jQuery(".therating").mouseout(function() {
     		var starchange = jQuery("input[id=starchange]").val();
     		if(starchange)
     		{
            	jQuery(".big-rating-description").html(jQuery('#'+starchange+'stars').attr("msg"));
     		}
     		else
     		{
     			jQuery(".big-rating-description").html('Please Rate Your Experience');
     		}
        })
     jQuery(function($){
            // load more button click event
            $('.loadmore_comment').click( function(){
                var button = $(this);
                $('#loadhidecomment').remove();
                cpage++;
         
                $.ajax({
                    url : ajaxurl, 
                    data : {
                        'action': 'cloadmore', 
                        'post_id': parent_post_id, 
                        'cpage' : cpage,
                    },
                    type : 'POST',
                    beforeSend : function ( xhr ) {
                        button.text('Loading...'); 
                    },
                    success : function( data ){
                        if( data ) {
                            $('ol.comment-list').append( data );
                            button.text('More comments'); 
                            if ( cpage == 1 )
                                button.remove();
                        } else {
                            button.remove();
                        }
                    }
                });
                return false;
            });
         
        });
});
function starreviewfb_loginCheck() {
   url = jQuery('#urlfacebook').val();
   window.location.href = url;
}
function shareon($id)
{
    //document.getElementById('share'+$id).style.display = "block";
    jQuery('#share'+$id).show();
}