<?php
$attachment = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ));

?>
<div class="col-4">
	<div class="upload-img">
		<div class="img-container">
			<img src="<?php echo $attachment ?>" class="gallery full-width" id="<?php echo "img-$post->ID"; ?>">
		</div>
	</div>
	<h5 class="py-10 left-content"><?php echo $post->meta_value?:"-" ?></h5>
	<h4 class="py-10 left-content"><?php echo $post->post_title?:"-" ?></h4>
</div>
