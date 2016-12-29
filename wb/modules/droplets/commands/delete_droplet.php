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
 * @version         $Id: delete_droplet.php 1503 2011-08-18 02:18:59Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/droplets/delete_droplet.php $
 * @lastmodified    $Date: 2011-08-18 04:18:59 +0200 (Do, 18. Aug 2011) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Cannot access '.basename(__DIR__).'/'.basename(__FILE__).' directly'); }
/* -------------------------------------------------------- */
// Get id
if ($droplet_id===false) {
    $oApp->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], $ToolUrl);
    exit();
}

if( !$oApp->checkFTAN() ){
    $oApp->print_error($MESSAGE['GENERIC_SECURITY_ACCESS'], $ToolUrl );
    exit();
}

if (!isset( $aRequestVars['DropletsToDelete']))
{
    $sDropletsToDelete = ( isset($droplet_id) && !isset( $aRequestVars['cb']) ? $droplet_id : '' );
    $iDELETED = (isset($droplet_id) ? 1 : 0 );
    if( isset( $aRequestVars['cb'])  ) {
        $aRequestVars['cb'] = array_flip(  $aRequestVars['cb'] );
        $aRequestVars['cb'] = array_unique($aRequestVars['cb'], SORT_NUMERIC);
        $iDELETED = sizeof( $aRequestVars['cb'] );
        $sDropletsToDelete = ( isset($droplet_id) ? implode(',',$aRequestVars['cb'] ) : '' );
    }
    $sql  = 'SELECT * FROM `'.TABLE_PREFIX.'mod_droplets` '
          . 'WHERE `id` IN ('.$sDropletsToDelete.') ';
    $inDroplets = '';
    if ( $oRes = $oDb->query($sql)) {
        while( $aRow = $oRes->fetchRow( MYSQLI_ASSOC ) ) {
          $inDroplets .= $aRow['name'].', '; 
        }
    }
    $iDropletIdKey = $oApp->getIDKEY($droplet_id);
    $aFtan = $admin->getFTAN('');
    // prepare default data for phplib and twig
    $aTplData = array (
        'FTAN_NAME' => $aFtan['name'],
        'FTAN_VALUE' => $aFtan['value'],
        'iDropletIdKey' => $iDropletIdKey,
        'sDropletsToDelete' => $sDropletsToDelete,
        'inDroplets' =>  rtrim($inDroplets, ', '),
        );
// Create new template object with phplib
    $oTpl = new Template($sAddonThemePath, 'keep' );
    $oTpl->set_file('page', 'delete_droplets.htt');
    $oTpl->set_block('page', 'main_block', 'main');
//    $oTpl->set_var('FTAN_NAME', $aFtan['name']);
//    $oTpl->set_var('FTAN_VALUE', $aFtan['value']);
    $oTpl->set_var($aLang);
    $oTpl->set_var($aTplDefaults);
    $oTpl->set_var($aTplData);
/*-- finalize the page -----------------------------------------------------------------*/
    $oTpl->parse('main', 'main_block', false);
    $oTpl->pparse('output', 'page');

} elseif (!isset($aRequestVars['cancel'])) {
    $sDropletsToDelete = $aRequestVars['DropletsToDelete'];
    $iDELETED = sizeof( explode(',', $sDropletsToDelete) );
    $sql  = 'DELETE FROM `'.TABLE_PREFIX.'mod_droplets` '
          . 'WHERE `id` IN ('.$sDropletsToDelete.') ';
    // Delete droplet
    $oDb->query($sql);
    
    // Check if there is a db error, otherwise say successful
    if($oDb->is_error()) {
        msgQueue::add( $oDb->get_error().'<br />'.$sql );
    } else {
        msgQueue::add( sprintf("%'.02d", $iDELETED ).'  '.$DR_TEXT['DROPLETS_DELETED'], true );
    }
} else { /* do nothing */}
