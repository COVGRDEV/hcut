<?php
	@$id_paciente = $_GET["id_paciente"];
	@$id_imagen = $_GET["id_imagen"];
	@$nombre_imagen = $_GET["nombre_imagen"];
	@$nombre_imagen_base = "../".$_GET["nombre_imagen_base"];
	@$ancho_img = $_GET["ancho_img"];
	@$alto_img = $_GET["alto_img"];
	
	if ($nombre_imagen == "") {
		$nombre_imagen = $nombre_imagen_base;
	} else {
		$nombre_imagen = "../".$nombre_imagen;
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width" />
    
    <title>wPaint</title>
    
    <script type="text/javascript" src="../../js/jquery.min.js"></script>
    
    <!-- wColorPicker -->
    <link rel="Stylesheet" type="text/css" href="lib/wColorPicker.min.css" />
    <script type="text/javascript" src="lib/wColorPicker.min.js"></script>
    
    <!-- wPaint -->
    <link rel="Stylesheet" type="text/css" href="wPaint.min.css" />
    <script type="text/javascript" src="wPaint.min.js"></script>
    <script type="text/javascript" src="plugins/main/wPaint.menu.main.min.js"></script>
    <script type="text/javascript" src="plugins/text/wPaint.menu.text.min.js"></script>
    <script type="text/javascript" src="plugins/file/wPaint.menu.main.file.min.js"></script>
    <script type="text/javascript">
		function guardar_imagen() {
			var imageData = $("#d_img_wpaint").wPaint("image");
			
			$.ajax({
				type: 'POST',
				url: 'test/upload.php',
				data: {image: imageData, nombre_img: '<?php echo($id_imagen); ?>', id_paciente: '<?php echo($id_paciente); ?>'},
				success: function (resp) {
					resp = $.parseJSON(resp);
					images.push(resp.img);
					$('#d_img_wpaint-img').attr('src', imageData);
				}
			});
		}
	</script>
</head>
<body>
    <div id="content">
        <div class="content-box">
            <div id="d_img_wpaint" style="position:relative; width:<?php echo($ancho_img); ?>px; height:<?php echo($alto_img); ?>px; background-color:#ffffff; margin:60px auto 0px auto; border:1px solid black"></div>
            <center id="d_img_wpaint-img"></center>
            <script type="text/javascript">
				var images = [];
				
				function censor(censor) {
					var i = 0;
					
					return function(key, value) {
						if (i !== 0 && typeof(censor) === 'object' && typeof(value) == 'object' && censor == value)
							return '[Circular]';
							
							if (i >= 29) {
								return '[Unknown]';
							}
							++i;
						return value;
					}
				}
				
				function saveImg(image) {
					var _this = this;
					
					$.ajax({
						type: 'POST',
						url: 'test/upload.php',
						data: {image: image},
						success: function (resp) {
							_this._displayStatus('Imagen guardada con éxito');
							resp = $.parseJSON(resp);
							images.push(resp.img);
							$('#d_img_wpaint-img').attr('src', image);
						}
					});
				}
				
				function loadImgBg () {
					this._showFileModal('bg', images);
				}
				
				function loadImgFg () {
					this._showFileModal('fg', images);
				}
				
				// init wPaint
				$('#d_img_wpaint').wPaint({
					path: '',
					theme: 'standard classic',
					autoScaleImage: false,
					menuHandle: false,
					//menuOffsetLeft: -35,
					menuOffsetTop: -60,
					saveImg: saveImg,
					loadImgBg: loadImgBg,
					loadImgFg: loadImgFg,
					image: '<?php echo($nombre_imagen); ?>',
					mode: 'pencil',
					lineWidth: '2',
					fillStyle: '#000000',
					strokeStyle: '#000000',
					fontSize: '14'
				});
				
				$('#d_img_wpaint').wPaint.extend({
					clear: function() {
						$('#d_img_wpaint').wPaint('image', '<?php echo($nombre_imagen_base); ?>');
					}
				});
			</script>
        </div>
    </div>
</body>
</html>
