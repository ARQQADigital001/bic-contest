<?php

class Bic_common
{
    public static function acfcs_get_state_data( $_code) {
        global $wpdb;
        $state_code = explode('-',$_code);
        $table   = $wpdb->prefix . 'cities';
        $query   = $wpdb->prepare( "SELECT * FROM $table WHERE country_code = %s and state_code = %s", $state_code[0],$state_code[1] );
        return $wpdb->get_row( $query );

    }
}