function save(id, shapes) {
	new Ajax.Request(
			"/features/" + id,
			{
				method: 'post',
				parameters: { "wkt" : shapes } ,
				onSuccess: function() {}
			}
	);
}