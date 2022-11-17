<html>
<head>
	<!--<script type="text/javascript" src="script.js"></script>-->
</head> 
<body>
	<?php
	$ruta="C:/imagenes_hce_dev/2017/08/27/203/noexisto.txt";
	if (file_exists($ruta)) {
		echo "existe $ruta";
	} else {
		echo "NO existe $ruta";
	}
	?>
	<script>
	
		function iniciaCanvas(idCanvas, ancho, alto){
		var elemento = document.getElementById(idCanvas);
		elemento.width = ancho; 
		elemento.height = alto; 
		if (elemento &&  elemento.getContext){
			var contexto = elemento.getContext('2d');
			if (contexto) {
			   return contexto;
			   }
			}
			return false;
		}	
		/* function drawLine
			ctx: referencia al contexto de dibujo
			startX: la coordenada X de la línea de punto de inicio
			startY: la coordenada Y de la línea de punto de inicio
			endX: la coordenada X de la línea de punto de inicio
			endY: la coordenada Y de la línea de punto de inicio			
		*/	
		function drawLine(ctx, startX, startY, endX, endY){ 
			ctx.beginPath();
			ctx.moveTo(startX,startY);		 
			ctx.lineTo(endX,endY);		 
			ctx.stroke();		 
		}		

		/* function drawArc
			ctx: referencia al contexto del dibujo
			centerX: la coordenada X del centro del círculo
			centerY: la coordenada Y del centro del círculo
			radio: la coordenada X de la línea del punto final
			startAngle: el ángulo inicial en radianes en donde comienza la porción del círculo
			endAngle: el ángulo final en radianes en donde termina la porción del círculo		
		*/
		function drawArc(ctx, centerX, centerY, radius, startAngle, endAngle){		 
			ctx.beginPath();		 
			ctx.arc(centerX, centerY, radius, startAngle, endAngle); //arc(x, y, radius, startAngle, endAngle, anticlockwise)		 
			ctx.stroke();		 
		}		
		
		/* function dwarPieSlice
			ctx: referencia al contexto del dibujo
			centerX: la coordenada X del centro del círculo
			centerY: la coordenada Y del centro del círculo
			radio: la coordenada X de la línea del punto final
			startAngle: el ángulo inicial en radianes en donde comienza la porción del círculo
			endAngle: el ángulo final en radianes en donde termina la porción final del círculo
			color: el color usado para rellenar la rebanada		
		*/
		function drawPieSlice(ctx,centerX, centerY, radius, startAngle, endAngle, color ){ 
			ctx.fillStyle = color; 
			ctx.beginPath(); 
			ctx.moveTo(centerX,centerY); 
			ctx.arc(centerX, centerY, radius, startAngle, endAngle); 
			ctx.closePath(); 
			ctx.fill(); 
		} 
	</script>
		
	<table border="1">
		<tr>
			<td>
				<canvas id="myCanvas" style="border-style: dashed;"></canvas> 
			</td>
			<td>
				<canvas id="myCanvas2" style="border-style: dashed;"></canvas> 
			</td>
			<td>
				<canvas id="myCanvas3" style="border-style: thin;"></canvas> 
			</td>			
			</tr>
		</table>
	<script>
		/*
		var myCanvas = document.getElementById("myCanvas");
		myCanvas.width = 300;
		myCanvas.height = 300;
		var ctx = myCanvas.getContext("2d"); 		
		*/
		
		context=iniciaCanvas("myCanvas", 270, 270); 
		
		var Imagen = new Image(); 
		Imagen.src = "../imagenes/husos_neso.png"; 
		context.drawImage(Imagen, 0, 0); 		
		
		/*
		drawArc(context, 150, 125, 50, 0, 180 * Math.PI); 
		//drawArc(ctx, 0, 0, 50, 90, 180); 
		//drawArc(ctx, 50, 50, 200, 30, 90); 
		
		context=iniciaCanvas("myCanvas2", 500, 500); 
		context.beginPath(); 
		//context.fillStyle = "rgb(0, 0, 0)"; 
		context.strokeStyle = "rgb(0, 0, 0)"; 
		//context.arc(250, 250, 125, 0, 1 * Math.PI, true); 
		
		/*
		context.arc(250, 250, 125, 0, 2 * Math.PI); 
		//context.fill(); 
		context.stroke(); 
		*/
		
		var husos_x_cuadrante=3;
		var husos_x_pi=6; //30grados=1/6 * PI
		var huso_ini, huso_fin, husos_comprometidos; 
		
		//Variables de entrada: 
		huso_ini = 4; 
		husos_comprometidos = 2; 
		
		// Graficación: 
		huso_ini = Math.abs(huso_ini - husos_x_cuadrante);
		huso_fin = huso_ini + husos_comprometidos; 		
		angulo_ini = (huso_ini/husos_x_pi) * Math.PI; 
		//angulo_ini = 1/6 * Math.PI; 
		angulo_fin = (huso_fin/husos_x_pi) * Math.PI; 
		//angulo_fin = 10/6 * Math.PI; 
		context.beginPath();
		context.fillStyle = "rgb(0, 0, 0)"; 
		context.arc(150, 150, 90, angulo_ini, angulo_fin, false);  //arc(x, y, radius, startAngle, endAngle, anticlockwise)				
		context.fill(); 
		
		context.beginPath();
		context.strokeStyle = "rgb(0, 0, 0)"; 
		context.arc(100, 100, 50, angulo_ini, angulo_fin, true);  //arc(x, y, radius, startAngle, endAngle, anticlockwise)	
		context.stroke(); 		

		
		//context.lineWidth = 1;
		context.beginPath();
		context.moveTo(50, 50);
		context.bezierCurveTo(80, 90, 100, 110, 250, 20, 270, 40);
		context.stroke();		
		
		function px(huso){
			switch (huso) {				
				case 1: return 190; break; 
				case 2: return 232; break; 
				case 3: return 247; break; 
				case 4: return 232; break; 
				case 5: return 190; break; 
				case 6: return 135; break; 
				case 7: return 78; break; 
				case 8: return 36; break; 
				case 9: return 20; break; 
				case 10: return 36; break; 
				case 11: return 78; break; 
				case 12: return 135; break; 
			} 
		}
		
		function py(huso){ 
			switch (huso) { 				
				case 1: return 36; break; 
				case 2: return 78; break; 
				case 3: return 135; break; 
				case 4: return 190; break; 
				case 5: return 232; break; 
				case 6: return 247; break; 
				case 7: return 232; break; 
				case 8: return 190; break; 
				case 9: return 135; break; 
				case 10: return 78; break; 
				case 11: return 36; break; 
				case 12: return 20; break; 
			} 
		}	
		
		context.lineWidth = 1; 
		context.beginPath(); 
		//context.moveTo(pxh(12, "x"), pxh(12, "y")); 
		//context.moveTo(px(1), py(1)); 
		xini=px(2); 
		yini=py(2); 
		xfin=px(6); 
		yfin=py(6);
		xmedio=px(4); 
		ymedio=py(4);
		context.bezierCurveTo(xini, yini, xmedio, ymedio, xfin, yfin); 
		//context.bezierCurveTo(232, 190, 190, 232, 134, 247); 
		context.stroke(); 
		
		context.lineWidth = 2;
		context.strokeStyle="red";
		context.beginPath();
		xini=px(1); 
		yini=py(1); 
		xfin=px(4); 
		yfin=py(4);
		mm=5;
		xmedio=135; 
		ymedio=135;
		
		context.bezierCurveTo(xini, yini, xmedio, ymedio, xfin, yfin); 		
		context.stroke(); 		
		
		context.lineWidth = 2; 
		context.strokeStyle="cyan"; 
		context.beginPath();
		xini=px(11); 
		yini=py(11); 
		xfin=px(8); 
		yfin=py(8); 
		
		diametro=270; 
		
		mm = 10; 
		//xmedio=135; 
		//ymedio=135;
		proporcion=10/270; 
		xmedio=Math.abs(xfin-xini) * (135 * mm/135);
		ymedio=Math.abs(yfin-yini) * (135 * mm/135);
		
		context.bezierCurveTo(xini, yini, xmedio, ymedio, xfin, yfin); 		
		context.stroke(); 		
		
		context.lineWidth = 3;
		context.strokeStyle="green";
		context.beginPath();
		context.moveTo(px(1), py(1));
		xini=px(7); 
		yini=py(7); 
		xfin=px(12); 
		yfin=py(12);
		xmedio=px(11); 
		ymedio=py(11);
		context.quadraticCurveTo(xini, yini, /*xmedio, ymedio, */xfin, yfin); 		
		context.stroke(); 				
/*		
		context.lineWidth = 10;
		context.beginPath();
		//context.moveTo(10, 10);
		context.bezierCurveTo(100, 100, 200, 150, 250, 250);
		context.stroke();		
*/		
		
		/* -------------------------------------------------------------- FM ------------------------------------------------------------ */
		
		var Imagen_husos = new Image(); 		
		Imagen_husos.src = "../imagenes/husos_neso.png"; 
		
		// Constantes
		var mmcornea=12;
		var xcentro=135;
		var ycentro=135;
		
		// Entradas
		var huso_ini=5; 
		var canti_husos=3; 
		var mm=5; 
		
		var huso_fin, huso_medio; 
		
		context=iniciaCanvas("myCanvas3", 300, 300); 
		context.drawImage(Imagen_husos, 0, 0); 
		
		context.lineWidth = 3;
		context.strokeStyle="green";
		context.beginPath();
		/*xini=px(5); 
		yini=py(5); 
		xfin=px(8); 
		yfin=py(8);*/
		//xmedio=px(11); 
		//ymedio=py(11);
		//xmedio=135;
		//ymedio=135;
		
		var distancia1=120; 
		xini=Math.cos( (Math.PI/6) * (huso_ini-3) ) * xcentro + ((mmcornea - mm) / mmcornea) + distancia1; 
		yini=Math.sin( (Math.PI/6) * (huso_ini-3) ) * ycentro + ((mmcornea - mm) / mmcornea) + distancia1; 
		
		huso_fin=huso_ini+canti_husos;
		xfin=Math.cos( (Math.PI/6) * (huso_fin-3) ) * xcentro + ((mmcornea - mm) / mmcornea) + distancia1; 
		yfin=Math.sin( (Math.PI/6) * (huso_fin-3) ) * ycentro + ((mmcornea - mm) / mmcornea) + distancia1; 
		
		huso_medio = Math.round( Math.abs(huso_fin - huso_ini) / 2 ); 		
		xmedio=Math.cos( (Math.PI/6) * (huso_medio-3) ) * xcentro + ((mmcornea - mm) / mmcornea) + distancia1; 
		ymedio=Math.sin( (Math.PI/6) * (huso_medio-3) ) * ycentro + ((mmcornea - mm) / mmcornea) + distancia1; 		
				
		context.bezierCurveTo(xini, yini, xmedio, ymedio, xfin, yfin); 
		context.stroke(); 
		
		context.strokeStyle="red";
		context.beginPath();		
		//context.moveTo(xini, yini); 
		//context.quadraticCurveTo(xmedio, ymedio, xfin, yfin); 
		context.moveTo(px(huso_ini), py(huso_ini)); 
		context.quadraticCurveTo(xmedio, ymedio, px(huso_fin), py(huso_fin)); 
		context.stroke(); 		
	</script> 	
</body> 
</html>