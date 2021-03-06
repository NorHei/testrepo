<?PHP
header('Content-type: application/javascript');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0, false');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Pragma: no-cache');

/*
    This Plugin read files of a directory and outputs
    a javascript array. Output is:

    var InternPagesSelectBox = new Array(
        new Array( empty, empty ),
        new Array( name, link ),
        new Array( name, link )...
    );

    InternPagesSelectBox will loaded as select options
    to internpage plugin.
*/

// Include the config file
if ( !defined( 'WB_PATH' ) ){ require ( dirname(dirname(dirname(dirname(dirname(__DIR__))))).'/config.php'); }
// > wb283 to use new wblink options
$wb284  = (file_exists(WB_PATH.'/setup.ini.php')) ? true : false;

// Create new admin object
require(WB_PATH.'/framework/class.admin.php');
$admin = new admin('Pages', 'pages_modify', false);

if(!function_exists('cleanup')) {

    function cleanup ($string) {
        global $database;
        // if magic quotes on
        if (get_magic_quotes_gpc())
        {
            $string = stripslashes($string);
        }
        if(isset($database)&&method_exists($database,"escapeString")) {
          return preg_replace("/\r?\n/", "\\n", $database->escapeString($string));
        } elseif (is_object($database->db_handle) && (get_class($database->db_handle) === 'mysqli')){
          return preg_replace("/\r?\n/", "\\n", mysqli_real_escape_string($database->db_handle, $string)); 
        } else {
          return preg_replace("/\r?\n/", "\\n", mysql_real_escape_string($string)); 
        }
   } // end function cleanup
}

 /**
  * setPrettyArray()
  * 
  * @param integer $bLinefeed
  * @param integer $iWhiteSpaces
  * @param integer $iTabs
  * @return string 
  */
 function setPrettyArray($bLinefeed = 1, $iWhiteSpaces = 0, $iTabs = 0) {
   $sRetVal = "";
   if($bLinefeed > 0) {
     $sRetVal .= "\n";
   }
   if($iWhiteSpaces > 0) {
     $sRetVal .= str_repeat(" ", $iWhiteSpaces);
   }
   if($iTabs > 0) {
     $sRetVal .= str_repeat("\t", $iTabs);
   }
   return $sRetVal;
 }

$InternPagesSelectBox = "var InternPagesSelectBox = new Array( ";
$PagesTitleSelectBox = "var PagesTitleSelectBox = new Array( ";


function getPageTree($parent)
{
    global $admin, $database,$InternPagesSelectBox,$PagesTitleSelectBox;
    $sql  = 'SELECT * FROM `'.TABLE_PREFIX.'pages` '
          . 'WHERE `parent`= '.(int)$parent.' AND ' 
          .    '`level`<='.PAGE_LEVEL_LIMIT.' '
          .    ((PAGE_TRASH != 'inline') ?  'AND `visibility` != \'deleted\' ' : ' ')
          . 'ORDER BY `position` ASC';
    if (($resPage = $database->query($sql))) {
        while (!(false == ($page = $resPage->fetchRow( MYSQLI_ASSOC )))) {
            if (!$admin->page_is_visible($page)) { continue; }
            $menu_title = cleanup( $page['menu_title'] );
            $page_title = cleanup( $page['page_title'] );
            $title_prefix = str_repeat(' - ', $page['level']);
            $InternPagesSelectBox .= "new Array( '".$title_prefix.$menu_title."', '[wblink".$page['page_id']."]'), ";
            $PagesTitleSelectBox .= "new Array( '".$page_title."', '[wblink".$page['page_id']."]'), ";
            if($page['level'] < PAGE_LEVEL_LIMIT) {
                getPageTree($page['page_id']);
            }
        }
    }
}
// end of function

getPageTree(0);

$InternPagesSelectBox = substr($InternPagesSelectBox,0,-2);
$PagesTitleSelectBox = substr($PagesTitleSelectBox,0,-2);
echo $InternPagesSelectBox .= " );\n";
echo $PagesTitleSelectBox .= " );\n";

//generate news lists
$wblink_allowed_chars = "/[^ a-zA-Z0-9_äöüÄÖÜß!\"§$%&\/\(\)\[\]=\{\}\?\*#~+-;:,\.\'\`@€|]/";

$NewsItemsSelectBox = "var NewsItemsSelectBox = new Array();";
$ModuleList = "var ModuleList = new Array();";


if( is_readable( WB_PATH.'/modules/news/info.php' ) ) {
  
    $sql = 'SELECT * FROM `'.TABLE_PREFIX.'sections`  WHERE `module` = \'news\' ';
    $newsSections = $database->query($sql);

    while($section = $newsSections->fetchRow(MYSQLI_ASSOC)){
        $news = $database->query("SELECT `title`, `link`, `page_id`, `post_id` FROM `".TABLE_PREFIX."mod_news_posts` WHERE `active` = 1 AND `section_id` = ".$section['section_id']);

        $ModuleList .= "ModuleList[".$section['page_id']."] = 'News';";
        $NewsItemsSelectBox .= "NewsItemsSelectBox[".$section['page_id']."] = new Array();";

        while($item = $news->fetchRow(MYSQLI_ASSOC)) {
          $item['title'] = preg_replace($wblink_allowed_chars , "" , $item['title']);
          if ($wb284)
              $NewsItemsSelectBox .= "NewsItemsSelectBox[".$section['page_id']."][NewsItemsSelectBox[".$section['page_id']."].length] = new Array('".(addslashes($item['title']))."', '[wblink".$item['page_id'].'?addon=news&item='.$item['post_id']."]');";
            else    
                $NewsItemsSelectBox .= "NewsItemsSelectBox[".$section['page_id']."][NewsItemsSelectBox[".$section['page_id']."].length] = new Array('".(addslashes($item['title']))."', '".WB_URL.PAGES_DIRECTORY.(addslashes($item['link'])).PAGE_EXTENSION."');";
        }
    }
}

if( is_readable( WB_PATH.'/modules/topics/info.php' ) ) {
    $sql = 'SELECT * FROM `'.TABLE_PREFIX.'sections`  WHERE `module` = \'topics\' ';
    $topicsSections = $database->query($sql);

    while($section = $topicsSections->fetchRow(MYSQLI_ASSOC)){
        $topics = $database->query("SELECT `title`, `link`, `page_id`, `topic_id` FROM `".TABLE_PREFIX."mod_topics` WHERE `active` > 0 AND `section_id` = ".$section['section_id']);

        $ModuleList .= "ModuleList[".$section['page_id']."] = 'Topics';";
        $NewsItemsSelectBox .= "NewsItemsSelectBox[".$section['page_id']."] = new Array();";

        while($item = $topics->fetchRow(MYSQLI_ASSOC)) {
          $item['title'] = preg_replace($wblink_allowed_chars , "" , $item['title']);

          if ($wb284 && file_exists( WB_PATH."/modules/topics/WbLink.php"  ) )
              $NewsItemsSelectBox .= "NewsItemsSelectBox[".$section['page_id']."][NewsItemsSelectBox[".$section['page_id']."].length] = new Array('".(addslashes($item['title']))."', '[wblink".$item['page_id'].'?addon=topics&item='.$item['topic_id']."]');";
            else
                $NewsItemsSelectBox .= "NewsItemsSelectBox[".$section['page_id']."][NewsItemsSelectBox[".$section['page_id']."].length] = new Array('".(addslashes($item['title']))."', '".WB_URL.PAGES_DIRECTORY."/topics/".(addslashes($item['link'])).PAGE_EXTENSION."');";
        }
    }
}

if( is_readable( WB_PATH.'/modules/bakery/info.php' ) ) {
    $sql = 'SELECT * FROM `'.TABLE_PREFIX.'sections`  WHERE `module` = \'bakery\' ';
    $bakerySections = $database->query($sql);

    while($section = $bakerySections->fetchRow(MYSQLI_ASSOC)){
      $bakery = $database->query("SELECT `title`, `link`, `page_id`, `item_id` FROM `".TABLE_PREFIX."mod_bakery_items` WHERE `active`=1 AND `section_id` = ".$section['section_id']);

      $ModuleList .= "ModuleList[".$section['page_id']."] = 'Bakery';";
      $NewsItemsSelectBox .= "NewsItemsSelectBox[".$section['page_id']."] = new Array();";

      while($item = $bakery->fetchRow(MYSQLI_ASSOC)) {
          $item['title'] = preg_replace($wblink_allowed_chars , "" , $item['title']);

          if ($wb284 && file_exists(WB_PATH."/modules/bakery/WbLink.php"))
            $NewsItemsSelectBox .= "NewsItemsSelectBox[".$section['page_id']."][NewsItemsSelectBox[".$section['page_id']."].length] = new Array('".(addslashes($item['title']))."', '[wblink".$item['page_id'].'?addon=bakery&item='.$item['item_id']."]');";  
        else
            $NewsItemsSelectBox .= "NewsItemsSelectBox[".$section['page_id']."][NewsItemsSelectBox[".$section['page_id']."].length] = new Array('".(addslashes($item['title']))."', '".WB_URL.PAGES_DIRECTORY.(addslashes($item['link'])).PAGE_EXTENSION."');";
      }
    }
}

echo $NewsItemsSelectBox;

echo $ModuleList;
