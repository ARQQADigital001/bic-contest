<?php
acf_form_head();
global $wp;
$current_url = home_url( add_query_arg( array(), $wp->request ) );
$entries=$this->get_entries();
$attachments=$this->get_attachments($entries);
foreach ($entries as $i=>$entry){
    if(!empty($entry)) $entries[$i] = get_post($entry);
}
?>
<h2 class="first-title wp-block-post-title"><?php echo __("Dashboard",'bic')?></h2>
<form name="contest-form" id="contest-form" action="<?php echo $current_url; ?>"  method="post" enctype="multipart/form-data">
    <div class="contest-img-upload-container" id="image-upload">
        <div class="col-8">
            <h4 class="left-content"><?php echo __("Preview Your Art Images",'bic')?></h4>
            <div class="upload-img">
                <input type="text" name="<?php echo "title[0]"; ?>" id="<?php echo "img-title-0"; ?>" class="input mb-10 dark-border" value="<?php echo $entries[0]->post_title?:"" ?>">
                <div class="add-container v-center dark-border <?php echo $attachments[0]?"hidden":""?> mh-550" id="<?php echo "add-imgs-file-0"; ?>" data-file="<?php echo "imgs-file-0"; ?>">
                    <div class="circle h-center" id="<?php echo "img-circle-0"; ?>">+</div>
                </div>
                <div class="img-container">
                    <img src="<?php echo $attachments[0]?:""?>" class="<?php echo $attachments[0]?"":"hidden"?> full-width mh-550" id="<?php echo "img-0"; ?>">
                    <button type="button" class="bic-menu-btn" data-overlay="overlay-0">...</button>
                    <div class="overlay" id="overlay-0">
                        <div class="img-action edit-img"   data-file="imgs-file-0"><?php echo __('Edit','bic')?></div>
                        <div class="img-action delete-img" data-file="imgs-file-0"><?php echo __('Delete','bic')?></div>
                        <input type="hidden" name="delete_post[<?php echo $entries[0]->ID?:"" ?>]" class="delete_this">
                    </div>
                </div>
            </div>
            <input type="file" accept="image/png, image/jpeg" name="<?php echo "imgs[0]"; ?>" data-img="<?php echo "img-0"; ?>" data-circle="<?php echo "img-circle-0"; ?>" id="<?php echo "imgs-file-0"; ?>" data-add-id="<?php echo "add-imgs-file-0"; ?>" class="file-uploader hidden" value="">
            <div class="error <?php echo isset($_GET['entry_error'])&&$_GET['entry_error']=="img_0_size"?"":"hidden"?>" id="<?php echo "img-error-0-size"; ?>"><?php echo __('Image exceeded the 10MB allowed','bic')?></div>
            <div class="error <?php echo isset($_GET['entry_error'])&&$_GET['entry_error']=="img_0_filename"?"":"hidden"?>" id="<?php echo "img-error-0-filename"; ?>"><?php echo __('Image Type Not allowed,Allowed type is (jpg,png)','bic')?></div>

        </div>
        <div class="col-4">
            <h4 class="left-content"><?php echo __("Upload Extra images*",'bic')?></h4>
            <?php for($i=1;$i<3;$i++){?>
                <div>
                    <div class="upload-img">
                        <input type="text" name="<?php echo "title[$i]"; ?>" id="<?php echo "img-title-$i"; ?>" class="input mb-10 dark-border" value="<?php echo $entries[$i]->post_title?:"" ?>" >
                        <div class="add-container v-center dark-border <?php echo $attachments[$i]?"hidden":""?>"  data-file="<?php echo "imgs-file-$i"; ?>">
                            <div class="circle h-center" id="<?php echo "img-circle-$i"; ?>">+</div>
                        </div>
                        <div class="img-container <?php echo $attachments[$i]?"":"hidden"?>">
                            <img src="<?php echo $attachments[$i]?:""?>" class="full-width" id="<?php echo "img-$i"; ?>">
                            <button type="button" class="bic-menu-btn" data-overlay="<?php echo "overlay-$i"?>">...</button>
                            <div class="overlay" id="<?php echo "overlay-$i"?>">
                                <div class="img-action edit-img"   data-file="imgs-file-<?php echo $i ?>"><?php echo __('Edit','bic')?></div>
                                <div class="img-action delete-img" data-file="imgs-file-<?php echo $i ?>"><?php echo __('Delete','bic')?></div>
                                <input type="hidden" name="delete_post[<?php echo $entries[$i]->ID?:"" ?>]" class="delete_this">
                            </div>
                        </div>
                    </div>
                    <input type="file" accept="image/png, image/jpeg" name="<?php echo "imgs[$i]"; ?>" data-img="<?php echo "img-$i"; ?>" data-circle="<?php echo "img-circle-$i"; ?>" id="<?php echo "imgs-file-$i"; ?>" class="file-uploader hidden" value="">
                    <div class="error <?php echo isset($_GET['entry_error'])&&$_GET['entry_error']=="img_{$i}_size"?"":"hidden"?> size-error"     id="<?php echo "img-error-$i-size"; ?>"><?php echo __('Image exceeded the 10MB allowed','bic')?></div>
                    <div class="error <?php echo isset($_GET['entry_error'])&&$_GET['entry_error']=="img_{$i}_filename"?"":"hidden"?> type-error" id="<?php echo "img-error-$i-filename"; ?>"><?php echo __('Image Type Not allowed,Allowed type is (jpg,png)','bic')?></div>
                </div>
            <?php } ?>
            <p class="contest-consent left-content">
                <input required type="checkbox" name="consent" id="consent" autocomplete="consent" class="input width-25 mr-25" value="">
                <label for="consent"><?php echo __('I Agree to the Terms and Conditions','bic')?> *</label>
            </p>
            <p class="contest-submit">
                <input type="hidden" name="current_url" value="<?php echo $current_url; ?>">
                <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary full-width" value="<?php echo __('SUBMIT','bic')?>">
            </p>
        </div>
    </div>
</form>