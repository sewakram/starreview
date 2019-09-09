<?php
		global $wpdb;
		$totalhelp = $wpdb->get_row("SELECT count(*) as total FROM {$wpdb->prefix}commentmeta WHERE comment_id ='".$comment->comment_ID."' AND meta_key='helpfull'",OBJECT);
		$totalyes = $wpdb->get_row("SELECT count(*) as yes FROM {$wpdb->prefix}commentmeta WHERE comment_id ='".$comment->comment_ID."' AND meta_key='helpfull' AND meta_value='y'",OBJECT);
		?>

           <li  hidden="hidden" ><?php comment_class(); ?> id="comment-<?php comment_ID() ?>" ></li>


<div class="test">
		
	    <div id="comment-<?php comment_ID(); ?>" class="comment-body" style="<?php esc_attr_e( $border_color ); ?>" >
	    	
	    	
				<?php 
				// echo "Rating:".get_comment_meta( get_comment_ID(), 'rating', 'single' )."<br>";
				// ///////////////////////////////////////////////////
				$ratingval='';
				?>
				 <div class="gpur-comment-content">

				            <?php if ( '0' === $comment->comment_approved ) { ?>
				    
				                <p class="gpur-comment-meta"><em><?php esc_html_e( 'Your comment is awaiting approval.', 'gpur' ); ?></em></p>
				    
				            <?php } else { ?>
				                                
				                <span class="gpur-comment-meta">
				                	<?php 
				                	$user = get_user_by( 'email', $comment->comment_author_email );
				                	if($user)
				                	{
				                		$url = get_usermeta($user->ID , 'userphoto_thumb_file' );
				                		if($url)
				                		{
				                			echo get_avatar( $comment, 100 );
				                		}
				                		else
				                		{
				                			 $url = get_usermeta($user->ID , 'starreview_facebook_id' );
				                			 if($url)
				                			 {
				                			 	 $avatar_html = '<img alt="" src="https://graph.facebook.com/'.$url.'/picture?height=350&width=250" class="avatar avatar-100 photo" width="100" height="100">';
								    	 	 		echo $avatar_html;
				                			 }
				                			 else
				                			 {
				                			 	 echo get_avatar( $comment, 100 );
				                			 }
								    	 	
				                		}
				                	}
				                	else
					    			 {
					    			 	 echo get_avatar( $comment, 100 );
					    			 }
				                	?>
				                    <i>
									
									<?php// printf( '%s', 'By <span class="usrnm">'.get_comment_author_link( $comment ).'</span>,&nbsp;'.get_comment_date( get_option( 'date_format' ), $comment ).'&nbsp;'.get_comment_time( get_option( 'time_format' ), $comment ) ); ?>
									
				                        <?php printf( '%s', 'By <span class="usrnm">'.get_comment_author_link( $comment ).'</span>&nbsp;'); ?>
				                    </i>

				                </span>                

				            <?php } ?>
				</div>
				<?php
							// echo .$total_rating;exit;
						 if($rating=get_comment_meta( get_comment_ID(), 'rating', 'single' ))
						 {
							if(get_comment_meta(get_comment_ID(),'rating','single')==1){

							$ratingval.= '<fieldset style="max-width:150px !important;" class="therating">

							<label class="stars default" >5 stars</label>
							<label class="stars default" >4 stars</label>
							<label class="stars default" >3 stars</label>
							<label class="stars default" >2 star</label>
							<label class="stars red" >1 star</label>
							</fieldset>';
							}else if(get_comment_meta( get_comment_ID(), 'rating', 'single' )==2){
							$ratingval.= '<fieldset style="max-width:150px !important;" class="therating">

							<label class="stars default" >5 stars</label>
							<label class="stars default" >4 stars</label>
							<label class="stars default" >3 stars</label>
							<label class="stars red" >2 star</label>
							<label class="stars red" >1 star</label>
							</fieldset>';
							}else if(get_comment_meta( get_comment_ID(), 'rating', 'single' )==3){

							$ratingval.= '<fieldset style="max-width:150px !important;" class="therating">

							<label class="stars default" >5 stars</label>
							<label class="stars default" >4 stars</label>
							<label class="stars yellow" >3 stars</label>
							<label class="stars yellow" >2 star</label>
							<label class="stars yellow" >1 star</label>
							</fieldset>';

							}else if(get_comment_meta( get_comment_ID(), 'rating', 'single' )==4){

							$ratingval.= '<fieldset style="max-width:150px !important;" class="therating">

							<label class="stars default" >5 stars</label>
							<label class="stars yellow" >4 stars</label>
							<label class="stars yellow" >3 stars</label>
							<label class="stars yellow" >2 star</label>
							<label class="stars yellow" >1 star</label>
							</fieldset>';

							}else if(get_comment_meta( get_comment_ID(), 'rating', 'single' )==5){

							$ratingval.= '<fieldset style="max-width:150px !important;" class="therating">
							<label class="stars green" >5 stars</label>
							<label class="stars green" >4 stars</label>
							<label class="stars green" >3 stars</label>
							<label class="stars green" >2 star</label>
							<label class="stars green" >1 star</label>
							</fieldset>';
							}else{

							$ratingval.= '<fieldset style="max-width:150px !important;" class="therating">

							<label class="stars default" >5 stars</label>
							<label class="stars default" >4 stars</label>
							<label class="stars default" >3 stars</label>
							<label class="stars default" >2 star</label>
							<label class="stars default" >1 star</label>
							</fieldset>';

							}
						}
				        /////////////////////////////////////////////////////
						echo '<span class="rateval">'.$ratingval.'</span>';
				        // echo "Title:".get_comment_meta(get_comment_ID(), 'review_title', true ).'<br>';
				        // print_r(get_comment_meta(get_comment_ID(), 'review_title', true ).'id'.get_comment_ID());
						$isvar = get_comment_meta( $comment->comment_ID, 'isVerified' , 'single' );

				                    $label = "Verification Pending";
				                    
				                    if($isvar == '1') {
				                        $label = "Verified order";
				        ?>
						<a class="userverify" href="#openModal1">
						<span class="verifiedorder"><?php echo $label ?></span>
						<div class="userinvtd">
						<?php printf( '%s', '<span class="usrnm">'.get_comment_author_link( $comment ).'</span>,&nbsp;'); ?> was invited to write this review <a href="">Learn More</a>
						
						</div>
						</a>
					<?php } ?>
						<div class="clear"></div>
				        <?php 
						if(get_comment_meta(get_comment_ID(), 'review_title', true ))
				        echo '<h2>'.get_comment_meta(get_comment_ID(), 'review_title', true ).'</h2>';
				        ?>

				       
							 <div class="gpur-comment-description"><?php comment_text( $comment ); ?>
							 
							 
							 </div>

				<div class="revwnew">
							<div class="revnew2">
							<img src="<?php bloginfo('stylesheet_directory') ?>/images/reply.png" alt="Reply" class="replylink">
							<div class="textcontainer">
							 <?php 
				            	echo comment_reply_link( array_merge( $args, array( 'reply_text' => esc_html__( 'Reply', 'starreview' ), 'add_below' => 'comment', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ), $comment->comment_ID, $comment->comment_post_ID );
				             
				            ?>
							</div>
							</div>
								<!--<span class="hlpfl">Was this review helpful?</span>-->
								<span id="alreadyhelp_<?php comment_ID() ?>" class="<?php (empty($totalhelp->total) && empty($totalyes->yes))? '':'hide' ?>">
									
								</span>
								<div id="helpbtn_<?php comment_ID()?>" class="btnusefl">
									<span class="btnuseful">
								   		<a class="btn btn-light btn-small buttnuse" href="JavaScript:Void(0);"  onclick="set_helpful(<?php comment_ID()?>, 'y')">
										
										<img src="<?php bloginfo('stylesheet_directory') ?>/images/useful.png"/>
										<div class="textcontainer" alt="Useful">Useful</div>
								   		</a>
								   
								  
									</span>
									<!--<span>
										<a class="btn btn-light btn-small" href="JavaScript:Void(0);"  onclick="set_helpful(<?php //comment_ID()?>, 'n')">No</a>
									</span>=-->
								</div>
								<span class="btnuseful">
										<?php $reply = site_url().'/share-comment?comment_id='.$comment->comment_ID; 
										//$twitter = $_SERVER['REQUEST_URI'].'&hashtags=comment-'.$comment->comment_ID;
										?>


										<div class="shareon" onclick="shareon(<?= $comment->comment_ID?>)">	
										<a href="JavaScript:Void(0);" class="shareicon"><img src="<?php bloginfo('stylesheet_directory') ?>/images/share.png" alt="Share"/></a>
										<div class="sharevia1 textcontainer" id="share<?= $comment->comment_ID?>">
										<a href="#"  class="fcbk"
										onclick="
										window.open(
										'https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent('<?php echo $reply;?>'), 
										'facebook-share-dialog', 
										'width=626,height=436'); 
										return false;">
										<img src="<?php bloginfo('stylesheet_directory')?>/images/facebook-img.png">
										</a>
										<a class="twitter-share-button"
										href="https://twitter.com/intent/tweet?url=<?=$reply;?>"
										data-size="large">
										<img src="<?php bloginfo('stylesheet_directory')?>/images/twitter-img.png"></a>
										<div class="clear"></div>
										</div><div class="clear"></div>
										</div>


										<a class="useflag" href="JavaScript:Void(0);" onclick="set_flag(<?php comment_ID()?>)"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/flag.png" alt="Flag"/></a>
								</span>
						  <div id="flagclk_<?php comment_ID()?>" style="display:none;">
				           	
				           		<div class="blknew">
				           			
				           			<!-- <input type="radio" name="flag"  onclick="set_flagdata(<?php //comment_ID()?>,this)" value="1"> -->
				           			<input type="radio" name="flag" onclick="set_flagdatay(<?php comment_ID()?>,this)" value="1">
				           			<span>I’m a reviewer</span>
				           			<form id="optform" action="" method="post" name="optform">

					           			<div id="flagoption<?php comment_ID()?>" style="display: none;">
					           				<strong>I’m notifying consumerthought about this review because:</strong><br>
					           				<input type="checkbox" onchange="set_opt(ans1<?php comment_ID()?>,this)" name="personal_info"><span>It contains my personal information.</span>
					           				<input type="textarea" name="ans1" style="display:none;" id="ans1<?php comment_ID()?>" class="inpchk"></br>

					           				<input type="checkbox"  onchange="set_opt(ans2<?php comment_ID()?>,this)" name="language"><span>It contains language I find offensive.</span>
					           				<input type="textarea" name="ans2" style="display:none;" id="ans2<?php comment_ID()?>" class="inpchk"></br>

					           				<input type="checkbox"  onchange="set_opt(ans3<?php comment_ID()?>,this)" name="span_review"><span>It is a spam review.</span>
					           				<input type="textarea" name="ans3" style="display:none;" id="ans3<?php comment_ID()?>" class="inpchk"></br>

					           				<input type="checkbox" onchange="set_opt(ans4<?php comment_ID()?>,this)"  name="cbfowner"><span>It is written by a competitor or the business owner or family member of the owner.</span>
					           				<input type="textarea" name="ans4" style="display:none;" id="ans4<?php comment_ID()?>" class="inpchk"></br>

					           				<input type="checkbox"  onchange="set_opt(ans5<?php comment_ID()?>,this)" name="experience"><span>It does not reflect a genuine buying or service experience.</span>
					           				<input type="textarea" name="ans5" style="display:none;" id="ans5<?php comment_ID()?>" class="inpchk"></br>
					           				<input onclick="myfunction(<?php comment_ID()?>)" type="button" value="Submit" class="onsubmit">
					           			</div>
				           			</form>
				           		
				           		</div>
				           		<input type="radio" name="flag" onclick="set_flagdatan(<?php comment_ID()?>,this)"  value="0">
				           		<span>I represent Active Ventilation Products.</span>
				           		

				          </div>
						  
						 <p class="datentime"> <?php printf(get_comment_date( get_option( 'date_format' ), $comment ).'&nbsp;'.get_comment_time( get_option( 'time_format' ), $comment ) ); ?></p>
				 
				</div>
				<div class="clear"></div>
	 </div>    

    
	 <span > 
	        <meta content="<?php printf( '%s', get_comment_author_link( $comment ) ); ?>" />  
	</span>
</div>