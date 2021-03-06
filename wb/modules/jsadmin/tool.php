<?php
/**
 *
 * @category        modules
 * @package         JsAdmin
 * @author          WebsiteBaker Project, modified by Swen Uth for Website Baker 2.7
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: tool.php 1537 2011-12-10 11:04:33Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/jsadmin/tool.php $
 * @lastmodified    $Date: 2011-12-10 12:04:33 +0100 (Sa, 10. Dez 2011) $
 *
*/

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Illegale file access /'.basename(__DIR__).'/'.basename(__FILE__).''); }
/* -------------------------------------------------------- */

// check if module language file exists for the language set by the user (e.g. DE, EN)
$sAddonName = basename(__DIR__);
require(WB_PATH .'/modules/'.$sAddonName.'/languages/EN.php');
if(file_exists(WB_PATH .'/modules/'.$sAddonName.'/languages/'.LANGUAGE .'.php')) {
    require(WB_PATH .'/modules/'.$sAddonName.'/languages/'.LANGUAGE .'.php');
}
$sModulName = basename(__DIR__);
$js_back = ADMIN_URL.'/admintools/tool.php';
$ToolUrl = ADMIN_URL.'/admintools/tool.php?tool=jsadmin';
if( !$admin->get_permission($sModulName,'module' ) ) {
    $admin->print_error($MESSAGE['ADMIN_INSUFFICIENT_PRIVELLIGES'], $js_back);
}
if( !function_exists( 'get_setting' ) )  {  require(WB_PATH.'/modules/'.basename(__DIR__).'/jsadmin.php');  }

// Check if user selected what add-ons to reload
if(isset($_POST['save_settings']))  {
    if (!$admin->checkFTAN())
    {
        if(!$admin_header) { $admin->print_header(); }
        $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'],$_SERVER['REQUEST_URI']);
    }
    $aSql = array();

    $aSql[] = save_setting('mod_jsadmin_persist_order', intval(isset($_POST['persist_order'])) );
    $aSql[] = save_setting('mod_jsadmin_ajax_order_pages', intval(isset($_POST['ajax_order_pages'])) );
    $aSql[] = save_setting('mod_jsadmin_ajax_order_sections', intval(isset($_POST['ajax_order_sections'])) );

    // check if there is a database error, otherwise say successful implode('<br />',$aSql ). 
    if(!$admin_header) { $admin->print_header(); }
    if($database->is_error()) {
        $admin->print_error($database->get_error(), $js_back);
    } else {
        $admin->print_success( $MESSAGE['PAGES_SAVED'], $ToolUrl);
    }

} else {
    // $admin->print_header();
}

// Display form
    $persist_order = get_setting('mod_jsadmin_persist_order' ) ? 'checked="checked"' : '';
    $ajax_order_pages = get_setting('mod_jsadmin_ajax_order_pages'  ) ? 'checked="checked"' : '';
    $ajax_order_sections = get_setting('mod_jsadmin_ajax_order_sections' ) ? 'checked="checked"' : '';

// THIS ROUTINE CHECKS THE EXISTING OFF ALL NEEDED YUI FILES
  $YUI_ERROR=false; // ist there an Error
  $YUI_PUT ='';   // String with javascipt includes
  $YUI_PUT_MISSING_Files=''; // String with missing files
  reset($js_yui_scripts);
  foreach($js_yui_scripts as $script) {
     if(!file_exists($WB_MAIN_RELATIVE_PATH.$script)){
        $YUI_ERROR=true;
        $YUI_PUT_MISSING_Files =$YUI_PUT_MISSING_Files."- ".WB_URL.$script."<br />";   // catch all missing files
    }
    }
    if($YUI_ERROR)
    {
?>
    <div id="jsadmin_install" style="border: solid 2px #c99; background: #ffd; padding: 0.5em; margin-top: 1em">

     <?php echo $MOD_JSADMIN['TXT_ERROR_INSTALLINFO_B'].$YUI_PUT_MISSING_Files; ?>
      </div>
<?php
  }
  else
  {
  ?>
   <form id="jsadmin_form" name="store_settings" style="margin-top: 1em; display: true;" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
    <?php echo $admin->getFTAN(); ?>
   <table >
   <tr>
         <td colspan="2"><?php echo $MOD_JSADMIN['TXT_HEADING_B']; ?>:</td>
   </tr>
   <tr>
         <td width="20"><input type="checkbox" name="persist_order" id="persist_order" value="true" <?php echo $persist_order; ?>/></td>
         <td><label for="persist_order"><?php echo $MOD_JSADMIN['TXT_PERSIST_ORDER_B']; ?></label></td>
   </tr>
   <tr>
         <td width="20"><input type="checkbox" name="ajax_order_pages" id="ajax_order_pages" value="true" <?php echo $ajax_order_pages; ?>/></td>
         <td><label for="ajax_order_pages"><?php echo $MOD_JSADMIN['TXT_AJAX_ORDER_PAGES_B']; ?></label></td>
   </tr>
   <tr>
         <td width="20"><input type="checkbox" name="ajax_order_sections" id="ajax_order_sections" value="true" <?php echo $ajax_order_sections; ?>/></td>
         <td><label for="ajax_order_sections"><?php echo $MOD_JSADMIN['TXT_AJAX_ORDER_SECTIONS_B']; ?></label></td>
   </tr>
   <tr>
         <td>&nbsp;</td>
         <td>
           <input type="submit" name="save_settings" value="<?php echo $TEXT['SAVE']; ?>" />
        </td>
   </tr>
   </table>
   </form>
 <?php
 }
