<?php
class Bic_List_Effected_Artists {
    public static function list_effected_artists(){
        global $wpdb;
        $table_name = $wpdb->usermeta;

        $sql = "SELECT * 
                FROM $table_name 
                where meta_key = '_attachments_2024'";
        $results = $wpdb->get_results($sql,ARRAY_A);
//        $file_path  = BIC_PLUGIN_PATH.'effected_users.csv';
//        $file = fopen($file_path, 'a'); // 'a' mode appends to the file
        foreach ($results as $entryData){
            $entries = unserialize($entryData['meta_value']);
            foreach ($entries as $post_id){
                if(!empty($post_id)&!get_post($post_id) ) {
                    $user=get_user_by('ID',$entryData['user_id']);
                    $line = "$user->ID,$user->user_email,$post_id<br>";
                    echo $line;
//                    fwrite($file, $line);
                }
            }
        }
        return "done";
    }
}