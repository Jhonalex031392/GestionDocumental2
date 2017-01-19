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

$entrada_view = NULL; // Initialize page object first

class centrada_view extends centrada {

	// Page ID
	var $PageID = 'view';

	// Project ID
	var $ProjectID = "{D74DC9FA-763C-48C4-880F-6C317035A0C2}";

	// Table name
	var $TableName = 'entrada';

	// Page object name
	var $PageObjName = 'entrada_view';

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

	// Page URLs
	var $AddUrl;
	var $EditUrl;
	var $CopyUrl;
	var $DeleteUrl;
	var $ViewUrl;
	var $ListUrl;

	// Export URLs
	var $ExportPrintUrl;
	var $ExportHtmlUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportXmlUrl;
	var $ExportCsvUrl;
	var $ExportPdfUrl;

	// Update URLs
	var $InlineAddUrl;
	var $InlineCopyUrl;
	var $InlineEditUrl;
	var $GridAddUrl;
	var $GridEditUrl;
	var $MultiDeleteUrl;
	var $MultiUpdateUrl;

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
		$KeyUrl = "";
		if (@$_GET["numero"] <> "") {
			$this->RecKey["numero"] = $_GET["numero"];
			$KeyUrl .= "&numero=" . urlencode($this->RecKey["numero"]);
		}
		$this->ExportPrintUrl = $this->PageUrl() . "export=print" . $KeyUrl;
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html" . $KeyUrl;
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel" . $KeyUrl;
		$this->ExportWordUrl = $this->PageUrl() . "export=word" . $KeyUrl;
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml" . $KeyUrl;
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv" . $KeyUrl;
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf" . $KeyUrl;

		// Table object (usuarios)
		if (!isset($GLOBALS['usuarios'])) $GLOBALS['usuarios'] = new cusuarios();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'view', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'entrada', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "span";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "span";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "span";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
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

		// Get export parameters
		if (@$_GET["export"] <> "") {
			$this->Export = $_GET["export"];
		} elseif (ew_IsHttpPost()) {
			if (@$_POST["exporttype"] <> "")
				$this->Export = $_POST["exporttype"];
		} else {
			$this->setExportReturnUrl(ew_CurrentUrl());
		}
		$gsExport = $this->Export; // Get export parameter, used in header
		$gsExportFile = $this->TableVar; // Get export file, used in header
		if (@$_GET["numero"] <> "") {
			if ($gsExportFile <> "") $gsExportFile .= "_";
			$gsExportFile .= ew_StripSlashes($_GET["numero"]);
		}
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up curent action

		// Setup export options
		$this->SetupExportOptions();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Update url if printer friendly for Pdf
		if ($this->PrinterFriendlyForPdf)
			$this->ExportOptions->Items["pdf"]->Body = str_replace($this->ExportPdfUrl, $this->ExportPrintUrl . "&pdf=1", $this->ExportOptions->Items["pdf"]->Body);
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $conn;

		// Page Unload event
		$this->Page_Unload();
		if ($this->Export == "print" && @$_GET["pdf"] == "1") { // Printer friendly version and with pdf=1 in URL parameters
			$pdf = new cExportPdf($GLOBALS["Table"]);
			$pdf->Text = ob_get_contents(); // Set the content as the HTML of current page (printer friendly version)
			ob_end_clean();
			$pdf->Export();
		}

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
	var $ExportOptions; // Export options
	var $OtherOptions = array(); // Other options
	var $DisplayRecs = 1;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $RecCnt;
	var $RecKey = array();
	var $Recordset;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Load current record
		$bLoadCurrentRecord = FALSE;
		$sReturnUrl = "";
		$bMatchRecord = FALSE;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET["numero"] <> "") {
				$this->numero->setQueryStringValue($_GET["numero"]);
				$this->RecKey["numero"] = $this->numero->QueryStringValue;
			} else {
				$sReturnUrl = "entradalist.php"; // Return to list
			}

			// Get action
			$this->CurrentAction = "I"; // Display form
			switch ($this->CurrentAction) {
				case "I": // Get a record to display
					if (!$this->LoadRow()) { // Load record based on key
						if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "")
							$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
						$sReturnUrl = "entradalist.php"; // No matching record, return to list
					}
			}

			// Export data only
			if (in_array($this->Export, array("html","word","excel","xml","csv","email","pdf"))) {
				$this->ExportData();
				$this->Page_Terminate(); // Terminate response
				exit();
			}
		} else {
			$sReturnUrl = "entradalist.php"; // Not page request, return to list
		}
		if ($sReturnUrl <> "")
			$this->Page_Terminate($sReturnUrl);

		// Render row
		$this->RowType = EW_ROWTYPE_VIEW;
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = &$options["action"];

		// Add
		$item = &$option->Add("add");
		$item->Body = "<a class=\"ewAction ewAdd\" href=\"" . ew_HtmlEncode($this->AddUrl) . "\">" . $Language->Phrase("ViewPageAddLink") . "</a>";
		$item->Visible = ($this->AddUrl <> "" && $Security->IsLoggedIn());

		// Edit
		$item = &$option->Add("edit");
		$item->Body = "<a class=\"ewAction ewEdit\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" . $Language->Phrase("ViewPageEditLink") . "</a>";
		$item->Visible = ($this->EditUrl <> "" && $Security->IsLoggedIn());

		// Copy
		$item = &$option->Add("copy");
		$item->Body = "<a class=\"ewAction ewCopy\" href=\"" . ew_HtmlEncode($this->CopyUrl) . "\">" . $Language->Phrase("ViewPageCopyLink") . "</a>";
		$item->Visible = ($this->CopyUrl <> "" && $Security->IsLoggedIn());

		// Delete
		$item = &$option->Add("delete");
		$item->Body = "<a class=\"ewAction ewDelete\" href=\"" . ew_HtmlEncode($this->DeleteUrl) . "\">" . $Language->Phrase("ViewPageDeleteLink") . "</a>";
		$item->Visible = ($this->DeleteUrl <> "" && $Security->IsLoggedIn());

		// Set up options default
		foreach ($options as &$option) {
			$option->UseDropDownButton = FALSE;
			$option->UseButtonGroup = TRUE;
			$item = &$option->Add($option->GroupOptionName);
			$item->Body = "";
			$item->Visible = FALSE;
		}
		$options["detail"]->DropDownButtonPhrase = $Language->Phrase("ButtonDetails");
		$options["action"]->DropDownButtonPhrase = $Language->Phrase("ButtonActions");
	}

	// Set up starting record parameters
	function SetUpStartRec() {
		if ($this->DisplayRecs == 0)
			return;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET[EW_TABLE_START_REC] <> "") { // Check for "start" parameter
				$this->StartRec = $_GET[EW_TABLE_START_REC];
				$this->setStartRecordNumber($this->StartRec);
			} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
				$PageNo = $_GET[EW_TABLE_PAGE_NO];
				if (is_numeric($PageNo)) {
					$this->StartRec = ($PageNo-1)*$this->DisplayRecs+1;
					if ($this->StartRec <= 0) {
						$this->StartRec = 1;
					} elseif ($this->StartRec >= intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1) {
						$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1;
					}
					$this->setStartRecordNumber($this->StartRec);
				}
			}
		}
		$this->StartRec = $this->getStartRecordNumber();

		// Check if correct start record counter
		if (!is_numeric($this->StartRec) || $this->StartRec == "") { // Avoid invalid start record counter
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} elseif (intval($this->StartRec) > intval($this->TotalRecs)) { // Avoid starting record > total records
			$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to last page first record
			$this->setStartRecordNumber($this->StartRec);
		} elseif (($this->StartRec-1) % $this->DisplayRecs <> 0) {
			$this->StartRec = intval(($this->StartRec-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to page boundary
			$this->setStartRecordNumber($this->StartRec);
		}
	}

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
		$this->AddUrl = $this->GetAddUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();
		$this->ListUrl = $this->GetListUrl();
		$this->SetupOtherOptions();

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

	// Set up export options
	function SetupExportOptions() {
		global $Language;

		// Printer friendly
		$item = &$this->ExportOptions->Add("print");
		$item->Body = "<a href=\"" . $this->ExportPrintUrl . "\" class=\"ewExportLink ewPrint\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("PrinterFriendlyText")) . "\">" . $Language->Phrase("PrinterFriendly") . "</a>";
		$item->Visible = TRUE;

		// Export to Excel
		$item = &$this->ExportOptions->Add("excel");
		$item->Body = "<a href=\"" . $this->ExportExcelUrl . "\" class=\"ewExportLink ewExcel\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToExcelText")) . "\">" . $Language->Phrase("ExportToExcel") . "</a>";
		$item->Visible = TRUE;

		// Export to Word
		$item = &$this->ExportOptions->Add("word");
		$item->Body = "<a href=\"" . $this->ExportWordUrl . "\" class=\"ewExportLink ewWord\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToWordText")) . "\">" . $Language->Phrase("ExportToWord") . "</a>";
		$item->Visible = TRUE;

		// Export to Html
		$item = &$this->ExportOptions->Add("html");
		$item->Body = "<a href=\"" . $this->ExportHtmlUrl . "\" class=\"ewExportLink ewHtml\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToHtmlText")) . "\">" . $Language->Phrase("ExportToHtml") . "</a>";
		$item->Visible = FALSE;

		// Export to Xml
		$item = &$this->ExportOptions->Add("xml");
		$item->Body = "<a href=\"" . $this->ExportXmlUrl . "\" class=\"ewExportLink ewXml\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToXmlText")) . "\">" . $Language->Phrase("ExportToXml") . "</a>";
		$item->Visible = FALSE;

		// Export to Csv
		$item = &$this->ExportOptions->Add("csv");
		$item->Body = "<a href=\"" . $this->ExportCsvUrl . "\" class=\"ewExportLink ewCsv\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToCsvText")) . "\">" . $Language->Phrase("ExportToCsv") . "</a>";
		$item->Visible = FALSE;

		// Export to Pdf
		$item = &$this->ExportOptions->Add("pdf");
		$item->Body = "<a href=\"" . $this->ExportPdfUrl . "\" class=\"ewExportLink ewPdf\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToPDFText")) . "\">" . $Language->Phrase("ExportToPDF") . "</a>";
		$item->Visible = TRUE;

		// Export to Email
		$item = &$this->ExportOptions->Add("email");
		$item->Body = "<a id=\"emf_entrada\" href=\"javascript:void(0);\" class=\"ewExportLink ewEmail\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_entrada',hdr:ewLanguage.Phrase('ExportToEmail'),f:document.fentradaview,key:" . ew_ArrayToJsonAttr($this->RecKey) . ",sel:false});\">" . $Language->Phrase("ExportToEmail") . "</a>";
		$item->Visible = TRUE;

		// Drop down button for export
		$this->ExportOptions->UseDropDownButton = FALSE;
		$this->ExportOptions->DropDownButtonPhrase = $Language->Phrase("ButtonExport");

		// Add group option item
		$item = &$this->ExportOptions->Add($this->ExportOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Hide options for export
		if ($this->Export <> "")
			$this->ExportOptions->HideAllOptions();
	}

	// Export data in HTML/CSV/Word/Excel/XML/Email/PDF format
	function ExportData() {
		$utf8 = (strtolower(EW_CHARSET) == "utf-8");
		$bSelectLimit = FALSE;

		// Load recordset
		if ($bSelectLimit) {
			$this->TotalRecs = $this->SelectRecordCount();
		} else {
			if ($rs = $this->LoadRecordset())
				$this->TotalRecs = $rs->RecordCount();
		}
		$this->StartRec = 1;
		$this->SetUpStartRec(); // Set up start record position

		// Set the last record to display
		if ($this->DisplayRecs <= 0) {
			$this->StopRec = $this->TotalRecs;
		} else {
			$this->StopRec = $this->StartRec + $this->DisplayRecs - 1;
		}
		if (!$rs) {
			header("Content-Type:"); // Remove header
			header("Content-Disposition:");
			$this->ShowMessage();
			return;
		}
		$ExportDoc = ew_ExportDocument($this, "v");
		$ParentTable = "";
		if ($bSelectLimit) {
			$StartRec = 1;
			$StopRec = $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs;
		} else {
			$StartRec = $this->StartRec;
			$StopRec = $this->StopRec;
		}
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		$ExportDoc->Text .= $sHeader;
		$this->ExportDocument($ExportDoc, $rs, $StartRec, $StopRec, "view");
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		$ExportDoc->Text .= $sFooter;

		// Close recordset
		$rs->Close();

		// Export header and footer
		$ExportDoc->ExportHeaderAndFooter();

		// Clean output buffer
		if (!EW_DEBUG_ENABLED && ob_get_length())
			ob_end_clean();

		// Write debug message if enabled
		if (EW_DEBUG_ENABLED)
			echo ew_DebugMsg();

		// Output data
		if ($this->Export == "email") {
			echo $this->ExportEmail($ExportDoc->Text);
		} else {
			$ExportDoc->Export();
		}
	}

	// Export email
	function ExportEmail($EmailContent) {
		global $gTmpImages, $Language;
		$sSender = @$_GET["sender"];
		$sRecipient = @$_GET["recipient"];
		$sCc = @$_GET["cc"];
		$sBcc = @$_GET["bcc"];
		$sContentType = @$_GET["contenttype"];

		// Subject
		$sSubject = ew_StripSlashes(@$_GET["subject"]);
		$sEmailSubject = $sSubject;

		// Message
		$sContent = ew_StripSlashes(@$_GET["message"]);
		$sEmailMessage = $sContent;

		// Check sender
		if ($sSender == "") {
			return "<p class=\"text-error\">" . $Language->Phrase("EnterSenderEmail") . "</p>";
		}
		if (!ew_CheckEmail($sSender)) {
			return "<p class=\"text-error\">" . $Language->Phrase("EnterProperSenderEmail") . "</p>";
		}

		// Check recipient
		if (!ew_CheckEmailList($sRecipient, EW_MAX_EMAIL_RECIPIENT)) {
			return "<p class=\"text-error\">" . $Language->Phrase("EnterProperRecipientEmail") . "</p>";
		}

		// Check cc
		if (!ew_CheckEmailList($sCc, EW_MAX_EMAIL_RECIPIENT)) {
			return "<p class=\"text-error\">" . $Language->Phrase("EnterProperCcEmail") . "</p>";
		}

		// Check bcc
		if (!ew_CheckEmailList($sBcc, EW_MAX_EMAIL_RECIPIENT)) {
			return "<p class=\"text-error\">" . $Language->Phrase("EnterProperBccEmail") . "</p>";
		}

		// Check email sent count
		if (!isset($_SESSION[EW_EXPORT_EMAIL_COUNTER]))
			$_SESSION[EW_EXPORT_EMAIL_COUNTER] = 0;
		if (intval($_SESSION[EW_EXPORT_EMAIL_COUNTER]) > EW_MAX_EMAIL_SENT_COUNT) {
			return "<p class=\"text-error\">" . $Language->Phrase("ExceedMaxEmailExport") . "</p>";
		}

		// Send email
		$Email = new cEmail();
		$Email->Sender = $sSender; // Sender
		$Email->Recipient = $sRecipient; // Recipient
		$Email->Cc = $sCc; // Cc
		$Email->Bcc = $sBcc; // Bcc
		$Email->Subject = $sEmailSubject; // Subject
		$Email->Format = ($sContentType == "url") ? "text" : "html";
		$Email->Charset = EW_EMAIL_CHARSET;
		if ($sEmailMessage <> "") {
			$sEmailMessage = ew_RemoveXSS($sEmailMessage);
			$sEmailMessage .= ($sContentType == "url") ? "\r\n\r\n" : "<br><br>";
		}
		if ($sContentType == "url") {
			$sUrl = ew_ConvertFullUrl(ew_CurrentPage() . "?" . $this->ExportQueryString());
			$sEmailMessage .= $sUrl; // Send URL only
		} else {
			foreach ($gTmpImages as $tmpimage)
				$Email->AddEmbeddedImage($tmpimage);
			$sEmailMessage .= $EmailContent; // Send HTML
		}
		$Email->Content = $sEmailMessage; // Content
		$EventArgs = array();
		$bEmailSent = FALSE;
		if ($this->Email_Sending($Email, $EventArgs))
			$bEmailSent = $Email->Send();

		// Check email sent status
		if ($bEmailSent) {

			// Update email sent count
			$_SESSION[EW_EXPORT_EMAIL_COUNTER]++;

			// Sent email success
			return "<p class=\"text-success\">" . $Language->Phrase("SendEmailSuccess") . "</p>"; // Set up success message
		} else {

			// Sent email failure
			return "<p class=\"text-error\">" . $Email->SendErrDescription . "</p>";
		}
	}

	// Export QueryString
	function ExportQueryString() {

		// Initialize
		$sQry = "export=html";

		// Add record key QueryString
		$sQry .= "&" . substr($this->KeyUrl("", ""), 1);
		return $sQry;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$PageCaption = $this->TableCaption();
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", "entradalist.php", $this->TableVar);
		$PageCaption = $Language->Phrase("view");
		$Breadcrumb->Add("view", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", ew_CurrentUrl(), $this->TableVar);
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
if (!isset($entrada_view)) $entrada_view = new centrada_view();

// Page init
$entrada_view->Page_Init();

// Page main
$entrada_view->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$entrada_view->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($entrada->Export == "") { ?>
<script type="text/javascript">

// Page object
var entrada_view = new ew_Page("entrada_view");
entrada_view.PageID = "view"; // Page ID
var EW_PAGE_ID = entrada_view.PageID; // For backward compatibility

// Form object
var fentradaview = new ew_Form("fentradaview");

// Form_CustomValidate event
fentradaview.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fentradaview.ValidateRequired = true;
<?php } else { ?>
fentradaview.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fentradaview.Lists["x_clase_documento"] = {"LinkField":"x_nombre","Ajax":null,"AutoFill":false,"DisplayFields":["x_nombre","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fentradaview.Lists["x_pais"] = {"LinkField":"x_Code","Ajax":null,"AutoFill":false,"DisplayFields":["x_Name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fentradaview.Lists["x_departamento"] = {"LinkField":"x_Name","Ajax":null,"AutoFill":false,"DisplayFields":["x_Name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fentradaview.Lists["x_cuidad"] = {"LinkField":"x_Name","Ajax":null,"AutoFill":false,"DisplayFields":["x_Name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fentradaview.Lists["x_destino"] = {"LinkField":"x_nombre_sede","Ajax":null,"AutoFill":false,"DisplayFields":["x_nombre_sede","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fentradaview.Lists["x_SecciF3n"] = {"LinkField":"x_codigo_seccion","Ajax":null,"AutoFill":false,"DisplayFields":["x_nombre_seccion","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fentradaview.Lists["x_subseccion"] = {"LinkField":"x_codigo_subseccion","Ajax":null,"AutoFill":false,"DisplayFields":["x_nombre_subseccion","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($entrada->Export == "") { ?>
<?php $Breadcrumb->Render(); ?>
<?php } ?>
<?php if ($entrada->Export == "") { ?>
<div class="ewViewExportOptions">
<?php $entrada_view->ExportOptions->Render("body") ?>
<?php if (!$entrada_view->ExportOptions->UseDropDownButton) { ?>
</div>
<div class="ewViewOtherOptions">
<?php } ?>
<?php
	foreach ($entrada_view->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<?php } ?>
<?php $entrada_view->ShowPageHeader(); ?>
<?php
$entrada_view->ShowMessage();
?>
<form name="fentradaview" id="fentradaview" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="entrada">
<table cellspacing="0" class="ewGrid"><tr><td>
<table id="tbl_entradaview" class="table table-bordered table-striped">
<?php if ($entrada->numero->Visible) { // numero ?>
	<tr id="r_numero"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_numero"><?php echo $entrada->numero->FldCaption() ?></span></td>
		<td<?php echo $entrada->numero->CellAttributes() ?>><span id="el_entrada_numero" class="control-group">
<span<?php echo $entrada->numero->ViewAttributes() ?>>
<?php echo $entrada->numero->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($entrada->radicado->Visible) { // radicado ?>
	<tr id="r_radicado"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_radicado"><?php echo $entrada->radicado->FldCaption() ?></span></td>
		<td<?php echo $entrada->radicado->CellAttributes() ?>><span id="el_entrada_radicado" class="control-group">
<span<?php echo $entrada->radicado->ViewAttributes() ?>>
<?php echo $entrada->radicado->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($entrada->clase_documento->Visible) { // clase_documento ?>
	<tr id="r_clase_documento"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_clase_documento"><?php echo $entrada->clase_documento->FldCaption() ?></span></td>
		<td<?php echo $entrada->clase_documento->CellAttributes() ?>><span id="el_entrada_clase_documento" class="control-group">
<span<?php echo $entrada->clase_documento->ViewAttributes() ?>>
<?php echo $entrada->clase_documento->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($entrada->identificacion->Visible) { // identificacion ?>
	<tr id="r_identificacion"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_identificacion"><?php echo $entrada->identificacion->FldCaption() ?></span></td>
		<td<?php echo $entrada->identificacion->CellAttributes() ?>><span id="el_entrada_identificacion" class="control-group">
<span<?php echo $entrada->identificacion->ViewAttributes() ?>>
<?php echo $entrada->identificacion->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($entrada->nombre_rem->Visible) { // nombre_rem ?>
	<tr id="r_nombre_rem"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_nombre_rem"><?php echo $entrada->nombre_rem->FldCaption() ?></span></td>
		<td<?php echo $entrada->nombre_rem->CellAttributes() ?>><span id="el_entrada_nombre_rem" class="control-group">
<span<?php echo $entrada->nombre_rem->ViewAttributes() ?>>
<?php echo $entrada->nombre_rem->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($entrada->apellidos_rem->Visible) { // apellidos_rem ?>
	<tr id="r_apellidos_rem"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_apellidos_rem"><?php echo $entrada->apellidos_rem->FldCaption() ?></span></td>
		<td<?php echo $entrada->apellidos_rem->CellAttributes() ?>><span id="el_entrada_apellidos_rem" class="control-group">
<span<?php echo $entrada->apellidos_rem->ViewAttributes() ?>>
<?php echo $entrada->apellidos_rem->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($entrada->telefono->Visible) { // telefono ?>
	<tr id="r_telefono"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_telefono"><?php echo $entrada->telefono->FldCaption() ?></span></td>
		<td<?php echo $entrada->telefono->CellAttributes() ?>><span id="el_entrada_telefono" class="control-group">
<span<?php echo $entrada->telefono->ViewAttributes() ?>>
<?php echo $entrada->telefono->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($entrada->direccion->Visible) { // direccion ?>
	<tr id="r_direccion"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_direccion"><?php echo $entrada->direccion->FldCaption() ?></span></td>
		<td<?php echo $entrada->direccion->CellAttributes() ?>><span id="el_entrada_direccion" class="control-group">
<span<?php echo $entrada->direccion->ViewAttributes() ?>>
<?php echo $entrada->direccion->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($entrada->correo_electronico->Visible) { // correo_electronico ?>
	<tr id="r_correo_electronico"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_correo_electronico"><?php echo $entrada->correo_electronico->FldCaption() ?></span></td>
		<td<?php echo $entrada->correo_electronico->CellAttributes() ?>><span id="el_entrada_correo_electronico" class="control-group">
<span<?php echo $entrada->correo_electronico->ViewAttributes() ?>>
<?php echo $entrada->correo_electronico->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($entrada->pais->Visible) { // pais ?>
	<tr id="r_pais"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_pais"><?php echo $entrada->pais->FldCaption() ?></span></td>
		<td<?php echo $entrada->pais->CellAttributes() ?>><span id="el_entrada_pais" class="control-group">
<span<?php echo $entrada->pais->ViewAttributes() ?>>
<?php echo $entrada->pais->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($entrada->departamento->Visible) { // departamento ?>
	<tr id="r_departamento"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_departamento"><?php echo $entrada->departamento->FldCaption() ?></span></td>
		<td<?php echo $entrada->departamento->CellAttributes() ?>><span id="el_entrada_departamento" class="control-group">
<span<?php echo $entrada->departamento->ViewAttributes() ?>>
<?php echo $entrada->departamento->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($entrada->cuidad->Visible) { // cuidad ?>
	<tr id="r_cuidad"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_cuidad"><?php echo $entrada->cuidad->FldCaption() ?></span></td>
		<td<?php echo $entrada->cuidad->CellAttributes() ?>><span id="el_entrada_cuidad" class="control-group">
<span<?php echo $entrada->cuidad->ViewAttributes() ?>>
<?php echo $entrada->cuidad->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($entrada->asunto->Visible) { // asunto ?>
	<tr id="r_asunto"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_asunto"><?php echo $entrada->asunto->FldCaption() ?></span></td>
		<td<?php echo $entrada->asunto->CellAttributes() ?>><span id="el_entrada_asunto" class="control-group">
<span<?php echo $entrada->asunto->ViewAttributes() ?>>
<?php echo $entrada->asunto->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($entrada->descripcion->Visible) { // descripcion ?>
	<tr id="r_descripcion"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_descripcion"><?php echo $entrada->descripcion->FldCaption() ?></span></td>
		<td<?php echo $entrada->descripcion->CellAttributes() ?>><span id="el_entrada_descripcion" class="control-group">
<span<?php echo $entrada->descripcion->ViewAttributes() ?>>
<?php echo $entrada->descripcion->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($entrada->adjuntar->Visible) { // adjuntar ?>
	<tr id="r_adjuntar"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_adjuntar"><?php echo $entrada->adjuntar->FldCaption() ?></span></td>
		<td<?php echo $entrada->adjuntar->CellAttributes() ?>><span id="el_entrada_adjuntar" class="control-group">
<span<?php echo $entrada->adjuntar->ViewAttributes() ?>>
<?php if ($entrada->adjuntar->LinkAttributes() <> "") { ?>
<?php if (!empty($entrada->adjuntar->Upload->DbValue)) { ?>
<?php echo $entrada->adjuntar->ViewValue ?>
<?php } elseif (!in_array($entrada->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } else { ?>
<?php if (!empty($entrada->adjuntar->Upload->DbValue)) { ?>
<?php echo $entrada->adjuntar->ViewValue ?>
<?php } elseif (!in_array($entrada->CurrentAction, array("I", "edit", "gridedit"))) { ?>	
&nbsp;
<?php } ?>
<?php } ?>
</span>
</span></td>
	</tr>
<?php } ?>
<?php if ($entrada->destino->Visible) { // destino ?>
	<tr id="r_destino"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_destino"><?php echo $entrada->destino->FldCaption() ?></span></td>
		<td<?php echo $entrada->destino->CellAttributes() ?>><span id="el_entrada_destino" class="control-group">
<span<?php echo $entrada->destino->ViewAttributes() ?>>
<?php echo $entrada->destino->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($entrada->SecciF3n->Visible) { // Sección ?>
	<tr id="r_SecciF3n"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_SecciF3n"><?php echo $entrada->SecciF3n->FldCaption() ?></span></td>
		<td<?php echo $entrada->SecciF3n->CellAttributes() ?>><span id="el_entrada_SecciF3n" class="control-group">
<span<?php echo $entrada->SecciF3n->ViewAttributes() ?>>
<?php echo $entrada->SecciF3n->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($entrada->subseccion->Visible) { // subseccion ?>
	<tr id="r_subseccion"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_subseccion"><?php echo $entrada->subseccion->FldCaption() ?></span></td>
		<td<?php echo $entrada->subseccion->CellAttributes() ?>><span id="el_entrada_subseccion" class="control-group">
<span<?php echo $entrada->subseccion->ViewAttributes() ?>>
<?php echo $entrada->subseccion->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($entrada->Fecha_De_Ingreso->Visible) { // Fecha De Ingreso ?>
	<tr id="r_Fecha_De_Ingreso"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_Fecha_De_Ingreso"><?php echo $entrada->Fecha_De_Ingreso->FldCaption() ?></span></td>
		<td<?php echo $entrada->Fecha_De_Ingreso->CellAttributes() ?>><span id="el_entrada_Fecha_De_Ingreso" class="control-group">
<span<?php echo $entrada->Fecha_De_Ingreso->ViewAttributes() ?>>
<?php echo $entrada->Fecha_De_Ingreso->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($entrada->hora->Visible) { // hora ?>
	<tr id="r_hora"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_hora"><?php echo $entrada->hora->FldCaption() ?></span></td>
		<td<?php echo $entrada->hora->CellAttributes() ?>><span id="el_entrada_hora" class="control-group">
<span<?php echo $entrada->hora->ViewAttributes() ?>>
<?php echo $entrada->hora->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($entrada->Fecha_Documento->Visible) { // Fecha Documento ?>
	<tr id="r_Fecha_Documento"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_Fecha_Documento"><?php echo $entrada->Fecha_Documento->FldCaption() ?></span></td>
		<td<?php echo $entrada->Fecha_Documento->CellAttributes() ?>><span id="el_entrada_Fecha_Documento" class="control-group">
<span<?php echo $entrada->Fecha_Documento->ViewAttributes() ?>>
<?php echo $entrada->Fecha_Documento->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
<?php if ($entrada->Tiempo_Documento->Visible) { // Tiempo Documento ?>
	<tr id="r_Tiempo_Documento"<?php echo $entrada->RowAttributes() ?>>
		<td><span id="elh_entrada_Tiempo_Documento"><?php echo $entrada->Tiempo_Documento->FldCaption() ?></span></td>
		<td<?php echo $entrada->Tiempo_Documento->CellAttributes() ?>><span id="el_entrada_Tiempo_Documento" class="control-group">
<span<?php echo $entrada->Tiempo_Documento->ViewAttributes() ?>>
<?php echo $entrada->Tiempo_Documento->ViewValue ?></span>
</span></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
</form>
<script type="text/javascript">
fentradaview.Init();
</script>
<?php
$entrada_view->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($entrada->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$entrada_view->Page_Terminate();
?>
