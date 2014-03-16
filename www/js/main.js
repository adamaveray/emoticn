(function(document, undefined){
	if(!document.querySelectorAll){
		return;
	}

	// Center tooltips
	var tooltips	= document.querySelectorAll('.platform-emoticon-name');
	for(var i = 0; i < tooltips.length; i++){
		var element	= tooltips[i];

		element.style.visibility	= "hidden";
		element.style.display		= "block";

		console.log(element, element.offsetWidth);
		element.style.left			= "50%";
		element.style.marginLeft	= (-element.offsetWidth/2)+"px";

		element.style.visibility = element.style.display = null;
	}
}(document));
