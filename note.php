<?php
/**
 * @package Admin Ajax Note
 * @author Daniel Straub
 * @version 1.1.2
 */
/*
Plugin Name: Admin Ajax Note
Plugin URI: http://www.katzenhirn.com/projekte/admin-ajax-note/
Description: A lot of work to do? Organize with Admin Ajax Note.
Author: Daniel Straub
Version: 1.1.3
Author URI: http://www.katzenhirn.com
*/

// Wegen AJAX nur ab Version 2.8.3

/* ####################################### */
// Activation and Deactivation
/* ####################################### */

register_activation_hook(__FILE__, 'sd_admin_note_activation');
register_deactivation_hook(__FILE__, 'sd_admin_note_deactivation');

function sd_admin_note_activation()
{
global $wpdb;
$sd_install_note_sql = "CREATE TABLE `{$wpdb->prefix}sd_ajax_admin_notes` (`id` INT(20) NOT NULL AUTO_INCREMENT, `head` TEXT NOT NULL, `content` LONGTEXT NOT NULL, `user_id` BIGINT(20) NOT NULL, `share_id` BIGINT(20) NOT NULL, PRIMARY KEY (`id`)) ENGINE = MyISAM;"; 
$wpdb->query($sd_install_note_sql);
global $user_ID;
$info1 = base64_decode("QWJvdXQgdGhpcyBQbHVnaW4=");
$info2 = base64_decode("VGhpcyBwbHVnaW4gaXMgd3JpdHRlbiBieSBEYW5pZWwgU3RyYXViLiBWaXNpdCBteSBCbG9nIGF0IHd3dy5rYXR6ZW5oaXJuLmNvbSBvciBmb2xsb3cgbWUgb24gVHdpdHRlcjogd3d3LnR3aXR0ZXIuY29tL0Q0TjEzTC4gSWYgeW91IGZpbmQgYSBidWcgbGV0IG1lIGtub3cu");
$wpdb->query( $wpdb->prepare ("INSERT INTO {$wpdb->prefix}sd_ajax_admin_notes (head, content, user_id) VALUES ('%s', '%s', '%d')", $info1, $info2, $user_ID));

}

function sd_admin_note_deactivation()
{
global $wpdb;
$sd_uninstall_note_sql = "DROP TABLE `{$wpdb->prefix}sd_ajax_admin_notes`";
$wpdb->query($sd_uninstall_note_sql);
}

/* ####################################### */
// Activation and Deactivation Ende
/* ####################################### */


/* ####################################### */
// Globale Variablen + Textdomain
/* ####################################### */

// Globale Variablen
$admin_note_plugin_path = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
$admin_note_plugin_path_lang = dirname( plugin_basename( __FILE__ ) ) ."/";
//load_textdomain('sd_admin_note', $admin_note_plugin_path);
load_plugin_textdomain( 'sd_admin_note',false, $admin_note_plugin_path_lang);

$sd_admin_note_sharing = sd_admin_note_sharing();

/* ####################################### */
// Globale Variablen + Textdomain Ende
/* ####################################### */

function sd_admin_note()
{
global $user_ID;
$sd_get_note_data = sd_admin_notes_get_note();



foreach ($sd_get_note_data as $sd_get_note_data) {

$admin_note_author = get_userdata($sd_get_note_data->user_id);
$admin_note_author_name = $admin_note_author->user_login;

if($sd_get_note_data->share_id == "-1")
{
    $admin_note_share_name = "all";
}
else
{
    $admin_note_share = get_userdata($sd_get_note_data->share_id);
    $admin_note_share_name = $admin_note_share->user_login;
}
$admin_note_share = get_userdata($sd_get_note_data->share_id);

?>



<div id='sd_note_<?php echo $sd_get_note_data->id; ?>' title='<?php echo $sd_get_note_data->head; ?>' class='sd_admin_note'>
<?php echo $sd_get_note_data->content; ?> <br>
<?php if($sd_get_note_data->user_id == $user_ID)
{ ?>
<div class="admin_note_edit_button" onclick="load_edit_admin_note('<?php echo $sd_get_note_data->id; ?>')"></div>
	<?php
		if($sd_get_note_data->share_id != '0')
		{
		?>
		<span class="admin_note_info_button"><?php echo sprintf(__('This Note is visible for %s', 'sd_admin_note'), $admin_note_share_name); ?></span>
		<?php
		}
        else
        {
        ?>
            <span class="admin_note_info_button"><?php echo __('This Note ist private. Nobody can watch this', 'sd_admin_note'); ?> </span>
        <?php
        }
	    ?>
<?php
}
else
{
?>
<span class="admin_note_info_button"><?php echo sprintf(__('This Note is written by %s and visible for %s', 'sd_admin_note'), $admin_note_author_name, $admin_note_share_name); ?></span>
<?php } ?>
</div>



<?php
	}
}




function load_aan_script_and_style() {
global $admin_note_plugin_path;
$jquery_aan_cssui_style = $admin_note_plugin_path."jquery-css/jquery-ui.custom.css";
$aan_default_style = $admin_note_plugin_path."note_style.css";
$aan_default_script = $admin_note_plugin_path."note_script.js";
    wp_enqueue_script('jquery');  
    wp_enqueue_script('jquery_ui_core');
    wp_enqueue_script('jquery-ui-draggable');
    wp_enqueue_script('jquery-ui-resizable');  
    wp_enqueue_script('jquery-ui-dialog');
    wp_enqueue_script('aan-default-script', $aan_default_script);
    wp_enqueue_style('aan-cssui', $jquery_aan_cssui_style);
    wp_enqueue_style('aan-default-style', $aan_default_style);
}    

function sd_admin_note_mini()
{
global $user_ID;
global $sd_admin_note_sharing;
?>

<div id='new_node_dummy'></div>
<div id='new_admin_note' title="<?php echo __('New Note', 'sd_admin_note'); ?>">
	
	<div id='note_info'>
		<input type='text' name='new_admin_note_head' id='new_admin_note_head'>
	</div>
	
	<div id='note_content'>
		<textarea id='new_admin_textarea' rows='6' cols='25'><?php echo $sd_admin_note_sharing; ?></textarea><br>
	<input type='button' name='save' value='<?php echo __('Save', 'sd_admin_note'); ?>' onclick='add_new_admin_note()'>
	<?php
    if($sd_admin_note_sharing == true) {
	    $trans_visible1 = __('Visible for..', 'sd_admin_note');
	    $trans_visible2 = __('Visible for all', 'sd_admin_note');
	    wp_dropdown_users( array('show_option_all' => $trans_visible1, 'show_option_none' => $trans_visible2, 'exclude' => $user_ID, 'name' => 'new_admin_note_share'));
    }
    else { ?>
        <input type="hidden" id="new_admin_note_share" value="0">
    <?php } ?>
	<span class='admin_note_ajax_loader'></span>
	</div>
</div>


<div id="edit_admin_note" title="<?php echo __('Edit Note', 'sd_admin_note'); ?>">
	<div id="note_info">
		<input type="text" name="edit_admin_note_head" id="edit_admin_note_head">
	</div>
	
	<div id="note_content">
		<textarea id="edit_admin_note_textarea" row="6" cols="25"></textarea><br>
		<input type="button" name="edit" value="<?php echo __('Save', 'sd_admin_note'); ?>" onclick="edit_admin_note()"><input type="button" name="delete" value="<?php echo __('Delete', 'sd_admin_note'); ?>" onclick="delete_admin_note()">
			<?php
	if($sd_admin_note_sharing == true) {
		$trans_visible1 = __('Visible for nobody', 'sd_admin_note');
		$trans_visible2 = __('Visible for all', 'sd_admin_note');
		wp_dropdown_users( array('show_option_all' => $trans_visible1, 'show_option_none' => $trans_visible2, 'exclude' => $user_ID, 'name' => 'edit_admin_note_share')); 
		}
	else
		{ ?>
		<input type="hidden" id="edit_admin_note_share" value="0">
    <?php } ?>
		<span class="admin_note_ajax_loader"></span>
		<input type="hidden" id="edit_admin_note_id" value="">
	</div>
</div>

<div id="delete_admin_note_sure" title="<?php echo __('Are you Sure', 'sd_admin_note'); ?>">
<?php echo __('Do you realy want to delete this Note?', 'sd_admin_note'); ?> <br />
<input type="button" name="delete_yes" value="<?php echo __('Yes, im Sure', 'sd_admin_note'); ?>" onclick="delete_admin_note_yes()"><input type="button" name="delete_no" value="<?php echo __('No, maybe later', 'sd_admin_note'); ?>" onclick="delete_admin_note_no()">
</div>

<div class='note_admin_mini_menu'>
	<div class='note_mini' onclick='show_note_mini_menu()'></div>
	<div class='note_mini_menu'>
		<ul class='note_mini_menu_content'>
			<li><a class="note_mini_menu_new" href="javascript:new_admin_note();"><?php echo __('New Node', 'sd_admin_note'); ?></a></li>
<?php
	
$sd_get_menu_data = sd_admin_notes_get_menu();

foreach ($sd_get_menu_data as $sd_get_menu_data) {


?>
			<li><a id="note_mini_menu_<?php echo $sd_get_menu_data->id; ?>" class="note_mini_menu_note" href="javascript:sd_show_notes('<?php echo $sd_get_menu_data->id; ?>');"><?php echo $sd_get_menu_data->head; ?></a></li>
<?php
}
?>
<span id='new_note_menu_dummy'></span>
		</ul>
	</div>
</div>

<?php	
}

/* ####################################### */
// Ajax
/* ####################################### */


function sd_admin_notes_ajax_script() {
	
	$script = $_POST['script'];
	
	switch ($script) {
    case "new_note":
    
    $head = $_POST['head'];
    $content = $_POST['content'];
    $share = $_POST['share'];
    
    sd_admin_note_sql_new_note($head, $content, $share);
    die();
    
    //Function
    break;
    case "edit_note":

    
    $id = $_POST['id'];
    $head = $_POST['head'];
    $content = $_POST['content'];
    $share = $_POST['share'];

    if(sd_admin_note_sec($id))
    {
    
	    sd_admin_note_sql_update_note($id, $head, $content, $share);
    }
    die();
        
	break;
	case "delete_note":

	$id = $_POST['id'];

    if(sd_admin_note_sec($id))
    {
	    sd_admin_note_sql_delete_note($id);
    }
    break;
    case "get_note":

    $id = $_POST['id'];
    $row = $_POST['row'];

        if(sd_admin_note_sec($id))
        {
            sd_admin_note_get_note($id, $row);
        }

    die();

    }


}
/* ####################################### */
// Ajax Ende
/* ####################################### */


/* ####################################### */
// Datenbankquerys
/* ####################################### */

function sd_admin_notes_get_menu()
{
global $wpdb;
global $user_ID;

$sd_get_menu_sql = "SELECT id, head ";
$sd_get_menu_sql .= "FROM {$wpdb->prefix}sd_ajax_admin_notes ";
$sd_get_menu_sql .= "WHERE user_id = '$user_ID' ";
$sd_get_menu_sql .= "OR share_id = '-1' ";
$sd_get_menu_sql .= "OR share_id = '$user_ID' ";

return $wpdb->get_results($sd_get_menu_sql);

}

function sd_admin_notes_get_note()
{
global $wpdb;
global $user_ID;

$sd_get_note_sql = "SELECT id, head, content, user_id, share_id "; 
$sd_get_note_sql .= "FROM {$wpdb->prefix}sd_ajax_admin_notes ";
$sd_get_note_sql .= "WHERE user_id = '$user_ID' ";
$sd_get_note_sql .= "OR share_id = '-1' ";
$sd_get_note_sql .= "OR share_id = '$user_ID'";

return $wpdb->get_results($sd_get_note_sql);
}


function sd_admin_note_sql_new_note($head, $content, $share)
{
global $wpdb;
global $user_ID;


$wpdb->query( $wpdb->prepare ("INSERT INTO {$wpdb->prefix}sd_ajax_admin_notes (head, content, user_id, share_id) VALUES ('%s', '%s', '%d', '%d')", $head, $content, $user_ID, $share));
$new_id = mysql_insert_id();
echo $new_id;

}

function sd_admin_note_sql_update_note($id, $head, $content, $share)
{
global $wpdb;
global $user_ID;


$wpdb->query( $wpdb->prepare ("UPDATE {$wpdb->prefix}sd_ajax_admin_notes SET head = '%s', content = '%s', share_id = '%d' WHERE id = '%d' AND user_id = '$user_ID'", $head, $content, $share, $id));
}

function sd_admin_note_sql_delete_note($id)
{
global $wpdb;
global $user_ID;
$wpdb->query( $wpdb->prepare ("DELETE FROM {$wpdb->prefix}sd_ajax_admin_notes WHERE id = '%d' AND user_id = '%d'", $id, $user_ID));

}

function sd_admin_note_sec($id)
{
    global $wpdb;
    global $user_ID;

    $sd_admin_note_edit_note_sec_id = $wpdb->get_results($wpdb->prepare("SELECT user_id FROM {$wpdb->prefix}sd_ajax_admin_notes WHERE id = '%d'", $id));
    foreach ($sd_admin_note_edit_note_sec_id as $sd_admin_note_edit_note_sec_id)
    {
        if($sd_admin_note_edit_note_sec_id->user_id == $user_ID)
        {
        return true;
        }
        else
        {
        return false;
        }
    }

}

function sd_admin_note_sharing()
{
    global $wpdb;
    global $blog_id;
    $count_user = $wpdb->get_var("SELECT COUNT(umeta_id) FROM $wpdb->usermeta WHERE meta_key = 'primary_blog' AND meta_value = '$blog_id'");

    if($count_user == 0) {
        $count_user = $wpdb->get_var("SELECT COUNT(id) FROM $wpdb->users");
    }

    if($count_user > 1) {

        return true;

    }
    else {
        return false;
    }
    
}

function sd_admin_note_get_note($id, $row) {
    global $wpdb;
    $query = "SELECT $row FROM {$wpdb->prefix}sd_ajax_admin_notes WHERE id = '$id'";
    $data = $wpdb->get_var($query);
    echo stripslashes($data);
}

/* ####################################### */
// Datenbankquerys Ende
/* ####################################### */


/* ####################################### */
// Actions + Filters
/* ####################################### */

add_action('wp_ajax_admin_notes', 'sd_admin_notes_ajax_script');
add_action('admin_notices', 'sd_admin_note_mini');
add_action('admin_footer', 'sd_admin_note');
if(is_admin()) {
add_action('init', 'load_aan_script_and_style');
}

/* ####################################### */
// Actions + Filters Ende
/* ####################################### */


// Vielen Dank fŸr die Nutzung meines Plugins
// Mehr von mir auf katzenhirn.com
// oder auf twitter.com/D4N13L
// Freue mich auf eure Bugs, VerbesserungsvorschlŠge und Feedback

?>