//:Scan page and replace only img src= not href=
//:[[imgresizer]]
//: copy of imageresizer-v2
//: can also used with image-links
preg_match_all('/<img [^>]*>/im',$wb_page_data,$matches);
if(!count($matches))return true; // nothing found
foreach($matches[0] as $match){
$width=0;
$height=0;
if(preg_match('/width="[0-9]*"/i',$match) && preg_match('/height="[0-9]*"/i',$match)){
$width=preg_replace('/.*width="([0-9]*)".*/i','\1',$match);
$height=preg_replace('/.*height="([0-9]*)".*/i','\1',$match);
}
else if(preg_match('/style="[^"]*width: *[0-9]*px/i',$match) && preg_match('/style="[^"]*height: *[0-9]*px/i',$match)){
$width=preg_replace('/.*style="[^"]*width: *([0-9]*)px.*/i','\1',$match);
$height=preg_replace('/.*style="[^"]*height: *([0-9]*)px.*/i','\1',$match);
}
if(!$width || !$height) continue;
$imgsrc = preg_replace('/.*src="([^"]*)".*/i','\1',$match);
if(strpos($imgsrc,WB_URL.MEDIA_DIRECTORY) !== false ){
$src = str_replace(WB_URL.MEDIA_DIRECTORY.'/','', $imgsrc);
list($x,$y)=getimagesize(WB_PATH.MEDIA_DIRECTORY.'/'.urldecode($src));
if(!$x || !$y || ($x==$width && $y==$height))continue;
$search[]  = 'src="'.$imgsrc.'"';
if($width) {
$replace[] = 'src="'.WB_URL.'/media/images.php?i='.$src.'&amp;w='.$width.'"';
} else {
$replace[] = 'src="'.WB_URL.'/media/images.php?i='.$src.'&amp;h='.$height.'"';
}
}
}
if(isset($search)) $wb_page_data = str_replace($search,$replace,$wb_page_data);
return true;