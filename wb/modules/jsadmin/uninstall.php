<?php
/**
 *
 * @category        modules
 * @package         JsAdmin
 * @author          WebsiteBaker Project, modified by Swen Uth for WebsiteBaker 2.7
 * @copyright       (C) 2006, Stepan Riha, WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: uninstall.php 1537 2011-12-10 11:04:33Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/jsadmin/uninstall.php $
 * @lastmodified    $Date: 2011-12-10 12:04:33 +0100 (Sa, 10. Dez 2011) $
 *
*/

if(defined('WB_PATH'))
{
    // delete tables from sql dump file
    if (is_readable(__DIR__.'/install-struct.sql')) {
        $database->setSqlImportActionFile(__FILE__);
        $database->SqlImport(__DIR__.'/install-struct.sql', TABLE_PREFIX, false );
    }
}
