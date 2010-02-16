function save(id, shapes) {
	new Ajax.Request(
			"/features/" + id,
			{
				method: 'post',
				parameters: { "shapes" : shapes } ,
				onSuccess: function() {}
			}
	);
}