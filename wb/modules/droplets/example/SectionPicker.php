//:Load the view.php from any other section-module
//:Use [[SectionPicker?sid=123]]
global $database,$admin,$wb, $TEXT, $DGTEXT,$section_id,$page_id;
$content = '';
$sid = isset($sid) ? intval($sid) : 52;
if( $sid ) {
    $oldSid = $section_id; // save old sectionID
    $section_id = $sid;
    $sql  = 'SELECT `module` FROM `'.TABLE_PREFIX.'sections` '
          . 'WHERE `section_id`='.$sid;
    if (($module = $database->get_one($sql))) {
        if (is_readable(WB_PATH.'/modules/'.$module.'/view.php')) {
            ob_start();
            require(WB_PATH.'/modules/'.$module.'/view.php');
            $content = ob_get_clean();
            $_sFrontendCss = '/modules/'.$module.'/frontend.css';
            $_sFrontendCssrUrl= WB_URL.$_sFrontendCss;
            if(is_readable(WB_PATH.$_sFrontendCss)) {
                //$_sSearch = preg_quote(WB_URL.'/modules/'.$module.'/frontend.css', '/');
                $sPattern = '/\[\[.*?\]\]\s*?|<!--\s+.*?-->\s*?|<(link|style)[^>]*\/>\s*?|<(link|style)[^>]*.*?<\/\2>\s*?|\s*$/isU';
                $content = preg_replace ($sPattern, '', $content);
                $_sFrontendCss = "<script >\n"
                ."<!--\n"
                ."    LoadOnFly('head', '$_sFrontendCssrUrl');\n"
                ."-->\n"
                ."</script>\n";
            } else { 
              $_sFrontendCss = '';
            }
        }
        $section_id = $oldSid; // restore old sectionID
    }
}

return $_sFrontendCss.$content;