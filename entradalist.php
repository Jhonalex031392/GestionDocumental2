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

$entrada_list = NULL; // Initialize page object first

class centrada_list extends centrada {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{D74DC9FA-763C-48C4-880F-6C317035A0C2}";

	// Table name
	var $TableName = 'entrada';

	// Page object name
	var $PageObjName = 'entrada_list';

	// Grid form hidden field names
	var $FormName = 'fentradalist';
	var $FormActionName = 'k_action';
	var $FormKeyName = 'k_key';
	var $FormOldKeyName = 'k_oldkey';
	var $FormBlankRowName = 'k_blankrow';
	var $FormKeyCountName = 'key_count';

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

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "entradaadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "entradadelete.php";
		$this->MultiUpdateUrl = "entradaupdate.php";

		// Table object (usuarios)
		if (!isset($GLOBALS['usuarios'])) $GLOBALS['usuarios'] = new cusuarios();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'entrada', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

		// List options
		$this->ListOptions = new cListOptions();
		$this->ListOptions->TableVar = $this->TableVar;

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "span";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['addedit'] = new cListOptions();
		$this->OtherOptions['addedit']->Tag = "span";
		$this->OtherOptions['addedit']->TagClassName = "ewAddEditOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "span";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "span";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";
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
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up curent action

		// Get grid add count
		$gridaddcnt = @$_GET[EW_TABLE_GRID_ADD_ROW_COUNT];
		if (is_numeric($gridaddcnt) && $gridaddcnt > 0)
			$this->GridAddRowCount = $gridaddcnt;

		// Set up list options
		$this->SetupListOptions();

		// Setup export options
		$this->SetupExportOptions();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Setup other options
		$this->SetupOtherOptions();

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

	// Class variables
	var $ListOptions; // List options
	var $ExportOptions; // Export options
	var $OtherOptions = array(); // Other options
	var $DisplayRecs = 20;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $Pager;
	var $SearchWhere = ""; // Search WHERE clause
	var $RecCnt = 0; // Record count
	var $EditRowCnt;
	var $StartRowCnt = 1;
	var $RowCnt = 0;
	var $Attrs = array(); // Row attributes and cell attributes
	var $RowIndex = 0; // Row index
	var $KeyCount = 0; // Key count
	var $RowAction = ""; // Row action
	var $RowOldKey = ""; // Row old key (for copy)
	var $RecPerRow = 0;
	var $ColCnt = 0;
	var $DbMasterFilter = ""; // Master filter
	var $DbDetailFilter = ""; // Detail filter
	var $MasterRecordExists;	
	var $MultiSelectKey;
	var $Command;
	var $RestoreSearch = FALSE;
	var $Recordset;
	var $OldRecordset;

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError, $gsSearchError, $Security;

		// Search filters
		$sSrchAdvanced = ""; // Advanced search filter
		$sSrchBasic = ""; // Basic search filter
		$sFilter = "";

		// Get command
		$this->Command = strtolower(@$_GET["cmd"]);
		if ($this->IsPageRequest()) { // Validate request

			// Process custom action first
			$this->ProcessCustomAction();

			// Handle reset command
			$this->ResetCmd();

			// Set up Breadcrumb
			$this->SetupBreadcrumb();

			// Hide list options
			if ($this->Export <> "") {
				$this->ListOptions->HideAllOptions(array("sequence"));
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			} elseif ($this->CurrentAction == "gridadd" || $this->CurrentAction == "gridedit") {
				$this->ListOptions->HideAllOptions();
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			}

			// Hide export options
			if ($this->Export <> "" || $this->CurrentAction <> "")
				$this->ExportOptions->HideAllOptions();

			// Hide other options
			if ($this->Export <> "") {
				foreach ($this->OtherOptions as &$option)
					$option->HideAllOptions();
			}

			// Get basic search values
			$this->LoadBasicSearchValues();

			// Restore search parms from Session if not searching / reset
			if ($this->Command <> "search" && $this->Command <> "reset" && $this->Command <> "resetall" && $this->CheckSearchParms())
				$this->RestoreSearchParms();

			// Call Recordset SearchValidated event
			$this->Recordset_SearchValidated();

			// Set up sorting order
			$this->SetUpSortOrder();

			// Get basic search criteria
			if ($gsSearchError == "")
				$sSrchBasic = $this->BasicSearchWhere();
		}

		// Restore display records
		if ($this->getRecordsPerPage() <> "") {
			$this->DisplayRecs = $this->getRecordsPerPage(); // Restore from Session
		} else {
			$this->DisplayRecs = 20; // Load default
		}

		// Load Sorting Order
		$this->LoadSortOrder();

		// Load search default if no existing search criteria
		if (!$this->CheckSearchParms()) {

			// Load basic search from default
			$this->BasicSearch->LoadDefault();
			if ($this->BasicSearch->Keyword != "")
				$sSrchBasic = $this->BasicSearchWhere();
		}

		// Build search criteria
		ew_AddFilter($this->SearchWhere, $sSrchAdvanced);
		ew_AddFilter($this->SearchWhere, $sSrchBasic);

		// Call Recordset_Searching event
		$this->Recordset_Searching($this->SearchWhere);

		// Save search criteria
		if ($this->Command == "search" && !$this->RestoreSearch) {
			$this->setSearchWhere($this->SearchWhere); // Save to Session
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} else {
			$this->SearchWhere = $this->getSearchWhere();
		}

		// Build filter
		$sFilter = "";
		ew_AddFilter($sFilter, $this->DbDetailFilter);
		ew_AddFilter($sFilter, $this->SearchWhere);

		// Set up filter in session
		$this->setSessionWhere($sFilter);
		$this->CurrentFilter = "";

		// Export data only
		if (in_array($this->Export, array("html","word","excel","xml","csv","email","pdf"))) {
			$this->ExportData();
			$this->Page_Terminate(); // Terminate response
			exit();
		}
	}

	// Build filter for all keys
	function BuildKeyFilter() {
		global $objForm;
		$sWrkFilter = "";

		// Update row index and get row key
		$rowindex = 1;
		$objForm->Index = $rowindex;
		$sThisKey = strval($objForm->GetValue("k_key"));
		while ($sThisKey <> "") {
			if ($this->SetupKeyValues($sThisKey)) {
				$sFilter = $this->KeyFilter();
				if ($sWrkFilter <> "") $sWrkFilter .= " OR ";
				$sWrkFilter .= $sFilter;
			} else {
				$sWrkFilter = "0=1";
				break;
			}

			// Update row index and get row key
			$rowindex++; // Next row
			$objForm->Index = $rowindex;
			$sThisKey = strval($objForm->GetValue("k_key"));
		}
		return $sWrkFilter;
	}

	// Set up key values
	function SetupKeyValues($key) {
		$arrKeyFlds = explode($GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"], $key);
		if (count($arrKeyFlds) >= 1) {
			$this->numero->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->numero->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Return basic search SQL
	function BasicSearchSQL($Keyword) {
		$sKeyword = ew_AdjustSql($Keyword);
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->radicado, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->nombre_rem, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->apellidos_rem, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->direccion, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->correo_electronico, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->pais, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->departamento, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->cuidad, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->asunto, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->descripcion, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->adjuntar, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->destino, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->SecciF3n, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->subseccion, $Keyword);
		return $sWhere;
	}

	// Build basic search SQL
	function BuildBasicSearchSql(&$Where, &$Fld, $Keyword) {
		if ($Keyword == EW_NULL_VALUE) {
			$sWrk = $Fld->FldExpression . " IS NULL";
		} elseif ($Keyword == EW_NOT_NULL_VALUE) {
			$sWrk = $Fld->FldExpression . " IS NOT NULL";
		} else {
			$sFldExpression = ($Fld->FldVirtualExpression <> $Fld->FldExpression) ? $Fld->FldVirtualExpression : $Fld->FldBasicSearchExpression;
			$sWrk = $sFldExpression . ew_Like(ew_QuotedValue("%" . $Keyword . "%", EW_DATATYPE_STRING));
		}
		if ($Where <> "") $Where .= " OR ";
		$Where .= $sWrk;
	}

	// Return basic search WHERE clause based on search keyword and type
	function BasicSearchWhere() {
		global $Security;
		$sSearchStr = "";
		$sSearchKeyword = $this->BasicSearch->Keyword;
		$sSearchType = $this->BasicSearch->Type;
		if ($sSearchKeyword <> "") {
			$sSearch = trim($sSearchKeyword);
			if ($sSearchType <> "=") {
				while (strpos($sSearch, "  ") !== FALSE)
					$sSearch = str_replace("  ", " ", $sSearch);
				$arKeyword = explode(" ", trim($sSearch));
				foreach ($arKeyword as $sKeyword) {
					if ($sSearchStr <> "") $sSearchStr .= " " . $sSearchType . " ";
					$sSearchStr .= "(" . $this->BasicSearchSQL($sKeyword) . ")";
				}
			} else {
				$sSearchStr = $this->BasicSearchSQL($sSearch);
			}
			$this->Command = "search";
		}
		if ($this->Command == "search") {
			$this->BasicSearch->setKeyword($sSearchKeyword);
			$this->BasicSearch->setType($sSearchType);
		}
		return $sSearchStr;
	}

	// Check if search parm exists
	function CheckSearchParms() {

		// Check basic search
		if ($this->BasicSearch->IssetSession())
			return TRUE;
		return FALSE;
	}

	// Clear all search parameters
	function ResetSearchParms() {

		// Clear search WHERE clause
		$this->SearchWhere = "";
		$this->setSearchWhere($this->SearchWhere);

		// Clear basic search parameters
		$this->ResetBasicSearchParms();
	}

	// Load advanced search default values
	function LoadAdvancedSearchDefault() {
		return FALSE;
	}

	// Clear all basic search parameters
	function ResetBasicSearchParms() {
		$this->BasicSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->numero); // numero
			$this->UpdateSort($this->radicado); // radicado
			$this->UpdateSort($this->clase_documento); // clase_documento
			$this->UpdateSort($this->identificacion); // identificacion
			$this->UpdateSort($this->nombre_rem); // nombre_rem
			$this->UpdateSort($this->apellidos_rem); // apellidos_rem
			$this->UpdateSort($this->telefono); // telefono
			$this->UpdateSort($this->direccion); // direccion
			$this->UpdateSort($this->correo_electronico); // correo_electronico
			$this->UpdateSort($this->pais); // pais
			$this->UpdateSort($this->departamento); // departamento
			$this->UpdateSort($this->cuidad); // cuidad
			$this->UpdateSort($this->asunto); // asunto
			$this->UpdateSort($this->descripcion); // descripcion
			$this->UpdateSort($this->adjuntar); // adjuntar
			$this->UpdateSort($this->destino); // destino
			$this->UpdateSort($this->SecciF3n); // Sección
			$this->UpdateSort($this->subseccion); // subseccion
			$this->UpdateSort($this->Fecha_De_Ingreso); // Fecha De Ingreso
			$this->UpdateSort($this->hora); // hora
			$this->UpdateSort($this->Fecha_Documento); // Fecha Documento
			$this->UpdateSort($this->Tiempo_Documento); // Tiempo Documento
			$this->setStartRecordNumber(1); // Reset start position
		}
	}

	// Load sort order parameters
	function LoadSortOrder() {
		$sOrderBy = $this->getSessionOrderBy(); // Get ORDER BY from Session
		if ($sOrderBy == "") {
			if ($this->SqlOrderBy() <> "") {
				$sOrderBy = $this->SqlOrderBy();
				$this->setSessionOrderBy($sOrderBy);
			}
		}
	}

	// Reset command
	// - cmd=reset (Reset search parameters)
	// - cmd=resetall (Reset search and master/detail parameters)
	// - cmd=resetsort (Reset sort parameters)
	function ResetCmd() {

		// Check if reset command
		if (substr($this->Command,0,5) == "reset") {

			// Reset search criteria
			if ($this->Command == "reset" || $this->Command == "resetall")
				$this->ResetSearchParms();

			// Reset sorting order
			if ($this->Command == "resetsort") {
				$sOrderBy = "";
				$this->setSessionOrderBy($sOrderBy);
				$this->numero->setSort("");
				$this->radicado->setSort("");
				$this->clase_documento->setSort("");
				$this->identificacion->setSort("");
				$this->nombre_rem->setSort("");
				$this->apellidos_rem->setSort("");
				$this->telefono->setSort("");
				$this->direccion->setSort("");
				$this->correo_electronico->setSort("");
				$this->pais->setSort("");
				$this->departamento->setSort("");
				$this->cuidad->setSort("");
				$this->asunto->setSort("");
				$this->descripcion->setSort("");
				$this->adjuntar->setSort("");
				$this->destino->setSort("");
				$this->SecciF3n->setSort("");
				$this->subseccion->setSort("");
				$this->Fecha_De_Ingreso->setSort("");
				$this->hora->setSort("");
				$this->Fecha_Documento->setSort("");
				$this->Tiempo_Documento->setSort("");
			}

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Set up list options
	function SetupListOptions() {
		global $Security, $Language;

		// Add group option item
		$item = &$this->ListOptions->Add($this->ListOptions->GroupOptionName);
		$item->Body = "";
		$item->OnLeft = FALSE;
		$item->Visible = FALSE;

		// "view"
		$item = &$this->ListOptions->Add("view");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->IsLoggedIn();
		$item->OnLeft = FALSE;

		// "edit"
		$item = &$this->ListOptions->Add("edit");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->IsLoggedIn();
		$item->OnLeft = FALSE;

		// "copy"
		$item = &$this->ListOptions->Add("copy");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->IsLoggedIn();
		$item->OnLeft = FALSE;

		// "delete"
		$item = &$this->ListOptions->Add("delete");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->IsLoggedIn();
		$item->OnLeft = FALSE;

		// "checkbox"
		$item = &$this->ListOptions->Add("checkbox");
		$item->Visible = FALSE;
		$item->OnLeft = FALSE;
		$item->Header = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key\" id=\"key\" onclick=\"ew_SelectAllKey(this);\"></label>";
		if (count($this->CustomActions) > 0) $item->Visible = TRUE;
		$item->ShowInDropDown = FALSE;
		$item->ShowInButtonGroup = FALSE;

		// Drop down button for ListOptions
		$this->ListOptions->UseDropDownButton = FALSE;
		$this->ListOptions->DropDownButtonPhrase = $Language->Phrase("ButtonListOptions");
		$this->ListOptions->UseButtonGroup = FALSE;
		$this->ListOptions->ButtonClass = "btn-small"; // Class for button group

		// Call ListOptions_Load event
		$this->ListOptions_Load();
	}

	// Render list options
	function RenderListOptions() {
		global $Security, $Language, $objForm;
		$this->ListOptions->LoadDefault();

		// "view"
		$oListOpt = &$this->ListOptions->Items["view"];
		if ($Security->IsLoggedIn())
			$oListOpt->Body = "<a class=\"ewRowLink ewView\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewLink")) . "\" href=\"" . ew_HtmlEncode($this->ViewUrl) . "\">" . $Language->Phrase("ViewLink") . "</a>";
		else
			$oListOpt->Body = "";

		// "edit"
		$oListOpt = &$this->ListOptions->Items["edit"];
		if ($Security->IsLoggedIn()) {
			$oListOpt->Body = "<a class=\"ewRowLink ewEdit\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("EditLink")) . "\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" . $Language->Phrase("EditLink") . "</a>";
		} else {
			$oListOpt->Body = "";
		}

		// "copy"
		$oListOpt = &$this->ListOptions->Items["copy"];
		if ($Security->IsLoggedIn()) {
			$oListOpt->Body = "<a class=\"ewRowLink ewCopy\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("CopyLink")) . "\" href=\"" . ew_HtmlEncode($this->CopyUrl) . "\">" . $Language->Phrase("CopyLink") . "</a>";
		} else {
			$oListOpt->Body = "";
		}

		// "delete"
		$oListOpt = &$this->ListOptions->Items["delete"];
		if ($Security->IsLoggedIn())
			$oListOpt->Body = "<a class=\"ewRowLink ewDelete\"" . "" . " data-caption=\"" . ew_HtmlTitle($Language->Phrase("DeleteLink")) . "\" href=\"" . ew_HtmlEncode($this->DeleteUrl) . "\">" . $Language->Phrase("DeleteLink") . "</a>";
		else
			$oListOpt->Body = "";
		$this->RenderListOptionsExt();

		// Call ListOptions_Rendered event
		$this->ListOptions_Rendered();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = $options["addedit"];

		// Add
		$item = &$option->Add("add");
		$item->Body = "<a class=\"ewAddEdit ewAdd\" href=\"" . ew_HtmlEncode($this->AddUrl) . "\">" . $Language->Phrase("AddLink") . "</a>";
		$item->Visible = ($this->AddUrl <> "" && $Security->IsLoggedIn());
		$option = $options["action"];

		// Set up options default
		foreach ($options as &$option) {
			$option->UseDropDownButton = FALSE;
			$option->UseButtonGroup = TRUE;
			$option->ButtonClass = "btn-small"; // Class for button group
			$item = &$option->Add($option->GroupOptionName);
			$item->Body = "";
			$item->Visible = FALSE;
		}
		$options["addedit"]->DropDownButtonPhrase = $Language->Phrase("ButtonAddEdit");
		$options["detail"]->DropDownButtonPhrase = $Language->Phrase("ButtonDetails");
		$options["action"]->DropDownButtonPhrase = $Language->Phrase("ButtonActions");
	}

	// Render other options
	function RenderOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
			$option = &$options["action"];
			foreach ($this->CustomActions as $action => $name) {

				// Add custom action
				$item = &$option->Add("custom_" . $action);
				$item->Body = "<a class=\"ewAction ewCustomAction\" href=\"\" onclick=\"ew_SubmitSelected(document.fentradalist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
			}

			// Hide grid edit, multi-delete and multi-update
			if ($this->TotalRecs <= 0) {
				$option = &$options["addedit"];
				$item = &$option->GetItem("gridedit");
				if ($item) $item->Visible = FALSE;
				$option = &$options["action"];
				$item = &$option->GetItem("multidelete");
				if ($item) $item->Visible = FALSE;
				$item = &$option->GetItem("multiupdate");
				if ($item) $item->Visible = FALSE;
			}
	}

	// Process custom action
	function ProcessCustomAction() {
		global $conn, $Language, $Security;
		$sFilter = $this->GetKeyFilter();
		$UserAction = @$_POST["useraction"];
		if ($sFilter <> "" && $UserAction <> "") {
			$this->CurrentFilter = $sFilter;
			$sSql = $this->SQL();
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$rs = $conn->Execute($sSql);
			$conn->raiseErrorFn = '';
			$rsuser = ($rs) ? $rs->GetRows() : array();
			if ($rs)
				$rs->Close();

			// Call row custom action event
			if (count($rsuser) > 0) {
				$conn->BeginTrans();
				foreach ($rsuser as $row) {
					$Processed = $this->Row_CustomAction($UserAction, $row);
					if (!$Processed) break;
				}
				if ($Processed) {
					$conn->CommitTrans(); // Commit the changes
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage(str_replace('%s', $UserAction, $Language->Phrase("CustomActionCompleted"))); // Set up success message
				} else {
					$conn->RollbackTrans(); // Rollback changes

					// Set up error message
					if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

						// Use the message, do nothing
					} elseif ($this->CancelMessage <> "") {
						$this->setFailureMessage($this->CancelMessage);
						$this->CancelMessage = "";
					} else {
						$this->setFailureMessage(str_replace('%s', $UserAction, $Language->Phrase("CustomActionCancelled")));
					}
				}
			}
		}
	}

	function RenderListOptionsExt() {
		global $Security, $Language;
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

	// Load basic search values
	function LoadBasicSearchValues() {
		$this->BasicSearch->Keyword = @$_GET[EW_TABLE_BASIC_SEARCH];
		if ($this->BasicSearch->Keyword <> "") $this->Command = "search";
		$this->BasicSearch->Type = @$_GET[EW_TABLE_BASIC_SEARCH_TYPE];
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
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

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
		$item->Body = "<a id=\"emf_entrada\" href=\"javascript:void(0);\" class=\"ewExportLink ewEmail\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_entrada',hdr:ewLanguage.Phrase('ExportToEmail'),f:document.fentradalist,sel:false});\">" . $Language->Phrase("ExportToEmail") . "</a>";
		$item->Visible = TRUE;

		// Drop down button for export
		$this->ExportOptions->UseDropDownButton = FALSE;
		$this->ExportOptions->DropDownButtonPhrase = $Language->Phrase("ButtonExport");

		// Add group option item
		$item = &$this->ExportOptions->Add($this->ExportOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;
	}

	// Export data in HTML/CSV/Word/Excel/XML/Email/PDF format
	function ExportData() {
		$utf8 = (strtolower(EW_CHARSET) == "utf-8");
		$bSelectLimit = EW_SELECT_LIMIT;

		// Load recordset
		if ($bSelectLimit) {
			$this->TotalRecs = $this->SelectRecordCount();
		} else {
			if ($rs = $this->LoadRecordset())
				$this->TotalRecs = $rs->RecordCount();
		}
		$this->StartRec = 1;

		// Export all
		if ($this->ExportAll) {
			set_time_limit(EW_EXPORT_ALL_TIME_LIMIT);
			$this->DisplayRecs = $this->TotalRecs;
			$this->StopRec = $this->TotalRecs;
		} else { // Export one page only
			$this->SetUpStartRec(); // Set up start record position

			// Set the last record to display
			if ($this->DisplayRecs <= 0) {
				$this->StopRec = $this->TotalRecs;
			} else {
				$this->StopRec = $this->StartRec + $this->DisplayRecs - 1;
			}
		}
		if ($bSelectLimit)
			$rs = $this->LoadRecordset($this->StartRec-1, $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs);
		if (!$rs) {
			header("Content-Type:"); // Remove header
			header("Content-Disposition:");
			$this->ShowMessage();
			return;
		}
		$ExportDoc = ew_ExportDocument($this, "h");
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
		$this->ExportDocument($ExportDoc, $rs, $StartRec, $StopRec, "");
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

		// Build QueryString for search
		if ($this->BasicSearch->getKeyword() <> "") {
			$sQry .= "&" . EW_TABLE_BASIC_SEARCH . "=" . urlencode($this->BasicSearch->getKeyword()) . "&" . EW_TABLE_BASIC_SEARCH_TYPE . "=" . urlencode($this->BasicSearch->getType());
		}

		// Build QueryString for pager
		$sQry .= "&" . EW_TABLE_REC_PER_PAGE . "=" . urlencode($this->getRecordsPerPage()) . "&" . EW_TABLE_START_REC . "=" . urlencode($this->getStartRecordNumber());
		return $sQry;
	}

	// Add search QueryString
	function AddSearchQueryString(&$Qry, &$Fld) {
		$FldSearchValue = $Fld->AdvancedSearch->getValue("x");
		$FldParm = substr($Fld->FldVar,2);
		if (strval($FldSearchValue) <> "") {
			$Qry .= "&x_" . $FldParm . "=" . urlencode($FldSearchValue) .
				"&z_" . $FldParm . "=" . urlencode($Fld->AdvancedSearch->getValue("z"));
		}
		$FldSearchValue2 = $Fld->AdvancedSearch->getValue("y");
		if (strval($FldSearchValue2) <> "") {
			$Qry .= "&v_" . $FldParm . "=" . urlencode($Fld->AdvancedSearch->getValue("v")) .
				"&y_" . $FldParm . "=" . urlencode($FldSearchValue2) .
				"&w_" . $FldParm . "=" . urlencode($Fld->AdvancedSearch->getValue("w"));
		}
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$PageCaption = $this->TableCaption();
		$url = ew_CurrentUrl();
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$Breadcrumb->Add("list", "<span id=\"ewPageCaption\">" . $PageCaption . "</span>", $url, $this->TableVar);
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

	// ListOptions Load event
	function ListOptions_Load() {

		// Example:
		//$opt = &$this->ListOptions->Add("new");
		//$opt->Header = "xxx";
		//$opt->OnLeft = TRUE; // Link on left
		//$opt->MoveTo(0); // Move to first column

	}

	// ListOptions Rendered event
	function ListOptions_Rendered() {

		// Example: 
		//$this->ListOptions->Items["new"]->Body = "xxx";

	}

	// Row Custom Action event
	function Row_CustomAction($action, $row) {

		// Return FALSE to abort
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($entrada_list)) $entrada_list = new centrada_list();

// Page init
$entrada_list->Page_Init();

// Page main
$entrada_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$entrada_list->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($entrada->Export == "") { ?>
<script type="text/javascript">

// Page object
var entrada_list = new ew_Page("entrada_list");
entrada_list.PageID = "list"; // Page ID
var EW_PAGE_ID = entrada_list.PageID; // For backward compatibility

// Form object
var fentradalist = new ew_Form("fentradalist");
fentradalist.FormKeyCountName = '<?php echo $entrada_list->FormKeyCountName ?>';

// Form_CustomValidate event
fentradalist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fentradalist.ValidateRequired = true;
<?php } else { ?>
fentradalist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fentradalist.Lists["x_clase_documento"] = {"LinkField":"x_nombre","Ajax":null,"AutoFill":false,"DisplayFields":["x_nombre","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fentradalist.Lists["x_pais"] = {"LinkField":"x_Code","Ajax":null,"AutoFill":false,"DisplayFields":["x_Name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fentradalist.Lists["x_departamento"] = {"LinkField":"x_Name","Ajax":null,"AutoFill":false,"DisplayFields":["x_Name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fentradalist.Lists["x_cuidad"] = {"LinkField":"x_Name","Ajax":null,"AutoFill":false,"DisplayFields":["x_Name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fentradalist.Lists["x_destino"] = {"LinkField":"x_nombre_sede","Ajax":null,"AutoFill":false,"DisplayFields":["x_nombre_sede","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fentradalist.Lists["x_SecciF3n"] = {"LinkField":"x_codigo_seccion","Ajax":null,"AutoFill":false,"DisplayFields":["x_nombre_seccion","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fentradalist.Lists["x_subseccion"] = {"LinkField":"x_codigo_subseccion","Ajax":null,"AutoFill":false,"DisplayFields":["x_nombre_subseccion","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
var fentradalistsrch = new ew_Form("fentradalistsrch");
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($entrada->Export == "") { ?>
<?php $Breadcrumb->Render(); ?>
<?php } ?>
<?php if ($entrada_list->ExportOptions->Visible()) { ?>
<div class="ewListExportOptions"><?php $entrada_list->ExportOptions->Render("body") ?></div>
<?php } ?>
<?php
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$entrada_list->TotalRecs = $entrada->SelectRecordCount();
	} else {
		if ($entrada_list->Recordset = $entrada_list->LoadRecordset())
			$entrada_list->TotalRecs = $entrada_list->Recordset->RecordCount();
	}
	$entrada_list->StartRec = 1;
	if ($entrada_list->DisplayRecs <= 0 || ($entrada->Export <> "" && $entrada->ExportAll)) // Display all records
		$entrada_list->DisplayRecs = $entrada_list->TotalRecs;
	if (!($entrada->Export <> "" && $entrada->ExportAll))
		$entrada_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$entrada_list->Recordset = $entrada_list->LoadRecordset($entrada_list->StartRec-1, $entrada_list->DisplayRecs);
$entrada_list->RenderOtherOptions();
?>
<?php if ($Security->IsLoggedIn()) { ?>
<?php if ($entrada->Export == "" && $entrada->CurrentAction == "") { ?>
<form name="fentradalistsrch" id="fentradalistsrch" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<table class="ewSearchTable"><tr><td>
<div class="accordion" id="fentradalistsrch_SearchGroup">
	<div class="accordion-group">
		<div class="accordion-heading">
<a class="accordion-toggle" data-toggle="collapse" data-parent="#fentradalistsrch_SearchGroup" href="#fentradalistsrch_SearchBody"><?php echo $Language->Phrase("Search") ?></a>
		</div>
		<div id="fentradalistsrch_SearchBody" class="accordion-body collapse in">
			<div class="accordion-inner">
<div id="fentradalistsrch_SearchPanel">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="entrada">
<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="btn-group ewButtonGroup">
	<div class="input-append">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="input-large" value="<?php echo ew_HtmlEncode($entrada_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo $Language->Phrase("Search") ?>">
	<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?></button>
	</div>
	</div>
	<div class="btn-group ewButtonGroup">
	<a class="btn ewShowAll" href="<?php echo $entrada_list->PageUrl() ?>cmd=reset"><?php echo $Language->Phrase("ShowAll") ?></a>
</div>
<div id="xsr_2" class="ewRow">
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="="<?php if ($entrada_list->BasicSearch->getType() == "=") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("ExactPhrase") ?></label>
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND"<?php if ($entrada_list->BasicSearch->getType() == "AND") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AllWord") ?></label>
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR"<?php if ($entrada_list->BasicSearch->getType() == "OR") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AnyWord") ?></label>
</div>
</div>
</div>
			</div>
		</div>
	</div>
</div>
</td></tr></table>
</form>
<?php } ?>
<?php } ?>
<?php $entrada_list->ShowPageHeader(); ?>
<?php
$entrada_list->ShowMessage();
?>
<table cellspacing="0" class="ewGrid"><tr><td class="ewGridContent">
<form name="fentradalist" id="fentradalist" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="entrada">
<div id="gmp_entrada" class="ewGridMiddlePanel">
<?php if ($entrada_list->TotalRecs > 0) { ?>
<table id="tbl_entradalist" class="ewTable ewTableSeparate">
<?php echo $entrada->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$entrada_list->RenderListOptions();

// Render list options (header, left)
$entrada_list->ListOptions->Render("header", "left");
?>
<?php if ($entrada->numero->Visible) { // numero ?>
	<?php if ($entrada->SortUrl($entrada->numero) == "") { ?>
		<td><div id="elh_entrada_numero" class="entrada_numero"><div class="ewTableHeaderCaption"><?php echo $entrada->numero->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $entrada->SortUrl($entrada->numero) ?>',1);"><div id="elh_entrada_numero" class="entrada_numero">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $entrada->numero->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($entrada->numero->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($entrada->numero->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($entrada->radicado->Visible) { // radicado ?>
	<?php if ($entrada->SortUrl($entrada->radicado) == "") { ?>
		<td><div id="elh_entrada_radicado" class="entrada_radicado"><div class="ewTableHeaderCaption"><?php echo $entrada->radicado->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $entrada->SortUrl($entrada->radicado) ?>',1);"><div id="elh_entrada_radicado" class="entrada_radicado">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $entrada->radicado->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($entrada->radicado->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($entrada->radicado->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($entrada->clase_documento->Visible) { // clase_documento ?>
	<?php if ($entrada->SortUrl($entrada->clase_documento) == "") { ?>
		<td><div id="elh_entrada_clase_documento" class="entrada_clase_documento"><div class="ewTableHeaderCaption"><?php echo $entrada->clase_documento->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $entrada->SortUrl($entrada->clase_documento) ?>',1);"><div id="elh_entrada_clase_documento" class="entrada_clase_documento">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $entrada->clase_documento->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($entrada->clase_documento->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($entrada->clase_documento->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($entrada->identificacion->Visible) { // identificacion ?>
	<?php if ($entrada->SortUrl($entrada->identificacion) == "") { ?>
		<td><div id="elh_entrada_identificacion" class="entrada_identificacion"><div class="ewTableHeaderCaption"><?php echo $entrada->identificacion->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $entrada->SortUrl($entrada->identificacion) ?>',1);"><div id="elh_entrada_identificacion" class="entrada_identificacion">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $entrada->identificacion->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($entrada->identificacion->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($entrada->identificacion->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($entrada->nombre_rem->Visible) { // nombre_rem ?>
	<?php if ($entrada->SortUrl($entrada->nombre_rem) == "") { ?>
		<td><div id="elh_entrada_nombre_rem" class="entrada_nombre_rem"><div class="ewTableHeaderCaption"><?php echo $entrada->nombre_rem->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $entrada->SortUrl($entrada->nombre_rem) ?>',1);"><div id="elh_entrada_nombre_rem" class="entrada_nombre_rem">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $entrada->nombre_rem->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($entrada->nombre_rem->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($entrada->nombre_rem->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($entrada->apellidos_rem->Visible) { // apellidos_rem ?>
	<?php if ($entrada->SortUrl($entrada->apellidos_rem) == "") { ?>
		<td><div id="elh_entrada_apellidos_rem" class="entrada_apellidos_rem"><div class="ewTableHeaderCaption"><?php echo $entrada->apellidos_rem->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $entrada->SortUrl($entrada->apellidos_rem) ?>',1);"><div id="elh_entrada_apellidos_rem" class="entrada_apellidos_rem">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $entrada->apellidos_rem->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($entrada->apellidos_rem->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($entrada->apellidos_rem->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($entrada->telefono->Visible) { // telefono ?>
	<?php if ($entrada->SortUrl($entrada->telefono) == "") { ?>
		<td><div id="elh_entrada_telefono" class="entrada_telefono"><div class="ewTableHeaderCaption"><?php echo $entrada->telefono->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $entrada->SortUrl($entrada->telefono) ?>',1);"><div id="elh_entrada_telefono" class="entrada_telefono">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $entrada->telefono->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($entrada->telefono->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($entrada->telefono->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($entrada->direccion->Visible) { // direccion ?>
	<?php if ($entrada->SortUrl($entrada->direccion) == "") { ?>
		<td><div id="elh_entrada_direccion" class="entrada_direccion"><div class="ewTableHeaderCaption"><?php echo $entrada->direccion->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $entrada->SortUrl($entrada->direccion) ?>',1);"><div id="elh_entrada_direccion" class="entrada_direccion">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $entrada->direccion->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($entrada->direccion->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($entrada->direccion->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($entrada->correo_electronico->Visible) { // correo_electronico ?>
	<?php if ($entrada->SortUrl($entrada->correo_electronico) == "") { ?>
		<td><div id="elh_entrada_correo_electronico" class="entrada_correo_electronico"><div class="ewTableHeaderCaption"><?php echo $entrada->correo_electronico->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $entrada->SortUrl($entrada->correo_electronico) ?>',1);"><div id="elh_entrada_correo_electronico" class="entrada_correo_electronico">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $entrada->correo_electronico->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($entrada->correo_electronico->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($entrada->correo_electronico->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($entrada->pais->Visible) { // pais ?>
	<?php if ($entrada->SortUrl($entrada->pais) == "") { ?>
		<td><div id="elh_entrada_pais" class="entrada_pais"><div class="ewTableHeaderCaption"><?php echo $entrada->pais->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $entrada->SortUrl($entrada->pais) ?>',1);"><div id="elh_entrada_pais" class="entrada_pais">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $entrada->pais->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($entrada->pais->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($entrada->pais->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($entrada->departamento->Visible) { // departamento ?>
	<?php if ($entrada->SortUrl($entrada->departamento) == "") { ?>
		<td><div id="elh_entrada_departamento" class="entrada_departamento"><div class="ewTableHeaderCaption"><?php echo $entrada->departamento->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $entrada->SortUrl($entrada->departamento) ?>',1);"><div id="elh_entrada_departamento" class="entrada_departamento">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $entrada->departamento->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($entrada->departamento->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($entrada->departamento->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($entrada->cuidad->Visible) { // cuidad ?>
	<?php if ($entrada->SortUrl($entrada->cuidad) == "") { ?>
		<td><div id="elh_entrada_cuidad" class="entrada_cuidad"><div class="ewTableHeaderCaption"><?php echo $entrada->cuidad->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $entrada->SortUrl($entrada->cuidad) ?>',1);"><div id="elh_entrada_cuidad" class="entrada_cuidad">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $entrada->cuidad->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($entrada->cuidad->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($entrada->cuidad->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($entrada->asunto->Visible) { // asunto ?>
	<?php if ($entrada->SortUrl($entrada->asunto) == "") { ?>
		<td><div id="elh_entrada_asunto" class="entrada_asunto"><div class="ewTableHeaderCaption"><?php echo $entrada->asunto->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $entrada->SortUrl($entrada->asunto) ?>',1);"><div id="elh_entrada_asunto" class="entrada_asunto">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $entrada->asunto->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($entrada->asunto->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($entrada->asunto->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($entrada->descripcion->Visible) { // descripcion ?>
	<?php if ($entrada->SortUrl($entrada->descripcion) == "") { ?>
		<td><div id="elh_entrada_descripcion" class="entrada_descripcion"><div class="ewTableHeaderCaption"><?php echo $entrada->descripcion->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $entrada->SortUrl($entrada->descripcion) ?>',1);"><div id="elh_entrada_descripcion" class="entrada_descripcion">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $entrada->descripcion->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($entrada->descripcion->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($entrada->descripcion->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($entrada->adjuntar->Visible) { // adjuntar ?>
	<?php if ($entrada->SortUrl($entrada->adjuntar) == "") { ?>
		<td><div id="elh_entrada_adjuntar" class="entrada_adjuntar"><div class="ewTableHeaderCaption"><?php echo $entrada->adjuntar->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $entrada->SortUrl($entrada->adjuntar) ?>',1);"><div id="elh_entrada_adjuntar" class="entrada_adjuntar">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $entrada->adjuntar->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($entrada->adjuntar->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($entrada->adjuntar->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($entrada->destino->Visible) { // destino ?>
	<?php if ($entrada->SortUrl($entrada->destino) == "") { ?>
		<td><div id="elh_entrada_destino" class="entrada_destino"><div class="ewTableHeaderCaption"><?php echo $entrada->destino->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $entrada->SortUrl($entrada->destino) ?>',1);"><div id="elh_entrada_destino" class="entrada_destino">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $entrada->destino->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($entrada->destino->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($entrada->destino->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($entrada->SecciF3n->Visible) { // Sección ?>
	<?php if ($entrada->SortUrl($entrada->SecciF3n) == "") { ?>
		<td><div id="elh_entrada_SecciF3n" class="entrada_SecciF3n"><div class="ewTableHeaderCaption"><?php echo $entrada->SecciF3n->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $entrada->SortUrl($entrada->SecciF3n) ?>',1);"><div id="elh_entrada_SecciF3n" class="entrada_SecciF3n">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $entrada->SecciF3n->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($entrada->SecciF3n->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($entrada->SecciF3n->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($entrada->subseccion->Visible) { // subseccion ?>
	<?php if ($entrada->SortUrl($entrada->subseccion) == "") { ?>
		<td><div id="elh_entrada_subseccion" class="entrada_subseccion"><div class="ewTableHeaderCaption"><?php echo $entrada->subseccion->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $entrada->SortUrl($entrada->subseccion) ?>',1);"><div id="elh_entrada_subseccion" class="entrada_subseccion">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $entrada->subseccion->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($entrada->subseccion->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($entrada->subseccion->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($entrada->Fecha_De_Ingreso->Visible) { // Fecha De Ingreso ?>
	<?php if ($entrada->SortUrl($entrada->Fecha_De_Ingreso) == "") { ?>
		<td><div id="elh_entrada_Fecha_De_Ingreso" class="entrada_Fecha_De_Ingreso"><div class="ewTableHeaderCaption"><?php echo $entrada->Fecha_De_Ingreso->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $entrada->SortUrl($entrada->Fecha_De_Ingreso) ?>',1);"><div id="elh_entrada_Fecha_De_Ingreso" class="entrada_Fecha_De_Ingreso">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $entrada->Fecha_De_Ingreso->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($entrada->Fecha_De_Ingreso->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($entrada->Fecha_De_Ingreso->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($entrada->hora->Visible) { // hora ?>
	<?php if ($entrada->SortUrl($entrada->hora) == "") { ?>
		<td><div id="elh_entrada_hora" class="entrada_hora"><div class="ewTableHeaderCaption"><?php echo $entrada->hora->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $entrada->SortUrl($entrada->hora) ?>',1);"><div id="elh_entrada_hora" class="entrada_hora">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $entrada->hora->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($entrada->hora->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($entrada->hora->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($entrada->Fecha_Documento->Visible) { // Fecha Documento ?>
	<?php if ($entrada->SortUrl($entrada->Fecha_Documento) == "") { ?>
		<td><div id="elh_entrada_Fecha_Documento" class="entrada_Fecha_Documento"><div class="ewTableHeaderCaption"><?php echo $entrada->Fecha_Documento->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $entrada->SortUrl($entrada->Fecha_Documento) ?>',1);"><div id="elh_entrada_Fecha_Documento" class="entrada_Fecha_Documento">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $entrada->Fecha_Documento->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($entrada->Fecha_Documento->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($entrada->Fecha_Documento->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($entrada->Tiempo_Documento->Visible) { // Tiempo Documento ?>
	<?php if ($entrada->SortUrl($entrada->Tiempo_Documento) == "") { ?>
		<td><div id="elh_entrada_Tiempo_Documento" class="entrada_Tiempo_Documento"><div class="ewTableHeaderCaption"><?php echo $entrada->Tiempo_Documento->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $entrada->SortUrl($entrada->Tiempo_Documento) ?>',1);"><div id="elh_entrada_Tiempo_Documento" class="entrada_Tiempo_Documento">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $entrada->Tiempo_Documento->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($entrada->Tiempo_Documento->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($entrada->Tiempo_Documento->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$entrada_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($entrada->ExportAll && $entrada->Export <> "") {
	$entrada_list->StopRec = $entrada_list->TotalRecs;
} else {

	// Set the last record to display
	if ($entrada_list->TotalRecs > $entrada_list->StartRec + $entrada_list->DisplayRecs - 1)
		$entrada_list->StopRec = $entrada_list->StartRec + $entrada_list->DisplayRecs - 1;
	else
		$entrada_list->StopRec = $entrada_list->TotalRecs;
}
$entrada_list->RecCnt = $entrada_list->StartRec - 1;
if ($entrada_list->Recordset && !$entrada_list->Recordset->EOF) {
	$entrada_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $entrada_list->StartRec > 1)
		$entrada_list->Recordset->Move($entrada_list->StartRec - 1);
} elseif (!$entrada->AllowAddDeleteRow && $entrada_list->StopRec == 0) {
	$entrada_list->StopRec = $entrada->GridAddRowCount;
}

// Initialize aggregate
$entrada->RowType = EW_ROWTYPE_AGGREGATEINIT;
$entrada->ResetAttrs();
$entrada_list->RenderRow();
while ($entrada_list->RecCnt < $entrada_list->StopRec) {
	$entrada_list->RecCnt++;
	if (intval($entrada_list->RecCnt) >= intval($entrada_list->StartRec)) {
		$entrada_list->RowCnt++;

		// Set up key count
		$entrada_list->KeyCount = $entrada_list->RowIndex;

		// Init row class and style
		$entrada->ResetAttrs();
		$entrada->CssClass = "";
		if ($entrada->CurrentAction == "gridadd") {
		} else {
			$entrada_list->LoadRowValues($entrada_list->Recordset); // Load row values
		}
		$entrada->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$entrada->RowAttrs = array_merge($entrada->RowAttrs, array('data-rowindex'=>$entrada_list->RowCnt, 'id'=>'r' . $entrada_list->RowCnt . '_entrada', 'data-rowtype'=>$entrada->RowType));

		// Render row
		$entrada_list->RenderRow();

		// Render list options
		$entrada_list->RenderListOptions();
?>
	<tr<?php echo $entrada->RowAttributes() ?>>
<?php

// Render list options (body, left)
$entrada_list->ListOptions->Render("body", "left", $entrada_list->RowCnt);
?>
	<?php if ($entrada->numero->Visible) { // numero ?>
		<td<?php echo $entrada->numero->CellAttributes() ?>><span id="el<?php echo $entrada_list->RowCnt ?>_entrada_numero" class="control-group entrada_numero">
<span<?php echo $entrada->numero->ViewAttributes() ?>>
<?php echo $entrada->numero->ListViewValue() ?></span>
</span><a id="<?php echo $entrada_list->PageObjName . "_row_" . $entrada_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($entrada->radicado->Visible) { // radicado ?>
		<td<?php echo $entrada->radicado->CellAttributes() ?>><span id="el<?php echo $entrada_list->RowCnt ?>_entrada_radicado" class="control-group entrada_radicado">
<span<?php echo $entrada->radicado->ViewAttributes() ?>>
<?php echo $entrada->radicado->ListViewValue() ?></span>
</span><a id="<?php echo $entrada_list->PageObjName . "_row_" . $entrada_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($entrada->clase_documento->Visible) { // clase_documento ?>
		<td<?php echo $entrada->clase_documento->CellAttributes() ?>><span id="el<?php echo $entrada_list->RowCnt ?>_entrada_clase_documento" class="control-group entrada_clase_documento">
<span<?php echo $entrada->clase_documento->ViewAttributes() ?>>
<?php echo $entrada->clase_documento->ListViewValue() ?></span>
</span><a id="<?php echo $entrada_list->PageObjName . "_row_" . $entrada_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($entrada->identificacion->Visible) { // identificacion ?>
		<td<?php echo $entrada->identificacion->CellAttributes() ?>><span id="el<?php echo $entrada_list->RowCnt ?>_entrada_identificacion" class="control-group entrada_identificacion">
<span<?php echo $entrada->identificacion->ViewAttributes() ?>>
<?php echo $entrada->identificacion->ListViewValue() ?></span>
</span><a id="<?php echo $entrada_list->PageObjName . "_row_" . $entrada_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($entrada->nombre_rem->Visible) { // nombre_rem ?>
		<td<?php echo $entrada->nombre_rem->CellAttributes() ?>><span id="el<?php echo $entrada_list->RowCnt ?>_entrada_nombre_rem" class="control-group entrada_nombre_rem">
<span<?php echo $entrada->nombre_rem->ViewAttributes() ?>>
<?php echo $entrada->nombre_rem->ListViewValue() ?></span>
</span><a id="<?php echo $entrada_list->PageObjName . "_row_" . $entrada_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($entrada->apellidos_rem->Visible) { // apellidos_rem ?>
		<td<?php echo $entrada->apellidos_rem->CellAttributes() ?>><span id="el<?php echo $entrada_list->RowCnt ?>_entrada_apellidos_rem" class="control-group entrada_apellidos_rem">
<span<?php echo $entrada->apellidos_rem->ViewAttributes() ?>>
<?php echo $entrada->apellidos_rem->ListViewValue() ?></span>
</span><a id="<?php echo $entrada_list->PageObjName . "_row_" . $entrada_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($entrada->telefono->Visible) { // telefono ?>
		<td<?php echo $entrada->telefono->CellAttributes() ?>><span id="el<?php echo $entrada_list->RowCnt ?>_entrada_telefono" class="control-group entrada_telefono">
<span<?php echo $entrada->telefono->ViewAttributes() ?>>
<?php echo $entrada->telefono->ListViewValue() ?></span>
</span><a id="<?php echo $entrada_list->PageObjName . "_row_" . $entrada_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($entrada->direccion->Visible) { // direccion ?>
		<td<?php echo $entrada->direccion->CellAttributes() ?>><span id="el<?php echo $entrada_list->RowCnt ?>_entrada_direccion" class="control-group entrada_direccion">
<span<?php echo $entrada->direccion->ViewAttributes() ?>>
<?php echo $entrada->direccion->ListViewValue() ?></span>
</span><a id="<?php echo $entrada_list->PageObjName . "_row_" . $entrada_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($entrada->correo_electronico->Visible) { // correo_electronico ?>
		<td<?php echo $entrada->correo_electronico->CellAttributes() ?>><span id="el<?php echo $entrada_list->RowCnt ?>_entrada_correo_electronico" class="control-group entrada_correo_electronico">
<span<?php echo $entrada->correo_electronico->ViewAttributes() ?>>
<?php echo $entrada->correo_electronico->ListViewValue() ?></span>
</span><a id="<?php echo $entrada_list->PageObjName . "_row_" . $entrada_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($entrada->pais->Visible) { // pais ?>
		<td<?php echo $entrada->pais->CellAttributes() ?>><span id="el<?php echo $entrada_list->RowCnt ?>_entrada_pais" class="control-group entrada_pais">
<span<?php echo $entrada->pais->ViewAttributes() ?>>
<?php echo $entrada->pais->ListViewValue() ?></span>
</span><a id="<?php echo $entrada_list->PageObjName . "_row_" . $entrada_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($entrada->departamento->Visible) { // departamento ?>
		<td<?php echo $entrada->departamento->CellAttributes() ?>><span id="el<?php echo $entrada_list->RowCnt ?>_entrada_departamento" class="control-group entrada_departamento">
<span<?php echo $entrada->departamento->ViewAttributes() ?>>
<?php echo $entrada->departamento->ListViewValue() ?></span>
</span><a id="<?php echo $entrada_list->PageObjName . "_row_" . $entrada_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($entrada->cuidad->Visible) { // cuidad ?>
		<td<?php echo $entrada->cuidad->CellAttributes() ?>><span id="el<?php echo $entrada_list->RowCnt ?>_entrada_cuidad" class="control-group entrada_cuidad">
<span<?php echo $entrada->cuidad->ViewAttributes() ?>>
<?php echo $entrada->cuidad->ListViewValue() ?></span>
</span><a id="<?php echo $entrada_list->PageObjName . "_row_" . $entrada_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($entrada->asunto->Visible) { // asunto ?>
		<td<?php echo $entrada->asunto->CellAttributes() ?>><span id="el<?php echo $entrada_list->RowCnt ?>_entrada_asunto" class="control-group entrada_asunto">
<span<?php echo $entrada->asunto->ViewAttributes() ?>>
<?php echo $entrada->asunto->ListViewValue() ?></span>
</span><a id="<?php echo $entrada_list->PageObjName . "_row_" . $entrada_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($entrada->descripcion->Visible) { // descripcion ?>
		<td<?php echo $entrada->descripcion->CellAttributes() ?>><span id="el<?php echo $entrada_list->RowCnt ?>_entrada_descripcion" class="control-group entrada_descripcion">
<span<?php echo $entrada->descripcion->ViewAttributes() ?>>
<?php echo $entrada->descripcion->ListViewValue() ?></span>
</span><a id="<?php echo $entrada_list->PageObjName . "_row_" . $entrada_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($entrada->adjuntar->Visible) { // adjuntar ?>
		<td<?php echo $entrada->adjuntar->CellAttributes() ?>><span id="el<?php echo $entrada_list->RowCnt ?>_entrada_adjuntar" class="control-group entrada_adjuntar">
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
</span><a id="<?php echo $entrada_list->PageObjName . "_row_" . $entrada_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($entrada->destino->Visible) { // destino ?>
		<td<?php echo $entrada->destino->CellAttributes() ?>><span id="el<?php echo $entrada_list->RowCnt ?>_entrada_destino" class="control-group entrada_destino">
<span<?php echo $entrada->destino->ViewAttributes() ?>>
<?php echo $entrada->destino->ListViewValue() ?></span>
</span><a id="<?php echo $entrada_list->PageObjName . "_row_" . $entrada_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($entrada->SecciF3n->Visible) { // Sección ?>
		<td<?php echo $entrada->SecciF3n->CellAttributes() ?>><span id="el<?php echo $entrada_list->RowCnt ?>_entrada_SecciF3n" class="control-group entrada_SecciF3n">
<span<?php echo $entrada->SecciF3n->ViewAttributes() ?>>
<?php echo $entrada->SecciF3n->ListViewValue() ?></span>
</span><a id="<?php echo $entrada_list->PageObjName . "_row_" . $entrada_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($entrada->subseccion->Visible) { // subseccion ?>
		<td<?php echo $entrada->subseccion->CellAttributes() ?>><span id="el<?php echo $entrada_list->RowCnt ?>_entrada_subseccion" class="control-group entrada_subseccion">
<span<?php echo $entrada->subseccion->ViewAttributes() ?>>
<?php echo $entrada->subseccion->ListViewValue() ?></span>
</span><a id="<?php echo $entrada_list->PageObjName . "_row_" . $entrada_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($entrada->Fecha_De_Ingreso->Visible) { // Fecha De Ingreso ?>
		<td<?php echo $entrada->Fecha_De_Ingreso->CellAttributes() ?>><span id="el<?php echo $entrada_list->RowCnt ?>_entrada_Fecha_De_Ingreso" class="control-group entrada_Fecha_De_Ingreso">
<span<?php echo $entrada->Fecha_De_Ingreso->ViewAttributes() ?>>
<?php echo $entrada->Fecha_De_Ingreso->ListViewValue() ?></span>
</span><a id="<?php echo $entrada_list->PageObjName . "_row_" . $entrada_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($entrada->hora->Visible) { // hora ?>
		<td<?php echo $entrada->hora->CellAttributes() ?>><span id="el<?php echo $entrada_list->RowCnt ?>_entrada_hora" class="control-group entrada_hora">
<span<?php echo $entrada->hora->ViewAttributes() ?>>
<?php echo $entrada->hora->ListViewValue() ?></span>
</span><a id="<?php echo $entrada_list->PageObjName . "_row_" . $entrada_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($entrada->Fecha_Documento->Visible) { // Fecha Documento ?>
		<td<?php echo $entrada->Fecha_Documento->CellAttributes() ?>><span id="el<?php echo $entrada_list->RowCnt ?>_entrada_Fecha_Documento" class="control-group entrada_Fecha_Documento">
<span<?php echo $entrada->Fecha_Documento->ViewAttributes() ?>>
<?php echo $entrada->Fecha_Documento->ListViewValue() ?></span>
</span><a id="<?php echo $entrada_list->PageObjName . "_row_" . $entrada_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($entrada->Tiempo_Documento->Visible) { // Tiempo Documento ?>
		<td<?php echo $entrada->Tiempo_Documento->CellAttributes() ?>><span id="el<?php echo $entrada_list->RowCnt ?>_entrada_Tiempo_Documento" class="control-group entrada_Tiempo_Documento">
<span<?php echo $entrada->Tiempo_Documento->ViewAttributes() ?>>
<?php echo $entrada->Tiempo_Documento->ListViewValue() ?></span>
</span><a id="<?php echo $entrada_list->PageObjName . "_row_" . $entrada_list->RowCnt ?>"></a></td>
	<?php } ?>
<?php

// Render list options (body, right)
$entrada_list->ListOptions->Render("body", "right", $entrada_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($entrada->CurrentAction <> "gridadd")
		$entrada_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($entrada->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($entrada_list->Recordset)
	$entrada_list->Recordset->Close();
?>
<?php if ($entrada->Export == "") { ?>
<div class="ewGridLowerPanel">
<?php if ($entrada->CurrentAction <> "gridadd" && $entrada->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-horizontal" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($entrada_list->Pager)) $entrada_list->Pager = new cPrevNextPager($entrada_list->StartRec, $entrada_list->DisplayRecs, $entrada_list->TotalRecs) ?>
<?php if ($entrada_list->Pager->RecordCount > 0) { ?>
<table cellspacing="0" class="ewStdTable"><tbody><tr><td>
	<?php echo $Language->Phrase("Page") ?>&nbsp;
<div class="input-prepend input-append">
<!--first page button-->
	<?php if ($entrada_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-small" type="button" href="<?php echo $entrada_list->PageUrl() ?>start=<?php echo $entrada_list->Pager->FirstButton->Start ?>"><i class="icon-step-backward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" type="button" disabled="disabled"><i class="icon-step-backward"></i></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($entrada_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-small" type="button" href="<?php echo $entrada_list->PageUrl() ?>start=<?php echo $entrada_list->Pager->PrevButton->Start ?>"><i class="icon-prev"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" type="button" disabled="disabled"><i class="icon-prev"></i></a>
	<?php } ?>
<!--current page number-->
	<input class="input-mini" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $entrada_list->Pager->CurrentPage ?>">
<!--next page button-->
	<?php if ($entrada_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-small" type="button" href="<?php echo $entrada_list->PageUrl() ?>start=<?php echo $entrada_list->Pager->NextButton->Start ?>"><i class="icon-play"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" type="button" disabled="disabled"><i class="icon-play"></i></a>
	<?php } ?>
<!--last page button-->
	<?php if ($entrada_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-small" type="button" href="<?php echo $entrada_list->PageUrl() ?>start=<?php echo $entrada_list->Pager->LastButton->Start ?>"><i class="icon-step-forward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small" type="button" disabled="disabled"><i class="icon-step-forward"></i></a>
	<?php } ?>
</div>
	&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $entrada_list->Pager->PageCount ?>
</td>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $entrada_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $entrada_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $entrada_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($entrada_list->SearchWhere == "0=101") { ?>
	<p><?php echo $Language->Phrase("EnterSearchCriteria") ?></p>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoRecord") ?></p>
	<?php } ?>
<?php } ?>
</td>
</tr></table>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($entrada_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
</div>
<?php } ?>
</td></tr></table>
<?php if ($entrada->Export == "") { ?>
<script type="text/javascript">
fentradalistsrch.Init();
fentradalist.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php } ?>
<?php
$entrada_list->ShowPageFooter();
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
$entrada_list->Page_Terminate();
?>
