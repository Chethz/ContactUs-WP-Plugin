<?php
/**
 * Created by PhpStorm.
 * User: cheth
 * Date: 31/10/18
 * Time: 3:14 PM
 */

defined('ABSPATH') or die('Hey, Stay away!');

class Contatct_Us_Table{function register_custom_menu_page() {
    add_menu_page('custom menu title', 'Search Contacts', 'manage_options', 'contact_us-admin-menu', array($this,'Search_Contact_Details'), 'dashicons-search', 30);
}

    public function Search_Contact_Details(){

        global $wpdb;
        $table_name = $wpdb->prefix . 'contact_form';
        $results = $wpdb->get_results("SELECT * FROM $table_name");
        ?>
        <div class="wrap">
            <h2>Customer Data</h2>
        </div>
        <?php

        if (!empty($results)){
            echo '<table border="1">';
            echo '<tr>';
            echo '    <th style="width:20%;">First Name</th>';
            echo '    <th style="width:20%;">Last Name</th>';
            echo '   <th style="width:40%;">Email</th>';
            echo '</tr>';
            foreach($results as $row){
                echo '<tr>';
                echo "<td>".$row->first_name."</td>";
                echo "<td>".$row->last_name."</td>";
                echo "<td>".$row->email."</td>";
                echo '<tr>';
            }
            echo '</table>';
        }else{
            ?>
            <div class="wrap">
                <h1>No record found!</h1>
            </div>
            <?php
        }
    }
}