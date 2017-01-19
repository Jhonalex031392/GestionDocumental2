<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "entradainfo.php" ?>
<?php include_once "usuariosinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$entrada_delete = NULL; // Initialize page object first

class centrada_delete extends centrada {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{D74DC9FA-763C-48C4-880F-6C317035A0C2}";

	// Table name
	var $TableName = 'entrada';

	// Page object name
	var $PageObjName = 'entrada_delete';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-error ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<table class=\"ewStdTable\"><tr><td><div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div></td></tr></table>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language, $UserAgent;

		// User agent
		$UserAgent = ew_UserAgent();
		$GLOBALS["Page"] = &$this;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (entrada)
		if (!isset($GLOBALS["entrada"])) {
			$GLOBALS["entrada"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["entrada"];
		}

		// Table object (usuarios)
		if (!isset($GLOBALS['usuarios'])) $GLOBALS['usuarios'] = new cusuarios();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'entrada', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Security
		$Security = new cAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		if (!$Security->IsLoggedIn()) {
			$Security->SaveLastUrl();
			$this->Page_Terminate("login.php");
		}
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up curent action

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $conn;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();
		$this->Page_Redirecting($url);

		 // Close connection
		$conn->Close();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}
	var $TotalRecs = 0;
	var $RecCnt;
	var $RecKeys = array();
	var $Recordset;
	var $StartRowCnt = 1;
	var $RowCnt = 0;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Load key parameters
		$this->RecKeys = $this->GetRecordKeys(); // Load record keys
		$sFilter = $this->GetKeyFilter();
		if ($sFilter == "")
			$this->Page_Terminate("entradalist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in entrada class, entradainfo.php

		$this->CurrentFilter = $sFilter;

		// Get action
		if (@$_POST["a_delete"] <> "") {
			$this->CurrentAction = $_POST["a_delete"];
		} else {
			$this->CurrentAction = "I"; // Display record
		}
		switch ($this->CurrentAction) {
			case "D": // Delete
				$this->SendEmail = TRUE; // Send email on delete success
				if ($this->DeleteRows()) { // Delete rows
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("DeleteSuccess")); // Set up success message
					$this->Page_Terminate($this->getReturnUrl()); // Return to caller
				}
		}
	}

// No functions
	// Load recordset
	function LoadRecordset($offset = -1, $rowcnt = -1) {
		global $conn;

		// Call Recordset Selecting event
		$this->Recordset_Selecting($this->CurrentFilter);

		// Load List page SQL
		$sSql = $this->SelectSQL();
		if ($offset > -1 && $rowcnt > -1)
			$sSql .= " LIMIT $rowcnt OFFSET $offset";

		// Load recordset
		$rs = ew_LoadRecordset($sSql);

		// Call Recordset Selected event
		$this->Recordset_Selected($rs);
		return $rs;
	}

	// Load row based on key values
	function LoadRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		global $conn;
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->numero->setDbValue($rs->fields('numero'));
		$this->radicado->setDbValue($rs->fields('radicado'));
		$this->clase_documento->setDbValue($rs->fields('clase_documento'));
		$this->identificacion->setDbValue($rs->fields('identificacion'));
		$this->nombre_rem->setDbValue($rs->fields('nombre_rem'));
		$this->apellidos_rem->setDbValue($rs->fields('apellidos_rem'));
		$this->telefono->setDbValue($rs->fields('telefono'));
		$this->direccion->setDbValue($rs->fields('direccion'));
		$this->correo_electronico->setDbValue($rs->fields('correo_electronico'));
		$this->pais->setDbValue($rs->fields('pais'));
		$this->departamento->setDbValue($rs->fields('departamento'));
		$this->cuidad->setDbValue($rs->fields('cuidad'));
		$this->asunto->setDbValue($rs->fields('asunto'));
		$this->descripcion->setDbValue($rs->fields('descripcion'));
		$this->adjuntar->Upload->DbValue = $rs->fields('adjuntar');
		$this->destino->setDbValue($rs->fields('destino'));
		$this->SecciF3n->setDbValue($rs->fields('Sección'));
		$this->subseccion->setDbValue($rs->fields('subseccion'));
		$this->Fecha_De_Ingreso->setDbValue($rs->fields('Fecha De Ingreso'));
		$this->hora->setDbValue($rs->fields('hora'));
		$this->Fecha_Documento->setDbValue($rs->fields('Fecha Documento'));
		$this->Tiempo_Documento->setDbValue($rs->fields('Tiempo Documento'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->numero->DbValue = $row['numero'];
		$this->radicado->DbValue = $row['radicado'];
		$this->clase_documento->DbValue = $row['clase_documento'];
		$this->identificacion->DbValue = $row['identificacion'];
		$this->nombre_rem->DbValue = $row['nombre_rem'];
		$this->apellidos_rem->DbValue = $row['apellidos_rem'];
		$this->telefono->DbValue = $row['telefono'];
		$this->direccion->DbValue = $row['direccion'];
		$this->correo_electronico->DbValue = $row['correo_electronico'];
		$this->pais->DbValue = $row['pais'];
		$this->departamento->DbValue = $row['departamento'];
		$this->cuidad->DbValue = $row['cuidad'];
		$this->asunto->DbValue = $row['asunto'];
		$this->descripcion->DbValue = $row['descripcion'];
		$this->adjuntar->Upload->DbValue = $row['adjuntar'];
		$this->destino->DbValue = $row['destino'];
		$this->SecciF3n->DbValue = $row['Sección'];
		$this->subseccion->DbValue = $row['subseccion'];
		$this->Fecha_De_Ingreso->DbValue = $row['Fecha De Ingreso'];
		$this->hora->DbValue = $row['hora'];
		$this->Fecha_Documento->DbValue = $row['Fecha Documento'];
		$this->Tiempo_Documento->DbValue = $row['Tiempo Documento'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// numero
		// radicado
		// clase_documento
		// identificacion
		// nombre_rem
		// apellidos_rem
		// telefono
		// direccion
		// correo_electronico
		// pais
		// departamento
		// cuidad
		// asunto
		// descripcion
		// adjuntar
		// destino
		// Sección
		// subseccion
		// Fecha De Ingreso
		// hora
		// Fecha Documento
		// Tiempo Documento

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// numero
			$this->numero->ViewValue = $this->numero->CurrentValue;
			$this->numero->ViewCustomAttributes = "";

			// radicado
			$this->radicado->ViewValue = $this->radicado->CurrentValue;
			$this->radicado->ViewCustomAttributes = "";

			// clase_documento
			if (strval($this->clase_documento->CurrentValue) <> "") {
				$sFilterWrk = "`nombre`" . ew_SearchString("=", $this->clase_documento->CurrentValue, EW_DATATYPE_STRING);
			$sSqlWrk = "SELECT `nombre`, `nombre` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `clasedocumento`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->clase_documento, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->clase_documento->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->clase_documento->ViewValue = $this->clase_documento->CurrentValue;
				}
			} else {
				$this->clase_documento->ViewValue = NULL;
			}
			$this->clase_documento->ViewCustomAttributes = "";

			// identificacion
			$this->identificacion->ViewValue = $this->identificacion->CurrentValue;
			$this->identificacion->ViewCustomAttributes = "";

			// nombre_rem
			$this->nombre_rem->ViewValue = $this->nombre_rem->CurrentValue;
			$this->nombre_rem->ViewCustomAttributes = "";

			// apellidos_rem
			$this->apellidos_rem->ViewValue = $this->apellidos_rem->CurrentValue;
			$this->apellidos_rem->ViewCustomAttributes = "";

			// telefono
			$this->telefono->ViewValue = $this->telefono->CurrentValue;
			$this->telefono->ViewCustomAttributes = "";

			// direccion
			$this->direccion->ViewValue = $this->direccion->CurrentValue;
			$this->direccion->ViewCustomAttributes = "";

			// correo_electronico
			$this->correo_electronico->ViewValue = $this->correo_electronico->CurrentValue;
			$this->correo_electronico->ViewCustomAttributes = "";

			// pais
			if (strval($this->pais->CurrentValue) <> "") {
				$sFilterWrk = "`Code`" . ew_SearchString("=", $this->pais->CurrentValue, EW_DATATYPE_STRING);
			$sSqlWrk = "SELECT `Code`, `Name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `country`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->pais, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Name`";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->pais->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->pais->ViewValue = $this->pais->CurrentValue;
				}
			} else {
				$this->pais->ViewValue = NULL;
			}
			$this->pais->ViewCustomAttributes = "";

			// departamento
			if (strval($this->departamento->CurrentValue) <> "") {
				$sFilterWrk = "`Name`" . ew_SearchString("=", $this->departamento->CurrentValue, EW_DATATYPE_STRING);
			$sSqlWrk = "SELECT `Name`, `Name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `province`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->departamento, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Name`";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->departamento->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->departamento->ViewValue = $this->departamento->CurrentValue;
				}
			} else {
				$this->departamento->ViewValue = NULL;
			}
			$this->departamento->ViewCustomAttributes = "";

			// cuidad
			if (strval($this->cuidad->CurrentValue) <> "") {
				$sFilterWrk = "`Name`" . ew_SearchString("=", $this->cuidad->CurrentValue, EW_DATATYPE_STRING);
			$sSqlWrk = "SELECT `Name`, `Name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `city`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->cuidad, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Name`";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->cuidad->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->cuidad->ViewValue = $this->cuidad->CurrentValue;
				}
			} else {
				$this->cuidad->ViewValue = NULL;
			}
			$this->cuidad->ViewCustomAttributes = "";

			// asunto
			$this->asunto->ViewValue = $this->asunto->CurrentValue;
			$this->asunto->ViewCustomAttributes = "";

			// descripcion
			$this->descripcion->ViewValue = $this->descripcion->CurrentValue;
			$this->descripcion->ViewCustomAttributes = "";

			// adjuntar
			if (!ew_Empty($this->adjuntar->Upload->DbValue)) {
				$this->adjuntar->ViewValue = $this->adjuntar->Upload->DbValue;
			} else {
				$this->adjuntar->ViewValue = "";
			}
			$this->adjuntar->ViewCustomAttributes = "";

			// destino
			if (strval($this->destino->CurrentValue) <> "") {
				$sFilterWrk = "`nombre_sede`" . ew_SearchString("=", $this->destino->CurrentValue, EW_DATATYPE_STRING);
			$sSqlWrk = "SELECT `nombre_sede`, `nombre_sede` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `sede`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->destino, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `nombre_sede`";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->destino->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->destino->ViewValue = $this->destino->CurrentValue;
				}
			} else {
				$this->destino->ViewValue = NULL;
			}
			$this->destino->ViewCustomAttributes = "";

			// Sección
			if (strval($this->SecciF3n->CurrentValue) <> "") {
				$sFilterWrk = "`codigo_seccion`" . ew_SearchString("=", $this->SecciF3n->CurrentValue, EW_DATATYPE_STRING);
			$sSqlWrk = "SELECT `codigo_seccion`, `nombre_seccion` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `seccion`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->SecciF3n, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `nombre_seccion`";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->SecciF3n->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->SecciF3n->ViewValue = $this->SecciF3n->CurrentValue;
				}
			} else {
				$this->SecciF3n->ViewValue = NULL;
			}
			$this->SecciF3n->ViewCustomAttributes = "";

			// subseccion
			if (strval($this->subseccion->CurrentValue) <> "") {
				$sFilterWrk = "`codigo_subseccion`" . ew_SearchString("=", $this->subseccion->CurrentValue, EW_DATATYPE_STRING);
			$sSqlWrk = "SELECT `codigo_subseccion`, `nombre_subseccion` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `subseccion`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->subseccion, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `nombre_subseccion`";
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->subseccion->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->subseccion->ViewValue = $this->subseccion->CurrentValue;
				}
			} else {
				$this->subseccion->ViewValue = NULL;
			}
			$this->subseccion->ViewCustomAttributes = "";

			// Fecha De Ingreso
			$this->Fecha_De_Ingreso->ViewValue = $this->Fecha_De_Ingreso->CurrentValue;
			$this->Fecha_De_Ingreso->ViewValue = ew_FormatDateTime($this->Fecha_De_Ingreso->ViewValue, 5);
			$this->Fecha_De_Ingreso->ViewCustomAttributes = "";

			// hora
			$this->hora->ViewValue = $this->hora->CurrentValue;
			$this->hora->ViewCustomAttributes = "";

			// Fecha Documento
			$this->Fecha_Documento->ViewValue = $this->Fecha_Documento->CurrentValue;
			$this->Fecha_Documento->ViewValue = ew_FormatDateTime($this->Fecha_Documento->ViewValue, 5);
			$this->Fecha_Documento->ViewCustomAttributes = "";

			// Tiempo Documento
			$this->Tiempo_Documento->ViewValue = $this->Tiempo_Documento->CurrentValue;
			$this->Tiempo_Documento->ViewCustomAttributes = "";

			// numero
			$this->numero->LinkCustomAttributes = "";
			$this->numero->HrefValue = "";
			$this->numero->TooltipValue = "";

			// radicado
			$this->radicado->LinkCustomAttributes = "";
			$this->radicado->HrefValue = "";
			$this->radicado->TooltipValue = "";

			// clase_documento
			$this->clase_documento->LinkCustomAttributes = "";
			$this->clase_documento->HrefValue = "";
			$this->clase_documento->TooltipValue = "";

			// identificacion
			$this->identificacion->LinkCustomAttributes = "";
			$this->identificacion->HrefValue = "";
			$this->identificacion->TooltipValue = "";

			// nombre_rem
			$this->nombre_rem->LinkCustomAttributes = "";
			$this->nombre_rem->HrefValue = "";
			$this->nombre_rem->TooltipValue = "";

			// apellidos_rem
			$this->apellidos_rem->LinkCustomAttributes = "";
			$this->apellidos_rem->HrefValue = "";
			$this->apellidos_rem->TooltipValue = "";

			// telefono
			$this->telefono->LinkCustomAttributes = "";
			$this->telefono->HrefValue = "";
			$this->telefono->TooltipValue = "";

			// direccion
			$this->direccion->LinkCustomAttributes = "";
			$this->direccion->HrefValue = "";
			$this->direccion->TooltipValue = "";

			// correo_electronico
			$this->correo_electronico->LinkCustomAttributes = "";
			$this->correo_electronico->HrefValue = "";
			$this->correo_electronico->TooltipValue = "";

			// pais
			$this->pais->LinkCustomAttributes = "";
			$this->pais->HrefValue = "";
			$this->pais->TooltipValue = "";

			// departamento
			$this->departamento->LinkCustomAttributes = "";
			$this->departamento->HrefValue = "";
			$this->departamento->TooltipValue = "";

			// cuidad
			$this->cuidad->LinkCustomAttributes = "";
			$this->cuidad->HrefValue = "";
			$this->cuidad->TooltipValue = "";

			// asunto
			$this->asunto->LinkCustomAttributes = "";
			$this->asunto->HrefValue = "";
			$this->asunto->TooltipValue = "";

			// descripcion
			$this->descripcion->LinkCustomAttributes = "";
			$this->descripcion->HrefValue = "";
			$this->descripcion->TooltipValue = "";

			// adjuntar
			$this->adjuntar->LinkCustomAttributes = "";
			$this->adjuntar->HrefValue = "";
			$this->adjuntar->HrefValue2 = $this->adjuntar->UploadPath . $this->adjuntar->Upload->DbValue;
			$this->adjuntar->TooltipValue = "";

			// destino
			$this->destino->LinkCustomAttributes = "";
			$this->destino->HrefValue = "";
			$this->destino->TooltipValue = "";

			// Sección
			$this->SecciF3n->LinkCustomAttributes = "";
			$this->SecciF3n->HrefValue = "";
			$this->SecciF3n->TooltipValue = "";

			// subseccion
			$this->subseccion->LinkCustomAttributes = "";
			$this->subseccion->HrefValue = "";
			$this->subseccion->TooltipValue = "";

			// Fecha De Ingreso
			$this->Fecha_De_Ingreso->LinkCustomAttributes = "";
			$this->Fecha_De_Ingreso->HrefValue = "";
			$this->Fecha_De_Ingreso->TooltipValue = "";

			// hora
			$this->hora->LinkCustomAttributes = "";
			$this->hora->HrefValue = "";
			$this->hora->TooltipValue = "";

			// Fecha Documento
			$this->Fecha_Documento->LinkCustomAttributes = "";
			$this->Fecha_Documento->HrefValue = "";
			$this->Fecha_Documento->TooltipValue = "";

			// Tiempo Documento
			$this->Tiempo_Documento->LinkCustomAttributes = "";
			$this->Tiempo_Documento->HrefValue = "";
			$this->Tiempo_Documento->TooltipValue = "";
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	//
	// Delete records based on current filter
	//
	function DeleteRows() {
		global $conn, $Language, $Security;
		$DeleteRows = TRUE;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE) {
			return FALSE;
		} elseif ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
			$rs->Close();
			return FALSE;

		//} else {
		//	$this->LoadRowValues($rs); // Load row values

		}
		$conn->BeginTrans();

		// Clone old rows
		$rsold = ($rs) ? $rs->GetRows() : array();
		if ($rs)
			$rs->Close();

		// Call row deleting event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$DeleteRows = $this->Row_Deleting($row);
				if (!$DeleteRows) break;
			}
		}
		if ($DeleteRows) {
			$sKey = "";
			foreach ($rsold as $row) {
				$sThisKey = "";
				if ($sThisKey <> "") $sThisKey .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
				$sThisKey .= $row['numero'];
				$conn->raiseErrorFn = 'ew_ErrorFn';
				$DeleteRows = $this->Delete($row); // Delete
				$conn->raiseErrorFn = '';
				if ($DeleteRows === FALSE)
					break;
				if ($sKey <> "") $sKey .= ", ";
				$sKey .= $sThisKey;
			}
		} else {

			// Set up error message
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("DeleteCancelled"));
			}
		}
		if ($DeleteRows) {
			$conn->CommitTrans(); // Commit the changes
		} else {
			$conn->RollbackTrans(); // Rollback changes
		}

		// Call Row Deleted event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$this->Row_Deleted($row);
			}
		}
		return $DeleteRows;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$PageCaption = $this->TableCaption();
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "entradalist.php", $this->TableVar);
		$PageCaption = $Language->Phrase("delete");
		$Breadcrumb->Add("delete", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($entrada_delete)) $entrada_delete = new centrada_delete();

// Page init
$entrada_delete->Page_Init();

// Page main
$entrada_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$entrada_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var entrada_delete = new ew_Page("entrada_delete");
entrada_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = entrada_delete.PageID; // For backward compatibility

// Form object
var fentradadelete = new ew_Form("fentradadelete");

// Form_CustomValidate event
fentradadelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fentradadelete.ValidateRequired = true;
<?php } else { ?>
fentradadelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fentradadelete.Lists["x_clase_documento"] = {"LinkField":"x_nombre","Ajax":null,"AutoFill":false,"DisplayFields":["x_nombre","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fentradadelete.Lists["x_pais"] = {"LinkField":"x_Code","Ajax":null,"AutoFill":false,"DisplayFields":["x_Name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fentradadelete.Lists["x_departamento"] = {"LinkField":"x_Name","Ajax":null,"AutoFill":false,"DisplayFields":["x_Name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fentradadelete.Lists["x_cuidad"] = {"LinkField":"x_Name","Ajax":null,"AutoFill":false,"DisplayFields":["x_Name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fentradadelete.Lists["x_destino"] = {"LinkField":"x_nombre_sede","Ajax":null,"AutoFill":false,"DisplayFields":["x_nombre_sede","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fentradadelete.Lists["x_SecciF3n"] = {"LinkField":"x_codigo_seccion","Ajax":null,"AutoFill":false,"DisplayFields":["x_nombre_seccion","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fentradadelete.Lists["x_subseccion"] = {"LinkField":"x_codigo_subseccion","Ajax":null,"AutoFill":false,"DisplayFields":["x_nombre_subseccion","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($entrada_delete->Recordset = $entrada_delete->LoadRecordset())
	$entrada_deleteTotalRecs = $entrada_delete->Recordset->RecordCount(); // Get record count
if ($entrada_deleteTotalRecs <= 0) { // No record found, exit
	if ($entrada_delete->Recordset)
		$entrada_delete->Recordset->Close();
	$entrada_delete->Page_Terminate("entradalist.php"); // Return to list
}
?>
<?php $Breadcrumb->Render(); ?>
<?php $entrada_delete->ShowPageHeader(); ?>
<?php
$entrada_delete->ShowMessage();
?>
<form name="fentradadelete" id="fentradadelete" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="entrada">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($entrada_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_entradadelete" class="ewTable ewTableSeparate">
<?php echo $entrada->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
		<td><span id="elh_entrada_numero" class="entrada_numero"><?php echo $entrada->numero->FldCaption() ?></span></td>
		<td><span id="elh_entrada_radicado" class="entrada_radicado"><?php echo $entrada->radicado->FldCaption() ?></span></td>
		<td><span id="elh_entrada_clase_documento" class="entrada_clase_documento"><?php echo $entrada->clase_documento->FldCaption() ?></span></td>
		<td><span id="elh_entrada_identificacion" class="entrada_identificacion"><?php echo $entrada->identificacion->FldCaption() ?></span></td>
		<td><span id="elh_entrada_nombre_rem" class="entrada_nombre_rem"><?php echo $entrada->nombre_rem->FldCaption() ?></span></td>
		<td><span id="elh_entrada_apellidos_rem" class="entrada_apellidos_rem"><?php echo $entrada->apellidos_rem->FldCaption() ?></span></td>
		<td><span id="elh_entrada_telefono" class="entrada_telefono"><?php echo $entrada->telefono->FldCaption() ?></span></td>
		<td><span id="elh_entrada_direccion" class="entrada_direccion"><?php echo $entrada->direccion->FldCaption() ?></span></td>
		<td><span id="elh_entrada_correo_electronico" class="entrada_correo_electronico"><?php echo $entrada->correo_electronico->FldCaption() ?></span></td>
		<td><span id="elh_entrada_pais" class="entrada_pais"><?php echo $entrada->pais->FldCaption() ?></span></td>
		<td><span id="elh_entrada_departamento" class="entrada_departamento"><?php echo $entrada->departamento->FldCaption() ?></span></td>
		<td><span id="elh_entrada_cuidad" class="entrada_cuidad"><?php echo $entrada->cuidad->FldCaption() ?></span></td>
		<td><span id="elh_entrada_asunto" class="entrada_asunto"><?php echo $entrada->asunto->FldCaption() ?></span></td>
		<td><span id="elh_entrada_descripcion" class="entrada_descripcion"><?php echo $entrada->descripcion->FldCaption() ?></span></td>
		<td><span id="elh_entrada_adjuntar" class="entrada_adjuntar"><?php echo $entrada->adjuntar->FldCaption() ?></span></td>
		<td><span id="elh_entrada_destino" class="entrada_destino"><?php echo $entrada->destino->FldCaption() ?></span></td>
		<td><span id="elh_entrada_SecciF3n" class="entrada_SecciF3n"><?php echo $entrada->SecciF3n->FldCaption() ?></span></td>
		<td><span id="elh_entrada_subseccion" class="entrada_subseccion"><?php echo $entrada->subseccion->FldCaption() ?></span></td>
		<td><span id="elh_entrada_Fecha_De_Ingreso" class="entrada_Fecha_De_Ingreso"><?php echo $entrada->Fecha_De_Ingreso->FldCaption() ?></span></td>
		<td><span id="elh_entrada_hora" class="entrada_hora"><?php echo $entrada->hora->FldCaption() ?></span></td>
		<td><span id="elh_entrada_Fecha_Documento" class="entrada_Fecha_Documento"><?php echo $entrada->Fecha_Documento->FldCaption() ?></span></td>
		<td><span id="elh_entrada_Tiempo_Documento" class="entrada_Tiempo_Documento"><?php echo $entrada->Tiempo_Documento->FldCaption() ?></span></td>
	</tr>
	</thead>
	<tbody>
<?php
$entrada_delete->RecCnt = 0;
$i = 0;
while (!$entrada_delete->Recordset->EOF) {
	$entrada_delete->RecCnt++;
	$entrada_delete->RowCnt++;

	// Set row properties
	$entrada->ResetAttrs();
	$entrada->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$entrada_delete->LoadRowValues($entrada_delete->Recordset);

	// Render row
	$entrada_delete->RenderRow();
?>
	<tr<?php echo $entrada->RowAttributes() ?>>
		<td<?php echo $entrada->numero->CellAttributes() ?>><span id="el<?php echo $entrada_delete->RowCnt ?>_entrada_numero" class="control-group entrada_numero">
<span<?php echo $entrada->numero->ViewAttributes() ?>>
<?php echo $entrada->numero->ListViewValue() ?></span>
</span></td>
		<td<?php echo $entrada->radicado->CellAttributes() ?>><span id="el<?php echo $entrada_delete->RowCnt ?>_entrada_radicado" class="control-group entrada_radicado">
<span<?php echo $entrada->radicado->ViewAttributes() ?>>
<?php echo $entrada->radicado->ListViewValue() ?></span>
</span></td>
		<td<?php echo $entrada->clase_documento->CellAttributes() ?>><span id="el<?php echo $entrada_delete->RowCnt ?>_entrada_clase_documento" class="control-group entrada_clase_documento">
<span<?php echo $entrada->clase_documento->ViewAttributes() ?>>
<?php echo $entrada->clase_documento->ListViewValue() ?></span>
</span></td>
		<td<?php echo $entrada->identificacion->CellAttributes() ?>><span id="el<?php echo $entrada_delete->RowCnt ?>_entrada_identificacion" class="control-group entrada_identificacion">
<span<?php echo $entrada->identificacion->ViewAttributes() ?>>
<?php echo $entrada->identificacion->ListViewValue() ?></span>
</span></td>
		<td<?php echo $entrada->nombre_rem->CellAttributes() ?>><span id="el<?php echo $entrada_delete->RowCnt ?>_entrada_nombre_rem" class="control-group entrada_nombre_rem">
<span<?php echo $entrada->nombre_rem->ViewAttributes() ?>>
<?php echo $entrada->nombre_rem->ListViewValue() ?></span>
</span></td>
		<td<?php echo $entrada->apellidos_rem->CellAttributes() ?>><span id="el<?php echo $entrada_delete->RowCnt ?>_entrada_apellidos_rem" class="control-group entrada_apellidos_rem">
<span<?php echo $entrada->apellidos_rem->ViewAttributes() ?>>
<?php echo $entrada->apellidos_rem->ListViewValue() ?></span>
</span></td>
		<td<?php echo $entrada->telefono->CellAttributes() ?>><span id="el<?php echo $entrada_delete->RowCnt ?>_entrada_telefono" class="control-group entrada_telefono">
<span<?php echo $entrada->telefono->ViewAttributes() ?>>
<?php echo $entrada->telefono->ListViewValue() ?></span>
</span></td>
		<td<?php echo $entrada->direccion->CellAttributes() ?>><span id="el<?php echo $entrada_delete->RowCnt ?>_entrada_direccion" class="control-group entrada_direccion">
<span<?php echo $entrada->direccion->ViewAttributes() ?>>
<?php echo $entrada->direccion->ListViewValue() ?></span>
</span></td>
		<td<?php echo $entrada->correo_electronico->CellAttributes() ?>><span id="el<?php echo $entrada_delete->RowCnt ?>_entrada_correo_electronico" class="control-group entrada_correo_electronico">
<span<?php echo $entrada->correo_electronico->ViewAttributes() ?>>
<?php echo $entrada->correo_electronico->ListViewValue() ?></span>
</span></td>
		<td<?php echo $entrada->pais->CellAttributes() ?>><span id="el<?php echo $entrada_delete->RowCnt ?>_entrada_pais" class="control-group entrada_pais">
<span<?php echo $entrada->pais->ViewAttributes() ?>>
<?php echo $entrada->pais->ListViewValue() ?></span>
</span></td>
		<td<?php echo $entrada->departamento->CellAttributes() ?>><span id="el<?php echo $entrada_delete->RowCnt ?>_entrada_departamento" class="control-group entrada_departamento">
<span<?php echo $entrada->departamento->ViewAttributes() ?>>
<?php echo $entrada->departamento->ListViewValue() ?></span>
</span></td>
		<td<?php echo $entrada->cuidad->CellAttributes() ?>><span id="el<?php echo $entrada_delete->RowCnt ?>_entrada_cuidad" class="control-group entrada_cuidad">
<span<?php echo $entrada->cuidad->ViewAttributes() ?>>
<?php echo $entrada->cuidad->ListViewValue() ?></span>
</span></td>
		<td<?php echo $entrada->asunto->CellAttributes() ?>><span id="el<?php echo $entrada_delete->RowCnt ?>_entrada_asunto" class="control-group entrada_asunto">
<span<?php echo $entrada->asunto->ViewAttributes() ?>>
<?php echo $entrada->asunto->ListViewValue() ?></span>
</span></td>
		<td<?php echo $entrada->descripcion->CellAttributes() ?>><span id="el<?php echo $entrada_delete->RowCnt ?>_entrada_descripcion" class="control-group entrada_descripcion">
<span<?php echo $entrada->descripcion->ViewAttributes() ?>>
<?php echo $entrada->descripcion->ListViewValue() ?></span>
</span></td>
		<td<?php echo $entrada->adjuntar->CellAttributes() ?>><span id="el<?php echo $entrada_delete->RowCnt ?>_entrada_adjuntar" class="control-group entrada_adjuntar">
<span<?php echo $entrada->adjuntar->ViewAttributes() ?>>
<?php if ($entrada->adjuntar->LinkAttributes() <> "") { ?>
<?php if (!empty($entrada->adjuntar->Upload->DbValue)) { ?>
<?php echo $entrada->adjuntar->ListViewValue() ?>
<?php } elseif (!in_array($entrada->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } else { ?>
<?php if (!empty($entrada->adjuntar->Upload->DbValue)) { ?>
<?php echo $entrada->adjuntar->ListViewValue() ?>
<?php } elseif (!in_array($entrada->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } ?>
</span>
</span></td>
		<td<?php echo $entrada->destino->CellAttributes() ?>><span id="el<?php echo $entrada_delete->RowCnt ?>_entrada_destino" class="control-group entrada_destino">
<span<?php echo $entrada->destino->ViewAttributes() ?>>
<?php echo $entrada->destino->ListViewValue() ?></span>
</span></td>
		<td<?php echo $entrada->SecciF3n->CellAttributes() ?>><span id="el<?php echo $entrada_delete->RowCnt ?>_entrada_SecciF3n" class="control-group entrada_SecciF3n">
<span<?php echo $entrada->SecciF3n->ViewAttributes() ?>>
<?php echo $entrada->SecciF3n->ListViewValue() ?></span>
</span></td>
		<td<?php echo $entrada->subseccion->CellAttributes() ?>><span id="el<?php echo $entrada_delete->RowCnt ?>_entrada_subseccion" class="control-group entrada_subseccion">
<span<?php echo $entrada->subseccion->ViewAttributes() ?>>
<?php echo $entrada->subseccion->ListViewValue() ?></span>
</span></td>
		<td<?php echo $entrada->Fecha_De_Ingreso->CellAttributes() ?>><span id="el<?php echo $entrada_delete->RowCnt ?>_entrada_Fecha_De_Ingreso" class="control-group entrada_Fecha_De_Ingreso">
<span<?php echo $entrada->Fecha_De_Ingreso->ViewAttributes() ?>>
<?php echo $entrada->Fecha_De_Ingreso->ListViewValue() ?></span>
</span></td>
		<td<?php echo $entrada->hora->CellAttributes() ?>><span id="el<?php echo $entrada_delete->RowCnt ?>_entrada_hora" class="control-group entrada_hora">
<span<?php echo $entrada->hora->ViewAttributes() ?>>
<?php echo $entrada->hora->ListViewValue() ?></span>
</span></td>
		<td<?php echo $entrada->Fecha_Documento->CellAttributes() ?>><span id="el<?php echo $entrada_delete->RowCnt ?>_entrada_Fecha_Documento" class="control-group entrada_Fecha_Documento">
<span<?php echo $entrada->Fecha_Documento->ViewAttributes() ?>>
<?php echo $entrada->Fecha_Documento->ListViewValue() ?></span>
</span></td>
		<td<?php echo $entrada->Tiempo_Documento->CellAttributes() ?>><span id="el<?php echo $entrada_delete->RowCnt ?>_entrada_Tiempo_Documento" class="control-group entrada_Tiempo_Documento">
<span<?php echo $entrada->Tiempo_Documento->ViewAttributes() ?>>
<?php echo $entrada->Tiempo_Documento->ListViewValue() ?></span>
</span></td>
	</tr>
<?php
	$entrada_delete->Recordset->MoveNext();
}
$entrada_delete->Recordset->Close();
?>
</tbody>
</table>
</div>
</td></tr></table>
<div class="btn-group ewButtonGroup">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("DeleteBtn") ?></button>
</div>
</form>
<script type="text/javascript">
fentradadelete.Init();
</script>
<?php
$entrada_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$entrada_delete->Page_Terminate();
?>
