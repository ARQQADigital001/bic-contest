<?php
class Bic_init
{
    public function __construct()
    {
        //include all files
        $this->includes();
        $this->register_actions();

    }
    public function includes()
    {
        $include_arr =  array_merge(
	        glob(BIC_PLUGIN_PATH . "includes/helpers/*.php"),
	        glob(BIC_PLUGIN_PATH . "includes/custom/*.php"),
        );
        foreach ($include_arr as $file) {
            require_once($file);
        }
    }

    private function register_actions()
    {
        new Bic_Custom_Post_type();
        new Bic_Entry_Custom_Short_codes();
        new Bic_Dashboard_Custom_Short_codes();
        new Bic_Gallery_Custom_Short_codes();
        new Bic_Export_Media();
        new Bic_Search_Media();
        new Bic_Reset_Password();
    }

}
new Bic_init();