<?php
/**
 *
 * @category        backend
 * @package         modules
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: admin.php 1625 2012-02-29 00:50:57Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/modules/admin.php $
 * @lastmodified    $Date: 2012-02-29 01:50:57 +0100 (Mi, 29. Feb 2012) $
 *
*/

/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die("Cannot access this file directly"); }
/* -------------------------------------------------------- */

// Create new admin object, you can set the next variable in your module
// to print with or without header, default is with header
// it is recommed to set the variable before including the /modules/admin.php
    $admin_header = (!isset($admin_header)) ? true : $admin_header;
    if(!class_exists('admin', false)){ include(WB_PATH.'/framework/class.admin.php'); }
    $admin = new admin('Pages', 'pages_modify',(bool)$admin_header);
// get request method
    $requestMethod = '_'.strtoupper($_SERVER['REQUEST_METHOD']);
    $aRequestVars  = (isset(${$requestMethod}) ? ${$requestMethod} : null);
// Get page id (on error page_id == 0))
    $page_id = intval(isset(${$requestMethod}['page_id'])
                      ? ${$requestMethod}['page_id']
                      : (isset($page_id) ? $page_id : 0)
               );

    $requestMethod = '_'.strtoupper($_SERVER['REQUEST_METHOD']);
    $section_id = intval(isset(${$requestMethod}['section_id'])
                         ? ${$requestMethod}['section_id']
                         : (isset($section_id) ? $section_id : 0)
                  );

$module_dir = basename( dirname($_SERVER["SCRIPT_NAME"]) );

// Create js back link
$js_back = ADMIN_URL.'/pages/sections.php?page_id='.$page_id;

// Get perms
// unset($admin_header);
if( !is_numeric( $page_id ) ) {
        $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL );
} elseif ($page_id > 0) {
      $page = $admin->get_page_details($page_id, ADMIN_URL.'/pages/index.php' );
} else {
    $admin->print_error($MESSAGE['PAGES_INSUFFICIENT_PERMISSIONS'], ADMIN_URL );
}

$old_admin_groups = explode(',', str_replace('_', '', $page['admin_groups']));
$old_admin_users = explode(',', str_replace('_', '', $page['admin_users']));

$in_group = false;
foreach($admin->get_groups_id() as $cur_gid){
    if (in_array($cur_gid, $old_admin_groups)) {
        $in_group = true;
    }
}

if((!$in_group) && !is_numeric(array_search($admin->get_user_id(), $old_admin_users))) {
    print $admin->get_group_id().$admin->get_user_id();
    // print_r ($old_admin_groups);
    $admin->print_error($MESSAGE['PAGES_INSUFFICIENT_PERMISSIONS']  );
}

// some additional security checks:
// Check whether the section_id belongs to the page_id at all
if( !is_numeric( $section_id ) ) {
        $admin->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], ADMIN_URL );
    } elseif ($section_id > 0) {
    $section = $admin->get_section_details($section_id, ADMIN_URL.'/pages/index.php');
    if (!$admin->get_permission($section['module'], 'module'))
    {
        $admin->print_error($MESSAGE['PAGES_INSUFFICIENT_PERMISSIONS'], ADMIN_URL );
    }
}

// Workout if the developer wants to show the info banner
if(isset($print_info_banner) && $print_info_banner == true) {
    // Get page details already defined

    // Get display name of person who last modified the page
    $user = $admin->get_user_details($page['modified_by']);

    // Convert the unix ts for modified_when to human a readable form
    $modified_ts = 'Unknown';
    if($page['modified_when'] != 0) {
        $modified_ts = gmdate(TIME_FORMAT.', '.DATE_FORMAT, $page['modified_when']+TIMEZONE);
    }

    // Setup template object, parse vars to it, then parse it
    // Create new template object
    $template = new Template(dirname($admin->correct_theme_source('pages_modify.htt')));
    // $template->debug = true;
    $template->set_file('page', 'pages_modify.htt');
    $template->set_block('page', 'main_block', 'main');
    $template->set_block('main_block', 'section_block', 'section_list');
    $template->set_block('section_block', 'block_block', 'block_list');
    $template->set_var(array(
                'PAGE_ID' => $page['page_id'],
                // 'PAGE_IDKEY' => $admin->getIDKEY($page['page_id']),
                'PAGE_IDKEY' => $page['page_id'],
                'PAGE_TITLE' => ($page['page_title']),
                'MENU_TITLE' => ($page['menu_title']),
                'ADMIN_URL' => ADMIN_URL,
                'WB_URL' => WB_URL,
                'THEME_URL' => THEME_URL
                ));

    $template->set_var(array(
                'MODIFIED_BY' => $user['display_name'],
                'MODIFIED_BY_USERNAME' => $user['username'],
                'MODIFIED_WHEN' => $modified_ts,
                'LAST_MODIFIED' => $MESSAGE['PAGES_LAST_MODIFIED'],
                ));

    $template->set_block('main_block', 'show_modify_block', 'show_modify');
    if($modified_ts == 'Unknown')
    {
        $template->set_block('show_modify', '');
        $template->set_var('CLASS_DISPLAY_MODIFIED', 'hide');

    } else {
        $template->set_var('CLASS_DISPLAY_MODIFIED', '');
        $template->parse('show_modify', 'show_modify_block', true);
    }

    // Work-out if we should show the "manage sections" link
    $sql  = 'SELECT `section_id` FROM `'.TABLE_PREFIX.'sections` WHERE `page_id` = '.(int)$page_id.' ';
    $sql .= 'AND `module` = "menu_link"';
    $query_sections = $database->query($sql);

    $template->set_block('main_block', 'show_section_block', 'show_section');
    if($query_sections->numRows() > 0)
    {
        $template->set_block('show_section', '');
        $template->set_var('DISPLAY_MANAGE_SECTIONS', 'display:none;');

    } elseif(MANAGE_SECTIONS == 'enabled')
    {

        $template->set_var('TEXT_MANAGE_SECTIONS', $HEADING['MANAGE_SECTIONS']);
        $template->parse('show_section', 'show_section_block', true);

    } else {
        $template->set_block('show_section', '');
        $template->set_var('DISPLAY_MANAGE_SECTIONS', 'display:none;');

    }

    // Insert language TEXT
    $template->set_var(array(
                    'TEXT_CURRENT_PAGE' => $TEXT['CURRENT_PAGE'],
                    'TEXT_CHANGE_SETTINGS' => $TEXT['CHANGE_SETTINGS'],
                    'HEADING_MODIFY_PAGE' => $HEADING['MODIFY_PAGE']
                    ));

    // Parse and print header template
    $template->parse('main', 'main_block', false);
    $template->pparse('output', 'page');
    // unset($print_info_banner);
    unset($template);
    $sSectionBlock = '<div class="block-outer">'."\n";

    if (/*SECTION_BLOCKS && */isset($section) ) {
        if (isset($block[$section['block']]) && trim(strip_tags(($block[$section['block']]))) != '')
        {
            $block_name = htmlentities(strip_tags($block[$section['block']]));
        } else {
            if ($section['block'] == 1)
                     {
                $block_name = $TEXT['MAIN'];
            } else {
                $block_name = '#' . (int) $section['block'];
            }
        }
        $now = time();
        $bSectionInactive = !(($now<=$section['publ_end'] || $section['publ_end']==0) && ($now>=$section['publ_start'] || $section['publ_start']==0));
//        $sSectionInfoLine  = ($bSectionInactive ? false: true);
        $sSectionInfoLine  = ($bSectionInactive ? 'inactive': 'active');
//        $sSectionInfoLine  = ($bSectionInactive ? '<div class="section-inactive">': '<div class="section-active">')."\n" ;
/*
        $sec_anchor = (defined( 'SEC_ANCHOR' ) && ( SEC_ANCHOR != '' )  ? 'id="'.SEC_ANCHOR.$section['section_id'].'"' : '');
        $sSectionInfoLine = '<div class="section-info" '.$sec_anchor.' ><b>'.$TEXT['BLOCK']
                          . ': </b>'.$block_name.' ('.$section['block'].') <b> Modul: </b>'
                          . $section['module'].'<b>  ID: </b>'.$section_id.'</div>'.PHP_EOL;
        echo $sSectionInfoLine;
*/
        $sSectionIdPrefix = (defined( 'SEC_ANCHOR' ) && ( SEC_ANCHOR != '' )  ? SEC_ANCHOR : '' );
        $sCallingScript = $_SERVER['SCRIPT_NAME'];
        $data = array();
        echo $sSectionBlock;

        $tpl = new Template(dirname($admin->correct_theme_source('SectionInfoLine.htt')),'keep');
        // $template->debug = true;
        $tpl->set_file('page', 'SectionInfoLine.htt');

        $tpl->set_block('page', 'main_block', 'main');
        $tpl->set_block('main_block', 'section_block', 'section_save');

        $data['aTarget.SectionIdPrefix'] = $sSectionIdPrefix.$section_id;
        $data['aTarget.SectionInfoLine'] = $sSectionInfoLine;
        $data['aTarget.SectionIdPrefix'] = $sSectionIdPrefix.$section_id;
        $data['aTarget.sectionBlock'] = $section['block'];
        $data['aTarget.SectionId'] = $section_id;
        $data['aTarget.pageId'] = $page_id;
        $data['aTarget.FTAN'] = $admin->getFTAN();
        $data['aTarget.BlockName'] = $block_name;
        $data['aTarget.sectionUrl'] = ADMIN_URL.'/pages/';
        $data['aTarget.sectionModule'] = $section['module'];
        $data['aTarget.title'] = $section['title'];
        $tpl->parse('section_save', '');
        if( preg_match( '/'.preg_quote(ADMIN_PATH,'/').'\/pages\/(settings|sections)\.php$/is', $sCallingScript)) {
        if( $admin->get_permission('pages_settings') ) {
        $data['lang.TEXT_SUBMIT'] = $TEXT['SAVE'];
          $tpl->parse('section_save', 'section_block');
        }
        }
        $tpl->set_var($data);
        $tpl->parse('main', 'main_block', false);
        $tpl->pparse('output', 'page');
        unset($tpl);
    }
//print '<pre>';print_r( $aTokens = unserialize($_SESSION['TOKENS']) );print '</pre>';
} //

// Work-out if the developer wants us to update the timestamp for when the page was last modified
if(isset($update_when_modified) && $update_when_modified == true) {
    $sql  = 'UPDATE `'.TABLE_PREFIX.'pages` SET '
          . '`modified_when` = '.time().','
          . '`modified_by` = '.$admin->get_user_id().' '
          . 'WHERE `page_id` = '.$page_id;
    $database->query($sql);
}
