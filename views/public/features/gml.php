<?php header('Content-Type: text/xml');
$output = '<gml:FeatureCollection xmlns:gml="http://www.opengis.net/gml/3.2"
	gml:id="id"
	xmlns:n="http://' . $_SERVER['SERVER_NAME'] . '">';
foreach ($gmls as $gml) {
	$output .= '<gml:featureMember> <n:feature gml:id="' . md5($gml) . '.feature">' . $gml .' </n:feature> </gml:featureMember>';
}
$output .= '</gml:FeatureCollection>';
print neatlinefeatures_formatXML($output);
?>