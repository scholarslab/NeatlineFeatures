<gml:FeatureCollection
	xmlns:gml="http://www.opengis.net/gml/3.2"
	gml:id="id"
	xmlns:n="http://<?php print $_SERVER['SERVER_NAME'];?>">

<?php
foreach ($gmls as $gml) {
	?>
	<gml:featureMember>
			<n:feature gml:id="<?php print md5($gml) . ".feature" ?>">
				<?php print $gml;?>
			</n:feature>
	</gml:featureMember>
	<?php 
}

?>
</gml:FeatureCollection>