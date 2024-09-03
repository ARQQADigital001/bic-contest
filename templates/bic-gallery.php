<?php
global $wp;
$current_url = home_url( add_query_arg( array(), $wp->request ) );
?>
<h3 class="first-title"><?php echo __("Discover",'bic')?></h3>
<h2 class="wp-block-post-title gallery-title"><?php echo __("Our Gallery",'bic')?></h2>
<p class="gallery-description">
	<?php echo __(' "Ubuntu Together" is an African philosophy that stresses our interdependence and shared humanity. It calls on artists to illustrate the strength found in community bonds and collective harmony.','bic')?>
</p>
<form name="contest-form" id="contest-form" action="<?php echo $current_url; ?>"  method="post">
    <p class="gallery-search left-content">
        <label for="search"><?php echo __('Search','bic')?> *</label>
        <input required type="text" name="search" id="bic_gallery_search" autocomplete="name" class="input" value="" placeholder="<?php echo __("search by artist name and/or artwork title",'bic')?>">
    </p>
</form>
<div class="contest-img-upload-container" id="gallery-container">
    <?php echo $this->get_entries(); ?>
</div>