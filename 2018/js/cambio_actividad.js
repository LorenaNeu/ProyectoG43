function cambioActividad(periodo, empresa){
	//alert ("aca"+escape($("#obsacti").val()));
	if ($("#obsacti").val()!=''){
		if ( $("#vnacti3").val()!='' && $("#vnacti4").val()!='' ){
			var ciiu3 = parseInt($("#vnacti3").val());
			var ciiu4 = parseInt($("#vnacti4").val());
			if (periodo > 2012 && ciiu3!=0 && ciiu4!=0){
				$.ajax({
					type: "POST",
					url: "cambioacti.php",
					data: {
						'periodo': periodo,
						'empresa': empresa,
						'ciiu3': ciiu3,
						'ciiu4': ciiu4,
						'observaciones': escape($("#obsacti").val())
					},
					dataType: "html",
					contentType: "application/x-www-form-urlencoded; charset=UTF-8",
					cache: false,		
					success: function(data){
						alert(data);
						$("#vnacti3").val("");
						$("#vnacti4").val("");
						$("#obsacti").val("");
						$("#nacti").css("display","none"); //Ocultar el div de cambio de actividad
					}
				});
			}
			else if (periodo <= 2012 && ciiu3!=0){
				$.ajax({
					type: "POST",
					url: "cambioacti.php",
					data: {
						'periodo': periodo,
						'empresa': empresa,
						'ciiu3': ciiu3,
						'ciiu4': ciiu4,
						'observaciones': escape($("#obsacti").val())
					},
					dataType: "html",
					contentType: "application/x-www-form-urlencoded; charset=UTF-8",
					cache: false,		
					success: function(data){
						alert(data);
						$("#vnacti3").val("");
						$("#vnacti4").val("");
						$("#obsacti").val("");
						$("#nacti").css("display","none"); //Ocultar el div de cambio de actividad
					}
				});	
			}
			else{
				alert("Ingrese el c\u00F3digo de las actividad CIIU3");	
			}
		}
		else{
			alert("Ingrese los c\u00F3digos de las actividades CIIU3 y CIIU4");
		}
	}
	else {
		alert("Para realizar este cambio es necesario ingresar la  observaci\u00f3n");
	}
	
}