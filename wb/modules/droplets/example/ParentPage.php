//:Create a parent link to your page
//:Display a link to the parent page of the current page
$info = show_menu2(0, SM2_CURR, SM2_START, SM2_ALL|SM2_BUFFER, '[if(class==menu-current){[level] [sib] [sibCount] [parent]}]', '', '', '');
list($nLevel, $nSib, $nSibCount, $nParent) = explode(' ', $info);
// show up level
if ($nLevel > 0) {
$lev = $nLevel - 1;
return show_menu2(0, SM2_ROOT, SM2_CURR, SM2_CRUMB|SM2_BUFFER, "[if(level==$lev){[a][menu_title]</a>}]", '', '', '');
}
else
return '(no parent)';