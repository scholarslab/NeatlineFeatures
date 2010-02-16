function save(id, shapes) {
	new Ajax.Request(
			"/features/save/" + id,
			{
				method: 'post',
				parameters: { "shapes" : shapes } ,
				onSuccess: function() {}
			}
	);
}