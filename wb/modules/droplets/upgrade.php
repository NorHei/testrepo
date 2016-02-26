<?php
/**
 *
 * @category        module
 * @package         droplet
 * @author          Ruud Eisinga (Ruud) John (PCWacht)
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: upgrade.php 1503 2011-08-18 02:18:59Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/droplets/upgrade.php $
 * @lastmodified    $Date: 2011-08-18 04:18:59 +0200 (Do, 18. Aug 2011) $
 *
 */

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Illegale file access /'.basename(__DIR__).'/'.basename(__FILE__).''); }
/* -------------------------------------------------------- */
if(!function_exists('insertDropletFile')) { require('droplets.functions.php'); }

    $table_name = TABLE_PREFIX .'mod_droplets';
    $description = 'INT NOT NULL default 0 ';
    $database->field_add($table_name,'show_wysiwyg',$description.'AFTER `active`' );
    $database->field_add($table_name,'admin_view',$description.'AFTER `active`' );
    $database->field_add($table_name,'admin_edit',$description.'AFTER `active`' );

    $sBaseDir = realpath(dirname(__FILE__).'/example/');
    $sBaseDir    = rtrim(str_replace('\\', '/', $sBaseDir), '/').'/';
    $aDropletFiles = getDropletFromFiles($sBaseDir);
    $bOverwriteDroplets = false;
    insertDropletFile($aDropletFiles,$msg,$bOverwriteDroplets,$admin);

