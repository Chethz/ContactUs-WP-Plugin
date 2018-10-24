<?php
/**
 * Created by PhpStorm.
 * User: cheth
 * Date: 24/10/18
 * Time: 4:45 PM
 */

/*
 Plugin Name: Contact Us
 Description: This is my first custom plugin, This plugin can save validated data in to a WP database.
 Version: 1.0.0
 Author: Cheth
 License: GPLv2 or later
 Text Domain: Contact Us
 */

defined('ABSPATH') or die('Hey, Stay away!');

class Contact_Us{

    /** *********** Default functions ************* **/
    function activate(){
        $this->Create_DB_Table();
        flush_rewrite_rules();
    }

    function deactivate(){
        flush_rewrite_rules();
    }

    function uninstall(){

    }

    private function Plugin_Run(){

    }

    /** *********** Front End handling functions ************* **/
    public static function Create_HTML_Form(){
        echo '<p><h1>Please fill up the form below!</h1></p>';
        echo '
        <form action="' . get_permalink() . '" method="post">
            <div>
                <label>First Name</label>
                <input type="text" name="First_Name" /><br>
            </div>
            <div>
                <label>Last Name</label>
                <input type="text" name="Last_Name" /><br>
            </div>
            <div>
                <label>Email</label>
                <input type="text" name="Email" /><br>
            </div>
            <div>
                <input type="submit" name="Submit" value="send" /> <br>
            </div>
        </form>
        ';
    }

    private function Validate_Data($firstname, $lastname, $email){
        global $form_error;
        $form_error = new WP_Error();

        if (empty($firstname) || empty($lastname) || empty($email)){
            $form_error -> add('field', 'Fields shouldn\'t be empty!');
        }

        if(filter_var($firstname, FILTER_VALIDATE_REGEXP, array("options" => array("regexp"=>"/^[a-zA-Z\s]+$/")))){
            $form_error -> add('Invalid_FirstName', 'Invalid First Name Entered!');
        }

        if(filter_var($lastname, FILTER_VALIDATE_REGEXP, array("options" => array("regexp"=>"/^[a-zA-Z\s]+$/")))){
            $form_error -> add('Invalid_LastName', 'Invalid Last Name Entered!');
        }

        if (filter_var($email,FILTER_VALIDATE_EMAIL)){
            $form_error -> add('Invalid_Email','Invalid E-Mail Entered!');
        }

        if (is_wp_error($form_error)){
            foreach ($form_error -> get_error_messages() as $error) {
                echo '<div>';
                echo '<strong>ERROR</strong>';
                echo $error. '<br/>';
                echo '</div>';
            }
        }
    }

    /** *********** Database handling functions ************* **/
    private function Create_DB_Table(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'contact_form';

        if ($wpdb->get_var('SHOW TABLE LIKE' . $table_name) != $table_name) {
            $sql = 'CREATE TABLE ' . $table_name . '(
                id int(5) NOT NULL AUTO_INCREMENT,
                first_name text NOT NULL,
                last_name text NOT NULL,
                email text NOT NULL,
                PRIMARY KEY (id)
            )';

            require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    private function Save_Input_data(){

    }

    private function Search_Duplicate_Data(){

    }
}

if (class_exists('Contact_Us')){
    $contact_us_plugin = new Contact_Us();
    add_shortcode('Contact_Us_Form',array('Contact_Us','Create_HTML_Form'));
}


//activation
register_activation_hook(__FILE__,array($contact_us_plugin, 'activate'));

//deactivation
register_deactivation_hook(__FILE__, array($contact_us_plugin,'deactivate'));

//uninstall