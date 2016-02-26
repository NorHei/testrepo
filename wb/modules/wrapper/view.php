<?php
/**
 *
 * @category        modules
 * @package         wrapper
 * @author          WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: view.php 1538 2011-12-10 15:06:15Z Luisehahne $
 * @filesource      $HeadURL: http://svn.websitebaker2.org/branches/2.8.x/wb/modules/wrapper/install.php $
 * @lastmodified    $Date: 2011-01-10 13:21:47 +0100 (Mo, 10 Jan 2011) $
 *
 */

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Illegale file access /'.basename(__DIR__).'/'.basename(__FILE__).''); }
/* -------------------------------------------------------- */

// check if module language file exists for the language set by the user (e.g. DE, EN)
if(!file_exists(WB_PATH .'/modules/wrapper/languages/'.LANGUAGE .'.php')) {
    // no module language file exists for the language set by the user, include default module language file EN.php
    require_once(WB_PATH .'/modules/wrapper/languages/EN.php');
} else {
    // a module language file exists for the language defined by the user, load it
    require_once(WB_PATH .'/modules/wrapper/languages/'.LANGUAGE .'.php');
}

// get url
$get_settings = $database->query("SELECT `url`,`height` FROM `".TABLE_PREFIX."mod_wrapper` WHERE `section_id` = '$section_id'");
$fetch_settings = $get_settings->fetchRow(MYSQLI_ASSOC);
$url = ($fetch_settings['url']);

?>
<iframe src="<?php echo $url; ?>" width="100%" height="<?php echo $fetch_settings['height']; ?>" frameborder="0" scrolling="auto">
<?php echo $MOD_WRAPPER['NOTICE']; ?>
<a href="<?php echo $url; ?>" target="_blank"><?php echo $url; ?></a>
</iframe>