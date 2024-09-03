<?php
acf_form_head();
$all_countries   = acfcs_get_countries( false );
$countries       = [];
if ( ! empty( $all_countries ) ) {
    foreach ( $all_countries as $country_code => $label ) {

        $countries[] = [
            'code' => $country_code,
            'name' => esc_attr__( $label, 'acf-city-selector' ),
        ];
    }
}
$all_terms=[
        [
                "text"=> __("Create an artwork with any BIC Ball Pens and submit before 18 July 2024",'bic'),
                "icon"=>BIC_PLUGIN_URL."assets/images/Ball-Pen.png"
        ],
        [
            "text"=> __("Each artist needs to register once and enter as many as 3 artwork",'bic'),
            "icon"=>BIC_PLUGIN_URL."assets/images/3-artworks.png"
        ],
        [
                "text"=> __("This contest is open for African and Middle East residents only",'bic'),
                "icon"=>BIC_PLUGIN_URL."assets/images/map.png"
        ],
        [
            "text"=> __("The Artist needs to submit original and authentic artworks only",'bic'),
            "icon"=>BIC_PLUGIN_URL."assets/images/artist.png"
        ],
        [
                "text"=> __("The artwork needs to be prepared on minimum A3 size paper/medium",'bic'),
                "icon"=>BIC_PLUGIN_URL."assets/images/paper-artwork.png"
        ],
        [
                "text"=> __("The winning artist will have to present the original artwork when required",'bic'),
                "icon"=>BIC_PLUGIN_URL."assets/images/win-cup.png"
        ],
];

global $wp;
$current_url = home_url( add_query_arg( array(), $wp->request ) );
?>
<h2 class="first-title wp-block-post-title"><?php echo __("Create Your Account",'bic')?></h2>
<form name="contest-form" id="contest-form" action="<?php echo $current_url; ?>"  method="post" enctype="multipart/form-data">
    <p class="contest-name left-content">
        <label for="name"><?php echo __('Name','bic')?> *</label>
        <input required type="text" name="name" id="name" autocomplete="name" class="input" value="<?php echo $_POST['name']; ?>">
    </p>
    <p class="contest-email left-content">
        <label for="email"><?php echo __('Email','bic')?>  *</label>
        <input required type="email" name="email" id="email" autocomplete="email" class="input" value="<?php echo $_POST['email']; ?>">
        <div class="error left-content <?php echo isset($_GET['entry_error'])&&$_GET['entry_error']=='email_exits'?"":"hidden"?>" id="email-invalid"><?php echo __('Email already exists','bic')?></div>
    </p>
    <p class="contest-phone left-content">
        <label for="email"><?php echo __('Phone','bic')?>  *</label>
        <input required type="phone" name="phone" id="phone" autocomplete="phone" class="input" value="<?php echo $_POST['phone']; ?>">
    </p>
    <p class="contest-password left-content">
        <label for="password"><?php echo __('Password','bic')?> *</label>
        <input required type="password" name="password" id="password" autocomplete="current-password" spellcheck="false" class="input" value="">
    </p>
    <p class="contest-country col-6-left left-content">
        <label for="acfcs_country"><?php echo __('Country','bic')?> *</label>
        <select required name="acfcs_country" id="acfcs_country" class="input select2 acfcs__dropdown--countries">
            <option value="">
                <?php echo apply_filters( 'acfcs_select_country_label', esc_html__( 'Select a country', 'acf-city-selector' ) ); ?>
            </option>
            <?php foreach( $countries as $country ) { ?>
                <option value="<?php echo $country[ 'code' ]; ?>" <?php echo $_POST['acfcs_country']==$country[ 'code' ]?"selected":""; ?> >
                    <?php _e( $country[ 'name' ], 'acf-city-selector' ); ?>
                </option>
            <?php } ?>
        </select>
    </p>
    <p class="contest-state col-6-right left-content">
        <label for="acfcs_state"><?php echo __('City','bic')?> *</label>
        <select required name="acfcs_state" id="acfcs_state" class="input select2 acfcs__dropdown--states">
            <option value="">
                <?php echo apply_filters( 'acfcs_select_province_state_label', esc_html__( 'Select a province/state', 'acf-city-selector' ) ); ?>
            </option>
        </select>
    </p>
    <p class="contest-nationality left-content">
        <label for="nationality"><?php echo __('Nationality','bic')?> *</label>
        <input required type="text" name="nationality" id="nationality" autocomplete="nationality" class="input" value="<?php echo $_POST['nationality']; ?>">
    </p>
    <div class="contest-upload-img-title">
        <div class="contest-img-title col-6-left left-content text-top">
            <h3 class=""><?php echo __('Upload Your Art images *','bic')?></h3>
        </div>
        <div class="contest-img-hint col-6-right right-content">
            <h4><?php echo __('At Least upload one image','bic')?></h4>
            <p class="text-black"><?php echo __('(This includes A3,in jpg or/png)& maximum size 10MB','bic')?></p>
        </div>
    </div>
<!--    <p class="contest-image-title col-6-left left-content">-->
<!--        <label for="title">--><?php //echo __('Add Title','bic')?><!--</label>-->
<!--        <input required type="text" name="title" id="title" autocomplete="title" class="input" value="">-->
<!--    </p>-->
<!--    <p class="contest-image-description col-6-right left-content">-->
<!--        <label for="description">--><?php //echo __('Add Description','bic')?><!--</label>-->
<!--        <input required type="text" name="description" id="description" autocomplete="description" class="input" value="">-->
<!--    </p>-->
    <div class="contest-img-upload-container" id="image-upload">
        <?php for($i=0;$i<3;$i++){?>
            <div class="col-4<?php echo $i==2?" last":""; ?>">
                <div class="upload-img">
                    <input type="text" name="<?php echo "title[$i]"; ?>" id="<?php echo "img-title-$i"; ?>" class="filename input mb-10 dark-border" value="" placeholder="<?php echo __('Add Title','bic')?>">
                    <div class="add-container dark-border" data-file="<?php echo "imgs-file-$i"; ?>">
                        <div class="circle" id="<?php echo "img-circle-$i"; ?>">+</div>
                    </div>
                    <div class="img-container hidden" >
                        <img src="" class="full-width" id="<?php echo "img-$i"; ?>">
                        <button type="button" class="bic-menu-btn" data-overlay="<?php echo "overlay-$i"?>">...</button>
                        <div class="overlay" id="<?php echo "overlay-$i"?>">
                            <div class="img-action edit-img"   data-file="imgs-file-<?php echo $i ?>"><?php echo __('Edit','bic')?></div>
                            <div class="img-action delete-img" data-file="imgs-file-<?php echo $i ?>"><?php echo __('Delete','bic')?></div>
                        </div>
                    </div>
                </div>
                <input type="file" accept="image/png, image/jpeg" name="<?php echo "imgs[$i]"; ?>" data-img="<?php echo "img-$i"; ?>"
                       data-circle="<?php echo "img-circle-$i"; ?>"
                       id="<?php echo "imgs-file-$i"; ?>" class="file-uploader hidden" value="">
                <div class="error <?php echo isset($_GET['entry_error'])&&$_GET['entry_error']=="img_{$i}_size"?"":"hidden"?> size-error"    id="<?php echo "img-error-$i-size"; ?>"><?php echo __('Image exceeded the 10MB allowed','bic')?></div>
                <div class="error <?php echo isset($_GET['entry_error'])&&$_GET['entry_error']=="img_{$i}_filename"?"":"hidden"?> type-error" id="<?php echo "img-error-$i-filename"; ?>"><?php echo __('Image Type Not allowed,Allowed type is (jpg,png)','bic')?></div>
            </div>
        <?php } ?>
    </div>

    <div class="contest-terms-and-condition-container  left-content px-25">
        <h3 class=""><?php echo __('Terms & Conditions *','bic')?></h3>
        <?php foreach ($all_terms as $i=>$term){ ?>
            <div class="col-6-<?php echo $i % 2 == 0?"left":"right" ?>">
               <div class="contest-term ">
                    <span class="terms-icon"><img src="<?php echo $term['icon']?>"></span>
                    <span class="terms-text text-black"><?php echo $term['text']; ?></span>
                </div>
            </div>
        <?php } ?>
    </div>
    <p class="contest-consent left-content">
        <input required type="checkbox" name="consent" id="consent" autocomplete="consent" class="input width-25 mr-25" value="">
        <label for="consent"><?php echo __('I Agree to the Terms and Conditions','bic')?> *</label>
    </p>
    <p class="contest-submit">
        <input type="hidden" name="current_url" value="<?php echo $current_url; ?>">
        <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary" value="<?php echo __('SUBMIT','bic')?>">
    </p>
</form>