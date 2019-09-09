<?php



/**



 *@package StarReview



 */







/**



 * custom comment text



 */



class cust_comment_text



{



		



	public function register()



	{





		//add_filter( 'comment_text', array($this, 'starreview_custom_comment_text'), 10, 2 );



		



		add_filter( 'manage_edit-comments_columns', array($this, 'myplugin_comment_columns' ));







		add_filter( 'manage_comments_custom_column', array($this, 'myplugin_comment_column'), 10, 2 );


	}


	public function change_status_comment()
	{
		echo 'india';
	}


	function myplugin_comment_columns( $columns )



	{



		return array_merge( $columns, array(



			'rating' => __( 'Rating' ),



			'location' => __( 'Location' ),



			'product_file' => __( 'Product File' ),


			'isVerified' => __( 'isVerified' ),



		) );



	}



	







	function myplugin_comment_column( $column, $comment_ID )



	{



		switch ( $column ) {



			case 'rating':



			case 'location':



				if ( $meta = get_comment_meta( $comment_ID, $column , true ) ) {



					echo $meta;



				} else {



					echo '-';



				}



			break;



			case 'product_file':



				if ( $meta = get_comment_meta( $comment_ID, $column , true ) ) {



					$upload_dir = wp_upload_dir();



					$dir = trailingslashit($upload_dir['baseurl']) . 'commentProductBill';



					echo '<a href="'.$dir.'/'.$meta.'" target="_blank">View</a>';



				} else {



					echo '-';



				}



			break;


			case 'isVerified':

				 $isvar = get_comment_meta( $comment_ID, $column , true );

					$label = "Verification Pending";
					$value = 0;
					if($isvar == '1') {
						$label = "Verified";
						$value = 1;
					}

				echo '<a href="JavaScript:void(0);" name="isVerified" data-request="'.admin_url( 'admin-ajax.php' ).'" data-value = "'.$value.'" id="'.$comment_ID.'" class="verifybutton">'.$label.'</a>';
			break;



		}



	}



	











	public function starreview_custom_comment_text($text, $comment_obj)



	{


		// echo "<pre>";print_r();




		$rating =  get_comment_meta( $comment_obj->comment_ID, 'rating', 'single' );







		$location =  get_comment_meta( $comment_obj->comment_ID, 'location', 'single' );



		//get_comment_meta( $comment_id, $key, $single );



		$total_rating = '';



		$location_area = '';







		$totalrate = get_option('total_rating');







		if(!empty($rating))



		{



			$total_rating = esc_html__( 'Rating : '.intval($rating) , 'starreview' );



		}







		if(!empty($location))



		{



			$location_area = esc_html__( 'Location : '.$location, 'starreview' );



		}







		echo '<span>'.$total_rating.'<span></br>';
		echo get_comment_meta($comment_obj->comment_ID, 'review_title', true ).'<br>';
		echo '<p> '.$text.'</p><br>';
		echo '<p>'.$location_area.'</p>';



	}



}