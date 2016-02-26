<?php
/**
 *
 * @category        modules
 * @package         news
 * @author          WebsiteBaker Project
 * @copyright       2009-2011, Website Baker Org. e.V.
 * @link            http://www.websitebaker2.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.x
 * @requirements    PHP 5.2.2 and higher
 * @version          $Id: install.php 1587 2012-01-24 23:19:06Z darkviper $
 * @filesource        $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/news/install.php $
 * @lastmodified    $Date: 2012-01-25 00:19:06 +0100 (Mi, 25. Jan 2012) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
require_once( dirname(dirname(dirname(__FILE__))).'/framework/globalExceptionHandler.php');
if(!defined('WB_PATH')) { throw new IllegalFileException(); }
/* -------------------------------------------------------- */
    $sDefaultSql = dirname(__FILE__).'/install.sql';
    if (is_readable($sDefaultSql)) {
// create needet database tables and set default records
        if ($database->SqlImport($sDefaultSql, TABLE_PREFIX)) {
// Make news post access files dir
            require_once(WB_PATH.'/framework/functions.php');
            if(make_dir(WB_PATH.PAGES_DIRECTORY.'/posts')) {
            }
        }
    }
/* **** END INSTALL ********************************************************* */