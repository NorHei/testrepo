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
 * @version         $Id: index.php 1457 2011-06-25 17:18:50Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/droplets/index.php $
 * @lastmodified    $Date: 2011-06-25 19:18:50 +0200 (Sa, 25. Jun 2011) $
 *
 */
    function executeDropletTool()
    {
/* -------------------------------------------------------- */
        $sAddonName = basename(__DIR__);
        if (is_readable(dirname(__DIR__).'/SimpleRegister.php')) {
            require (dirname(__DIR__).'/SimpleRegister.php');
        }
/*
        if (is_readable(dirname(__DIR__).'/SimpleCommandDispatcher.inc')) {
            require (dirname(__DIR__).'/SimpleCommandDispatcher.inc');
        }
*/
// backward compatibilty for upgrade, install, uninstall called from core
        $database    = $oDb;
        $wb = $admin = $oApp;
/*******************************************************************************************/
//      SimpleCommandDispatcher
/*******************************************************************************************/
/*
*/
        $bIsBackend = ($oApp instanceof admin);
        // set addon depending path / url
        $sAddonPath = $oReg->AppPath.'modules/'.$sAddonName;
        $sAddonUrl  = $oReg->AppUrl.'modules/'.$sAddonName;
        $sAddonRel = '/modules/'.basename(__DIR__);
        $sAddonThemeRel ='/themes/default';
        // define the theme to use -----------------------------------------------------------
        if (is_readable($sAddonPath.'/themes/default')) {
        // first set fallback to system default theme
            $sAddonThemePath = $sAddonPath.'/themes/default';
            $sAddonThemeUrl  = $sAddonUrl.'/themes/default';
        }
        if (is_readable($sAddonPath.'/themes/'.$oReg->DefaultTheme)) {
        // overload with the selected theme if accessible
            $sAddonThemePath = $sAddonPath.'/themes/'.$oReg->DefaultTheme;
            $sAddonThemeUrl  = $sAddonUrl.'/themes/'.$oReg->DefaultTheme;
        }
        // define the template to use --------------------------------------------------------
        if (is_readable($sAddonPath.'/templates/default')) {
            // first set fallback to system default template
            $sAddonTemplatePath = $sAddonPath.'/templates/default';
            $sAddonTemplateUrl  = $sAddonUrl.'/templates/default';
        }
        if (is_readable($sAddonPath.'/templates/'.$oReg->DefaultTemplate)) {
            // try setting to the template of global settings
            $sAddonTemplatePath = $sAddonPath.'/templates/'.$oReg->DefaultTemplate;
            $sAddonTemplateUrl  = $sAddonUrl.'/templates/'.$oReg->DefaultTemplate;
        }
        if (!$bIsBackend && is_readable($sAddonPath.'/templates/'.$oReg->Template)) {
            // try setting to the template of page depending settings
            $sAddonTemplatePath = $sAddonPath.'/templates/'.$oReg->Template;
            $sAddonTemplateUrl  = $sAddonUrl.'/templates/'.$oReg->Template;
        }
        // load core depending language file ------------------------------------------------
        if(is_readable($oReg->AppPath.'/languages/EN.php') ){
            include($oReg->AppPath.'languages/EN.php');
        }
        if(is_readable($oReg->AppPath.'/languages/'.$oReg->Language.'.php') ){
            include($oReg->AppPath.'/languages/'.$oReg->Language.'.php');
        }
        // load addon depending language file ------------------------------------------------
        if (is_readable($sAddonPath.'/languages/EN.php')) {
            // first load fallback to system default language (EN)
            include $sAddonPath.'/languages/EN.php';
        }
        if (is_readable($sAddonPath.'/languages/'.$oReg->DefaultLanguage.'.php')) {
            // try loading language of global settings
            include $sAddonPath.'/languages/'.$oReg->DefaultLanguage.'.php';
        }
        if (is_readable($sAddonPath.'/languages/'.$oReg->Language.'.php')) {
            // try loading language of user (backend) or page (frontend) defined settings
            include $sAddonPath.'/languages/'.$oReg->Language.'.php';
        }
    // load addon Theme/Template depending language file ---------------------------------
        $sTmp = ($bIsBackend ? $sAddonThemePath : $sAddonTemplatePath).'/languages/';
        if (is_readable($sTmp.'EN.php')) {
            // first load fallback to system default language (EN)
            include $sTmp.'EN.php';
        }
        if (is_readable($sTmp.$oReg->DefaultLanguage.'.php')) {
            // try loading language of global settings
            include $sTmp.$oReg->DefaultLanguage.'.php';
        }
        if (is_readable($sTmp.$oReg->Language.'.php')) {
            // try loading language of user (backend) or page (frontend) defined settings
            include $sTmp.$oReg->Language.'.php';
        }
        if (!class_exists('Translate')) {
            include $oReg->AppPath.'framework/Translate.php';
        }
        Translate::getInstance ()->enableAddon ('modules\\'.$sAddonName);

    // end of Simple Command Dispatcher ---------------------------------------------------------
        if(!function_exists('getUniqueName')) { require($sAddonPath.'/droplets.functions.php'); }
        $ToolUrl  = $oReg->AcpUrl.'admintools/tool.php?tool=droplets';
        $ApptoolLink = $oReg->AcpUrl.'admintools/index.php';
        // create default placeholder array for templates htt or Twig use
        $aLang = array_merge($HEADING,$MENU,$TEXT,$DR_TEXT,$Droplet_Header,$Droplet_Message,$Droplet_Help,$Droplet_Import);
        $aTplDefaults = array (
              'ToolUrl' => $ToolUrl,
              'sAddonUrl' => $sAddonUrl,
              'ApptoolLink' => $ApptoolLink,
              'sAddonThemeUrl'  => $sAddonThemeUrl,
              );
        $output = '';
        if ( !class_exists('msgQueue', false) ) { require($oReg->AppPath.'/framework/class.msg_queue.php'); }
        msgQueue::clear();
        if( !$oApp->get_permission($sAddonName,'module' ) ) {
            $oApp->print_error($MESSAGE['ADMIN_INSUFFICIENT_PRIVELLIGES'], $js_back);
            exit();
        }
        $sOverviewDroplets = $TEXT['LIST_OPTIONS'].' '.$DR_TEXT['DROPLETS'];
        // prepare to get parameters (query)) from this URL string e.g. modify_droplet?droplet_id
        $aQuery = array('command'=>'overview');
        $sql = '';
        $aRequestVars = $_REQUEST;
        $aParseUrl  = ( isset($aRequestVars['command'])?  parse_url ($aRequestVars['command']): $aQuery );
        // sanitize command from compatibility file
        $action = preg_replace(
            '/[^a-z\/0-1_]/siu',
            '',
            (isset($aParseUrl['path']) ? $aParseUrl['path'] : 'overview')
        );
        $sCommand = $sAddonPath.'/commands/'.$action.'.php';
        $subCommand = (@$aRequestVars['subCommand']?:$action);
        if ( isset( $aParseUrl['query']) ) { parse_str($aParseUrl['query'], $aQuery); }
        if( !function_exists( 'make_dir' ) ) { require($oReg->AppPath.'/framework/functions.php');  }
        ob_start();
        extract($aQuery, EXTR_PREFIX_SAME, "dr");
        switch ($action):
            case 'add_droplet':
            case 'copy_droplet':
                $iDropletAddId = ($oApp->checkIDKEY($droplet_id, false, ''));
                if ( is_readable($sCommand)) { include ( $sCommand ); }
                $sCommand = $sAddonPath.'/commands/'.'rename_droplet.php';
            case 'rename_droplet':
                if ( is_readable($sCommand)) { include ( $sCommand ); }
                $sCommand = $sAddonPath.'/commands/'.'overview.php';
            case 'modify_droplet':
            case 'backup_droplets':
            case 'import_droplets':
                if (is_readable($sCommand)) { include ( $sCommand ); }
                break;
            case 'save_rename':
                $droplet_id = $aRequestVars['CopyDropletId'];
//                $droplet_id = ($oApp->checkIDKEY($droplet_id, false, ''));
                if ( is_readable($sCommand)) { include ( $sCommand ); }
                $sCommand = $sAddonPath.'/commands/'.'overview.php';
                if (is_readable($sCommand)) { include ( $sCommand ); }
                break;
            case 'save_droplet':
                $droplet_id = $aRequestVars['droplet_id'];
            case 'ToggleStatus':
            case 'delete_droplet':
                $droplet_id = ($oApp->checkIDKEY($droplet_id, false, ''));
            case 'restore_droplets':
            case 'call_help':
            case 'call_import':
            case 'select_archiv':
            case 'delete_archiv':
                if ( is_readable($sCommand)) { include ( $sCommand ); }
            default:
                $sCommand = $sAddonPath.'/commands/'.'overview.php';
                if (is_readable($sCommand)) { include ( $sCommand ); }
                break;
        endswitch;
        $output = ob_get_clean();
        if( ($msg = msgQueue::getSuccess()) != '')
        {
            $output = $oApp->print_success($msg, $ToolUrl ).$output;
        }
        if( ($msg = msgQueue::getError()) != '')
        {
            $output = $oApp->print_error($msg, $ToolUrl).$output;
        }
        print $output;
        $oApp->print_footer();
    } // end executeDropletTool
/* -------------------------------------------------------------------------------------------- */
/*                                                                                              */
/* -------------------------------------------------------------------------------------------- */
    if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
    if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }
    $oApp = new admin('admintools', 'admintools', false);
    $requestMethod = '_'.strtoupper($_SERVER['REQUEST_METHOD']);
    $aRequestVars  = (isset(${$requestMethod})) ? ${$requestMethod} : null;
    executeDropletTool();
    exit;
// end of file
