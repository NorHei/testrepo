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
 * @version         $Id: tool.php 1543 2011-12-14 00:13:54Z Luisehahne $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/tags/2.8.3/wb/modules/droplets/tool.php $
 * @lastmodified    $Date: 2011-12-14 01:13:54 +0100 (Mi, 14. Dez 2011) $
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(defined('WB_PATH') == false) { die('Illegale file access /'.basename(__DIR__).'/'.basename(__FILE__).''); }
/* -------------------------------------------------------- */
$msg = array();
if( !$oApp->get_permission($sAddonName,'module' ) ) {
    $oApp->print_error($MESSAGE['ADMIN_INSUFFICIENT_PRIVELLIGES'], $js_back);
    exit();
}
// Get userid for showing admin only droplets or not
$loggedin_user = ($oApp->ami_group_member('1') ? 1 : $oApp->get_user_id() );
$loggedin_group = $oApp->get_groups_id();
$oApp_user = ( ($oApp->get_home_folder() == '') && ($oApp->ami_group_member('1') ) || ($loggedin_user == '1'));
//removes empty entries from the table so they will not be displayed
$sql = 'DELETE FROM `'.TABLE_PREFIX.'mod_droplets` '
     . 'WHERE name = \'\' ';
if( !$oDb->query($sql) ) {
    $msg[] = $oDb->get_error();
}
// if import failed after installation, should be only 1 time
$sql = 'SELECT COUNT(`id`) FROM `'.TABLE_PREFIX.'mod_droplets` ';
if( !$oDb->get_one($sql) ) {
    include($sAddonPath.'/install.php');
}
function check_syntax($code) {
    return @eval('return true;' . $code);
}
?><script type="text/javascript">
<!--
var Droplet = { 
    ADMIN_DIRECTORY : '<?php echo ADMIN_DIRECTORY ?>',
    WB_URL : '<?php echo $oReg->AppUrl; ?>',
    ADMIN_URL : '<?php echo $oReg->AcpUrl; ?>',
    AddonUrl : '<?php echo $sAddonUrl; ?>/',
    ThemeUrl : '<?php echo $sAddonThemeUrl; ?>/'
};
-->
</script>
<script src="<?php echo $sAddonThemeUrl; ?>/js/wz_tooltip.js"></script>
<script src="<?php echo $sAddonThemeUrl; ?>/js/tip_balloon.js"></script>

<br />
<div class="droplets overview" id="cb-droplets" >
<form action="<?php echo $ToolUrl; ?>" method="post" name="droplets_form" enctype="multipart/form-data" >
    <?php echo $oApp->getFTAN(); ?>
    <table class="droplets">
        <tbody>
            <tr>
                <td >
                    <button class="btn" type="submit" name="command" value="add_droplet?droplet_id=<?php echo $oApp->getIDKEY(0); ?>"><?php echo $DR_TEXT['ADD_DROPLET']; ?></button>
                    <button class="btn" type="submit" name="command" value="select_archiv#openModal"><?php echo $DR_TEXT['IMPORT']; ?></button>
               </td>
                <td style="float: right;">
                    <button class="btn" type="submit" name="command" value="call_help#openModal" class="modal-header_btn modal-trigger btn-fixed">Droplet <?php echo $DR_TEXT['HELP']; ?></button>
                    <button class="btn" type="submit" name="command" value="backup_droplets"><?php echo $DR_TEXT['BACKUP']; ?></button>
                </td>
            </tr>
        </tbody>
    </table>
    <br />
<h2><?php echo $TEXT['MODIFY'].'/'.$TEXT['DELETE'].' '.$DR_TEXT['DROPLETS']; ?></h2>
<?php
$sql = 'SELECT * FROM `'.TABLE_PREFIX.'mod_droplets` ';
if (!$oApp_user) {
    $sql .= 'WHERE `admin_view` <> 1 ';
}
$sql .= 'ORDER BY `modified_when` DESC';
$oDroplets = $oDb->query($sql);
$num_droplets = $oDroplets->numRows();
if($num_droplets > 0) {

?>    <table class="droplets_data sortierbar">
        <thead>
            <tr>
                <th>
          <label>
              <input name="select_all" id="select_all" type="checkbox" value="1"  />
          </label>
                </th>
                <th ></th>
                <th ></th>
                <th ></th>
                <th class="sortierbar" style="white-space: nowrap; max-width: 5.225em; " title=""><?php echo $TEXT['NAME']; ?></th>
                <th class="sortierbar" title=""><?php echo $TEXT['DESCRIPTION']; ?></th>
                <th class="sortiere-" style="white-space: nowrap; text-align: center;" title=""><?php echo $DR_TEXT['MODIFIED_WHEN'];?></th>
                <th style=""></th>
                <th class="sortierbar" title=""><?php echo $TEXT['ACTIVE']; ?></th>
                <th ></th>
            </tr>
        </thead>
        <tbody>
<?php
    while($aDroplets = $oDroplets->fetchRow(MYSQLI_ASSOC))
    {
        $aComment =  array();
//        $aDroplets['code'] = 'return false;';
        $modified_user = $TEXT['UNKNOWN'];
        $modified_userid = 0;
        $sql = 'SELECT `display_name`,`username`, `user_id` FROM `'.TABLE_PREFIX.'users` '
        .'WHERE `user_id` = '.$aDroplets['modified_by'];
        $get_modified_user = $oDb->query($sql);
        if($get_modified_user->numRows() > 0) {
            $fetch_modified_user = $get_modified_user->fetchRow(MYSQLI_ASSOC);
            $modified_user = $fetch_modified_user['username'];
            $modified_userid = $fetch_modified_user['user_id'];
        }
        $sDropletName  =  mb_strlen($aDroplets['name']) > 15 ? mb_substr($aDroplets['name'], 0, 14).'…' : $aDroplets['name'];
        $sDropletDescription  =  mb_strlen($aDroplets['description']) > 120 ? mb_substr($aDroplets['description'], 0, 119).'…' : $aDroplets['description'];
        $iDropletIdKey = $aDroplets['id'];
        $iDropletIdKey = $oApp->getIDKEY($aDroplets['id']);
        $comments = '';
//        $comments = str_replace(array("\r\n", "\n", "\r"), '<br >', $aDroplets['comments']);
        if (!strpos($comments,"[[")) $comments = "Use: [[".$aDroplets['name']."]]<br />".$comments;
        $comments = str_replace(array("[[", "]]"), array('<b>[[',']]</b>'), $comments);
        $valid_code = true;
        $valid_code = check_syntax($aDroplets['code']);
        if (!$valid_code === true) $comments = '<span color=\'red\'><strong>'.$DR_TEXT['INVALIDCODE'].'</strong></span><br />'.$comments;
        $unique_droplet = true;
        if ($unique_droplet === false ) {$comments = '<span color=\'red\'><strong>'.$DR_TEXT['NOTUNIQUE'].'</strong></span><br />'.$comments;}
//        $comments = '<span>'.$comments.'</span>';
?><tr >
            <td >
               <input type="checkbox" name="cb[<?php echo $aDroplets['id']; ?>]" id="L<?php echo $aDroplets['id']; ?>cb" value="<?php echo $aDroplets['name']; ?>" />
            </td>
            <td >
                <button name="command" type="submit" class="noButton" value="copy_droplet?droplet_id=<?php echo $iDropletIdKey; ?>" title="">
                    <img src="<?php echo $sAddonThemeUrl; ?>/img/copy_24.png" alt="" />
                </button>
            </td>
            <td style="cursor: pointer;">
                <button name="command" type="submit" class="noButton" value="modify_droplet?droplet_id=<?php echo $iDropletIdKey; ?>" title="">
                    <img src="<?php echo $sAddonThemeUrl; ?>/img/modify_24.png"  alt="Modify" />
                </button>
            </td>
            <td style="cursor: pointer;">
                <button name="command" type="submit" class="noButton" value="modify_droplet?droplet_id=<?php echo $iDropletIdKey; ?>">
                        <?php if ($valid_code && $unique_droplet) { ?><img src="<?php echo $sAddonThemeUrl; ?>/img/droplet.png" alt=""/>
                        <?php } else {  ?><img src="<?php echo $sAddonThemeUrl; ?>/img/invalid.gif"  alt=""/><?php }  ?>
                </button>
            </td>
            <td onmouseover="TagToTip('tooltip_<?php echo $aDroplets['id']; ?>', BGCOLOR, '#F2F0A3', BALLOON, true, FONTSIZE, '10pt', HEIGHT,'0', BALLOONIMGPATH, '<?php echo $sAddonThemeUrl; ?>/img/tip_balloon/', BALLOONIMGEXT, 'gif' )" onmouseout="UnTip()">
                <button  class=" noButton" name="command" type="submit" class="noButton" value="modify_droplet?droplet_id=<?php echo $iDropletIdKey; ?>">
                    <?php echo $sDropletName; ?>
                <span id="tooltip_<?php echo $aDroplets['id']; ?>"><?php echo trim($comments); ?></span></button>
            </td>
            <td onmouseover="TagToTip('tooltip_<?php echo $aDroplets['id']; ?>', BGCOLOR, '#F2F0A3', BALLOON, true, FONTSIZE, '10pt', BALLOONIMGPATH, '<?php echo $sAddonThemeUrl; ?>/img/tip_balloon/', BALLOONIMGEXT, 'gif' )" onmouseout="UnTip()">
                <?php echo $sDropletDescription; ?>
            </td>
            <td style="white-space: nowrap; text-align: center;">
                <b><?php echo date('d.m.Y'.' '.'H:i', $aDroplets['modified_when']+TIMEZONE) ?></b>
            </td>
            <td style="cursor: pointer; text-align: center;">
                <button name="command" type="submit" class="noButton" style="width: auto;" value="rename_droplet?droplet_id=<?php echo $iDropletIdKey; ?>" title="<?php echo $aDroplets['id']; ?>">
                    <img src="<?php echo $sAddonThemeUrl; ?>/img/rename_24.png" alt="X" />
                </button>
            </td>
            <td style="text-align: center;"> 
                <?php 
                if($aDroplets['active'] == 1){$ActiveIcon = 'status_1_1';} else {$ActiveIcon = 'status_1_0';}
                ?>
                <button name="command" type="submit" class="noButton" style="width: auto;" value="ToggleStatus?droplet_id=<?php echo $iDropletIdKey; ?>" title="<?php echo $aDroplets['id']; ?>">
                    <img src="<?php echo $sAddonThemeUrl; ?>/img/<?php echo $ActiveIcon; ?>.png" alt="X" />
                </button>
            </td>
            <td style="cursor: pointer;">
                <button name="command" type="submit" class="noButton" style="width: auto;" value="delete_droplet?droplet_id=<?php echo $iDropletIdKey; ?>" title="<?php echo $aDroplets['id']; ?>">
                    <img src="<?php echo $sAddonThemeUrl; ?>/img/delete_24.png" alt="X" />
                </button>
            </td>
        </tr>
<?php 
}
?>
      <tr>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
      </tr>
      </tbody>
    </table>
<?php
}
#onmouseover="TagToTip('tooltip_{DropletId}', BGCOLOR, '#F2F0A3', BALLOON, true, FONTSIZE, '10pt', HEIGHT,'0', BALLOONIMGPATH, '{sAddonThemeUrl}/img/tip_balloon/', BALLOONIMGEXT, 'gif' )" onmouseout="UnTip()"
function toolTip($id, $sText,$sTitle)
{
    $retVal  = 'onmouseover="return TagToTip(';
    $retVal .= 'tooltip_'.$id.',';
    $retVal .= 'FGCOLOR,\'#F2F0A3\',';
    $retVal .= 'BALLOON,true,';
    $retVal .= 'FONTSIZE,\'10pt\',';
    $retVal .= 'HEIGHT,\'0\'';
    $retVal .= 'BALLOONIMGPATH,\'{sAddonThemeUrl}/img/tip_balloon/\',';
    $retVal .= 'BALLOONIMGEXT, \'gif\'';
    $retVal .= ')" onmouseout="return UnTip()"';
/*
    $retVal .= '\''.$sText.'\',';
    $retVal .= 'CAPTION,\''.$sTitle.'\',';
    $retVal .= 'BGCOLOR,\'#557c9e\',';
    $retVal .= 'BORDER,1,';
//    $retVal .= 'WIDTH,';
//    $retVal .= 'STICKY,';
    $retVal .= 'CAPTIONSIZE,\'13px\',';
    $retVal .= 'CLOSETEXT,\'X\',';
    $retVal .= 'CLOSESIZE,\'14px\',';
    $retVal .= 'CLOSECOLOR,\'#ffffff\',';
    $retVal .= 'TEXTSIZE,\'12px\',';
    $retVal .= 'VAUTO,';
    $retVal .= 'HAUTO,';
//    $retVal .= 'MOUSEOFF,';
    $retVal .= 'WRAP,';
    $retVal .= 'CELLPAD,5';
    $retVal .= ')" onmouseout="return nd()"';
*/
//    $retVal .= '';
    return $retVal;
}

?>
</form><!-- droplets_form -->
</div><!-- droplets -->
<script src="<?php echo $sAddonThemeUrl; ?>/js/TableSort.js"></script>
<script src="<?php echo $sAddonThemeUrl; ?>/js/custom-file-input.js"></script>
