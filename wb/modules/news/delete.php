<?php
/**
 *
 * @category        modules
 * @package         news
 * @author          WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: delete.php 1538 2011-12-10 15:06:15Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/news/delete.php $
 * @lastmodified    $Date: 2011-12-10 16:06:15 +0100 (Sa, 10. Dez 2011) $
 *
 */

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Illegale file access /'.basename(__DIR__).'/'.basename(__FILE__).''); }
/* -------------------------------------------------------- */
function mod_news_delete($database, $page_id, $section_id)
{
    //get and remove all php files created for the news section
    $query_details = $database->query("SELECT * FROM `".TABLE_PREFIX."mod_news_posts` WHERE `section_id` = '$section_id'");
    if($query_details->numRows() > 0) {
        while($link = $query_details->fetchRow(MYSQLI_ASSOC)) {
            if(is_writable(WB_PATH.PAGES_DIRECTORY.$link['link'].PAGE_EXTENSION)) {
            unlink(WB_PATH.PAGES_DIRECTORY.$link['link'].PAGE_EXTENSION);
            }
        }
    }
    //check to see if any other sections are part of the news page, if only 1 news is there delete it
    $query_details = $database->query("SELECT * FROM `".TABLE_PREFIX."sections` WHERE `page_id` = '$page_id'");
    if($query_details->numRows() == 1) {
        $query_details2 = $database->query("SELECT * FROM `".TABLE_PREFIX."pages` WHERE `page_id` = '$page_id'");
        $link = $query_details2->fetchRow(MYSQLI_ASSOC);
        if(is_writable(WB_PATH.PAGES_DIRECTORY.$link['link'].PAGE_EXTENSION)) {
            unlink(WB_PATH.PAGES_DIRECTORY.$link['link'].PAGE_EXTENSION);
        }
    }

    $database->query("DELETE FROM `".TABLE_PREFIX."mod_news_posts` WHERE `section_id` = '$section_id'");
    $database->query("DELETE FROM `".TABLE_PREFIX."mod_news_groups` WHERE `section_id` = '$section_id'");
    $database->query("DELETE FROM `".TABLE_PREFIX."mod_news_comments` WHERE `section_id` = '$section_id'");
    $database->query("DELETE FROM `".TABLE_PREFIX."mod_news_settings` WHERE `section_id` = '$section_id'");
}

mod_news_delete($database, $page_id, $section_id);

