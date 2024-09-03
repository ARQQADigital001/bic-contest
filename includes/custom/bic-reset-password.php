<?php

class Bic_Reset_Password
{
	public function __construct(){
		add_filter('lostpassword_url'                 , array($this, 'change_url')           , 99,2);
		add_filter('somfrp_retrieve_password_title'   , array($this, 'change_email_title')   , 1,3);
		add_filter('somfrp_retrieve_password_message' , array($this, 'change_email_message') , 1,4);
	}
	public function change_url($lostpassword_url, $redirect){
		return som_get_lost_password_url()?:$lostpassword_url;
	}
	public function change_email_title($title, $user_login, $user_data ){
		return __('Reset your BIC Art Master Account Password','bic');
	}
	public function change_email_message($message, $key, $user_login, $user_data ){
		$reset_url = esc_url_raw(
			add_query_arg(
				array(
					'somresetpass' => 'true',
					'somfrp_action' => 'rp',
					'key' => $key,
					'uid' => $user_data->ID
				),
				som_get_lost_password_url()
			)
		);
		$current_lang=get_user_locale($user_data)?:"en";
		ob_start();
		$banner = BIC_PLUGIN_URL."assets/images/email-banner-$current_lang.png";
		include BIC_PLUGIN_PATH."templates/bic-reset-email.php";
		return ob_get_clean();
	}
}