<?php
/**
 * Do not use or reference this directly from your client-side code.
 * Instead, this should be required via the endpoint.php or endpoint-cors.php
 * file(s).
 */
class UploadHandler {
    public $allowedExtensions = array();
    public $sizeLimit = null;
    public $inputName = 'qqfile';
    public $chunksFolder = 'chunks';
    public $chunksCleanupProbability = 0.001; // Once in 1000 requests on avg
    public $chunksExpireIn = 604800; // One week
    protected $uploadName;
    /**
     * Get the original filename
     */
    public function getName(){
        if (isset($_REQUEST['qqfilename']))
            return $_REQUEST['qqfilename'];
        if (isset($_FILES[$this->inputName]))
            return $_FILES[$this->inputName]['name'];
    }
    public function getInitialFiles() {
        $initialFiles = array();
        for ($i = 0; $i < 5000; $i++) {
            array_push($initialFiles, array("name" => "name" + $i, uuid => "uuid" + $i, thumbnailUrl => "/test/dev/handlers/vendor/fineuploader/php-traditional-server/fu.png"));
        }
        return $initialFiles;
    }
    /**
     * Get the name of the uploaded file
     */
    public function getUploadName(){
        return $this->uploadName;
    }
    public function combineChunks($uploadDirectory, $name = null) {
        $uuid = $_POST['qquuid'];
        if ($name === null){
            $name = $this->getName();
        }
        $targetFolder = $this->chunksFolder.DIRECTORY_SEPARATOR.$uuid;
        $totalParts = isset($_REQUEST['qqtotalparts']) ? (int)$_REQUEST['qqtotalparts'] : 1;
        $targetPath = join(DIRECTORY_SEPARATOR, array($uploadDirectory, $uuid, $name));
        $this->uploadName = $name;
        if (!file_exists($targetPath)){
            mkdir(dirname($targetPath), 0777, true);
        }
        $target = fopen($targetPath, 'wb');
        for ($i=0; $i<$totalParts; $i++){
            $chunk = fopen($targetFolder.DIRECTORY_SEPARATOR.$i, "rb");
            stream_copy_to_stream($chunk, $target);
            fclose($chunk);
        }
        // Success
        fclose($target);
        for ($i=0; $i<$totalParts; $i++){
            unlink($targetFolder.DIRECTORY_SEPARATOR.$i);
        }
        rmdir($targetFolder);
        if (!is_null($this->sizeLimit) && filesize($targetPath) > $this->sizeLimit) {
            unlink($targetPath);
            http_response_code(413);
            return array("success" => false, "uuid" => $uuid, "preventRetry" => true);
        }
        return array("success" => true, "uuid" => $uuid);
    }
    /**
     * Process the upload.
     * @param string $uploadDirectory Target directory.
     * @param string $name Overwrites the name of the file.
     */
    public function handleUpload($uploadDirectory, $name = null){
        if (is_writable($this->chunksFolder) &&
            1 == mt_rand(1, 1/$this->chunksCleanupProbability)){
            // Run garbage collection
            $this->cleanupChunks();
        }
        // Check that the max upload size specified in class configuration does not
        // exceed size allowed by server config
        if ($this->toBytes(ini_get('post_max_size')) < $this->sizeLimit ||
            $this->toBytes(ini_get('upload_max_filesize')) < $this->sizeLimit){
            $neededRequestSize = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
            return array('error'=>"Server error. Increase post_max_size and upload_max_filesize to ".$neededRequestSize);
        }
		
		if (!is_dir($uploadDirectory)){
			mkdir($uploadDirectory, 0755, true);
		} 		
        if ($this->isInaccessible($uploadDirectory)){
            return array('error' => "Server error. Uploads directory isn't writable");
        }
        $type = $_SERVER['CONTENT_TYPE'];
        if (isset($_SERVER['HTTP_CONTENT_TYPE'])) {
            $type = $_SERVER['HTTP_CONTENT_TYPE'];
        }
        if(!isset($type)) {
            return array('error' => "No files were uploaded.");
        } else if (strpos(strtolower($type), 'multipart/') !== 0){
            return array('error' => "Server error. Not a multipart request. Please set forceMultipart to default value (true).");
        }
        // Get size and name
        $file = $_FILES[$this->inputName];
        $size = $file['size'];
        if (isset($_REQUEST['qqtotalfilesize'])) {
            $size = $_REQUEST['qqtotalfilesize'];
        }
        if ($name === null){
            $name = $this->getName();
        }
        // check file error
        if($file['error']) {
            return array('error' => 'Upload Error #'.$file['error']);
        }
        	
        // Validate name
        if ($name === null || $name === ''){
            return array('error' => 'File name empty.');
        }
        // Validate file size
        if ($size == 0){
            return array('error' => 'File is empty.');
        }
        if (!is_null($this->sizeLimit) && $size > $this->sizeLimit) {
            return array('error' => 'File is too large.', 'preventRetry' => true);
        }
        // Validate file extension
        $pathinfo = pathinfo($name);
        $ext = isset($pathinfo['extension']) ? $pathinfo['extension'] : '';
        if($this->allowedExtensions && !in_array(strtolower($ext), array_map("strtolower", $this->allowedExtensions))){
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
        }
        // Save a chunk
        $totalParts = isset($_REQUEST['qqtotalparts']) ? (int)$_REQUEST['qqtotalparts'] : 1;
        $uuid = $_REQUEST['qquuid'];
        if ($totalParts > 1){
        # chunked upload
            $chunksFolder = $this->chunksFolder;
            $partIndex = (int)$_REQUEST['qqpartindex'];
            if (!is_writable($chunksFolder) && !is_executable($uploadDirectory)){
                return array('error' => "Server error. Chunks directory isn't writable or executable.");
            }
            $targetFolder = $this->chunksFolder.DIRECTORY_SEPARATOR.$uuid;
            if (!file_exists($targetFolder)){
                mkdir($targetFolder, 0777, true);
            }
            $target = $targetFolder.'/'.$partIndex;
            $success = move_uploaded_file($_FILES[$this->inputName]['tmp_name'], $target);
            return array("success" => true, "uuid" => $uuid);
        }
        else {
        # non-chunked upload
            $target = join(DIRECTORY_SEPARATOR, array($uploadDirectory, $uuid, $name));
            if ($target){
                $this->uploadName = basename($target);
                if (!is_dir(dirname($target))){
                    mkdir(dirname($target), 0777, true);
                }
                if (move_uploaded_file($file['tmp_name'], $target)){
                    return array('success'=> true, "uuid" => $uuid);
                }
            }
            return array('error'=> 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered');
        }
    }
    /**
     * Process a delete.
     * @param string $uploadDirectory Target directory.
     * @params string $name Overwrites the name of the file.
     *
     */
    public function handleDelete($uploadDirectory, $name=null)
    {
        if ($this->isInaccessible($uploadDirectory)) {
            return array('error' => "Server error. Uploads directory isn't writable" . ((!$this->isWindows()) ? " or executable." : "."));
        }
        $targetFolder = $uploadDirectory;
        $uuid = false;
        $method = $_SERVER["REQUEST_METHOD"];
	    if ($method == "DELETE") {
            $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $tokens = explode('/', $url);
            $uuid = $tokens[sizeof($tokens)-1];
        } else if ($method == "POST") {
            $uuid = $_REQUEST['qquuid'];
        } else {
            return array("success" => false,
                "error" => "Invalid request method! ".$method
            );
        }
        $target = join(DIRECTORY_SEPARATOR, array($targetFolder, $uuid));
        if (is_dir($target)){
            $this->removeDir($target);
            return array("success" => true, "uuid" => $uuid);
        } else {
            return array("success" => false,
                "error" => "File not found! Unable to delete.".$url,
                "path" => $uuid
            );
        }
    }
    /**
     * Returns a path to use with this upload. Check that the name does not exist,
     * and appends a suffix otherwise.
     * @param string $uploadDirectory Target directory
     * @param string $filename The name of the file to use.
     */
    protected function getUniqueTargetPath($uploadDirectory, $filename)
    {
        // Allow only one process at the time to get a unique file name, otherwise
        // if multiple people would upload a file with the same name at the same time
        // only the latest would be saved.
        if (function_exists('sem_acquire')){
            $lock = sem_get(ftok(__FILE__, 'u'));
            sem_acquire($lock);
        }
        $pathinfo = pathinfo($filename);
        $base = $pathinfo['filename'];
        $ext = isset($pathinfo['extension']) ? $pathinfo['extension'] : '';
        $ext = $ext == '' ? $ext : '.' . $ext;
        $unique = $base;
        $suffix = 0;
        // Get unique file name for the file, by appending random suffix.
        while (file_exists($uploadDirectory . DIRECTORY_SEPARATOR . $unique . $ext)){
            $suffix += rand(1, 999);
            $unique = $base.'-'.$suffix;
        }
        $result =  $uploadDirectory . DIRECTORY_SEPARATOR . $unique . $ext;
        // Create an empty target file
        if (!touch($result)){
            // Failed
            $result = false;
        }
        if (function_exists('sem_acquire')){
            sem_release($lock);
        }
        return $result;
    }
    /**
     * Deletes all file parts in the chunks folder for files uploaded
     * more than chunksExpireIn seconds ago
     */
    protected function cleanupChunks(){
        foreach (scandir($this->chunksFolder) as $item){
            if ($item == "." || $item == "..")
                continue;
            $path = $this->chunksFolder.DIRECTORY_SEPARATOR.$item;
            if (!is_dir($path))
                continue;
            if (time() - filemtime($path) > $this->chunksExpireIn){
                $this->removeDir($path);
            }
        }
    }
    /**
     * Removes a directory and all files contained inside
     * @param string $dir
     */
    protected function removeDir($dir){
        foreach (scandir($dir) as $item){
            if ($item == "." || $item == "..")
                continue;
            if (is_dir($item)){
                $this->removeDir($item);
            } else {
                unlink(join(DIRECTORY_SEPARATOR, array($dir, $item)));
            }
        }
        rmdir($dir);
    }
    /**
     * Converts a given size with units to bytes.
     * @param string $str
     */
    protected function toBytes($str){
	$str = trim($str);
        $last = strtolower($str[strlen($str)-1]);
	$val;
	if(is_numeric($last)) {
		$val = (int) $str;
	} else {
		$val = (int) substr($str, 0, -1);
	}
        switch($last) {
            case 'g': case 'G': $val *= 1024;
            case 'm': case 'M': $val *= 1024;
            case 'k': case 'K': $val *= 1024;
        }
        return $val;
    }
    /**
     * Determines whether a directory can be accessed.
     *
     * is_executable() is not reliable on Windows prior PHP 5.0.0
     *  (http://www.php.net/manual/en/function.is-executable.php)
     * The following tests if the current OS is Windows and if so, merely
     * checks if the folder is writable;
     * otherwise, it checks additionally for executable status (like before).
     *
     * @param string $directory The target directory to test access
     */
    protected function isInaccessible($directory) {
        $isWin = $this->isWindows();
        $folderInaccessible = ($isWin) ? !is_writable($directory) : ( !is_writable($directory) && !is_executable($directory) );
        return $folderInaccessible;
    }
    /**
     * Determines is the OS is Windows or not
     *
     * @return boolean
     */
    protected function isWindows() {
    	$isWin = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
    	return $isWin;
    }
	
	// This will retrieve the "intended" request method.  Normally, this is the
	// actual method of the request.  Sometimes, though, the intended request method
	// must be hidden in the parameters of the request.  For example, when attempting to
	// send a DELETE request in a cross-origin environment in IE9 or older, it is not
	// possible to send a DELETE request.  So, we send a POST with the intended method,
	// DELETE, in a "_method" parameter.
	public function get_request_method() {
		global $HTTP_RAW_POST_DATA;

		// This should only evaluate to true if the Content-Type is undefined
		// or unrecognized, such as when XDomainRequest has been used to
		// send the request.
		if(isset($HTTP_RAW_POST_DATA)) {
			parse_str($HTTP_RAW_POST_DATA, $_POST);
		}

		if (isset($_POST["_method"]) && $_POST["_method"] != null) {
			return $_POST["_method"];
		}

		return $_SERVER["REQUEST_METHOD"];
	}	
	
	/* ----------------------------------------------------------------------------------------------------------------------- */
	
	/**
	 * PHP Server-Side Example for Fine Uploader (traditional endpoint handler).
	 * Maintained by Widen Enterprises.
	 *
	 * This example:
	 *  - handles chunked and non-chunked requests
	 *  - supports the concurrent chunking feature
	 *  - assumes all upload requests are multipart encoded
	 *  - handles delete requests
	 *  - handles cross-origin environments
	 *
	 * Follow these steps to get up and running with Fine Uploader in a PHP environment:
	 *
	 * 1. Setup your client-side code, as documented on http://docs.fineuploader.com.
	 *
	 * 2. Copy this file and handler.php to your server.
	 *
	 * 3. Ensure your php.ini file contains appropriate values for
	 *    max_input_time, upload_max_filesize and post_max_size.
	 *
	 * 4. Ensure your "chunks" and "files" folders exist and are writable.
	 *    "chunks" is only needed if you have enabled the chunking feature client-side.
	 *
	 * 5. If you have chunking enabled in Fine Uploader, you MUST set a value for the `chunking.success.endpoint` option.
	 *    This will be called by Fine Uploader when all chunks for a file have been successfully uploaded, triggering the
	 *    PHP server to combine all parts into one file. This is particularly useful for the concurrent chunking feature,
	 *    but is now required in all cases if you are making use of this PHP example.
	 */

	protected function parseRequestHeaders() {
		$headers = array();
		foreach($_SERVER as $key => $value) {
			if (substr($key, 0, 5) <> 'HTTP_') {
				continue;
			}
			$header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
			$headers[$header] = $value;
		}
		return $headers;
	}

	protected function handleCorsRequest() {
		header("Access-Control-Allow-Origin: *");
	}

	/*
	 * handle pre-flighted requests. Needed for CORS operation
	 */
	protected function handlePreflight() {
		$this->handleCorsRequest();
		header("Access-Control-Allow-Methods: POST, DELETE");
		header("Access-Control-Allow-Credentials: true");
		header("Access-Control-Allow-Headers: Content-Type, X-Requested-With, Cache-Control");
	}
	
	public function procesarUploadFile($directorio/*, $nombre_archivo=""*/) { 

		// Include the upload handler class
		//require_once "../funciones/fine-uploader/Class_UploadHandler.php"; 

		//z$uploader = new UploadHandler();
		//$uploader = $this;

		// Specify the list of valid extensions, ex. array("jpeg", "xml", "bmp")
		//z$uploader->allowedExtensions = array(); // all files types allowed by default

		// Specify max file size in bytes.
		//z$uploader->sizeLimit = null;

		// Specify the input name set in the javascript.
		//z$uploader->inputName = "qqfile"; // matches Fine Uploader's default inputName value by default

		// If you want to use the chunking/resume feature, specify the folder to temporarily save parts.
		$this->chunksFolder = "chunks";

		//$method = $_SERVER["REQUEST_METHOD"];
		$method = $this->get_request_method();
	
		//$directorio="../tmp/uploads";
		// Determine whether we are dealing with a regular ol' XMLHttpRequest, or
		// an XDomainRequest
		$_HEADERS = $this->parseRequestHeaders();
		$iframeRequest = false;
		if (!isset($_HEADERS['X-Requested-With']) || $_HEADERS['X-Requested-With'] != "XMLHttpRequest") {
			$iframeRequest = true;
		}

		/*
		 * handle the preflighted OPTIONS request. Needed for CORS operation.
		 */
		if ($method == "OPTIONS") {
			$this->handlePreflight();
		}

		/*
		 * handle a DELETE request or a POST with a _method of DELETE.
		 */
		else if ($method == "DELETE") {			
			$this->handleCorsRequest();
			$result = $this->handleDelete($directorio);
			// iframe uploads require the content-type to be 'text/html' and
			// return some JSON along with self-executing javascript (iframe.ss.response)
			// that will parse the JSON and pass it along to Fine Uploader via
			// window.postMessage
			$msjs["method"]=$method; 
			$result=array_merge($result, $msjs); 
			if ($iframeRequest == true) { 
				header("Content-Type: text/html"); 
				//echo json_encode($result)."<script src='http://10.0.2.2/jquery.fineuploader-4.1.1/iframe.xss.response-4.1.1.js'></script>"; 
				$result["result Class_UploadHandler (method:DELETE)"] = "<script src='http://{{SERVER_URL}}/{{FINE_UPLOADER_FOLDER}}/iframe.xss.response.js'></script>";
			} else { 
				//echo json_encode($result); 
			} 
		}
		else if ($method == "POST") {
			$this->handleCorsRequest();
			header("Content-Type: text/plain");

			// Assumes you have a chunking.success.endpoint set to point here with a query parameter of "done".
			// For example: /myserver/handlers/endpoint.php?done
			if (isset($_GET["done"])) {
				$result = $this->combineChunks($directorio);
			}
			// Handles upload requests
			else {
				// Call handleUpload() with the name of the folder, relative to PHP's getcwd()
				$result = $this->handleUpload($directorio);

				// To return a name used for uploaded file you can use the following line.
				$result["uploadName"] = $this->getUploadName();

				// iframe uploads require the content-type to be 'text/html' and
				// return some JSON along with self-executing javascript (iframe.ss.response)
				// that will parse the JSON and pass it along to Fine Uploader via
				// window.postMessage
				if ($iframeRequest == true) {
					header("Content-Type: text/html");
				    //echo json_encode($result)."<script src='http://{{SERVER_URL}}/{{FINE_UPLOADER_FOLDER}}/iframe.xss.response.js'></script>";
					$result["result Class_UploadHandler (method:POST)"] = "<script src='http://{{SERVER_URL}}/{{FINE_UPLOADER_FOLDER}}/iframe.xss.response.js'></script>";
				} else {
				    //echo json_encode($result);
				}
			}
		}
		else {
			header("HTTP/1.0 405 Method Not Allowed");
		}
		
		return $result;
	}
	
	
	/* 
	FUNCIONES PARA LA CARGA EN BD
	*/
	
	//Mueve al directorio definitivo y Registra en BD los archivos ya cargados a traves de plugin FineUploader
	/* @modulo: módulo/tabla del sistema con el cual se va a relacionar el lote de archivos
	   @id_modulo: ID de registro en tabla del módulo
	*/	
	function registrarArchivosAdjuntos($arr_archivos, $reg_lote_archivos, $directorio_origen, $directorio_destino, $id_usuario, $modulo, $id_modulo){ 
		
		$arr_resultado=array();
		$arr_resultado["codigo_resultado"]=1;
		$arr_resultado["msj_resultado"]=""; 
		//$arr_resultado["id_lote"]=$reg_lote_archivos->id_reg_archivos;
		$arr_resultado["archivos"]=array();
				
		$reg_archivo = new DBArchivo();
		$dbHistoriaClinica = new DbHistoriaClinica(); 
		//echo "<br>pasar de directorio_origen=$directorio_origen ==> directorio_destino".$directorio_destino;  
		
		if (sizeof($arr_archivos)==0) return $arr_resultado; //PENDIENTE????				
		
		// Registrar el lote de los archivos 
		$id_lote = $reg_lote_archivos->CrearEditarRegistroArchivos($id_usuario, $modulo, $id_modulo); 
		
		if ($id_lote<0) {
			$arr_resultado["codigo_resultado"]=-2;
			$arr_resultado["msj_resultado"]="Error -2- al guardar ".$reg_lote_archivos->prefijo.". No se registraron los archivos"; 														
		} else {
			
			// Registrar los archivos			
			$arr_resultado["id_lote"]=$id_lote;			
			
			//Crear el directorio para los archivos 
			//echo "<br>directorio_destino ==> ".$directorio_destino; 
			if (!is_dir($directorio_destino)) { 
				mkdir($directorio_destino, 0755, true);
			}
			
			//ojo: esto pasaría al guardar... FALTA "borrar" los que se eliminaron	PENDIENTE			
			$indice_arch=$reg_lote_archivos->getMaximoConsecutivoArchivos(); 			
			if (!isset($indice_arch["max_consecutivo"])) { $indice_arch["max_consecutivo"]=0; }
			$indice_arch=$indice_arch["max_consecutivo"];
			
			$i=0;
			foreach ($arr_archivos as $archivo) {			
				//echo "<br>*Archivo[0] NAME = ".$archivo["name"]." - STATUS = ".$archivo["status"]." - size=".$archivo["size"]; 				
				switch ($archivo["status"]) {
					
					case "upload successful":
						
						if ($archivo["size"]<0) { /*echo " --> obviado xq existía en bd!";*/ continue; } 
						
						$indice_arch++;
						//$uploader-> 
						//Se obtiene el nombre que tendrá el archivo
						$ruta_archivo = $dbHistoriaClinica->construir_nombre_arch($reg_lote_archivos->id_hc, $archivo["name"], $reg_lote_archivos->prefijo, $indice_arch); 				
						
						//Se copia el archivo
						$ruta_archivo_tmp = $directorio_origen ."/". $archivo["uuid"] ."/". $archivo["name"]; 
						//echo "<br>ruta_archivo_tmp = ".$ruta_archivo_tmp." - ruta_archivo = ".$ruta_archivo; 	
						$rta=copy($ruta_archivo_tmp, $ruta_archivo); 
						
						// Se registra el archivo: 
						if ($rta==true) {
							
							$reg_archivo->id_archivo = -1; 
							$reg_archivo->id_reg_archivos = $id_lote; 
							$reg_archivo->ruta = $ruta_archivo;
							$reg_archivo->nombre_original = $archivo["name"];
							
							$id_archivo = $reg_archivo->CrearEditarArchivo($id_usuario); 
							//echo "<br>id_archi_insertado=$id_archivo";
							if ($id_archivo<0) { 
								$arr_resultado["codigo_resultado"]=-4; 
								$arr_resultado["msj_resultado"].=". Error -4- al guardar ".$reg_lote_archivos->prefijo.": ".$ruta_archivo; 
							} else { 					
								$reg_archivo->id_archivo=$id_archivo; 
								$arr_archivos[$i]["reg_archivo"]=$reg_archivo;
							} 
						}
					break;
					
					case "deleted": 
						//echo " ===> obviado!";

						// Borrar de BD: 
						$reg_archivo->id_archivo = 0; //$POST["Uuid"]; PENDIENTE							
						$resultado = $reg_archivo->BorrarArchivo($id_usuario); 
						if ($resultado<0) {
							$resultado=-3; 
							$msj_resultado="Error -3- al eliminar Foto(s) ".$reg_archivo->id_archivo." Pterigio OD"; 
						} 
						
						// Borrar de Sistema de archivos??: PENDIENTE
						
					break; 
				}					
				$i++;
			} 			
		} 
		
		$arr_resultado["archivos"]=$arr_archivos;
		
		return $arr_resultado;
	} 
	
	function getThumbnail($archivo, $nuevo_ancho=80, $nuevo_alto=null){ 

		// Dependiendo de la extensión, asignar o generar el thumbnail 
		$ext = explode(".", $archivo);
		$ext = strtolower($ext[count($ext) - 1]);
		if ($ext == "jpeg") { $ext = "jpg"; }
/*		
		require_once "../Db/DbVariables.php"; 
$ruta_privada="C:/imagenes_hce_dev"; 		
		$dbVariables = new Dbvariables(); 
		$resultado=$dbVariables->getVariable(17); 
		$ruta_privada=$resultado["valor_variable"]; 
*/ 
		require_once "../Db/DbVariables.php"; 
//$ruta_privada="C:/imagenes_hce_dev"; 		
		$dbVariables = new Dbvariables(); 
		$resultado=$dbVariables->getVariable(17); 
		$ruta_privada=$resultado["valor_variable"]; 			
echo "<li>RutaPrivada = ".$ruta_privada;
		
//echo " __________________".$ruta_privada."_ "; 
$ruta_privada="C:/imagenes_hce_dev"; 
		$ruta_privada=str_replace("/", "", $ruta_privada); 
		$ruta_privada=str_replace("\\", "", $ruta_privada); 
		switch ($ext) { 
			case "jpg": 
			case "png": 
			case "gif": 
				$ruta_destino="../tmp/thumbnails/thumb".str_replace( "/" , "_" , str_replace($ruta_privada, "", dirname($archivo)))."_".basename($archivo); 
				break; 
			case "pdf": 
				$ruta_destino="../imagenes/thumbnail_pdf.png"; 
				break;				
			default: 
				$ruta_destino="../imagenes/thumbnail.png"; 
		}	

//echo "<li>dirname(archivo) =".dirname($archivo)."; ruta_destino=".$ruta_destino."; ruta_privada=".$ruta_privada."; archivo=".$archivo."; replaceInt=".str_replace($ruta_privada, "", dirname($archivo))."; replaceExt=".str_replace( "/" , "_" , str_replace($ruta_privada, "", dirname($archivo))); 
		if (is_file($ruta_destino)) { return $ruta_destino; } 
//echo " ===> existe!!!"; 
		
		// Obtener la relación de aspecto
		list($ancho, $alto) = getimagesize($archivo);
		$porcentaje = $ancho / $alto;  
		 
		// Calcular las nuevas dimensiones
		if (is_null($nuevo_alto)) { 
			$nuevo_alto = round($nuevo_ancho / $porcentaje); 
		} 

		// Dependiendo de la extensión, llamar a distintas funciones para crear la nueva imagen 
		switch ($ext) {
			case "jpg": $imagen = imagecreatefromjpeg($archivo); break;
			case "png": $imagen = imagecreatefrompng($archivo); break;
			case "gif": $imagen = imagecreatefromgif($archivo); break;
			/* imagecreatefrombmp			imagecreatefromgd2			imagecreatefromgd2part			imagecreatefromgd			imagecreatefromstring			imagecreatefromwbmp
			imagecreatefromwebp			imagecreatefromxbm			imagecreatefromxpm			*/			
		}
		
		// Redimensionar
		$imagen_p = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);	
		imagecopyresampled($imagen_p, $imagen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);

		// Imprimir 		
		imagejpeg($imagen_p, $ruta_destino); 
		//'../imagenes/ojos_tonometria_od.png', //pathinfo($archivo["ruta"], PATHINFO_DIRNAME); //$archivo["ruta"] 
		
		return $ruta_destino; 
	}
}