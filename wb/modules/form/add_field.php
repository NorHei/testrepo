<?php
/**
 *
 * @category        module
 * @package         Form
 * @author          WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id:  $
 * @filesource      $HeadURL:  $
 * @lastmodified    $Date:  $
 * @description
 */
// Include config file
$config_file = dirname(dirname((__DIR__))).'/config.php';
if(file_exists($config_file) && !defined('WB_URL')) { require($config_file); }

// Include WB admin wrapper script
require(WB_PATH.'/modules/admin.php');

$sBacklink = ADMIN_URL.'/pages/modify.php?page_id='.$page_id;
if (!$admin->checkFTAN( $_SERVER["REQUEST_METHOD"] ))
{
//    $admin->print_header();
    $admin->print_error('FTAN:: '.$MESSAGE['GENERIC_SECURITY_ACCESS'], $sBacklink);
}
$aFtan = $admin->getFTAN('');

// Include the ordering class
require(WB_PATH.'/framework/class.order.php');
// Get new order
$order = new order(TABLE_PREFIX.'mod_form_fields', 'position', 'field_id', 'section_id');
$position = $order->get_new($section_id);
$field_id = 0;
try {
// Insert new row into database
 $sql = 'INSERT INTO `'.TABLE_PREFIX.'mod_form_fields` SET '
      . '`section_id` = '.$section_id.', '
      . '`page_id` = '.$page_id.', '
      . '`position` = '.$position.', '
      . '`title` = \'\', '
      . '`type` = \'\', '
      . '`required` = 0, '
      . '`value` = \'\', '
      . '`extra` = \'\' ';
    if(!$database->query($sql)) {
        $admin->print_error($database->get_error(), ADMIN_URL.'/pages/modify.php?page_id='.$page_id );
    }
    $field_id = $database->getLastInsertId();
} catch(ErrorMsgException $e) {
    $admin->print_error($database->get_error(), WB_URL.'/modules/form/modify_field.php?page_id='.$page_id.'&section_id='.$section_id.'&field_id='.$admin->getIDKEY($field_id));
}

$admin->print_success($TEXT['SUCCESS'], WB_URL.'/modules/form/modify_field.php?page_id='.$page_id.'&section_id='.$section_id.'&field_id='.$admin->getIDKEY($field_id));
// Print admin footer
$admin->print_footer();
