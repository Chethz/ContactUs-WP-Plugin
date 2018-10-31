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

    /** *********** Main function ************* **/
    private function Plugin_Run(){
        global $form_error;

        $firstname = filter_var(trim($_POST['First_Name']), FILTER_SANITIZE_STRING);
        $lastname = filter_var(trim($_POST['Last_Name']),FILTER_SANITIZE_STRING);
        $email = filter_var(trim($_POST['Email']), FILTER_SANITIZE_EMAIL);

        self::Validate_Data($firstname,$lastname,$email);
        if ( 1 > count( $form_error->get_error_messages() ) ) {
            if (self::Search_Duplicate_Data($firstname, $lastname, $email) == true) {
                self::Save_Input_data($firstname, $lastname, $email);
            }
        }

    }

    /** *********** Front end handling functions ************* **/
    public function Create_HTML_Form(){
        echo '<p><h1>Please fill up the form below!</h1></p>';
        echo '
        <form method="post">
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

        if (isset($_POST['Submit'])){
            self::Plugin_Run();
        }
    }

    private function Validate_Data($first_name, $last_name, $e_mail){
        global $form_error;
        $form_error = new WP_Error();

        if (empty($first_name) || empty($last_name) || empty($e_mail)){
            $form_error -> add('field', 'Fields should not be empty!');
        }

        if(!filter_var($first_name, FILTER_VALIDATE_REGEXP, array("options" => array("regexp"=>"/^[a-zA-Z\s]+$/")))){
            $form_error -> add('Invalid_FirstName', 'Invalid First Name Entered!');
        }

        if(!filter_var($last_name, FILTER_VALIDATE_REGEXP, array("options" => array("regexp"=>"/^[a-zA-Z\s]+$/")))){
            $form_error -> add('Invalid_LastName', 'Invalid Last Name Entered!');
        }

        if (!filter_var($e_mail,FILTER_VALIDATE_EMAIL)){
            $form_error -> add('Invalid_Email','Invalid E-Mail Entered!');
        }

        if (is_wp_error($form_error)){
            foreach ($form_error -> get_error_messages() as $error) {
                echo '<div>';
                echo '<strong>ERROR : </strong>';
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

    private function Save_Input_data($firstname, $lastname, $email){
        global $wpdb;
        $tablename = $wpdb->prefix . 'contact_form';

        $wpdb->insert($tablename, array(
                'first_name' => $firstname,
                'last_name' => $lastname,
                'email' => $email)
        );
    }

    private function Search_Duplicate_Data($fname, $lname, $email){
        global $wpdb;
        global $form_error;
        $form_error = new WP_Error();

        $table_name = $wpdb->prefix . 'contact_form';
        $firstname = $fname;
        $lastname = $lname;
        $email = $email;

        $result = $wpdb->get_results("SELECT * FROM $table_name WHERE first_name = '".$firstname."' AND last_name = '".$lastname."' AND email = '".$email."'");

        if (count($result) > 0){
            $form_error->add('Record exists', 'Record already exist!');
            return false;
        }else{
            return true;
        }
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