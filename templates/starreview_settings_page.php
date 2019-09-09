<?php
/**
 *@package StarReview
 */
echo '<form action="options.php" method="post">';

	settings_fields( 'star-review-setting-dynamics' );

	do_settings_sections( 'star-reviews-setting' );

	submit_button();

echo '</form>';