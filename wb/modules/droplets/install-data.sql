-- phpMyAdmin SQL Dump
-- version 4.5.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 01. Feb 2016 um 19:55
-- Server-Version: 5.6.24
-- PHP-Version: 7.0.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Daten für Tabelle `{TABLE_PREFIX}mod_droplets`
--

INSERT INTO `{TABLE_PREFIX}mod_droplets` (`id`, `name`, `code`, `description`, `modified_when`, `modified_by`, `active`, `admin_edit`, `admin_view`, `show_wysiwyg`, `comments`) VALUES
(1, 'EmailFilter', 'return \'\';\n', 'Emailfiltering on your output - dummy Droplet', 1453286422, 1, 1, 0, 0, 0, 'usage:  [[EmailFilter]]\n'),
(2, 'LoginBox', 'global $database,$wb,$page_id,$TEXT, $MENU, $HEADING;\r\n$return_value = \'<div class="login-box">\'."\\n";\r\n$return_admin = \' \';\r\n// Return a system permission\r\n$get_permission = function ($name, $type = \'system\') use ( $wb )\r\n{\r\n// Append to permission type\r\n$type .= \'_permissions\';\r\n// Check if we have a section to check for\r\nif($name == \'start\') {\r\nreturn true;\r\n} else {\r\n// Set system permissions var\r\n$system_permissions = $wb->get_session(\'SYSTEM_PERMISSIONS\');\r\n// Set module permissions var\r\n$module_permissions = $wb->get_session(\'MODULE_PERMISSIONS\');\r\n// Set template permissions var\r\n$template_permissions = $wb->get_session(\'TEMPLATE_PERMISSIONS\');\r\n// Return true if system perm = 1\r\nif (isset($$type) && is_array($$type) && is_numeric(array_search($name, $$type))) {\r\nif($type == \'system_permissions\') {\r\nreturn true;\r\n} else {\r\nreturn false;\r\n}\r\n} else {\r\nif($type == \'system_permissions\') {\r\nreturn false;\r\n} else {\r\nreturn true;\r\n}\r\n}\r\n}\r\n};\r\n$get_page_permission = function ($page, $action=\'admin\') use ( $database, $wb )\r\n{\r\nif ($action!=\'viewing\'){ $action=\'admin\';}\r\n$action_groups=$action.\'_groups\';\r\n$action_users=$action.\'_users\';\r\nif (is_array($page)) {\r\n$groups=$page[$action_groups];\r\n$users=$page[$action_users];\r\n} else {\r\n$sql = \'SELECT \'.$action_groups.\',\'.$action_users.\' FROM \'.TABLE_PREFIX.\'pages \'\r\n. \'WHERE page_id = \\\'\'.$page.\'\\\'\';\r\n$results = $database->query( $sql );\r\n$result = $results->fetchRow( MYSQLI_ASSOC );\r\n$groups = explode(\',\', str_replace(\'_\', \'\', $result[$action_groups]));\r\n$users = explode(\',\', str_replace(\'_\', \'\', $result[$action_users]));\r\n}\r\n$in_group = FALSE;\r\nforeach($wb->get_groups_id() as $cur_gid){\r\nif (in_array($cur_gid, $groups)) {\r\n$in_group = TRUE;\r\n}\r\n}\r\nif((!$in_group) AND !is_numeric(array_search($wb->get_user_id(), $users))) {\r\nreturn false;\r\n}\r\nreturn true;\r\n};\r\n// Get redirect\r\n$redirect_url = ((isset($_SESSION[\'HTTP_REFERER\']) && $_SESSION[\'HTTP_REFERER\'] != \'\') ? $_SESSION[\'HTTP_REFERER\'] : WB_URL );\r\n$redirect_url = (isset($redirect) && ($redirect!=\'\') ? $redirect : $redirect_url);\r\nif ( ( FRONTEND_LOGIN == \'enabled\') &&\r\n( VISIBILITY != \'private\') &&\r\n( $wb->get_session(\'USER_ID\') == \'\')  )\r\n{\r\n$return_value .= \'<form action="\'.LOGIN_URL.\'" method="post">\'."\\n";\r\n$return_value .= \'<input type="hidden" name="redirect" value="\'.$redirect_url.\'" />\'."\\n";\r\n$return_value .= \'<fieldset>\'."\\n";\r\n$return_value .= \'<h1>\'.$TEXT[\'LOGIN\'].\'</h1>\'."\\n";\r\n$return_value .= \'<label for="username">\'.$TEXT[\'USERNAME\'].\':</label>\'."\\n";\r\n$return_value .= \'<p><input type="text" name="username" id="username"  /></p>\'."\\n";\r\n$return_value .= \'<label for="password">\'.$TEXT[\'PASSWORD\'].\':</label>\'."\\n";\r\n$return_value .= \'<p><input type="password" name="password" id="password"/></p>\'."\\n";\r\n$return_value .= \'<p><input type="submit" id="submit" value="\'.$TEXT[\'LOGIN\'].\'" class="dbutton" /></p>\'."\\n";\r\n$return_value .= \'<ul class="login-advance">\'."\\n";\r\n$return_value .= \'<li class="forgot"><a href="\'.FORGOT_URL.\'"><span>\'.$TEXT[\'FORGOT_DETAILS\'].\'</span></a></li>\'."\\n";\r\nif (intval(FRONTEND_SIGNUP) > 0)\r\n{\r\n$return_value .= \'<li class="sign"><a href="\'.SIGNUP_URL.\'">\'.$TEXT[\'SIGNUP\'].\'</a></li>\'."\\n";\r\n}\r\n$return_value .= \'</ul>\'."\\n";\r\n$return_value .= \'</fieldset>\'."\\n";\r\n$return_value .= \'</form>\'."\\n";\r\n} elseif( (FRONTEND_LOGIN == \'enabled\') &&\r\n(is_numeric($wb->get_session(\'USER_ID\'))) )\r\n{\r\n$return_value .= \'<form action="\'.LOGOUT_URL.\'" method="post" class="login-table">\'."\\n";\r\n$return_value .= \'<fieldset>\'."\\n";\r\n$return_value .= \'<h1>\'.$TEXT["LOGGED_IN"].\'</h1>\'."\\n";\r\n$return_value .= \'<label>\'.$TEXT[\'WELCOME_BACK\'].\', \'.$wb->get_display_name().\'</label>\'."\\n";\r\n$return_value .= \'<p><input type="submit" name="submit" value="\'.$MENU[\'LOGOUT\'].\'" class="dbutton" /></p>\'."\\n";\r\n$return_value .= \'<ul class="logout-advance">\'."\\n";\r\n$return_value .= \'<li class="preference"><a href="\'.PREFERENCES_URL.\'" title="\'.$MENU[\'PREFERENCES\'].\'">\'.$MENU[\'PREFERENCES\'].\'</a></li>\'."\\n";\r\nif ($wb->ami_group_member(\'1\'))  //change ot the group that should get special links\r\n{\r\n$return_admin .= \'<li class="admin"><a target="_blank" href="\'.ADMIN_URL.\'/index.php" title="\'.$TEXT[\'ADMINISTRATION\'].\'" class="blank_target">\'.$TEXT["ADMINISTRATION"].\'</a></li>\'."\\n";\r\n//you can add more links for your users like userpage, lastchangedpages or something\r\n$return_value .= $return_admin;\r\n}\r\n//change ot the group that should get special links\r\nif( $get_permission(\'pages_modify\') && $get_page_permission( PAGE_ID ) )\r\n{\r\n$return_value .= \'<li class="modify"><a target="_blank" href="\'.ADMIN_URL.\'/pages/modify.php?page_id=\'.PAGE_ID.\'" title="\'.$HEADING[\'MODIFY_PAGE\'].\'" class="blank_target">\'.$HEADING[\'MODIFY_PAGE\'].\'</a></li>\'."\\n";\r\n}\r\n$return_value .= \'</ul>\'."\\n";\r\n$return_value .= \'</fieldset>\'."\\n";\r\n$return_value .= \'</form>\'."\\n";\r\n}\r\n$return_value .= \'</div>\'."\\n";\r\nreturn $return_value;\r\n', 'Puts a Login / Logout box on your page.', 1453286576, 1, 1, 0, 0, 0, 'Use: [[LoginBox?redirect=url]]\r\nAbsolute or relative url possible\r\nRemember to enable frontend login in your website settings!!'),
(3, 'Lorem', '$lorem = array();\n$lorem[] = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Ut odio. Nam sed est. Nam a risus et est iaculis adipiscing. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Integer ut justo. In tincidunt viverra nisl. Donec dictum malesuada magna. Curabitur id nibh auctor tellus adipiscing pharetra. Fusce vel justo non orci semper feugiat. Cras eu leo at purus ultrices tristique.<br /><br />";\n$lorem[] = "Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.<br /><br />";\n$lorem[] = "Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.<br /><br />";\n$lorem[] = "Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.<br /><br />";\n$lorem[] = "Cras consequat magna ac tellus. Duis sed metus sit amet nunc faucibus blandit. Fusce tempus cursus urna. Sed bibendum, dolor et volutpat nonummy, wisi justo convallis neque, eu feugiat leo ligula nec quam. Nulla in mi. Integer ac mauris vel ligula laoreet tristique. Nunc eget tortor in diam rhoncus vehicula. Nulla quis mi. Fusce porta fringilla mauris. Vestibulum sed dolor. Aliquam tincidunt interdum arcu. Vestibulum eget lacus. Curabitur pellentesque egestas lectus. Duis dolor. Aliquam erat volutpat. Aliquam erat volutpat. Duis egestas rhoncus dui. Sed iaculis, metus et mollis tincidunt, mauris dolor ornare odio, in cursus justo felis sit amet arcu. Aenean sollicitudin. Duis lectus leo, eleifend mollis, consequat ut, venenatis at, ante.<br /><br />";\n$lorem[] = "Consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.<br /><br />";\nif (!isset($blocks)) $blocks=1;\n$blocks = (int)$blocks - 1;\nif ($blocks <= 0) $blocks = 0;\nif ($blocks > 5) $blocks = 5;\n$returnvalue = "";\nfor ( $i=0 ; $i<=$blocks ; $i++) {\n$returnvalue .= $lorem[$i];\n}\nreturn $returnvalue;\n', 'Create Lorum Ipsum text', 1453286422, 1, 1, 0, 0, 0, 'Use: [[Lorem?blocks=6]] (max 6 paragraphs)\n'),
(4, 'ModifiedWhen', 'global $database, $wb;\nif (PAGE_ID>0) {\n$query=$database->query("SELECT modified_when FROM ".TABLE_PREFIX."pages where page_id=".PAGE_ID);\n$mod_details=$query->fetchRow();\nreturn "This page was last modified on ".date("d/m/Y",$mod_details[0]). " at ".date("H:i",$mod_details[0]).".";\n}\n', 'Displays the last modification time of the current page', 1453286422, 1, 1, 0, 0, 0, 'Use [[ModifiedWhen]]\n'),
(5, 'NextPage', '$info = show_menu2(0, SM2_CURR, SM2_START, SM2_ALL|SM2_BUFFER, \'[if(class==menu-current){[level] [sib] [sibCount] [parent]}]\', \'\', \'\', \'\');\nlist($nLevel, $nSib, $nSibCount, $nParent) = explode(\' \', $info);\n// show next\n$nxt = $nSib < $nSibCount ? $nSib + 1 : 0;\nif ($nxt > 0) {\nreturn show_menu2(0, SM2_CURR, SM2_START, SM2_ALL|SM2_BUFFER,    "[if(sib==$nxt){&gt;&gt; [a][menu_title]</a>}]", \'\', \'\', \'\');\n}\nelse return \'(no next)\';\n', 'Create a next link to your page', 1453286422, 1, 1, 0, 0, 0, 'Display a link to the next page on the same menu level\n'),
(6, 'Oneliner', '$line = file (dirname(__FILE__)."/example/oneliners.txt");\nshuffle($line);\nreturn $line[0];\n', 'Create a random oneliner on your page', 1453286422, 1, 1, 0, 0, 0, 'Use: [[OneLiner]].\nThe file with the oneliner data is located in /modules/droplets/example/oneliners.txt;\n'),
(7, 'ParentPage', '$info = show_menu2(0, SM2_CURR, SM2_START, SM2_ALL|SM2_BUFFER, \'[if(class==menu-current){[level] [sib] [sibCount] [parent]}]\', \'\', \'\', \'\');\nlist($nLevel, $nSib, $nSibCount, $nParent) = explode(\' \', $info);\n// show up level\nif ($nLevel > 0) {\n$lev = $nLevel - 1;\nreturn show_menu2(0, SM2_ROOT, SM2_CURR, SM2_CRUMB|SM2_BUFFER, "[if(level==$lev){[a][menu_title]</a>}]", \'\', \'\', \'\');\n}\nelse\nreturn \'(no parent)\';\n', 'Create a parent link to your page', 1453286422, 1, 1, 0, 0, 0, 'Display a link to the parent page of the current page\n'),
(8, 'PreviousPage', '$info = show_menu2(0, SM2_CURR, SM2_START, SM2_ALL|SM2_BUFFER, \'[if(class==menu-current){[level] [sib] [sibCount] [parent]}]\', \'\', \'\', \'\');\nlist($nLevel, $nSib, $nSibCount, $nParent) = explode(\' \', $info);\n// show previous\n$prv = $nSib > 1 ? $nSib - 1 : 0;\nif ($prv > 0) {\nreturn show_menu2(0, SM2_CURR, SM2_START, SM2_ALL|SM2_BUFFER, "[if(sib==$prv){[a][menu_title]</a> <<}]", \'\', \'\', \'\');\n}\nelse\nreturn \'(no previous)\';\n', 'Create a previous link to your page', 1453286422, 1, 1, 0, 0, 0, 'Display a link to the previous page on the same menu level\n'),
(9, 'RandomImage', '$dir = ( (isset($dir) && ($dir!=\'\') ) ? $dir : \'\');\n$folder=opendir(WB_PATH.MEDIA_DIRECTORY.\'/\'.$dir.\'/.\');\n$names = array();\nwhile ($file = readdir($folder))  {\n$ext=strtolower(substr($file,-4));\nif ($ext==".jpg"||$ext==".gif"||$ext==".png"){\n$names[count($names)] = $file;\n}\n}\nclosedir($folder);\nshuffle($names);\n$image=$names[0];\n$name=substr($image,0,-4);\nreturn \'<img src="\'.WB_URL.MEDIA_DIRECTORY.\'/\'.$dir.\'/\'.$image.\'" alt="\'.$name.\'" width="95%" />\';\n', 'Get a random image from a folder in the MEDIA folder.', 1453286422, 1, 1, 0, 0, 0, 'Commandline to use: [[RandomImage?dir=subfolder_in_mediafolder]]\n'),
(10, 'SearchBox', 'global $TEXT;\n$return_value = true;\nif (!isset($msg)) $msg=\'search this site..\';\n$j = "onfocus=\\"if(this.value==\'$msg\'){this.value=\'\';this.style.color=\'#000\';}else{this.select();}\\"\nonblur=\\"if(this.value==\'\'){this.value=\'$msg\';this.style.color=\'#b3b3b3\';}\\"";\nif(SHOW_SEARCH) {\n$return_value  = \'<div class="searchbox">\';\n$return_value  .= \'<form action="\'.WB_URL.\'/search/index\'.PAGE_EXTENSION.\'" method="get" name="search" class="searchform" id="search">\';\n$return_value  .= \'<input style="color:#b3b3b3;" type="text" name="string" size="25" class="textbox" value="\'.$msg.\'" \'.$j.\'  />&nbsp;\';\n$return_value  .= \'</form>\';\n$return_value  .= \'</div>\';\n}\nreturn $return_value;\n', 'Create a searchbox on the position', 1453286422, 1, 1, 0, 0, 0, 'usage: [[searchbox]].\nOptional parameter "?msg=the search message"\n'),
(11, 'SectionPicker', 'global $database, $wb, $TEXT, $DGTEXT,$section_id,$page_id;\n$content = \'\';\n$sid = isset($sid) ? intval($sid) : 0;\nif( $sid ) {\n$oldSid = $section_id; // save old sectionID\n$sql  = \'SELECT `module` FROM `\'.TABLE_PREFIX.\'sections` \';\n$sql .= \'WHERE `section_id`=\'.$sid;\nif (($module = $database->get_one($sql))) {\nif (is_readable(WB_PATH.\'/modules/\'.$module.\'/view.php\')) {\n$_sFrontendCss = \'/modules/\'.$module.\'/frontend.css\';\nif(is_readable(WB_PATH.$_sFrontendCss)) {\n$_sSearch = preg_quote(WB_URL.\'/modules/\'.$module.\'/frontend.css\', \'/\');\nif(preg_match(\'/<link[^>]*?href\\s*=\\s*\\"\'.$_sSearch.\'\\".*?\\/>/si\', $wb_page_data)) {\n$_sFrontendCss = \'\';\n}else {\n$_sFrontendCss = \'<link href="\'.WB_URL.$_sFrontendCss.\'" rel="stylesheet" type="text/css" media="screen" />\';\n}\n} else { $_sFrontendCss = \'\'; }\n$section_id = $sid;\nob_start();\nrequire(WB_PATH.\'/modules/\'.$module.\'/view.php\');\n$content = $_sFrontendCss.ob_get_clean();\n$section_id = $oldSid; // restore old sectionID\n}\n}\n}\nreturn $content;\n', 'Load the view.php from any other section-module', 1453286422, 1, 1, 0, 0, 0, 'Use [[SectionPicker?sid=123]]\n'),
(12, 'ShowRandomWysiwyg', 'global $database;\n$content = \'\';\nif (isset($section)) {\nif( preg_match(\'/^[0-9]+(?:\\s*[\\,\\|\\-\\;\\:\\+\\#\\/]\\s*[0-9]+\\s*)*$/\', $section)) {\nif (is_readable(WB_PATH.\'/modules/wysiwyg/view.php\')) {\n// if valid arguments given and module wysiwyg is installed\n// split and sanitize arguments\n$aSections = preg_split(\'/[\\s\\,\\|\\-\\;\\:\\+\\#\\/]+/\', $section);\n$section_id = $aSections[array_rand($aSections)]; // get random element\nob_start(); // generate output by wysiwyg module\nrequire(WB_PATH.\'/modules/wysiwyg/view.php\');\n$content = ob_get_clean();\n}\n}\n}\nreturn $content;\n', 'Randomly display one WYSIWYG section from a given list', 1453286422, 1, 1, 0, 0, 0, 'Use [[ShowRandomWysiwyg?section=10,12,15,20]]\npossible Delimiters: [ ,;:|-+#/ ]\n'),
(13, 'ShowWysiwyg', 'global $database, $section_id, $module;\n$content = \'\';\n$section = isset($section) ? intval($section) : 0;\nif ($section) {\nif (is_readable(WB_PATH.\'/modules/wysiwyg/view.php\')) {\n// if valid section is given and module wysiwyg is installed\n$iOldSectionId = intval($section_id); // save old SectionID\n$section_id = $section;\nob_start(); // generate output by regulary wysiwyg module\nrequire(WB_PATH.\'/modules/wysiwyg/view.php\');\n$content = ob_get_clean();\n$section_id = $iOldSectionId; // restore old SectionId\n}\n}\nreturn $content;\n', 'Display one defined WYSIWYG section', 1453286422, 1, 1, 0, 0, 0, 'Use [[ShowWysiwyg?section=10]]\n'),
(14, 'SiteModified', 'global $database, $wb;\nif (PAGE_ID>0) {\n$query=$database->query("SELECT max(modified_when) FROM ".TABLE_PREFIX."pages");\n$mod_details=$query->fetchRow();\nreturn "This site was last modified on ".date("d/m/Y",$mod_details[0]). " at ".date("H:i",$mod_details[0]).".";\n}\n', 'Create information on when your site was last updated.', 1453286422, 1, 1, 0, 0, 0, 'Create information on when your site was last updated. Any page update counts.\n'),
(15, 'Skype', '$content=\'<div class="popup">Check skypename!</div>\';\n$user = (isset($user) && ($user!=\'\') ? $user : \'\');\nif($user==\'\') { return $content; }\nreturn \'<div class="popup"><img src="http://mystatus.skype.com/\'.$user.\'.png?t=\'.time().\'" alt="My Skype status" /></div>\';\n', 'Your skype status as an image', 1453286422, 1, 1, 0, 0, 0, 'Commandline to use: [[skype?user=skypename]]\n'),
(16, 'Text2Image', '//clean up old files..\n$dir = WB_PATH.\'/temp/\';\n$dp = opendir($dir) or die (\'Could not open \'.$dir);\nwhile ($file = readdir($dp)) {\nif ((preg_match(\'/img_/\',$file)) && (filemtime($dir.$file)) <  (strtotime(\'-10 minutes\'))) {\nunlink($dir.$file);\n}\n}\nclosedir($dp);\n$imgfilename = \'img_\'.rand().\'_\'.time().\'.jpg\';\n//create image\n$padding = 0;\n$font = 3;\n$height = imagefontheight($font) + ($padding * 2);\n$width = imagefontwidth($font) * strlen($text) + ($padding * 2);\n$image_handle = imagecreatetruecolor($width, $height);\n$text_color = imagecolorallocate($image_handle, 0, 0, 0);\n$background_color = imagecolorallocate($image_handle, 255, 255, 255);\n$bg_height = imagesy($image_handle);\n$bg_width = imagesx($image_handle);\nimagefilledrectangle($image_handle, 0, 0, $bg_width, $bg_height, $background_color);\nimagestring($image_handle, $font, $padding, $padding, $text, $text_color);\nimagejpeg($image_handle,WB_PATH.\'/temp/\'.$imgfilename,100);\nimagedestroy($image_handle);\nreturn \'<img src="\'.WB_URL.\'/temp/\'.$imgfilename.\'" style="border:0px;margin:0px;padding:0px;vertical-align:middle;" />\';\n', 'Create an image from the textparameter', 1453286422, 1, 1, 0, 0, 0, 'Use [[text2image?text=The text to create]]\n'),
(17, 'Zitate', '$line = file (dirname(__FILE__)."/example/oneliners.txt");\nshuffle($line);\nreturn $line[0];\n', 'Create a random oneliner on your page', 1453286422, 1, 1, 0, 0, 0, 'Use: [[Zitate]].\nThe file with the oneliner data is located in /modules/droplets/example/oneliners.txt;\n');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
