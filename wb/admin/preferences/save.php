<?php
/**
 *
 * @category        admin
 * @package         preferences
 * @author          WebsiteBaker Project
 * @copyright       Ryan Djurovich
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: save.php 5 2015-04-27 08:02:19Z luisehahne $
 * @filesource      $HeadURL: https://localhost:8443/svn/wb283Sp4/SP4/branches/wb/admin/preferences/save.php $
 * @lastmodified    $Date: 2015-04-27 10:02:19 +0200 (Mo, 27. Apr 2015) $
 *
 */


// Print admin header

if ( !defined( 'WB_PATH' ) ){ require( dirname(dirname((__DIR__))).'/config.php' ); }
if ( !class_exists('admin', false) ) { require(WB_PATH.'/framework/class.admin.php'); }
// suppress to print the header, so no new FTAN will be set
$admin = new admin('Preferences','start', false);

function save_preferences( $admin, $database)
{
    global $MESSAGE;
    $err_msg = array();
    $iMinPassLength = 6;
    $bPassRequest = false;
    $bMailHasChanged = false;
// first check form-tan
    if(!$admin->checkFTAN()){ $err_msg[] = $MESSAGE['GENERIC_SECURITY_ACCESS']; }
    $sLanguagesAddonDefaultFile = WB_PATH.'/account/languages/EN.php';
    if (is_readable($sLanguagesAddonDefaultFile)){include $sLanguagesAddonDefaultFile;}
    $sLanguagesAddonFile = WB_PATH.'/account/languages/'.LANGUAGE.'.php';
    if (is_readable($sLanguagesAddonFile)){include $sLanguagesAddonFile;}
// Get entered values and validate all
    // remove any dangerouse chars from display_name
//    $display_name     = $admin->add_slashes(strip_tags(trim($admin->get_post('display_name'))));
    $display_name = strip_tags( $admin->StripCodeFromText($admin->get_post('display_name')));
    $display_name     = ( $display_name == '' ? $admin->get_display_name() : $display_name );
    // check that display_name is unique in whoole system (prevents from User-faking)
    $sql  = 'SELECT COUNT(*) FROM `'.TABLE_PREFIX.'users` ';
    $sql .= 'WHERE `user_id` <> '.(int)$admin->get_user_id().' AND `display_name` LIKE "'.$display_name.'"';
    if( $database->get_one($sql) > 0 ){ $err_msg[] = ( @$MESSAGE['USERS_DISPLAYNAME_TAKEN']?:$MESSAGE['MEDIA_BLANK_NAME'].' ('.$TEXT['DISPLAY_NAME'].')'); }
// language must be 2 upercase letters only
    $language         = strtoupper($admin->get_post('language'));
    $language         = (preg_match('/^[A-Z]{2}$/', $language) ? $language : DEFAULT_LANGUAGE);
    $user_time = true;
// timezone must be between -12 and +13  or -20 as system_default
    $timezone         = $admin->get_post('timezone');
    $timezone         = (is_numeric($timezone) ? $timezone : DEFAULT_TIMEZONE/3600);
    $timezone         = (($timezone >= -12 && $timezone <= 13) ? $timezone : DEFAULT_TIMEZONE/3600) * 3600;

// date_format must be a key from /interface/date_formats
    $date_format      = $admin->get_post('date_format');
    $date_format_key  = str_replace(' ', '|', $date_format);
    include( ADMIN_PATH.'/interface/date_formats.php' );
    $date_format = (array_key_exists($date_format_key, $DATE_FORMATS) ? $date_format : 'system_default');
    $date_format = ($date_format == 'system_default' ? '' : $date_format);
    unset($DATE_FORMATS);
// time_format must be a key from /interface/time_formats
    $time_format      = $admin->get_post('time_format');
    $time_format_key  = str_replace(' ', '|', $time_format);
    $user_time = true;
    include( ADMIN_PATH.'/interface/time_formats.php' );
    $time_format = (array_key_exists($time_format_key, $TIME_FORMATS) ? $time_format : 'system_default');
    $time_format = ($time_format == 'system_default' ? '' : $time_format);
    unset($TIME_FORMATS);

// email should be validatet by core
    $email = trim( $admin->get_post('email') == null ? '' : $admin->get_post('email') );
    if( (!$admin->validate_email($email)) )
    {
        $email = '';
        $err_msg[] = $MESSAGE['USERS_INVALID_EMAIL'];
    }else {
        if($email != '') {
            $sql = 'SELECT `email` FROM `'.TABLE_PREFIX.'users` '
                 . 'WHERE `user_id` = '.(int)$admin->get_user_id().' AND `email` LIKE \''.$email.'\'';
            $IsOldMail = $database->get_one($sql);
        // check that email is unique in whoole system
            $email = $admin->add_slashes($email);
            $sql = 'SELECT `email` FROM `'.TABLE_PREFIX.'users` '
                 . 'WHERE `user_id` <> '.(int)$admin->get_user_id().' AND `email` LIKE \''.$email.'\'';
            $checkMail = $database->get_one($sql);

            if( $checkMail == $email ){ $err_msg[] = $MESSAGE['USERS_EMAIL_TAKEN']; }
            $bMailHasChanged = ($email != $IsOldMail);
        }
    }
// receive password vars and calculate needed action
    $sCurrentPassword = $admin->get_post('current_password');
    $sCurrentPassword = (is_null($sCurrentPassword) ? '' : $sCurrentPassword);
    $sNewPassword = $admin->get_post('new_password_1');
    $sNewPassword = (is_null($sNewPassword) ? '' : $sNewPassword);
    $sNewPasswordRetyped = $admin->get_post('new_password_2');
    $sNewPasswordRetyped= (is_null($sNewPasswordRetyped) ? '' : $sNewPasswordRetyped);

    if($bMailHasChanged == true)
    {
        $bPassRequest = $bMailHasChanged;
    } else {
        $bPassRequest = ( ( $sCurrentPassword != '') || ($sNewPassword != '') || ($sNewPasswordRetyped != '') ) ? true : false;
    }

// Check existing password
    $sql  = 'SELECT `password` ';
    $sql .= 'FROM `'.TABLE_PREFIX.'users` ';
    $sql .= 'WHERE `user_id` = '.$admin->get_user_id();
    if ( $bPassRequest && md5($sCurrentPassword) != $database->get_one($sql)) {
// access denied
        $err_msg[] = $MESSAGE['PREFERENCES_CURRENT_PASSWORD_INCORRECT'];
    }else {
// validate new password
        $sPwHashNew = false;
        if($sNewPassword != '') {
            if(strlen($sNewPassword) < $iMinPassLength) {
                $err_msg[] = $MESSAGE['USERS_PASSWORD_TOO_SHORT'];
            }else {
                if($sNewPassword != $sNewPasswordRetyped) {
                    $err_msg[] = $MESSAGE['USERS_PASSWORD_MISMATCH'];
                }else {
                    $pattern = '/[^'.$admin->password_chars.']/';
                    if (preg_match($pattern, $sNewPassword)) {
                        $err_msg[] = $MESSAGE['PREFERENCES_INVALID_CHARS'];
                    }else {
                        $sPwHashNew = md5($sNewPassword);
                    }
                }
            }
        }
// if no validation errors, try to update the database, otherwise return errormessages
        if(sizeof($err_msg) == 0)
        {
            $sql  = 'UPDATE `'.TABLE_PREFIX.'users` ';
            $sql .= 'SET `display_name`=\''.$database->escapeString($display_name).'\', ';
            if($sPwHashNew) {
                $sql .=     '`password`=\''.$database->escapeString($sPwHashNew).'\', ';
            }
            if($email != '') {
                $sql .=     '`email`=\''.$database->escapeString($email).'\', ';
            }
            $sql .= '`language`=\''.$database->escapeString($language).'\', '
                  . '`timezone`=\''.$database->escapeString($timezone).'\', '
                  . '`date_format`=\''.$database->escapeString($date_format).'\', '
                  . '`time_format`=\''.$database->escapeString($time_format).'\' '
                  . 'WHERE `user_id`='.(int)$admin->get_user_id();
            if( $database->query($sql) )
            {
                // update successfull, takeover values into the session
                $_SESSION['DISPLAY_NAME'] = $display_name;
                $_SESSION['LANGUAGE'] = $language;
                $_SESSION['TIMEZONE'] = $timezone;
                $_SESSION['EMAIL'] = $email;
                // Update date format
                if($date_format != '') {
                    $_SESSION['DATE_FORMAT'] = $date_format;
                    if(isset($_SESSION['USE_DEFAULT_DATE_FORMAT'])) { unset($_SESSION['USE_DEFAULT_DATE_FORMAT']); }
                } else {
                    $_SESSION['USE_DEFAULT_DATE_FORMAT'] = true;
                    if(isset($_SESSION['DATE_FORMAT'])) { unset($_SESSION['DATE_FORMAT']); }
                }
                // Update time format
                if($time_format != '') {
                    $_SESSION['TIME_FORMAT'] = $time_format;
                    if(isset($_SESSION['USE_DEFAULT_TIME_FORMAT'])) { unset($_SESSION['USE_DEFAULT_TIME_FORMAT']); }
                } else {
                    $_SESSION['USE_DEFAULT_TIME_FORMAT'] = true;
                    if(isset($_SESSION['TIME_FORMAT'])) { unset($_SESSION['TIME_FORMAT']); }
                }
            }else {
                $err_msg[] = 'invalid database UPDATE call in '.__FILE__.'::'.__FUNCTION__.'before line '.__LINE__;
            }
        }
    }
    return ( (sizeof($err_msg) > 0) ? implode('<br />', $err_msg) : '' );
}
$retval = save_preferences($admin, $database);
if( $retval == '')
{
    // print the header
    $admin->print_header();
    $admin->print_success($MESSAGE['PREFERENCES_DETAILS_SAVED']);
    $admin->print_footer();
}else {
    // print the header
    $admin->print_header();
    $admin->print_error($retval);
}
