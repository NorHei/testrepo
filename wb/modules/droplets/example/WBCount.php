//:Online Besucher Statistik
//:useage: [[WBCount?tpl=default]]
$retVal=' ';
$tpl =(@$tpl?:'default');
if( is_readable( WB_PATH.'/modules/wbCounter/include.php' ))
{
if (!function_exists('wbCounter')) {
include WB_PATH.'/modules/wbCounter/include.php';
}
$retVal = wbCounter($tpl);
$retVal = (@$retVal?:' ');
}
return $retVal;
