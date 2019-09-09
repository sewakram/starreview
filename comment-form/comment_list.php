<?php
/**
 *@package StarReview
 */
/**
 * user comment list
 */
?>
<style type="text/css">
			/* component */

			.star-rating {
			  display:flex;
			  flex-direction: row-reverse;
			  font-size:1.6em;
			  justify-content:space-around;
			  padding:0 .2em;
			  text-align:center;
			  width:5em;
			}

			.star-rating label {
			  color:#ccc;
			  cursor:pointer;
			}

			.star-rating input {
			  display:none;
			}

		</style>
<?php
function comment_template($comment, $args, $depth)
{
?>
		<li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">
			<div id="comment-<?php comment_ID(); ?>" class="comment-body" >
				<h3 class="title"><a href="<?php echo get_post_permalink($comment->comment_post_ID)."#comment-".$comment->comment_ID;?>"><?php echo get_the_title($comment->comment_post_ID); ?></a></h3>
				<div class="star-rating">
				<?php 
				$rating =  get_comment_meta( $comment->comment_ID, 'rating', 'single' );
				if(intval($rating) == 5)
				{ 
					$color = '#92bd50'; 
				} 
				elseif(intval($rating) <= 2)
				{ 
					$color = '#f8991d';
				}
				else
				{
					$color = '#ffcd05';
				}
				
				for($i=5; $i>=1; $i--)
				{	
					if(intval($rating) >= $i)
					{
						echo '<input type="radio" id="5-stars" name="rating" value="5" /><label for="5-stars" class="star" style="color:'.$color.'">&#9733;</label>';
					}
					else
					{
						echo '<input type="radio" id="5-stars" name="rating" value="5" /><label for="5-stars" class="star">&#9733;</label>';
					}
					
				}

				echo '</div>';

				echo '<p><b>'.get_comment_meta( $comment->comment_ID, 'review_title', 'single' ).'</b></p>';
				//echo get_avatar( $comment, 30 ); ?>

			
					<div itemprop="description" class="gpur-comment-description"><?php comment_text( $comment ); ?></div>

					<?php 
						if(get_comment_meta( $comment->comment_ID, 'bottom_line', 'single' ) === 'y')
						{
							echo '<p class="recommendations"><strong>Bottom Line:</strong> Yes, I would recommend this to a friend </p>';
						}
						else
						{
							echo '<p class="recommendations"><strong>Bottom Line:</strong> No,  I would not recommend this to a friend </p>';
						}
					?>

					<?php if ( '0' === $comment->comment_approved ) { ?>
			
						<span style="float:right;font-size:10px;"><em><?php esc_html_e( 'Your comment is awaiting approval.', 'gpur' ); ?></em></span>
			
					<?php } ?>
					<?php //comment_reply_link( array_merge( $args, array( 'reply_text' => esc_html__( 'Reply', 'gpur' ), 'add_below' => 'comment', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>

				</div>	

			<span itemprop="author" itemscope itemtype="http://schema.org/Person"> 
				<meta itemprop="name" content="<?php printf( '%s', get_comment_author_link( $comment ) ); ?>" />  
		</span>

<?php
}
?>
<?php 
echo '<div class="heading-section">
<hr class="long">
<h1>Reviews You’ve Shared <span>('.count($comments).')</span></h1>
<hr class="hr-big long">
</div>';

wp_list_comments(array(
	'style'       => 'ol',
	'per_page'          => 5,
	'callback' => 'comment_template',
	'reverse_top_level' => false //Show the oldest comments at the top of the list
), $comments);
 
 $page = (get_query_var('cpage')) ? get_query_var('cpage') : 1;

 $limit = 5;

$offset = ($page * $limit) - $limit;

$pages = ceil(count($comments)/$limit);

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

echo paginate_links( $args );


