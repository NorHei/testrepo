<?php
/**
 *
 * @category        modules
 * @package         captcha_control
 * @author          WebsiteBaker Project
 * @copyright       2009-2011, Website Baker Org. e.V.
 * @link            http://www.websitebaker2.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: uninstall.php 1536 2011-12-10 04:22:29Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/captcha_control/uninstall.php $
 * @lastmodified    $Date: 2011-12-10 05:22:29 +0100 (Sa, 10. Dez 2011) $
 *
 */
if(defined('WB_PATH'))
{
    // delete tables from sql dump file
    if (is_readable(__DIR__.'/install-struct.sql')) {
        $database->SqlImport(__DIR__.'/install-struct.sql', TABLE_PREFIX, __FILE__ );
    }
}
