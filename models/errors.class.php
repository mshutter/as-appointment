<?php 
class phpDump{	
	var $xmlDepth=array();
	var $xmlCData;
	var $xmlSData;
	var $xmlDData;
	var $xmlCount=0;
	var $xmlAttrib;
	var $xmlName;
	static $arrType=array("array","object","resource","boolean","NULL");
	static $bInitialized = false;
	static $bCollapsed = false;
	public static $arrHistory = array();
	public static $css;
	public static $javascript;
	public static $content = "";
	
	//make the constructor private and empty so that no code will create an object of this class.
	private function __construct(){}
	
	public static function debug($var,$forceType="",$bCollapsed=false) {
		//include js and css scripts
		if(!defined('BDBUGINIT')) {
			define("BDBUGINIT", TRUE);
			self::initJSandCSS();
		}
		$arrAccept=array("array","object","xml"); //array of variable types that can be "forced"
		self::$bCollapsed = $bCollapsed;
		if(in_array($forceType,$arrAccept))
			$this->{"varIs".ucfirst($forceType)}($var);
		else
			self::checkType($var);
	}

	//get variable name
	function getVariableName() {
		$arrBacktrace = debug_backtrace();

		//possible 'included' functions
		$arrInclude = array("include","include_once","require","require_once");
		
		//check for any included/required files. if found, get array of the last included file (they contain the right line numbers)
		for($i=count($arrBacktrace)-1; $i>=0; $i--) {
			$arrCurrent = $arrBacktrace[$i];
			if(array_key_exists("function", $arrCurrent) && 
				(in_array($arrCurrent["function"], $arrInclude) || (0 != strcasecmp($arrCurrent["function"], "debug"))))
				continue;

			$arrFile = $arrCurrent;
			
			break;
		}
		
		if(isset($arrFile)) {
			$arrLines = file($arrFile["file"]);
			$code = $arrLines[($arrFile["line"]-1)];
	
			//find call to debug class
			preg_match('/\bnew debug\s*\(\s*(.+)\s*\);/i', $code, $arrMatches);
			if(isset($arrMatches[1])){
				return $arrMatches[1];
			}
		}
		return "";
	}
	
	//create the main table header
	function makeTableHeader($type,$header,$colspan=2) {
		if(!self::$bInitialized) {
			$header = self::getVariableName() . " (" . $header . ")";
			self::$bInitialized = true;
		}
		$str_i = (self::$bCollapsed) ? "style='font-style:italic' " : ""; 
		
		self::$content .= "<table cellspacing=2 cellpadding=3 class='debug_".$type."'>
				<tr>
					<td ".$str_i."class='debug_".$type."Header' colspan=".$colspan." onClick='debug_toggleTable(this)'>".$header."</td>
				</tr>";
	}
	
	//create the table row header
	function makeTDHeader($type,$header) {
		$str_d = (self::$bCollapsed) ? " style='display:none'" : "";
		self::$content .= "<tr".$str_d.">
				<td valign='top' onClick='debug_toggleRow(this)' class='debug_".$type."Key'>".$header."</td>
				<td>";
	}
	
	//close table row
	function closeTDRow() {
		return "</td></tr>\n";
	}
	
	//error
	function  error($type) {
		$error="Error: Variable cannot be a";
		// this just checks if the type starts with a vowel or "x" and displays either "a" or "an"
		if(in_array(substr($type,0,1),array("a","e","i","o","u","x")))
			$error.="n";
		return ($error." ".$type." type");
	}

	//check variable type
	function checkType($var) {
		switch(gettype($var)) {
			case "resource":
				self::varIsResource($var);
				break;
			case "object":
				self::varIsObject($var);
				break;
			case "array":
				self::varIsArray($var);
				break;
			case "NULL":
				self::varIsNULL();
				break;
			case "boolean":
				self::varIsBoolean($var);
				break;
			default:
				$var=($var=="") ? "[empty string]" : $var;
				self::$content .= "<table cellspacing=0><tr>\n<td>".$var."</td>\n</tr>\n</table>\n";
				break;
		}
	}
	
	//if variable is a NULL type
	function varIsNULL() {
		self::$content .= "NULL";
	}
	
	//if variable is a boolean type
	function varIsBoolean($var) {
		$var=($var==1) ? "TRUE" : "FALSE";
		self::$content .= $var;
	}
			
	//if variable is an array type
	function varIsArray($var) {
		$var_ser = serialize($var);
		array_push(self::$arrHistory, $var_ser);
		
		self::makeTableHeader("array","array");
		if(is_array($var)) {
			foreach($var as $key=>$value) {
				self::makeTDHeader("array",$key);
				
				//check for recursion
				if(is_array($value)) {
					$var_ser = serialize($value);
					if(in_array($var_ser, self::$arrHistory, TRUE))
						$value = "*RECURSION*";
				}
				
				if(in_array(gettype($value),self::$arrType))
					self::checkType($value);
				else {
					$value=(trim($value)=="") ? "[empty string]" : $value;
					self::$content .= $value;
				}
				self::$content .= self::closeTDRow();
			}
		}
		else self::$content .= "<tr><td>".$this->error("array").self::closeTDRow();
		array_pop(self::$arrHistory);
		self::$content .= "</table>";
	}
	
	//if variable is an object type
	function varIsObject($var) {
		$var_ser = serialize($var);
		array_push(self::$arrHistory, $var_ser);
		self::makeTableHeader("object","object");
		
		if(is_object($var)) {
			$arrObjVars=get_object_vars($var);
			foreach($arrObjVars as $key=>$value) {

				$value=(!is_object($value) && !is_array($value) && trim($value)=="") ? "[empty string]" : $value;
				self::makeTDHeader("object",$key);
				
				//check for recursion
				if(is_object($value)||is_array($value)) {
					$var_ser = serialize($value);
					if(in_array($var_ser, self::$arrHistory, TRUE)) {
						$value = (is_object($value)) ? "*RECURSION* -> $".get_class($value) : "*RECURSION*";

					}

				}
				if(in_array(gettype($value),self::$arrType))
					self::checkType($value);
				else self::$content .= $value;
				self::$content .= self::closeTDRow();
			}
			$arrObjMethods=get_class_methods(get_class($var));
			foreach($arrObjMethods as $key=>$value) {
				self::makeTDHeader("object",$value);
				self::$content .= "[function]".self::closeTDRow();
			}
		}
		else self::$content .= "<tr><td>".self::error("object").self::closeTDRow();
		array_pop(self::$arrHistory);
		self::$content .= "</table>";
	}

	//if variable is a resource type
	function varIsResource($var) {
		self::makeTableHeader("resourceC","resource",1);
		self::$content .= "<tr>\n<td>\n";
		switch(get_resource_type($var)) {
			case "fbsql result":
			case "mssql result":
			case "msql query":
			case "pgsql result":
			case "sybase-db result":
			case "sybase-ct result":
			case "mysql result":
				$db=current(explode(" ",get_resource_type($var)));
				self::varIsDBResource($var,$db);
				break;
			case "gd":
				self::varIsGDResource($var);
				break;
			case "xml":
				self::varIsXmlResource($var);
				break;
			default:
				self::$content .= get_resource_type($var).self::closeTDRow();
				break;
		}
		self::$content .= self::closeTDRow()."</table>\n";
	}

	//if variable is a database resource type
	function varIsDBResource($var,$db="mysql") {
		if($db == "pgsql")
			$db = "pg";
		if($db == "sybase-db" || $db == "sybase-ct")
			$db = "sybase";
		$arrFields = array("name","type","flags");	
		$numrows=call_user_func($db."_num_rows",$var);
		$numfields=call_user_func($db."_num_fields",$var);
		self::makeTableHeader("resource",$db." result",$numfields+1);
		self::$content .= "<tr><td class='debug_resourceKey'>&nbsp;</td>";
		for($i=0;$i<$numfields;$i++) {
			$field_header = "";
			for($j=0; $j<count($arrFields); $j++) {
				$db_func = $db."_field_".$arrFields[$j];
				if(function_exists($db_func)) {
					$fheader = call_user_func($db_func, $var, $i). " ";
					if($j==0)
						$field_name = $fheader;
					else
						$field_header .= $fheader;
				}
			}
			$field[$i]=call_user_func($db."_fetch_field",$var,$i);
			self::$content .= "<td class='debug_resourceKey' title='".$field_header."'>".$field_name."</td>";
		}
		self::$content .= "</tr>";
		for($i=0;$i<$numrows;$i++) {
			$row=call_user_func($db."_fetch_array",$var,constant(strtoupper($db)."_ASSOC"));
			self::$content .= "<tr>\n";
			self::$content .= "<td class=\"debug_resourceKey\">".($i+1)."</td>"; 
			for($k=0;$k<$numfields;$k++) {
				$tempField=$field[$k]->name;
				$fieldrow=$row[($field[$k]->name)];
				$fieldrow=($fieldrow=="") ? "[empty string]" : $fieldrow;
				self::$content .= "<td>".$fieldrow."</td>\n";
			}
			self::$content .= "</tr>\n";
		}
		self::$content .= "</table>";
		if($numrows>0)
			call_user_func($db."_data_seek",$var,0);
	}
	
	//if variable is an image/gd resource type
	function varIsGDResource($var) {
		self::makeTableHeader("resource","gd",2);
		self::makeTDHeader("resource","Width");
		self::$content .= imagesx($var).self::closeTDRow();
		self::makeTDHeader("resource","Height");
		self::$content .= imagesy($var).self::closeTDRow();
		self::makeTDHeader("resource","Colors");
		self::$content .= imagecolorstotal($var).self::closeTDRow();
		self::$content .= "</table>";
	}
	
	//if variable is an xml type
	function varIsXml($var) {
		self::varIsXmlResource($var);
	}
	
	//if variable is an xml resource type
	function varIsXmlResource($var) {
		$xml_parser=xml_parser_create();
		xml_parser_set_option($xml_parser,XML_OPTION_CASE_FOLDING,0); 
		xml_set_element_handler($xml_parser,array(&$this,"xmlStartElement"),array(&$this,"xmlEndElement")); 
		xml_set_character_data_handler($xml_parser,array(&$this,"xmlCharacterData"));
		xml_set_default_handler($xml_parser,array(&$this,"xmlDefaultHandler")); 
		
		self::makeTableHeader("xml","xml document",2);
		self::makeTDHeader("xml","xmlRoot");
		
		//attempt to open xml file
		$bFile=(!($fp=@fopen($var,"r"))) ? false : true;
		
		//read xml file
		if($bFile) {
			while($data=str_replace("\n","",fread($fp,4096)))
				self::xmlParse($xml_parser,$data,feof($fp));
		}
		//if xml is not a file, attempt to read it as a string
		else {
			if(!is_string($var)) {
				self::$content .= self::error("xml").$this->closeTDRow()."</table>\n";
				return;
			}
			$data=$var;
			self::xmlParse($xml_parser,$data,1);
		}
		
		self::$content .= self::closeTDRow()."</table>\n";
		
	}
	
	//parse xml
	function xmlParse($xml_parser,$data,$bFinal) {
		if (!xml_parse($xml_parser,$data,$bFinal)) { 
				   die(sprintf("XML error: %s at line %d\n", 
							   xml_error_string(xml_get_error_code($xml_parser)), 
							   xml_get_current_line_number($xml_parser)));
		}
	}
	
	//xml: inititiated when a start tag is encountered
	function xmlStartElement($parser,$name,$attribs) {
		$this->xmlAttrib[$this->xmlCount]=$attribs;
		$this->xmlName[$this->xmlCount]=$name;
		$this->xmlSData[$this->xmlCount]='self::makeTableHeader("xml","xml element",2);';
		$this->xmlSData[$this->xmlCount].='$this->makeTDHeader("xml","xmlName");';
		$this->xmlSData[$this->xmlCount].='self::$content .= "<strong>'.$this->xmlName[$this->xmlCount].'</strong>".$this->closeTDRow();';
		$this->xmlSData[$this->xmlCount].='$this->makeTDHeader("xml","xmlAttributes");';
		if(count($attribs)>0)
			$this->xmlSData[$this->xmlCount].='$this->varIsArray($this->xmlAttrib['.$this->xmlCount.']);';
		else
			$this->xmlSData[$this->xmlCount].='self::$content .= "&nbsp;";';
		$this->xmlSData[$this->xmlCount].='self::$content .= $this->closeTDRow();';
		$this->xmlCount++;
	} 
	
	//xml: initiated when an end tag is encountered
	function xmlEndElement($parser,$name) {
		for($i=0;$i<$this->xmlCount;$i++) {
			eval($this->xmlSData[$i]);
			$this->makeTDHeader("xml","xmlText");
			self::$content .= (!empty($this->xmlCData[$i])) ? $this->xmlCData[$i] : "&nbsp;";
			self::$content .= $this->closeTDRow();
			$this->makeTDHeader("xml","xmlComment");
			self::$content .= (!empty($this->xmlDData[$i])) ? $this->xmlDData[$i] : "&nbsp;";
			self::$content .= $this->closeTDRow();
			$this->makeTDHeader("xml","xmlChildren");
			unset($this->xmlCData[$i],$this->xmlDData[$i]);
		}
		self::$content .= $this->closeTDRow();
		self::$content .= "</table>";
		$this->xmlCount=0;
	} 
	
	//xml: initiated when text between tags is encountered
	function xmlCharacterData($parser,$data) {
		$count=$this->xmlCount-1;
		if(!empty($this->xmlCData[$count]))
			$this->xmlCData[$count].=$data;
		else
			$this->xmlCData[$count]=$data;
	} 
	
	//xml: initiated when a comment or other miscellaneous texts is encountered
	function xmlDefaultHandler($parser,$data) {
		//strip '<!--' and '-->' off comments
		$data=str_replace(array("&lt;!--","--&gt;"),"",htmlspecialchars($data));
		$count=$this->xmlCount-1;
		if(!empty($this->xmlDData[$count]))
			$this->xmlDData[$count].=$data;
		else
			$this->xmlDData[$count]=$data;
	}

	function initJSandCSS() {
		self::$javascript = "
			<script language='JavaScript'>
			/* code modified from ColdFusion's cfdump code */
				function debug_toggleRow(source) {
					var target = (document.all) ? source.parentElement.cells[1] : source.parentNode.lastChild;
					debug_toggleTarget(target,debug_toggleSource(source));
				}
				
				function debug_toggleSource(source) {
					if (source.style.fontStyle=='italic') {
						source.style.fontStyle='normal';
						source.title='click to collapse';
						return 'open';
					} else {
						source.style.fontStyle='italic';
						source.title='click to expand';
						return 'closed';
					}
				}
			
				function debug_toggleTarget(target,switchToState) {
					target.style.display = (switchToState=='open') ? '' : 'none';
				}
			
				function debug_toggleTable(source) {
					var switchToState=debug_toggleSource(source);
					if(document.all) {
						var table=source.parentElement.parentElement;
						for(var i=1;i<table.rows.length;i++) {
							target=table.rows[i];
							debug_toggleTarget(target,switchToState);
						}
					}
					else {
						var table=source.parentNode.parentNode;
						for (var i=1;i<table.childNodes.length;i++) {
							target=table.childNodes[i];
							if(target.style) {
								debug_toggleTarget(target,switchToState);
							}
						}
					}
				}
			</script>";
		self::$css = "
			<style type='text/css'>
				table.debug_array,table.debug_object,table.debug_resource,table.debug_resourceC,table.debug_xml {
					font-family:Verdana, Arial, Helvetica, sans-serif; color:#000000; font-size:12px;
				}
				
				.debug_arrayHeader,
				.debug_objectHeader,
				.debug_resourceHeader,
				.debug_resourceCHeader,
				.debug_xmlHeader 
					{ font-weight:bold; color:#FFFFFF; cursor:pointer; }
				
				.debug_arrayKey,
				.debug_objectKey,
				.debug_xmlKey 
					{ cursor:pointer; }
					
				/* array */
				table.debug_array { background-color:#006600; }
				table.debug_array td { background-color:#FFFFFF; }
				table.debug_array td.debug_arrayHeader { background-color:#009900; }
				table.debug_array td.debug_arrayKey { background-color:#CCFFCC; }
				
				/* object */
				table.debug_object { background-color:#0000CC; }
				table.debug_object td { background-color:#FFFFFF; }
				table.debug_object td.debug_objectHeader { background-color:#4444CC; }
				table.debug_object td.debug_objectKey { background-color:#CCDDFF; }
				
				/* resource */
				table.debug_resourceC { background-color:#884488; }
				table.debug_resourceC td { background-color:#FFFFFF; }
				table.debug_resourceC td.debug_resourceCHeader { background-color:#AA66AA; }
				table.debug_resourceC td.debug_resourceCKey { background-color:#FFDDFF; }
				
				/* resource */
				table.debug_resource { background-color:#884488; }
				table.debug_resource td { background-color:#FFFFFF; }
				table.debug_resource td.debug_resourceHeader { background-color:#AA66AA; }
				table.debug_resource td.debug_resourceKey { background-color:#FFDDFF; }
				
				/* xml */
				table.debug_xml { background-color:#888888; }
				table.debug_xml td { background-color:#FFFFFF; }
				table.debug_xml td.debug_xmlHeader { background-color:#AAAAAA; }
				table.debug_xml td.debug_xmlKey { background-color:#DDDDDD; }
			</style>";
	}

}

class errors{	
	public $errorMessage;
	public $css;	
	public $javascript;
	// error handler function	
	function errorHandler($errno, $errstr, $errfile, $errline){
		$message = "";
		if(!(error_reporting() & $errno)){
			// This error code is not included in error_reporting
			return;
		}
		$caller = next(debug_backtrace());
		phpDump::debug($caller);
		$this->javascript = phpDump::$javascript;
		$this->css = phpDump::$css;
		$this->errorMessage = phpDump::$content;				
		echo phpDump::$content;
		switch ($errno) {
		case E_USER_ERROR:
			$message .= "<b>My ERROR</b> [$errno] $errstr<br />\n";
			$message .= "  Fatal error on line $errline in file $errfile";
			$message .= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
			$message .= "Aborting...<br />\n";
			exit(1);
			break;
	
		case E_USER_WARNING:
			$message .= "<b>My WARNING</b> [$errno] $errstr<br />\n";
			$message .= "  Warning is on line $errline in file $errfile";
			break;
	
		case E_USER_NOTICE:
			$message .= "<b>My NOTICE</b> [$errno] $errstr<br />\n";
			break;
	
		default:
			$message .= "Unknown error type: [$errno] $errstr<br />\n";
			$message .= "  Unknown error is on line $errline in file $errfile";
			break;
		}
		$this->errorEmailHandler("Site Wide Error Handler", $message);
				
		/* Don't execute PHP internal error handler */
		return true;
	}
	
	// error handler function
	function fatalErrorHandler(){
		$message = "";
		$error = error_get_last();
		if($error["type"] == E_ERROR){
			phpDump::debug($caller);
			$this->javascript = phpDump::$javascript;
			$this->css = phpDump::$css;
			$this->errorMessage = phpDump::$content;
			
			$this->errorEmailHandler("Fatal Error Handler", $message);
		}
	}
	
	function exceptionHandler($exception){			
		$this->errorEmailHandler("Exception Handler", $exception->getMessage());
	}
	
	//if an error happens the web programmer is notified on live, 
	public function errorEmailHandler($subject, $message){
		if(strpos($message, "Invalid credentials") !== false){
			$message = "Invalid Username or Password";
			/*if(settings::settings()->debug == "true"){
				settings::message($message, "alert alert-danger", "fa fa-times");		
			}*/
		}else{
			$message = "<h2>" . $subject . "</h2>" . $message;
			$message = str_replace("\\", "&#92;", $message);
			$message = str_replace("<&#92;/script>", "<\\/script>", $message);
			$message = str_replace("FALSE", "", $message);
			/*if(settings::settings()->debug == "true"){
				settings::$javascript .= "\n$('#message').append(\"" . trim(preg_replace('/\s+/', ' ', $message)) . "\");\n";		
			}*/
		}
		//echo $message;
		$this->errorMessage = $message;
		/*if(gethostname() == "WEBSOFT"){*/				
			$to = "cogsweay@alfredstate.edu";
			$subject = "Print Request - $subject";
			$from = "webteam@alfredstate.edu";
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: webteam@alfredstate.edu' . "\r\n" .
			'Reply-To: ' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
			//send the email
			//mail($to, $subject, $message, $headers);
		/*}else{*/
			
		//}			
	}	
}//end class
?>