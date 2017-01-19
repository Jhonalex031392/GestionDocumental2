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

$entrada_add = NULL; // Initialize page object first

class centrada_add extends centrada {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{D74DC9FA-763C-48C4-880F-6C317035A0C2}";

	// Table name
	var $TableName = 'entrada';

	// Page object name
	var $PageObjName = 'entrada_add';

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
			define("EW_PAGE_ID", 'add', TRUE);

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

		// Create form object
		$objForm = new cFormObj();
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
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $Priv = 0;
	var $OldRecordset;
	var $CopyRecord;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Process form if post back
		if (@$_POST["a_add"] <> "") {
			$this->CurrentAction = $_POST["a_add"]; // Get form action
			$this->CopyRecord = $this->LoadOldRecord(); // Load old recordset
			$this->LoadFormValues(); // Load form values
		} else { // Not post back

			// Load key values from QueryString
			$this->CopyRecord = TRUE;
			if (@$_GET["numero"] != "") {
				$this->numero->setQueryStringValue($_GET["numero"]);
				$this->setKey("numero", $this->numero->CurrentValue); // Set up key
			} else {
				$this->setKey("numero", ""); // Clear key
				$this->CopyRecord = FALSE;
			}
			if ($this->CopyRecord) {
				$this->CurrentAction = "C"; // Copy record
			} else {
				$this->CurrentAction = "I"; // Display blank record
				$this->LoadDefaultValues(); // Load default values
			}
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Validate form if post back
		if (@$_POST["a_add"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = "I"; // Form error, reset action
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues(); // Restore form values
				$this->setFailureMessage($gsFormError);
			}
		}

		// Perform action based on action code
		switch ($this->CurrentAction) {
			case "I": // Blank record, no action required
				break;
			case "C": // Copy an existing record
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("entradalist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "entradaview.php")
						$sReturnUrl = $this->GetViewUrl(); // View paging, return to view page with keyurl directly
					$this->Page_Terminate($sReturnUrl); // Clean up and return
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Add failed, restore form values
				}
		}

		// Render row based on row type
		$this->RowType = EW_ROWTYPE_ADD;  // Render add type

		// Render row
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm;

		// Get upload data
		$this->adjuntar->Upload->Index = $objForm->Index;
		if ($this->adjuntar->Upload->UploadFile()) {

			// No action required
		} else {
			echo $this->adjuntar->Upload->Message;
			$this->Page_Terminate();
			exit();
		}
		$this->adjuntar->CurrentValue = $this->adjuntar->Upload->FileName;
	}

	// Load default values
	function LoadDefaultValues() {
		$this->radicado->CurrentValue = NULL;
		$this->radicado->OldValue = $this->radicado->CurrentValue;
		$this->clase_documento->CurrentValue = NULL;
		$this->clase_documento->OldValue = $this->clase_documento->CurrentValue;
		$this->identificacion->CurrentValue = NULL;
		$this->identificacion->OldValue = $this->identificacion->CurrentValue;
		$this->nombre_rem->CurrentValue = NULL;
		$this->nombre_rem->OldValue = $this->nombre_rem->CurrentValue;
		$this->apellidos_rem->CurrentValue = NULL;
		$this->apellidos_rem->OldValue = $this->apellidos_rem->CurrentValue;
		$this->telefono->CurrentValue = NULL;
		$this->telefono->OldValue = $this->telefono->CurrentValue;
		$this->direccion->CurrentValue = NULL;
		$this->direccion->OldValue = $this->direccion->CurrentValue;
		$this->correo_electronico->CurrentValue = NULL;
		$this->correo_electronico->OldValue = $this->correo_electronico->CurrentValue;
		$this->pais->CurrentValue = NULL;
		$this->pais->OldValue = $this->pais->CurrentValue;
		$this->departamento->CurrentValue = NULL;
		$this->departamento->OldValue = $this->departamento->CurrentValue;
		$this->cuidad->CurrentValue = NULL;
		$this->cuidad->OldValue = $this->cuidad->CurrentValue;
		$this->asunto->CurrentValue = NULL;
		$this->asunto->OldValue = $this->asunto->CurrentValue;
		$this->descripcion->CurrentValue = NULL;
		$this->descripcion->OldValue = $this->descripcion->CurrentValue;
		$this->adjuntar->Upload->DbValue = NULL;
		$this->adjuntar->OldValue = $this->adjuntar->Upload->DbValue;
		$this->adjuntar->CurrentValue = NULL; // Clear file related field
		$this->destino->CurrentValue = NULL;
		$this->destino->OldValue = $this->destino->CurrentValue;
		$this->SecciF3n->CurrentValue = NULL;
		$this->SecciF3n->OldValue = $this->SecciF3n->CurrentValue;
		$this->subseccion->CurrentValue = NULL;
		$this->subseccion->OldValue = $this->subseccion->CurrentValue;
		$this->Fecha_De_Ingreso->CurrentValue = NULL;
		$this->Fecha_De_Ingreso->OldValue = $this->Fecha_De_Ingreso->CurrentValue;
		$this->hora->CurrentValue = NULL;
		$this->hora->OldValue = $this->hora->CurrentValue;
		$this->Fecha_Documento->CurrentValue = NULL;
		$this->Fecha_Documento->OldValue = $this->Fecha_Documento->CurrentValue;
		$this->Tiempo_Documento->CurrentValue = NULL;
		$this->Tiempo_Documento->OldValue = $this->Tiempo_Documento->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		$this->GetUploadFiles(); // Get upload files
		if (!$this->radicado->FldIsDetailKey) {
			$this->radicado->setFormValue($objForm->GetValue("x_radicado"));
		}
		if (!$this->clase_documento->FldIsDetailKey) {
			$this->clase_documento->setFormValue($objForm->GetValue("x_clase_documento"));
		}
		if (!$this->identificacion->FldIsDetailKey) {
			$this->identificacion->setFormValue($objForm->GetValue("x_identificacion"));
		}
		if (!$this->nombre_rem->FldIsDetailKey) {
			$this->nombre_rem->setFormValue($objForm->GetValue("x_nombre_rem"));
		}
		if (!$this->apellidos_rem->FldIsDetailKey) {
			$this->apellidos_rem->setFormValue($objForm->GetValue("x_apellidos_rem"));
		}
		if (!$this->telefono->FldIsDetailKey) {
			$this->telefono->setFormValue($objForm->GetValue("x_telefono"));
		}
		if (!$this->direccion->FldIsDetailKey) {
			$this->direccion->setFormValue($objForm->GetValue("x_direccion"));
		}
		if (!$this->correo_electronico->FldIsDetailKey) {
			$this->correo_electronico->setFormValue($objForm->GetValue("x_correo_electronico"));
		}
		if (!$this->pais->FldIsDetailKey) {
			$this->pais->setFormValue($objForm->GetValue("x_pais"));
		}
		if (!$this->departamento->FldIsDetailKey) {
			$this->departamento->setFormValue($objForm->GetValue("x_departamento"));
		}
		if (!$this->cuidad->FldIsDetailKey) {
			$this->cuidad->setFormValue($objForm->GetValue("x_cuidad"));
		}
		if (!$this->asunto->FldIsDetailKey) {
			$this->asunto->setFormValue($objForm->GetValue("x_asunto"));
		}
		if (!$this->descripcion->FldIsDetailKey) {
			$this->descripcion->setFormValue($objForm->GetValue("x_descripcion"));
		}
		if (!$this->destino->FldIsDetailKey) {
			$this->destino->setFormValue($objForm->GetValue("x_destino"));
		}
		if (!$this->SecciF3n->FldIsDetailKey) {
			$this->SecciF3n->setFormValue($objForm->GetValue("x_SecciF3n"));
		}
		if (!$this->subseccion->FldIsDetailKey) {
			$this->subseccion->setFormValue($objForm->GetValue("x_subseccion"));
		}
		if (!$this->Fecha_De_Ingreso->FldIsDetailKey) {
			$this->Fecha_De_Ingreso->setFormValue($objForm->GetValue("x_Fecha_De_Ingreso"));
			$this->Fecha_De_Ingreso->CurrentValue = ew_UnFormatDateTime($this->Fecha_De_Ingreso->CurrentValue, 5);
		}
		if (!$this->hora->FldIsDetailKey) {
			$this->hora->setFormValue($objForm->GetValue("x_hora"));
		}
		if (!$this->Fecha_Documento->FldIsDetailKey) {
			$this->Fecha_Documento->setFormValue($objForm->GetValue("x_Fecha_Documento"));
			$this->Fecha_Documento->CurrentValue = ew_UnFormatDateTime($this->Fecha_Documento->CurrentValue, 5);
		}
		if (!$this->Tiempo_Documento->FldIsDetailKey) {
			$this->Tiempo_Documento->setFormValue($objForm->GetValue("x_Tiempo_Documento"));
		}
		if (!$this->numero->FldIsDetailKey)
			$this->numero->setFormValue($objForm->GetValue("x_numero"));
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->numero->CurrentValue = $this->numero->FormValue;
		$this->radicado->CurrentValue = $this->radicado->FormValue;
		$this->clase_documento->CurrentValue = $this->clase_documento->FormValue;
		$this->identificacion->CurrentValue = $this->identificacion->FormValue;
		$this->nombre_rem->CurrentValue = $this->nombre_rem->FormValue;
		$this->apellidos_rem->CurrentValue = $this->apellidos_rem->FormValue;
		$this->telefono->CurrentValue = $this->telefono->FormValue;
		$this->direccion->CurrentValue = $this->direccion->FormValue;
		$this->correo_electronico->CurrentValue = $this->correo_electronico->FormValue;
		$this->pais->CurrentValue = $this->pais->FormValue;
		$this->departamento->CurrentValue = $this->departamento->FormValue;
		$this->cuidad->CurrentValue = $this->cuidad->FormValue;
		$this->asunto->CurrentValue = $this->asunto->FormValue;
		$this->descripcion->CurrentValue = $this->descripcion->FormValue;
		$this->destino->CurrentValue = $this->destino->FormValue;
		$this->SecciF3n->CurrentValue = $this->SecciF3n->FormValue;
		$this->subseccion->CurrentValue = $this->subseccion->FormValue;
		$this->Fecha_De_Ingreso->CurrentValue = $this->Fecha_De_Ingreso->FormValue;
		$this->Fecha_De_Ingreso->CurrentValue = ew_UnFormatDateTime($this->Fecha_De_Ingreso->CurrentValue, 5);
		$this->hora->CurrentValue = $this->hora->FormValue;
		$this->Fecha_Documento->CurrentValue = $this->Fecha_Documento->FormValue;
		$this->Fecha_Documento->CurrentValue = ew_UnFormatDateTime($this->Fecha_Documento->CurrentValue, 5);
		$this->Tiempo_Documento->CurrentValue = $this->Tiempo_Documento->FormValue;
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

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("numero")) <> "")
			$this->numero->CurrentValue = $this->getKey("numero"); // numero
		else
			$bValidKey = FALSE;

		// Load old recordset
		if ($bValidKey) {
			$this->CurrentFilter = $this->KeyFilter();
			$sSql = $this->SQL();
			$this->OldRecordset = ew_LoadRecordset($sSql);
			$this->LoadRowValues($this->OldRecordset); // Load row values
		} else {
			$this->OldRecordset = NULL;
		}
		return $bValidKey;
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
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// radicado
			$this->radicado->EditCustomAttributes = "";
			$this->radicado->EditValue = ew_HtmlEncode($this->radicado->CurrentValue);
			$this->radicado->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->radicado->FldCaption()));

			// clase_documento
			$this->clase_documento->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `nombre`, `nombre` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `clasedocumento`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->clase_documento, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->clase_documento->EditValue = $arwrk;

			// identificacion
			$this->identificacion->EditCustomAttributes = "";
			$this->identificacion->EditValue = ew_HtmlEncode($this->identificacion->CurrentValue);
			$this->identificacion->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->identificacion->FldCaption()));

			// nombre_rem
			$this->nombre_rem->EditCustomAttributes = "";
			$this->nombre_rem->EditValue = ew_HtmlEncode($this->nombre_rem->CurrentValue);
			$this->nombre_rem->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->nombre_rem->FldCaption()));

			// apellidos_rem
			$this->apellidos_rem->EditCustomAttributes = "";
			$this->apellidos_rem->EditValue = ew_HtmlEncode($this->apellidos_rem->CurrentValue);
			$this->apellidos_rem->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->apellidos_rem->FldCaption()));

			// telefono
			$this->telefono->EditCustomAttributes = "";
			$this->telefono->EditValue = ew_HtmlEncode($this->telefono->CurrentValue);
			$this->telefono->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->telefono->FldCaption()));

			// direccion
			$this->direccion->EditCustomAttributes = "";
			$this->direccion->EditValue = ew_HtmlEncode($this->direccion->CurrentValue);
			$this->direccion->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->direccion->FldCaption()));

			// correo_electronico
			$this->correo_electronico->EditCustomAttributes = "";
			$this->correo_electronico->EditValue = ew_HtmlEncode($this->correo_electronico->CurrentValue);
			$this->correo_electronico->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->correo_electronico->FldCaption()));

			// pais
			$this->pais->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `Code`, `Name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `country`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->pais, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Name`";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->pais->EditValue = $arwrk;

			// departamento
			$this->departamento->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `Name`, `Name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `Country` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `province`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->departamento, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Name`";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->departamento->EditValue = $arwrk;

			// cuidad
			$this->cuidad->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `Name`, `Name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `Province` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `city`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->cuidad, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `Name`";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->cuidad->EditValue = $arwrk;

			// asunto
			$this->asunto->EditCustomAttributes = "";
			$this->asunto->EditValue = ew_HtmlEncode($this->asunto->CurrentValue);
			$this->asunto->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->asunto->FldCaption()));

			// descripcion
			$this->descripcion->EditCustomAttributes = "";
			$this->descripcion->EditValue = ew_HtmlEncode($this->descripcion->CurrentValue);
			$this->descripcion->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->descripcion->FldCaption()));

			// adjuntar
			$this->adjuntar->EditCustomAttributes = "";
			if (!ew_Empty($this->adjuntar->Upload->DbValue)) {
				$this->adjuntar->EditValue = $this->adjuntar->Upload->DbValue;
			} else {
				$this->adjuntar->EditValue = "";
			}
			if ($this->CurrentAction == "I" || $this->CurrentAction == "C") ew_RenderUploadField($this->adjuntar);

			// destino
			$this->destino->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `nombre_sede`, `nombre_sede` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `sede`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->destino, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `nombre_sede`";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->destino->EditValue = $arwrk;

			// Sección
			$this->SecciF3n->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `codigo_seccion`, `nombre_seccion` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `seccion`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->SecciF3n, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `nombre_seccion`";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->SecciF3n->EditValue = $arwrk;

			// subseccion
			$this->subseccion->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `codigo_subseccion`, `nombre_subseccion` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `codigo_seccion` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `subseccion`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->subseccion, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$sSqlWrk .= " ORDER BY `nombre_subseccion`";
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->subseccion->EditValue = $arwrk;

			// Fecha De Ingreso
			$this->Fecha_De_Ingreso->EditCustomAttributes = "";
			$this->Fecha_De_Ingreso->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->Fecha_De_Ingreso->CurrentValue, 5));
			$this->Fecha_De_Ingreso->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->Fecha_De_Ingreso->FldCaption()));

			// hora
			$this->hora->EditCustomAttributes = "";
			$this->hora->EditValue = ew_HtmlEncode($this->hora->CurrentValue);
			$this->hora->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->hora->FldCaption()));

			// Fecha Documento
			$this->Fecha_Documento->EditCustomAttributes = "";
			$this->Fecha_Documento->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->Fecha_Documento->CurrentValue, 5));
			$this->Fecha_Documento->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->Fecha_Documento->FldCaption()));

			// Tiempo Documento
			$this->Tiempo_Documento->EditCustomAttributes = "";
			$this->Tiempo_Documento->EditValue = ew_HtmlEncode($this->Tiempo_Documento->CurrentValue);
			$this->Tiempo_Documento->PlaceHolder = ew_HtmlEncode(ew_RemoveHtml($this->Tiempo_Documento->FldCaption()));

			// Edit refer script
			// radicado

			$this->radicado->HrefValue = "";

			// clase_documento
			$this->clase_documento->HrefValue = "";

			// identificacion
			$this->identificacion->HrefValue = "";

			// nombre_rem
			$this->nombre_rem->HrefValue = "";

			// apellidos_rem
			$this->apellidos_rem->HrefValue = "";

			// telefono
			$this->telefono->HrefValue = "";

			// direccion
			$this->direccion->HrefValue = "";

			// correo_electronico
			$this->correo_electronico->HrefValue = "";

			// pais
			$this->pais->HrefValue = "";

			// departamento
			$this->departamento->HrefValue = "";

			// cuidad
			$this->cuidad->HrefValue = "";

			// asunto
			$this->asunto->HrefValue = "";

			// descripcion
			$this->descripcion->HrefValue = "";

			// adjuntar
			$this->adjuntar->HrefValue = "";
			$this->adjuntar->HrefValue2 = $this->adjuntar->UploadPath . $this->adjuntar->Upload->DbValue;

			// destino
			$this->destino->HrefValue = "";

			// Sección
			$this->SecciF3n->HrefValue = "";

			// subseccion
			$this->subseccion->HrefValue = "";

			// Fecha De Ingreso
			$this->Fecha_De_Ingreso->HrefValue = "";

			// hora
			$this->hora->HrefValue = "";

			// Fecha Documento
			$this->Fecha_Documento->HrefValue = "";

			// Tiempo Documento
			$this->Tiempo_Documento->HrefValue = "";
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!$this->clase_documento->FldIsDetailKey && !is_null($this->clase_documento->FormValue) && $this->clase_documento->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->clase_documento->FldCaption());
		}
		if (!$this->identificacion->FldIsDetailKey && !is_null($this->identificacion->FormValue) && $this->identificacion->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->identificacion->FldCaption());
		}
		if (!ew_CheckInteger($this->identificacion->FormValue)) {
			ew_AddMessage($gsFormError, $this->identificacion->FldErrMsg());
		}
		if (!ew_CheckInteger($this->telefono->FormValue)) {
			ew_AddMessage($gsFormError, $this->telefono->FldErrMsg());
		}
		if (!$this->destino->FldIsDetailKey && !is_null($this->destino->FormValue) && $this->destino->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->destino->FldCaption());
		}
		if (!$this->SecciF3n->FldIsDetailKey && !is_null($this->SecciF3n->FormValue) && $this->SecciF3n->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->SecciF3n->FldCaption());
		}
		if (!$this->subseccion->FldIsDetailKey && !is_null($this->subseccion->FormValue) && $this->subseccion->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->subseccion->FldCaption());
		}
		if (!ew_CheckDate($this->Fecha_De_Ingreso->FormValue)) {
			ew_AddMessage($gsFormError, $this->Fecha_De_Ingreso->FldErrMsg());
		}
		if (!ew_CheckTime($this->hora->FormValue)) {
			ew_AddMessage($gsFormError, $this->hora->FldErrMsg());
		}
		if (!$this->Fecha_Documento->FldIsDetailKey && !is_null($this->Fecha_Documento->FormValue) && $this->Fecha_Documento->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->Fecha_Documento->FldCaption());
		}
		if (!ew_CheckDate($this->Fecha_Documento->FormValue)) {
			ew_AddMessage($gsFormError, $this->Fecha_Documento->FldErrMsg());
		}
		if (!$this->Tiempo_Documento->FldIsDetailKey && !is_null($this->Tiempo_Documento->FormValue) && $this->Tiempo_Documento->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->Tiempo_Documento->FldCaption());
		}
		if (!ew_CheckInteger($this->Tiempo_Documento->FormValue)) {
			ew_AddMessage($gsFormError, $this->Tiempo_Documento->FldErrMsg());
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	// Add record
	function AddRow($rsold = NULL) {
		global $conn, $Language, $Security;

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// radicado
		$this->radicado->SetDbValueDef($rsnew, $this->radicado->CurrentValue, NULL, FALSE);

		// clase_documento
		$this->clase_documento->SetDbValueDef($rsnew, $this->clase_documento->CurrentValue, NULL, FALSE);

		// identificacion
		$this->identificacion->SetDbValueDef($rsnew, $this->identificacion->CurrentValue, NULL, FALSE);

		// nombre_rem
		$this->nombre_rem->SetDbValueDef($rsnew, $this->nombre_rem->CurrentValue, NULL, FALSE);

		// apellidos_rem
		$this->apellidos_rem->SetDbValueDef($rsnew, $this->apellidos_rem->CurrentValue, NULL, FALSE);

		// telefono
		$this->telefono->SetDbValueDef($rsnew, $this->telefono->CurrentValue, NULL, FALSE);

		// direccion
		$this->direccion->SetDbValueDef($rsnew, $this->direccion->CurrentValue, NULL, FALSE);

		// correo_electronico
		$this->correo_electronico->SetDbValueDef($rsnew, $this->correo_electronico->CurrentValue, NULL, FALSE);

		// pais
		$this->pais->SetDbValueDef($rsnew, $this->pais->CurrentValue, NULL, FALSE);

		// departamento
		$this->departamento->SetDbValueDef($rsnew, $this->departamento->CurrentValue, NULL, FALSE);

		// cuidad
		$this->cuidad->SetDbValueDef($rsnew, $this->cuidad->CurrentValue, NULL, FALSE);

		// asunto
		$this->asunto->SetDbValueDef($rsnew, $this->asunto->CurrentValue, NULL, FALSE);

		// descripcion
		$this->descripcion->SetDbValueDef($rsnew, $this->descripcion->CurrentValue, NULL, FALSE);

		// adjuntar
		if (!$this->adjuntar->Upload->KeepFile) {
			if ($this->adjuntar->Upload->FileName == "") {
				$rsnew['adjuntar'] = NULL;
			} else {
				$rsnew['adjuntar'] = $this->adjuntar->Upload->FileName;
			}
		}

		// destino
		$this->destino->SetDbValueDef($rsnew, $this->destino->CurrentValue, NULL, FALSE);

		// Sección
		$this->SecciF3n->SetDbValueDef($rsnew, $this->SecciF3n->CurrentValue, NULL, FALSE);

		// subseccion
		$this->subseccion->SetDbValueDef($rsnew, $this->subseccion->CurrentValue, "", FALSE);

		// Fecha De Ingreso
		$this->Fecha_De_Ingreso->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->Fecha_De_Ingreso->CurrentValue, 5), NULL, FALSE);

		// hora
		$this->hora->SetDbValueDef($rsnew, $this->hora->CurrentValue, NULL, FALSE);

		// Fecha Documento
		$this->Fecha_Documento->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->Fecha_Documento->CurrentValue, 5), ew_CurrentDate(), FALSE);

		// Tiempo Documento
		$this->Tiempo_Documento->SetDbValueDef($rsnew, $this->Tiempo_Documento->CurrentValue, 0, FALSE);
		if (!$this->adjuntar->Upload->KeepFile) {
			if (!ew_Empty($this->adjuntar->Upload->Value)) {
				$rsnew['adjuntar'] = ew_UploadFileNameEx($this->adjuntar->UploadPath, $rsnew['adjuntar']); // Get new file name
			}
		}

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);

		// Check if key value entered
		if ($bInsertRow && $this->ValidateKey && $this->numero->CurrentValue == "" && $this->numero->getSessionValue() == "") {
			$this->setFailureMessage($Language->Phrase("InvalidKeyValue"));
			$bInsertRow = FALSE;
		}

		// Check for duplicate key
		if ($bInsertRow && $this->ValidateKey) {
			$sFilter = $this->KeyFilter();
			$rsChk = $this->LoadRs($sFilter);
			if ($rsChk && !$rsChk->EOF) {
				$sKeyErrMsg = str_replace("%f", $sFilter, $Language->Phrase("DupKey"));
				$this->setFailureMessage($sKeyErrMsg);
				$rsChk->Close();
				$bInsertRow = FALSE;
			}
		}
		if ($bInsertRow) {
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {
				if (!$this->adjuntar->Upload->KeepFile) {
					if (!ew_Empty($this->adjuntar->Upload->Value)) {
						$this->adjuntar->Upload->SaveToFile($this->adjuntar->UploadPath, $rsnew['adjuntar'], TRUE);
					}
				}
			}
		} else {
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("InsertCancelled"));
			}
			$AddRow = FALSE;
		}

		// Get insert id if necessary
		if ($AddRow) {
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
		}

		// adjuntar
		ew_CleanUploadTempPath($this->adjuntar, $this->adjuntar->Upload->Index);
		return $AddRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$PageCaption = $this->TableCaption();
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "entradalist.php", $this->TableVar);
		$PageCaption = ($this->CurrentAction == "C") ? $Language->Phrase("Copy") : $Language->Phrase("Add");
		$Breadcrumb->Add("add", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
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

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($entrada_add)) $entrada_add = new centrada_add();

// Page init
$entrada_add->Page_Init();

// Page main
$entrada_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$entrada_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var entrada_add = new ew_Page("entrada_add");
entrada_add.PageID = "add"; // Page ID
var EW_PAGE_ID = entrada_add.PageID; // For backward compatibility

// Form object
var fentradaadd = new ew_Form("fentradaadd");

// Validate form
fentradaadd.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	this.PostAutoSuggest();
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
			elm = this.GetElements("x" + infix + "_clase_documento");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($entrada->clase_documento->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_identificacion");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($entrada->identificacion->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_identificacion");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($entrada->identificacion->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_telefono");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($entrada->telefono->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_destino");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($entrada->destino->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_SecciF3n");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($entrada->SecciF3n->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_subseccion");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($entrada->subseccion->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_Fecha_De_Ingreso");
			if (elm && !ew_CheckDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($entrada->Fecha_De_Ingreso->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_hora");
			if (elm && !ew_CheckTime(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($entrada->hora->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Fecha_Documento");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($entrada->Fecha_Documento->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_Fecha_Documento");
			if (elm && !ew_CheckDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($entrada->Fecha_Documento->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Tiempo_Documento");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($entrada->Tiempo_Documento->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_Tiempo_Documento");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($entrada->Tiempo_Documento->FldErrMsg()) ?>");

			// Set up row object
			ew_ElementsToRow(fobj);

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}

	// Process detail forms
	var dfs = $fobj.find("input[name='detailpage']").get();
	for (var i = 0; i < dfs.length; i++) {
		var df = dfs[i], val = df.value;
		if (val && ewForms[val])
			if (!ewForms[val].Validate())
				return false;
	}
	return true;
}

// Form_CustomValidate event
fentradaadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fentradaadd.ValidateRequired = true;
<?php } else { ?>
fentradaadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fentradaadd.Lists["x_clase_documento"] = {"LinkField":"x_nombre","Ajax":null,"AutoFill":false,"DisplayFields":["x_nombre","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fentradaadd.Lists["x_pais"] = {"LinkField":"x_Code","Ajax":null,"AutoFill":false,"DisplayFields":["x_Name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fentradaadd.Lists["x_departamento"] = {"LinkField":"x_Name","Ajax":null,"AutoFill":false,"DisplayFields":["x_Name","","",""],"ParentFields":["x_pais"],"FilterFields":["x_Country"],"Options":[]};
fentradaadd.Lists["x_cuidad"] = {"LinkField":"x_Name","Ajax":null,"AutoFill":false,"DisplayFields":["x_Name","","",""],"ParentFields":["x_departamento"],"FilterFields":["x_Province"],"Options":[]};
fentradaadd.Lists["x_destino"] = {"LinkField":"x_nombre_sede","Ajax":null,"AutoFill":false,"DisplayFields":["x_nombre_sede","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fentradaadd.Lists["x_SecciF3n"] = {"LinkField":"x_codigo_seccion","Ajax":null,"AutoFill":false,"DisplayFields":["x_nombre_seccion","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fentradaadd.Lists["x_subseccion"] = {"LinkField":"x_codigo_subseccion","Ajax":null,"AutoFill":false,"DisplayFields":["x_nombre_subseccion","","",""],"ParentFields":["x_SecciF3n"],"FilterFields":["x_codigo_seccion"],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $entrada_add->ShowPageHeader(); ?>
<?php
$entrada_add->ShowMessage();
?>
<form name="fentradaadd" id="fentradaadd" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="entrada">
<input type="hidden" name="a_add" id="a_add" value="A">
<table cellspacing="0" class="ewGrid"><tr><td>
<table id="tbl_entradaadd" class="table table-bordered table-striped">
<?php if ($entrada->radicado->Visible) { // radicado ?>
	<tr id="r_radicado"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_radicado"><?php echo $entrada->radicado->FldCaption() ?></span></td>
		<td<?php echo $entrada->radicado->CellAttributes() ?>><span id="el_entrada_radicado" class="control-group">
<input type="text" data-field="x_radicado" name="x_radicado" id="x_radicado" size="30" maxlength="45" placeholder="<?php echo $entrada->radicado->PlaceHolder ?>" value="<?php echo $entrada->radicado->EditValue ?>"<?php echo $entrada->radicado->EditAttributes() ?>>
</span><?php echo $entrada->radicado->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($entrada->clase_documento->Visible) { // clase_documento ?>
	<tr id="r_clase_documento"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_clase_documento"><?php echo $entrada->clase_documento->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $entrada->clase_documento->CellAttributes() ?>><span id="el_entrada_clase_documento" class="control-group">
<select data-field="x_clase_documento" id="x_clase_documento" name="x_clase_documento"<?php echo $entrada->clase_documento->EditAttributes() ?>>
<?php
if (is_array($entrada->clase_documento->EditValue)) {
	$arwrk = $entrada->clase_documento->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($entrada->clase_documento->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
	
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<script type="text/javascript">
fentradaadd.Lists["x_clase_documento"].Options = <?php echo (is_array($entrada->clase_documento->EditValue)) ? ew_ArrayToJson($entrada->clase_documento->EditValue, 1) : "[]" ?>;
</script>
</span><?php echo $entrada->clase_documento->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($entrada->identificacion->Visible) { // identificacion ?>
	<tr id="r_identificacion"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_identificacion"><?php echo $entrada->identificacion->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $entrada->identificacion->CellAttributes() ?>><span id="el_entrada_identificacion" class="control-group">
<input type="text" data-field="x_identificacion" name="x_identificacion" id="x_identificacion" size="30" placeholder="<?php echo $entrada->identificacion->PlaceHolder ?>" value="<?php echo $entrada->identificacion->EditValue ?>"<?php echo $entrada->identificacion->EditAttributes() ?>>
</span><?php echo $entrada->identificacion->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($entrada->nombre_rem->Visible) { // nombre_rem ?>
	<tr id="r_nombre_rem"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_nombre_rem"><?php echo $entrada->nombre_rem->FldCaption() ?></span></td>
		<td<?php echo $entrada->nombre_rem->CellAttributes() ?>><span id="el_entrada_nombre_rem" class="control-group">
<input type="text" data-field="x_nombre_rem" name="x_nombre_rem" id="x_nombre_rem" size="30" maxlength="30" placeholder="<?php echo $entrada->nombre_rem->PlaceHolder ?>" value="<?php echo $entrada->nombre_rem->EditValue ?>"<?php echo $entrada->nombre_rem->EditAttributes() ?>>
</span><?php echo $entrada->nombre_rem->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($entrada->apellidos_rem->Visible) { // apellidos_rem ?>
	<tr id="r_apellidos_rem"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_apellidos_rem"><?php echo $entrada->apellidos_rem->FldCaption() ?></span></td>
		<td<?php echo $entrada->apellidos_rem->CellAttributes() ?>><span id="el_entrada_apellidos_rem" class="control-group">
<input type="text" data-field="x_apellidos_rem" name="x_apellidos_rem" id="x_apellidos_rem" size="30" maxlength="45" placeholder="<?php echo $entrada->apellidos_rem->PlaceHolder ?>" value="<?php echo $entrada->apellidos_rem->EditValue ?>"<?php echo $entrada->apellidos_rem->EditAttributes() ?>>
</span><?php echo $entrada->apellidos_rem->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($entrada->telefono->Visible) { // telefono ?>
	<tr id="r_telefono"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_telefono"><?php echo $entrada->telefono->FldCaption() ?></span></td>
		<td<?php echo $entrada->telefono->CellAttributes() ?>><span id="el_entrada_telefono" class="control-group">
<input type="text" data-field="x_telefono" name="x_telefono" id="x_telefono" size="30" placeholder="<?php echo $entrada->telefono->PlaceHolder ?>" value="<?php echo $entrada->telefono->EditValue ?>"<?php echo $entrada->telefono->EditAttributes() ?>>
</span><?php echo $entrada->telefono->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($entrada->direccion->Visible) { // direccion ?>
	<tr id="r_direccion"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_direccion"><?php echo $entrada->direccion->FldCaption() ?></span></td>
		<td<?php echo $entrada->direccion->CellAttributes() ?>><span id="el_entrada_direccion" class="control-group">
<input type="text" data-field="x_direccion" name="x_direccion" id="x_direccion" size="30" maxlength="45" placeholder="<?php echo $entrada->direccion->PlaceHolder ?>" value="<?php echo $entrada->direccion->EditValue ?>"<?php echo $entrada->direccion->EditAttributes() ?>>
</span><?php echo $entrada->direccion->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($entrada->correo_electronico->Visible) { // correo_electronico ?>
	<tr id="r_correo_electronico"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_correo_electronico"><?php echo $entrada->correo_electronico->FldCaption() ?></span></td>
		<td<?php echo $entrada->correo_electronico->CellAttributes() ?>><span id="el_entrada_correo_electronico" class="control-group">
<input type="text" data-field="x_correo_electronico" name="x_correo_electronico" id="x_correo_electronico" size="30" maxlength="45" placeholder="<?php echo $entrada->correo_electronico->PlaceHolder ?>" value="<?php echo $entrada->correo_electronico->EditValue ?>"<?php echo $entrada->correo_electronico->EditAttributes() ?>>
</span><?php echo $entrada->correo_electronico->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($entrada->pais->Visible) { // pais ?>
	<tr id="r_pais"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_pais"><?php echo $entrada->pais->FldCaption() ?></span></td>
		<td<?php echo $entrada->pais->CellAttributes() ?>><span id="el_entrada_pais" class="control-group">
<?php $entrada->pais->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x_departamento']); " . @$entrada->pais->EditAttrs["onchange"]; ?>
<select data-field="x_pais" id="x_pais" name="x_pais"<?php echo $entrada->pais->EditAttributes() ?>>
<?php
if (is_array($entrada->pais->EditValue)) {
	$arwrk = $entrada->pais->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($entrada->pais->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<script type="text/javascript">
fentradaadd.Lists["x_pais"].Options = <?php echo (is_array($entrada->pais->EditValue)) ? ew_ArrayToJson($entrada->pais->EditValue, 1) : "[]" ?>;
</script>
</span><?php echo $entrada->pais->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($entrada->departamento->Visible) { // departamento ?>
	<tr id="r_departamento"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_departamento"><?php echo $entrada->departamento->FldCaption() ?></span></td>
		<td<?php echo $entrada->departamento->CellAttributes() ?>><span id="el_entrada_departamento" class="control-group">
<?php $entrada->departamento->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x_cuidad']); " . @$entrada->departamento->EditAttrs["onchange"]; ?>
<select data-field="x_departamento" id="x_departamento" name="x_departamento"<?php echo $entrada->departamento->EditAttributes() ?>>
<?php
if (is_array($entrada->departamento->EditValue)) {
	$arwrk = $entrada->departamento->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($entrada->departamento->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<script type="text/javascript">
fentradaadd.Lists["x_departamento"].Options = <?php echo (is_array($entrada->departamento->EditValue)) ? ew_ArrayToJson($entrada->departamento->EditValue, 1) : "[]" ?>;
</script>
</span><?php echo $entrada->departamento->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($entrada->cuidad->Visible) { // cuidad ?>
	<tr id="r_cuidad"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_cuidad"><?php echo $entrada->cuidad->FldCaption() ?></span></td>
		<td<?php echo $entrada->cuidad->CellAttributes() ?>><span id="el_entrada_cuidad" class="control-group">
<select data-field="x_cuidad" id="x_cuidad" name="x_cuidad"<?php echo $entrada->cuidad->EditAttributes() ?>>
<?php
if (is_array($entrada->cuidad->EditValue)) {
	$arwrk = $entrada->cuidad->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($entrada->cuidad->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<script type="text/javascript">
fentradaadd.Lists["x_cuidad"].Options = <?php echo (is_array($entrada->cuidad->EditValue)) ? ew_ArrayToJson($entrada->cuidad->EditValue, 1) : "[]" ?>;
</script>
</span><?php echo $entrada->cuidad->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($entrada->asunto->Visible) { // asunto ?>
	<tr id="r_asunto"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_asunto"><?php echo $entrada->asunto->FldCaption() ?></span></td>
		<td<?php echo $entrada->asunto->CellAttributes() ?>><span id="el_entrada_asunto" class="control-group">
<input type="text" data-field="x_asunto" name="x_asunto" id="x_asunto" size="30" maxlength="45" placeholder="<?php echo $entrada->asunto->PlaceHolder ?>" value="<?php echo $entrada->asunto->EditValue ?>"<?php echo $entrada->asunto->EditAttributes() ?>>
</span><?php echo $entrada->asunto->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($entrada->descripcion->Visible) { // descripcion ?>
	<tr id="r_descripcion"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_descripcion"><?php echo $entrada->descripcion->FldCaption() ?></span></td>
		<td<?php echo $entrada->descripcion->CellAttributes() ?>><span id="el_entrada_descripcion" class="control-group">
<input type="text" data-field="x_descripcion" name="x_descripcion" id="x_descripcion" size="30" maxlength="100" placeholder="<?php echo $entrada->descripcion->PlaceHolder ?>" value="<?php echo $entrada->descripcion->EditValue ?>"<?php echo $entrada->descripcion->EditAttributes() ?>>
</span><?php echo $entrada->descripcion->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($entrada->adjuntar->Visible) { // adjuntar ?>
	<tr id="r_adjuntar"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_adjuntar"><?php echo $entrada->adjuntar->FldCaption() ?></span></td>
		<td<?php echo $entrada->adjuntar->CellAttributes() ?>><span id="el_entrada_adjuntar" class="control-group">
<span id="fd_x_adjuntar">
<span class="btn btn-small fileinput-button">
	<span><?php echo $Language->Phrase("ChooseFile") ?></span>
	<input type="file" data-field="x_adjuntar" name="x_adjuntar" id="x_adjuntar">
</span>
<input type="hidden" name="fn_x_adjuntar" id= "fn_x_adjuntar" value="<?php echo $entrada->adjuntar->Upload->FileName ?>">
<input type="hidden" name="fa_x_adjuntar" id= "fa_x_adjuntar" value="0">
<input type="hidden" name="fs_x_adjuntar" id= "fs_x_adjuntar" value="45">
</span>
<table id="ft_x_adjuntar" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span><?php echo $entrada->adjuntar->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($entrada->destino->Visible) { // destino ?>
	<tr id="r_destino"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_destino"><?php echo $entrada->destino->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $entrada->destino->CellAttributes() ?>><span id="el_entrada_destino" class="control-group">
<select data-field="x_destino" id="x_destino" name="x_destino"<?php echo $entrada->destino->EditAttributes() ?>>
<?php
if (is_array($entrada->destino->EditValue)) {
	$arwrk = $entrada->destino->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($entrada->destino->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<script type="text/javascript">
fentradaadd.Lists["x_destino"].Options = <?php echo (is_array($entrada->destino->EditValue)) ? ew_ArrayToJson($entrada->destino->EditValue, 1) : "[]" ?>;
</script>
</span><?php echo $entrada->destino->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($entrada->SecciF3n->Visible) { // Sección ?>
	<tr id="r_SecciF3n"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_SecciF3n"><?php echo $entrada->SecciF3n->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $entrada->SecciF3n->CellAttributes() ?>><span id="el_entrada_SecciF3n" class="control-group">
<?php $entrada->SecciF3n->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x_subseccion']); " . @$entrada->SecciF3n->EditAttrs["onchange"]; ?>
<select data-field="x_SecciF3n" id="x_SecciF3n" name="x_SecciF3n"<?php echo $entrada->SecciF3n->EditAttributes() ?>>
<?php
if (is_array($entrada->SecciF3n->EditValue)) {
	$arwrk = $entrada->SecciF3n->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($entrada->SecciF3n->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<script type="text/javascript">
fentradaadd.Lists["x_SecciF3n"].Options = <?php echo (is_array($entrada->SecciF3n->EditValue)) ? ew_ArrayToJson($entrada->SecciF3n->EditValue, 1) : "[]" ?>;
</script>
</span><?php echo $entrada->SecciF3n->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($entrada->subseccion->Visible) { // subseccion ?>
	<tr id="r_subseccion"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_subseccion"><?php echo $entrada->subseccion->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $entrada->subseccion->CellAttributes() ?>><span id="el_entrada_subseccion" class="control-group">
<select data-field="x_subseccion" id="x_subseccion" name="x_subseccion"<?php echo $entrada->subseccion->EditAttributes() ?>>
<?php
if (is_array($entrada->subseccion->EditValue)) {
	$arwrk = $entrada->subseccion->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($entrada->subseccion->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<script type="text/javascript">
fentradaadd.Lists["x_subseccion"].Options = <?php echo (is_array($entrada->subseccion->EditValue)) ? ew_ArrayToJson($entrada->subseccion->EditValue, 1) : "[]" ?>;
</script>
</span><?php echo $entrada->subseccion->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($entrada->Fecha_De_Ingreso->Visible) { // Fecha De Ingreso ?>
	<tr id="r_Fecha_De_Ingreso"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_Fecha_De_Ingreso"><?php echo $entrada->Fecha_De_Ingreso->FldCaption() ?></span></td>
		<td<?php echo $entrada->Fecha_De_Ingreso->CellAttributes() ?>><span id="el_entrada_Fecha_De_Ingreso" class="control-group">
<input type="date" data-field="x_Fecha_De_Ingreso" name="x_Fecha_De_Ingreso" id="x_Fecha_De_Ingreso" placeholder="<?php echo $entrada->Fecha_De_Ingreso->PlaceHolder ?>" value=""<?php echo $entrada->Fecha_De_Ingreso->EditAttributes() ?> onchange="mostrar()">
</span><?php echo $entrada->Fecha_De_Ingreso->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($entrada->hora->Visible) { // hora ?>
	<tr id="r_hora"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_hora"><?php echo $entrada->hora->FldCaption() ?></span></td>
		<td<?php echo $entrada->hora->CellAttributes() ?>><span id="el_entrada_hora" class="control-group">
<input type="text" data-field="x_hora" name="x_hora" id="x_hora" size="30" placeholder="<?php echo $entrada->hora->PlaceHolder ?>" value="<?php echo $entrada->hora->EditValue ?>"<?php echo $entrada->hora->EditAttributes() ?>>
</span><?php echo $entrada->hora->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($entrada->Fecha_Documento->Visible) { // Fecha Documento ?>
	<tr id="r_Fecha_Documento"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_Fecha_Documento"><?php echo $entrada->Fecha_Documento->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
        
        
         <script>
           var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0!

    var yyyy = today.getFullYear();
    if(dd<10){
        dd='0'+dd
    } 
    if(mm<10){
        mm='0'+mm
    } 
    var today = dd+'/'+mm+'/'+ yyyy;
    document.getElementById("x_Fecha_De_Ingreso").value = today;
	
		
        function restar(f1,f2)
 {
 var aFecha1 = f1.split('-'); 
 var aFecha2 = f2.split('-'); 
 var fFecha1 = Date.UTC(aFecha1[0],aFecha1[1]-1,aFecha1[2]); 
 var fFecha2 = Date.UTC(aFecha2[0],aFecha2[1]-1,aFecha2[2]); 
 var dif = fFecha2 - fFecha1;
 var dias = Math.floor(dif / (1000 * 60 * 60 * 24)); 
 return dias;
 }
 
 function mostrar(){
	 var f1= document.getElementById("x_Fecha_De_Ingreso").value
	 var f2= document.getElementById("x_Fecha_Documento").value
	 fe1=f1.toString()
	  fe2=f2.toString()
	 if((fe2!="")&&(fe1!="")){
	 restaFechas = restar(fe1,fe2)
 document.fentradaadd.x_Tiempo_Documento.value= restaFechas
 }
 }
        </script>
        
        
		<td<?php echo $entrada->Fecha_Documento->CellAttributes() ?>><span id="el_entrada_Fecha_Documento" class="control-group">
<input type="date" data-field="x_Fecha_Documento" name="x_Fecha_Documento" id="x_Fecha_Documento" placeholder="<?php echo $entrada->Fecha_Documento->PlaceHolder ?>" value="" <?php echo $entrada->Fecha_Documento->EditAttributes() ?> onchange="mostrar()">
</span><?php echo $entrada->Fecha_Documento->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($entrada->Tiempo_Documento->Visible) { // Tiempo Documento ?>
	<tr id="r_Tiempo_Documento"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_Tiempo_Documento"><?php echo $entrada->Tiempo_Documento->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $entrada->Tiempo_Documento->CellAttributes() ?>><span id="el_entrada_Tiempo_Documento" class="control-group">
<input type="text" data-field="x_Tiempo_Documento" name="x_Tiempo_Documento" id="x_Tiempo_Documento" size="30" placeholder="tiempo" readonly>
</span><?php echo $entrada->Tiempo_Documento->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
</form>
<script type="text/javascript">
fentradaadd.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$entrada_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$entrada_add->Page_Terminate();
?>
