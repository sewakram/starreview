<style type="text/css">
	.btn, 
	.btn.btn-small{
		color: #fff!important;
    display: inline-block;
    background-color: #39c;
    font: 600 12px/1em proxima-nova,Helvetica,Arial,sans-serif!important;
    -webkit-border-radius: 7px;
    -moz-border-radius: 7px;
    border-radius: 7px;
    width: auto;
    -webkit-box-shadow: none;
    -moz-box-shadow: none;
    box-shadow: none;
    border: none;
    padding: .8em 1em;
    cursor: pointer;
    text-decoration: none;
	}
	.btn.btn-small {
    text-transform: capitalize;
    padding: .5em 1em;
    font-size: 12px!important
}
.btn.btn-light {
    background-color: #7ebedf;
}
.btn.btn-light:hover {
    background-color: #39c;
}
.btn:hover {
    background-color: #92bd51;
    color: #fff;
    text-decoration: none!important;
}
a:hover {
    color: #92bd51;
}
a:active, a:hover {
    outline: 0;
}
.businessuser-form {
    margin: 8px 0 8px 28px;
}
.alert-danger {
    background-color: #f2dede;
    border-color: #eed3d7;
    color: #b94a48;
}
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}
.reporting {
    border: 0!important;
}

.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}

/*load more start*/
div .test{
    display:none;
    padding: 10px;
    border-width: 0 1px 1px 0;
    border-style: solid;
    border-color: #fff;
    box-shadow: 0 1px 1px #ccc;
    margin-bottom: 5px;
    background-color: #f1f1f1;
}
.totop {
    position: fixed;
    bottom: 10px;
    right: 20px;
}
.totop a {
    display: none;
}
a, a:visited {
    color: #33739E;
    text-decoration: none;
    display: block;
    margin: 10px 0;
}
a:hover {
    text-decoration: none;
}
/*#loadMore {
    padding: 10px;
    text-align: center;
    background-color: #33739E;
    color: #fff;
    border-width: 0 1px 1px 0;
    border-style: solid;
    border-color: #fff;
    box-shadow: 0 1px 1px #ccc;
    transition: all 600ms ease-in-out;
    -webkit-transition: all 600ms ease-in-out;
    -moz-transition: all 600ms ease-in-out;
    -o-transition: all 600ms ease-in-out;
}
#loadMore:hover {
    background-color: #fff;
    color: #33739E;
}*/
/*load more end*/
</style>
<div class="titl" id="wrtrvw">

	<?php

	global $wpdb;

	// echo get_the_ID()."and";

	// $comms = get_comments( array( 'post_id'=>get_the_ID(),'include_approved'=>1 ) );

	$comms=$wpdb->get_results("

	SELECT * FROM ". $wpdb->prefix."comments

	WHERE 

	comment_post_ID = '" . get_the_ID() . "' AND 

	comment_approved = 1
	ORDER BY comment_date DESC;
	"); 



	// echo $wpdb->num_rows;

				$counter = null;

				if( $comms )

				{

					// echo "total comments:".count( $comms )."</br>";

				$total = count( $comms );

				# Iterate all comments and add its rating to the counter

				foreach( $comms as $c )

				{

					// echo "comment id:".$c->comment_ID."and approve:".$c->comment_approved."and postid:".$c->comment_post_ID."</br>";

				$rating = (int) get_comment_meta( $c->comment_ID, 'rating', true );

				$counter +=  $rating;

				}

				// echo "total rate:".$counter;

				# Calculate and print the totals

				$counter = $counter / $total;

				}

				

			
			$avgrat ='<ul class="ratelist"><li class="ratelistli"></li></ul>';

        /////////////////////////////////////////////////////
			?>
			<h1><b><?php echo get_the_title();?> </b><span class="icon icon-checked2"></span></h1></br>
	
<div class="short-info">
	
	<div class="col-md col-md-6 col-lg-5">
	
	<a class="btn btn-light btn-small btnreview" href="#respond" rel="nofollow">Write a Review</a>
	
	</div>
</div><br>
			
<div class="review openreview" >
			<div class="starrate">
				<span>
					<?php 
						$image = '';
						$ratetot = number_format((float)$counter, 1, '.', '');
						if($ratetot <= 2 && $ratetot > 0)
						{
							$image = 'star-img-org.png';
						}
						elseif($ratetot <= 4 && $ratetot > 2)
						{
							$image = 'star-img-yllw.png';
						}
						elseif($ratetot <= 5 && $ratetot > 4)
						{
							$image = 'star-img-green.png';
						}
						else
						{
							$image = 'star-img-grey.png';
						}
						//echo $image;
					?>
						<style>

						.ratelist{width:160px;position:relative;margin:0 auto 10px !important}
						.ratelist, .ratelist li{list-style-type:none;margin:0;padding:0;background:transparent url(<?php bloginfo ('template_directory') ?>/images/<?php echo $image ?>) repeat-x;height:25px}
						.ratelist li{display:block;width:<?php echo number_format((float)$counter, 1, '.', '') * 32;?>px;background-position:left center;position: absolute;
						text-indent:-9999px;z-index:1;} 

						</style>

					<?php echo "<span class='avgrat'>Average Consumer Rating:</span><span class='numvall'>".number_format((float)$counter, 1, '.', '')."</span>&nbsp;"; echo $avgrat;?>
					
				</span>
				<span class="description">
				<span><span ><b><?php echo $total//get_comments_number()?></b></span><b> Consumer Reviews</b></span>
				</span></br>
			</div>
			
			<div class="commentrating">
				<p class="ratesnap">Rating Snapshot: </p><a class="btn btn-light btn-small btnoldnew" href="<?php global $wp;echo ($_GET['sort'])? home_url( $wp->request ).'#comment-list':home_url( $wp->request ).'?sort=true#comment-list';?>" rel="nofollow"><?= ($_GET['sort'])? "Newest first":"Oldest first"?></a><a class="btn btn-light btn-small btnreset" href="<?= get_permalink( get_the_ID())?>">Reset sorting</a>
				<div class="ratavg">
					<style>
					.hotel-bar {
					    width:150px;
					    height:12px;
					    border:1px solid #BBB;
					    margin:0 0 1px 0;
					    background-color:#DDD;
						display: inline-block;margin-right: 8px;
					}
					.ranking {
					    height:11px;
					    background-color: #92bd50;
					}
						</style>
						<?php 
							//progress bar code

						
						    for ($i=5; $i >= 1; $i--) {
						    	$args = array(
									'post_id' => get_the_ID(),
									'count' => true,
									'meta_key' => 'rating',
									'meta_value' => $i,
									'status' => 'approve'
								);
								
								if($i == 5)
								{
									$ratetext = 'Excellent';
								}else
								if($i == 4)
								{
									$ratetext = 'Great';
								}else
								if($i == 3)
								{
									$ratetext = 'Average';
								}else
								if($i == 2)
								{
									$ratetext = 'Poor';
								}else
								if($i == 1)
								{
									$ratetext = 'Bad';
								}
								
						    	$geton = get_comments( $args );
						    		$percentile = ($geton/ $total) * 100;
						    		// <input type="checkbox" onclick="checkesort('.get_permalink( get_the_ID()).'?filter='.$i.'#comment-list)">
						    	echo '<a href="'.get_permalink( get_the_ID()).'?filter='.$i.'#comment-list"><div class="barrate"><span class="startext"> '.$ratetext.'&nbsp;<div class="ratestar"><div class="startext1">'.$i.' Star :</div></span><div class="hotel-bar">
									    <div class="ranking" data-ranking="'.ceil($percentile).'"></div>
									</div><span class="geton">'.$geton.'</span></div></div></a>';
						    }
							    
						?>
						<script>
							jQuery(window).load(function($){
							    jQuery('.ranking').each(function(){jQuery(this).css('width',jQuery(this).attr('data-ranking')+'%');});
							});
						</script>
					
					</div>
			</div>
			
			<div class="peoplerecommend">
				Bottom Line: 
					<span class="digital">
						<?php 
							$args = array(
								'post_id' => get_the_ID(),
								'count' => true,
								'meta_key' => 'bottom_line',
								'meta_value' => 'y',
								'status' => 'approve'
							);
					    	$getbottom = get_comments( $args );
					    	$percentage = ($getbottom / $total) * 100;
					    	echo ceil($percentage).'%';
					    ?>
					</span> 
				would recommend it to a friend
			</div>
			<div class="clear"></div>
</div>
	<ol class="comment-list" id="comment-list">
	<?php
	$filterdata=$wpdb->get_results("

	SELECT * FROM ". $wpdb->prefix."comments c,". $wpdb->prefix."commentmeta cm

	WHERE 
	c.comment_ID=cm.comment_id AND
	c.comment_post_ID = '" . get_the_ID() . "' AND 
	cm.meta_key='rating' AND 
	cm.meta_value='".$_GET['filter']."' AND
	c.comment_approved = 1
	ORDER BY comment_date DESC
	"); 
// echo "<pre>";print_r();
	if($_GET['sort']){
		wp_list_comments(

					array(
						'avatar_size' => 100,
						'page'        => '',
						'per_page'    => '',
						'style'       => 'ol',
						'short_ping'  => true,
						'reply_text'  => esc_html__( 'Reply', 'starreview' ),
						'callback'=>'format_comment',
						'reverse_top_level' => false
						)

				);
	}else if($_GET['filter']){
		wp_list_comments(

					array(
						'avatar_size' => 100,
						'page'        => '',
						'per_page'    => '',
						'style'       => 'ol',
						'short_ping'  => true,
						'reply_text'  => esc_html__( 'Reply', 'starreview' ),
						'callback'=>'format_comment',
						'reverse_top_level' => true
						),$filterdata

				);
	}else{
		wp_list_comments(

					array(
						'avatar_size' => 100,
						'page'        => '',
						'per_page'    => '',
						'style'       => 'ol',
						'short_ping'  => true,
						'reply_text'  => esc_html__( 'Reply', 'starreview' ),
						'callback'=>'format_comment',
						'reverse_top_level' => true
						)

				);
	}
	
    echo "</ol>";
	echo '<a href="#" id="loadMore" class="loadmore_comment">Load More</a>

	<p class="totop"> 
	<a href="#top">Back to top</a> 
	</p>';

			// $comments_number = get_comments_number();
			// $cpage = get_query_var('cpage') ? get_query_var('cpage') : 1;
			// //echo $cpage;
			// $value = number_format_i18n( $comments_number )/get_option('comments_per_page');
			// if( $cpage <= round($value) ) {
			// 	echo '<div class="loadmore_comment">More comments</div>
			// 	<script>
			// 	var ajaxurl = \'' . site_url('wp-admin/admin-ajax.php') . '\',
			// 	    parent_post_id = ' . get_the_ID() . ',
			// 	    comments_number = ' . round($value) . ',
			//     	    cpage = ' . $cpage . '
			// 	</script>';
			// }

			function starreview_prs_schema($counter) 
			{

				$comment_args = array(
				        'post_id'  => get_the_ID(),
						'orderby' => 'comment_date_gmt',
						'order' => 'DESC',
						'number' => 10,
						'offset' => 10,
						'parent' => 0,
						'status' => 'approve'
				);
				$datacomment = get_comments( $comment_args );
				// echo "<pre>";
				// print_r($datacomment);
				$review = '"review": [';
				for($i = 0; $i < count($datacomment); $i++)
				{
					if($datacomment[$i]->comment_author != '')
					{
						if($datacomment[$i+1]->comment_author == '')
						{
							$review .= '{"@type": "Review",
									      "author": "'.$datacomment[$i]->comment_author.'",
									      "datePublished": "'.$datacomment[$i]->comment_date.'",
									      "reviewBody": "'.$datacomment[$i]->comment_content.'",
									      "name": "'.$datacomment[$i]->comment_author.'",
									      "reviewRating": {
									        "@type": "Rating",
									        "bestRating": "5",
									        "ratingValue": "'.round(get_comment_meta( $datacomment[$i]->comment_ID, 'rating', true ),1).'",
									        "worstRating": "1"
									      }
									    }';
						}
						else
						{
							
								$review .= '{"@type": "Review",
									      "author": "'.$datacomment[$i]->comment_author.'",
									      "datePublished": "'.$datacomment[$i]->comment_date.'",
									      "reviewBody": "'.$datacomment[$i]->comment_content.'",
									      "name": "'.$datacomment[$i]->comment_author.'",
									      "reviewRating": {
									        "@type": "Rating",
									        "bestRating": "5",
									        "ratingValue": "'.round(get_comment_meta( $datacomment[$i]->comment_ID, 'rating', true ),1).'",
									        "worstRating": "1"
									      }
									    },';
						}
					}
				}

				$review .=  ']';

					  $content_post = get_post(get_the_ID());
		              $content = $content_post->post_content;
		              $first_img = '';
		              $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches);
		             $first_img = $matches [1] [0];

					 $thumbnail_image = explode(".jpg", $first_img);

						if (strpos($thumbnail_image[0], 'uploads') !== false) {
						 $thumb_image = $thumbnail_image[0].'-90x90.jpg';
						} else {
						 $thumb_image = $thumbnail_image[0].'.jpg';
						}
			              if(empty($first_img)) {
			                $first_img = "https://placehold.it/235x235/f2f2f2/dddddd&text=No+Image";
			              }
			              $post_id = get_the_ID();
				$atts = array(
					'url' => esc_url( get_permalink( $post_id ) ),
					'title' => the_title_attribute( array( 'post' => $post_id, 'echo' => false ) ),
					'author' => get_the_author_meta( 'display_name' ),
					'description' => '',
				    'image' => esc_url( get_the_post_thumbnail_url( 'thumbnail' ) ),
					'rating_value' => 0,
					'user_votes' => wp_rand( 0, 50 ),
					'max_rating' => 5,
				);

				$atts['description'] = substr(strip_tags(  get_post_meta(get_the_ID(), '_yoast_wpseo_metadesc', true) ), 0, 255);

				$atts['rating_value'] =  round($counter,1);
				if(ceil(floatval( $atts['rating_value'] )) > 5)
				{
					 $ratingvalue = 5; 
				}
				else
				{ 
					if(floatval( $atts['rating_value'] > 1))
					{
						$ratingvalue = floatval( $atts['rating_value'] );
					}
					else
					{
						$ratingvalue = 1;
					}
				}

				if($atts['user_votes'])
				{
					$user_votes = $atts['user_votes'];
				}
				else
				{
					$user_votes = 1;
				}
			
				$output = '';
				if(get_post_meta(get_the_ID(),'offer',true))
				{ 
					$price = get_post_meta(get_the_ID(),'offer',true); 
				}else
				{ 
					$price = 55;
				}
				$output .= '{';
					if ( 'site-rating' === $data OR 'custom' === $data ) {
						$output .= '"@context": "http://schema.org/",
						"@type": "Review",
						"mainEntityOfPage": {
							  "@type": "WebPage",
							  "@id": "' . $atts['url'] . '"
						},
						"itemReviewed": {
							"@type": "Thing",
							"name": "' . $atts['title'] . '"
						},
						"author": {
							"@type": "Person",
							"name": "' . $atts['author'] . '"
						},
						"reviewRating": {
							"@type": "Rating",
							"ratingValue": "' . $atts['rating_value'] . '",
							"worstRating" : "0",
							"bestRating": "' . $atts['max_rating'] . '"
						}';
					} else {
						$output .= '"@context": "http://schema.org/",
						"@type": "Product",
							"name": "' . $atts['title'] . '",
							"image": "' . $first_img . '",
							"description": "' . $atts['description'] . '",
							"sku": "'.get_post_meta(get_the_ID(),'sku',true).'",
							"mpn": "'.get_post_meta(get_the_ID(),'sku',true).'",
							"brand": "'.get_post_meta(get_the_ID(),'brand',true).'",
							"offers": {
							    "@type": "Offer",
							    "availability": "http://schema.org/InStock",
							    "price": "'.$price.'",
							    "priceCurrency": "USD",
							    "priceValidUntil": "' . date('Y-m-d H:i:s') . '",
							    "url": "' . $atts['url'] . '"
							  },
							"aggregateRating": {
								"@type": "AggregateRating",
								"ratingValue": "' . $ratingvalue . '",
								"reviewCount": "' . $user_votes . '"
							},

							'.$review.'
						}';
					}	
				$output .= '}';
				
				return $output;
				
			}
			$starreview_schema_js = starreview_prs_schema($counter);
				 
			add_action( 'wp_footer', function() use ( $starreview_schema_js ) { //
				echo '<script type="application/ld+json">' . wp_kses_post( $starreview_schema_js ) .'</script>';
			});
	// Pagination start
			$page = (get_query_var('cpage')) ? get_query_var('cpage') : 1;

			$limit = get_option('comments_per_page');

			$offset = ($page * $limit) - $limit;

			$pages = ceil(count($comms)/$limit);

			$args = array(

			'base'         => @add_query_arg('cpage','%#%'),

			'format'       => '?page=%#%',

			'total'        => $pages,

			'current'      => $page,

			'show_all'     => False,

			'end_size'     => 1,

			'mid_size'     => 2,

			'prev_next'    => True,

			'prev_text'    => __('Previous'),

			'next_text'    => __('Next'),

			'type'         => 'plain');

			// ECHO THE PAGENATION 

			//echo paginate_links( $args );

		// Pagination end 
	function format_comment($comment, $args, $depth) {
		include( plugin_dir_path( __FILE__ ) . 'comment-template-list.php' );
	} 
echo '<style>

		.modalDialog {
    position: fixed;
    font-family: Arial, Helvetica, sans-serif;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: rgba(0, 0, 0, 0.8);
    z-index: 99999;
    opacity:0;
    -webkit-transition: opacity 400ms ease-in;
    -moz-transition: opacity 400ms ease-in;
    transition: opacity 400ms ease-in;
    pointer-events: none;
}
.modalDialog:target {
    opacity:1;
    pointer-events: auto;
}
#openModal1 a.close {
	text-decoration: none;
	color: #fff;
}
#openModal1 p {
	font-size: 15px;
	line-height: 20px;
}
.modalDialog > div {
    max-width: 630px;
    position: relative;
    margin: 10% auto;
    padding: 5px 20px 13px 20px;
    border-radius: 10px;
    background: #fff;
}
.close {
    background: #606061;
    color: #FFFFFF;
    line-height: 25px;
    position: absolute;
    right: -12px;
    text-align: center;
    top: -10px;
    width: 24px;
    text-decoration: none;
    font-weight: bold;
    -webkit-border-radius: 12px;
    -moz-border-radius: 12px;
    border-radius: 12px;
    -moz-box-shadow: 1px 1px 3px #000;
    -webkit-box-shadow: 1px 1px 3px #000;
    box-shadow: 1px 1px 3px #000;
}
.close:hover {
    background: #606061;
}	
		</style>';

echo'

<div id="openModal1" class="modalDialog">
    <div>	<a href="#close" title="Close" class="close">X</a>

        <h3>What is a "Verified Reviewer" review? </h3>

<p>When a review is marked "Verified", it means Consumer Health Digest has the additional trust signal necessary to ensure that the review is genuine. Customers reading the reviews can then use this information to help them decide which reviews are more relevant and steer them in their purchasing decisions.</p>

<p>If a review does not have the "Verified Reviewer" label next to it, we do not have the additional information from the customer/company to verify that the review was written from the privileged user position. Even though some reviews do not carry the "Verified Reviewer" badge, it does not mean that the reviewer has no experience with the company - it just means that we could not confirm a specific purchase. The "Verified Reviewer" badge offers one more way to help gauge the quality and relevance of a company review.</p>

 <h3>How can I make my review "Verified"? <h3>

<p>If we are unable to automatically verify you as a "Verified Reviewer", you can email supporting documents via contact us(hyperlink) form so we can investigate and mark your review as "Verified". We prefer an invoice, delivery note or receipt.	</p>
	
        
    </div>
</div>';

	?>
          
	

	<?php 

	comment_form();
	
	?>
	
	</div>
	
	<script type="text/javascript">
			function checkFilter(age) {
				var checked = [];
				if(age.value!=''){
					// console.log(age);
					if(age.name=='ans3')
						{
							checked['span_review']=age.value;
						}

					if(age.name=='ans4')
						{
							checked['cbfowner']=age.value;
						}

					if(age.name=='ans1')
						{
							checked['personal_info']=age.value;
						}

					if(age.name=='ans2')
					{
						checked['language']=age.value;
					}

					if(age.name=='ans5')
					{
						checked['experience']=age.value;
					}
					return checked;
				}
				// console.log('checked',checked);
			
			}
			function myfunction(id) {
				
					var yesdata=$("#optform").serializeArray();
			//console.log("yesdata",yesdata);
			var filtered=yesdata.filter(checkFilter)
		console.log('filtered',filtered);
		var mydata=[];
		
		var i;
				for(i=0;i<filtered.length;i++){
					// if(yesdata[i].name==='personal_info'){
						var nm=filtered[i].name;
						var countnext=i+1;
						// alert(nm+"and"+countnext);
						mydata[nm]=filtered[countnext].value;
						i++;
						
						// console.log("inside"+i,mydata);
					}
				console.log("outside",Object.assign({},mydata));
				console.log("stgify",JSON.stringify(Object.assign({},mydata)));
		
				jQuery.ajax({					
				async: true,
				type : "POST",
				dataType : "json",
				url: '<?php echo admin_url('admin-ajax.php') ?>',
				
				data:{ 
				'action': 'helpfullflag',
				'cid': id,
				'myvalue': Object.assign({},mydata)
				},
				// cache: false,
				success : function(respons){
				console.log("my"+respons.status); 
				///////////////////////////////////
					if(respons.status==false){
					
					$( '#flagclk_'+id ).replaceWith('<b style="color:red;float:right;margin-right:15px">Already flag </b>' );
					setTimeout(function(){ $('.revwnew b').fadeOut(); }, 1000);
					}else{
					// $( '#flagclk_'+id ).replaceWith('<b style="color:green;float:right;margin-right:15px">Thank You</b>' );
					var sucessmsg='<div class="reporting"><div class="alert alert-success"><h4>Thanks for notifying us!</h4>We`ll look into this review and get back to you. Learn more about our <a href="#" target="_blank">notification process</a>.</div></div>';
					$( '#flagclk_'+id ).replaceWith(sucessmsg);
					setTimeout(function(){ $('.reporting').fadeOut(); }, 3000);
					
					}
					
				// ////////////////////////////////
						
				}
					
				});	
			}
	
     	
	// console.log('outside',checked);
     function set_opt(vid,thisref){
     	$(vid).toggle("show");
	 }				

	function set_flagdatay(id,thisref){
		$("#flagoption"+id).show();
		$("#optionn"+id).hide();
		
	}

	function set_flagdatan(id,thisref){
		$("#flagoption"+id).hide();
		// $("#optionn"+id).show();
		

		// console.log($(thisref).val());
		jQuery.ajax({
		async: true,
		type : "POST",
		dataType : "json",
		url: '<?php echo admin_url('admin-ajax.php') ?>',
		// async: true,
		data:{ 
		'action': 'helpfullflag',
		'cid': id,
		'myvalue': 'n'//$(thisref).val()
		},
		// cache: false,
		success : function(respons){
		console.log("my"+respons.status); 
		if(respons.status==false){
            
            $( '#flagclk_'+id ).replaceWith('<b style="color:red;float:right;margin-right:15px">Already flag </b>' );
            setTimeout(function(){ $('.revwnew b').fadeOut(); }, 1000);
        }else{
        	var falseopt='<div class="businessuser-form" data-notify-compliance-businessuser-form=""><div class="alert alert-danger"><span>To report this review, log in to your business account, and go to the <a href="#" target="_blank" rel="nofollow">Service Reviews page</a>.</span><br><span>Don`t have an account? It`s free and easy to <a href="#" target="_blank" rel="nofollow">sign up</a>.</span></div></div>';
            $( '#flagclk_'+id ).replaceWith(falseopt );
            // $( '#flagclk_'+id ).replaceWith('<b style="color:green;float:right;margin-right:15px">Thank You</b>' );
		setTimeout(function(){ $('.businessuser-form').fadeOut(); }, 5000);
            
        }

		}
		//error : function(error){ console.log("fail",error);} 

			
			
		});
	}

     function set_flag(cid){
     	$("#flagclk_"+cid).toggle("show");
     	
     }

	function set_helpful(cid,mydata){
		// console.log("holla",cid,myaction,mydata);
		jQuery.ajax({
		async: true,
		type : "POST",
		dataType : "json",
		
		url: '<?php echo admin_url('admin-ajax.php') ?>',
		// async: true,
		data:{ 
		'action': 'helpfull',
		'cid': cid,
		'myvalue': mydata
		},
		// cache: false,
		success : function(respons){
		console.log(respons); 
		$( '#helpbtn_'+cid ).hide();
		if(respons==0){
			$( '#alreadyhelp_'+cid ).html( '<b style="color:red;">You`ve Voted Already</b>' );
			$( '#alreadyhelp_'+cid ).show();
		}else{
			$( '#alreadyhelp_'+cid ).html( '<b style="color:green;">Thank You</b>' );
			$( '#alreadyhelp_'+cid ).show();
			$( '#helpful_'+cid ).html( respons[1].yes+' out '+respons[0].total+' people found this review helpful' );
			$( '#helpful_'+cid).show();
		}
		
		}
		//error : function(error){ console.log("fail",error);} 

			
			
		});
	}

	jQuery(function ($) {

	// copy text to another field start
	var $foo = $(".comment-form-comment textarea");
	var $bar = $("#review_title");
	function onChange() {
	$bar.val($foo.val().substr(0, 100));
	};
	$(".comment-form-comment textarea")
	.change(onChange)
	.keyup(onChange);
	// copy text to another field end

    $("div .test").slice(0, 10).show();
    $("#loadMore").on('click', function (e) {
        e.preventDefault();
        $("div .test:hidden").slice(0, 10).slideDown();
        if ($("div .test:hidden").length == 0) {
            $("#load").fadeOut('slow');
        }
        $('html,body').animate({
            scrollTop: $(this).offset().top
        }, 1500);
    });
});
 
// jQuery('a[href=#top]').click(function ($) {
//     $('body,html').animate({
//         scrollTop: 0
//     }, 600);
//     return false;
// });
 
// jQuery(window).scroll(function ($) {
//     if ($(this).scrollTop() > 50) {
//         $('.totop a').fadeIn();
//     } else {
//         $('.totop a').fadeOut();
//     }
// });
	</script>