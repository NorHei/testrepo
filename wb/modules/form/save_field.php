<?php
/**
 *
 * @category        module
 * @package         Form
 * @author          Ryan Djurovich, WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: save_field.php 1919 2013-06-07 04:21:49Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/modules/form/save_field.php $
 * @lastmodified    $Date: 2013-06-07 06:21:49 +0200 (Fr, 07. Jun 2013) $
 * @description
 */

require( dirname(dirname((__DIR__))).'/config.php' );

// suppress to print the header, so no new FTAN will be set
$admin_header = false;
// Tells script to update when this page was last updated
$update_when_modified = true;
// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');
/* */

$sSectionIdPrefix = (defined( 'SEC_ANCHOR' ) && ( SEC_ANCHOR != '' )  ? SEC_ANCHOR : 'Sec' );

// check FTAN
if (!$admin->checkFTAN())
{
    $admin->print_header();
    $admin->print_error('::'.$MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id.'#'.$sSectionIdPrefix.$section_id);
}

// Get id
$field_id = intval($admin->checkIDKEY('field_id', false ));
if (!$field_id) {
 $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'].'::', ADMIN_URL.'/pages/modify.php?page_id='.$page_id.'#'.$sSectionIdPrefix.$section_id);
}
// After check print the header to get a new FTAN
$admin->print_header();

/*
// Get id
if(!isset($_POST['field_id']) OR !is_numeric($_POST['field_id'])) {
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
} else {
    $field_id = (int)$_POST['field_id'];
}
/*
// Include WB admin wrapper script
$update_when_modified = true; // Tells script to update when this page was last updated

if (!$admin->checkFTAN())
{
    $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
    exit();
}
*/
// Validate all fields
if($admin->get_post('title') == '' OR $admin->get_post('type') == '') {
    $admin->print_error($MESSAGE['GENERIC']['FILL_IN_ALL'], WB_URL.'/modules/form/modify_field.php?page_id='.$page_id.'&section_id='.$section_id.'&field_id='.$admin->getIDKEY($field_id));
} else {
    $title = str_replace(array("[[", "]]"), '', htmlspecialchars($admin->get_post_escaped('title'), ENT_QUOTES));
    $type = $admin->add_slashes($admin->get_post('type'));
    $required = (int) $admin->add_slashes($admin->get_post('required'));
}
$value = '';

// Update row
$sql  = 'UPDATE `'.TABLE_PREFIX.'mod_form_fields SET` ';
$sql .= 'title = \''.$title.'\', ';
$sql .= 'type = \''.$type.'\', ';
$sql .= 'required = \''.$required.'\' ';
$sql .= 'WHERE field_id = '.(int)$field_id.' ';
if($database->query($sql)) { }

// If field type has multiple options, get all values and implode them
$list_count = $admin->get_post('list_count');
if(is_numeric($list_count)) {
    $values = array();
    for($i = 1; $i <= $list_count; $i++) {
        if($admin->get_post('value'.$i) != '') {
            $values[] = str_replace(",","&#44;",$admin->get_post('value'.$i));
        }
    }
    $value = implode(',', $values);
}

// Get extra fields for field-type-specific settings
if($admin->get_post('type') == 'textfield') {
    $length = $admin->get_post_escaped('length');
    $value = str_replace(array("[[", "]]"), '', $admin->get_post_escaped('value'));
    $database->query("UPDATE ".TABLE_PREFIX."mod_form_fields SET value = '$value', extra = '$length' WHERE field_id = '$field_id'");
} elseif($admin->get_post('type') == 'textarea') {
    $value = str_replace(array("[[", "]]"), '', $admin->get_post_escaped('value'));
    $database->query("UPDATE ".TABLE_PREFIX."mod_form_fields SET value = '$value', extra = '' WHERE field_id = '$field_id'");
} elseif($admin->get_post('type') == 'heading') {
    $extra = str_replace(array("[[", "]]"), '', $admin->get_post('template'));
    if(trim($extra) == '') $extra = '<tr><td class="frm-field_heading" colspan="2">{TITLE}{FIELD}</td></tr>';
    $extra = $admin->add_slashes($extra);
    $database->query("UPDATE ".TABLE_PREFIX."mod_form_fields SET value = '', extra = '$extra' WHERE field_id = '$field_id'");
} elseif($admin->get_post('type') == 'select') {
    $extra = intval($admin->get_post_escaped('size')).','.$admin->get_post_escaped('multiselect');
    $database->query("UPDATE ".TABLE_PREFIX."mod_form_fields SET value = '$value', extra = '$extra' WHERE field_id = '$field_id'");
} elseif($admin->get_post('type') == 'checkbox') {
    $extra = str_replace(array("[[", "]]"), '', $admin->get_post_escaped('seperator'));
    $database->query("UPDATE ".TABLE_PREFIX."mod_form_fields SET value = '$value', extra = '$extra' WHERE field_id = '$field_id'");
} elseif($admin->get_post('type') == 'radio') {
    $extra = str_replace(array("[[", "]]"), '', $admin->get_post_escaped('seperator'));
    $database->query("UPDATE ".TABLE_PREFIX."mod_form_fields SET value = '$value', extra = '$extra' WHERE field_id = '$field_id'");
}

// Check if there is a db error, otherwise say successful
if($database->is_error()) {
    $admin->print_error($database->get_error(), WB_URL.'/modules/form/modify_field.php?page_id='.$page_id.'&section_id='.$section_id.'&field_id='.$admin->getIDKEY($field_id));
} else {
    $admin->print_success($TEXT['SUCCESS'],     WB_URL.'/modules/form/modify_field.php?page_id='.$page_id.'&section_id='.$section_id.'&field_id='.$admin->getIDKEY($field_id));
}

// Print admin footer
$admin->print_footer();
