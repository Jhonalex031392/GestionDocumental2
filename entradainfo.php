<?php

// Global variable for table object
$entrada = NULL;

//
// Table class for entrada
//
class centrada extends cTable {
	var $numero;
	var $radicado;
	var $clase_documento;
	var $identificacion;
	var $nombre_rem;
	var $apellidos_rem;
	var $telefono;
	var $direccion;
	var $correo_electronico;
	var $pais;
	var $departamento;
	var $cuidad;
	var $asunto;
	var $descripcion;
	var $adjuntar;
	var $destino;
	var $SecciF3n;
	var $subseccion;
	var $Fecha_De_Ingreso;
	var $hora;
	var $Fecha_Documento;
	var $Tiempo_Documento;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'entrada';
		$this->TableName = 'entrada';
		$this->TableType = 'TABLE';
		$this->ExportAll = FALSE;
		$this->ExportPageBreakCount = 0; // Page break per every n record (PDF only)
		$this->ExportPageOrientation = "portrait"; // Page orientation (PDF only)
		$this->ExportPageSize = "a4"; // Page size (PDF only)
		$this->DetailAdd = FALSE; // Allow detail add
		$this->DetailEdit = FALSE; // Allow detail edit
		$this->DetailView = FALSE; // Allow detail view
		$this->ShowMultipleDetails = FALSE; // Show multiple details
		$this->GridAddRowCount = 5;
		$this->AllowAddDeleteRow = ew_AllowAddDeleteRow(); // Allow add/delete row
		$this->UserIDAllowSecurity = 0; // User ID Allow
		$this->BasicSearch = new cBasicSearch($this->TableVar);

		// numero
		$this->numero = new cField('entrada', 'entrada', 'x_numero', 'numero', '`numero`', '`numero`', 3, -1, FALSE, '`numero`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->numero->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['numero'] = &$this->numero;

		// radicado
		$this->radicado = new cField('entrada', 'entrada', 'x_radicado', 'radicado', '`radicado`', '`radicado`', 200, -1, FALSE, '`radicado`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['radicado'] = &$this->radicado;

		// clase_documento
		$this->clase_documento = new cField('entrada', 'entrada', 'x_clase_documento', 'clase_documento', '`clase_documento`', '`clase_documento`', 3, -1, FALSE, '`clase_documento`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->clase_documento->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['clase_documento'] = &$this->clase_documento;

		// identificacion
		$this->identificacion = new cField('entrada', 'entrada', 'x_identificacion', 'identificacion', '`identificacion`', '`identificacion`', 3, -1, FALSE, '`identificacion`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->identificacion->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['identificacion'] = &$this->identificacion;

		// nombre_rem
		$this->nombre_rem = new cField('entrada', 'entrada', 'x_nombre_rem', 'nombre_rem', '`nombre_rem`', '`nombre_rem`', 200, -1, FALSE, '`nombre_rem`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['nombre_rem'] = &$this->nombre_rem;

		// apellidos_rem
		$this->apellidos_rem = new cField('entrada', 'entrada', 'x_apellidos_rem', 'apellidos_rem', '`apellidos_rem`', '`apellidos_rem`', 200, -1, FALSE, '`apellidos_rem`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['apellidos_rem'] = &$this->apellidos_rem;

		// telefono
		$this->telefono = new cField('entrada', 'entrada', 'x_telefono', 'telefono', '`telefono`', '`telefono`', 3, -1, FALSE, '`telefono`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->telefono->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['telefono'] = &$this->telefono;

		// direccion
		$this->direccion = new cField('entrada', 'entrada', 'x_direccion', 'direccion', '`direccion`', '`direccion`', 200, -1, FALSE, '`direccion`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['direccion'] = &$this->direccion;

		// correo_electronico
		$this->correo_electronico = new cField('entrada', 'entrada', 'x_correo_electronico', 'correo_electronico', '`correo_electronico`', '`correo_electronico`', 200, -1, FALSE, '`correo_electronico`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['correo_electronico'] = &$this->correo_electronico;

		// pais
		$this->pais = new cField('entrada', 'entrada', 'x_pais', 'pais', '`pais`', '`pais`', 200, -1, FALSE, '`pais`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['pais'] = &$this->pais;

		// departamento
		$this->departamento = new cField('entrada', 'entrada', 'x_departamento', 'departamento', '`departamento`', '`departamento`', 200, -1, FALSE, '`departamento`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['departamento'] = &$this->departamento;

		// cuidad
		$this->cuidad = new cField('entrada', 'entrada', 'x_cuidad', 'cuidad', '`cuidad`', '`cuidad`', 200, -1, FALSE, '`cuidad`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['cuidad'] = &$this->cuidad;

		// asunto
		$this->asunto = new cField('entrada', 'entrada', 'x_asunto', 'asunto', '`asunto`', '`asunto`', 200, -1, FALSE, '`asunto`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['asunto'] = &$this->asunto;

		// descripcion
		$this->descripcion = new cField('entrada', 'entrada', 'x_descripcion', 'descripcion', '`descripcion`', '`descripcion`', 200, -1, FALSE, '`descripcion`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['descripcion'] = &$this->descripcion;

		// adjuntar
		$this->adjuntar = new cField('entrada', 'entrada', 'x_adjuntar', 'adjuntar', '`adjuntar`', '`adjuntar`', 200, -1, TRUE, '`adjuntar`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['adjuntar'] = &$this->adjuntar;

		// destino
		$this->destino = new cField('entrada', 'entrada', 'x_destino', 'destino', '`destino`', '`destino`', 200, -1, FALSE, '`destino`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['destino'] = &$this->destino;

		// Sección
		$this->SecciF3n = new cField('entrada', 'entrada', 'x_SecciF3n', 'Sección', '`Sección`', '`Sección`', 200, -1, FALSE, '`Sección`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Sección'] = &$this->SecciF3n;

		// subseccion
		$this->subseccion = new cField('entrada', 'entrada', 'x_subseccion', 'subseccion', '`subseccion`', '`subseccion`', 200, -1, FALSE, '`subseccion`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['subseccion'] = &$this->subseccion;

		// Fecha De Ingreso
		$this->Fecha_De_Ingreso = new cField('entrada', 'entrada', 'x_Fecha_De_Ingreso', 'Fecha De Ingreso', '`Fecha De Ingreso`', 'DATE_FORMAT(`Fecha De Ingreso`, \'%Y/%m/%d\')', 133, 5, FALSE, '`Fecha De Ingreso`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Fecha_De_Ingreso->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateYMD"));
		$this->fields['Fecha De Ingreso'] = &$this->Fecha_De_Ingreso;

		// hora
		$this->hora = new cField('entrada', 'entrada', 'x_hora', 'hora', '`hora`', 'DATE_FORMAT(`hora`, \'%Y/%m/%d\')', 134, -1, FALSE, '`hora`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->hora->FldDefaultErrMsg = $Language->Phrase("IncorrectTime");
		$this->fields['hora'] = &$this->hora;

		// Fecha Documento
		$this->Fecha_Documento = new cField('entrada', 'entrada', 'x_Fecha_Documento', 'Fecha Documento', '`Fecha Documento`', 'DATE_FORMAT(`Fecha Documento`, \'%Y/%m/%d\')', 133, 5, FALSE, '`Fecha Documento`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Fecha_Documento->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateYMD"));
		$this->fields['Fecha Documento'] = &$this->Fecha_Documento;

		// Tiempo Documento
		$this->Tiempo_Documento = new cField('entrada', 'entrada', 'x_Tiempo_Documento', 'Tiempo Documento', '`Tiempo Documento`', '`Tiempo Documento`', 3, -1, FALSE, '`Tiempo Documento`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Tiempo_Documento->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Tiempo Documento'] = &$this->Tiempo_Documento;
	}

	// Single column sort
	function UpdateSort(&$ofld) {
		if ($this->CurrentOrder == $ofld->FldName) {
			$sSortField = $ofld->FldExpression;
			$sLastSort = $ofld->getSort();
			if ($this->CurrentOrderType == "ASC" || $this->CurrentOrderType == "DESC") {
				$sThisSort = $this->CurrentOrderType;
			} else {
				$sThisSort = ($sLastSort == "ASC") ? "DESC" : "ASC";
			}
			$ofld->setSort($sThisSort);
			$this->setSessionOrderBy($sSortField . " " . $sThisSort); // Save to Session
		} else {
			$ofld->setSort("");
		}
	}

	// Table level SQL
	function SqlFrom() { // From
		return "`entrada`";
	}

	function SqlSelect() { // Select
		return "SELECT * FROM " . $this->SqlFrom();
	}

	function SqlWhere() { // Where
		$sWhere = "";
		$this->TableFilter = "";
		ew_AddFilter($sWhere, $this->TableFilter);
		return $sWhere;
	}

	function SqlGroupBy() { // Group By
		return "";
	}

	function SqlHaving() { // Having
		return "";
	}

	function SqlOrderBy() { // Order By
		return "";
	}

	// Check if Anonymous User is allowed
	function AllowAnonymousUser() {
		switch (@$this->PageID) {
			case "add":
			case "register":
			case "addopt":
				return FALSE;
			case "edit":
			case "update":
			case "changepwd":
			case "forgotpwd":
				return FALSE;
			case "delete":
				return FALSE;
			case "view":
				return FALSE;
			case "search":
				return FALSE;
			default:
				return FALSE;
		}
	}

	// Apply User ID filters
	function ApplyUserIDFilters($sFilter) {
		return $sFilter;
	}

	// Check if User ID security allows view all
	function UserIDAllow($id = "") {
		$allow = EW_USER_ID_ALLOW;
		switch ($id) {
			case "add":
			case "copy":
			case "gridadd":
			case "register":
			case "addopt":
				return (($allow & 1) == 1);
			case "edit":
			case "gridedit":
			case "update":
			case "changepwd":
			case "forgotpwd":
				return (($allow & 4) == 4);
			case "delete":
				return (($allow & 2) == 2);
			case "view":
				return (($allow & 32) == 32);
			case "search":
				return (($allow & 64) == 64);
			default:
				return (($allow & 8) == 8);
		}
	}

	// Get SQL
	function GetSQL($where, $orderby) {
		return ew_BuildSelectSql($this->SqlSelect(), $this->SqlWhere(),
			$this->SqlGroupBy(), $this->SqlHaving(), $this->SqlOrderBy(),
			$where, $orderby);
	}

	// Table SQL
	function SQL() {
		$sFilter = $this->CurrentFilter;
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql($this->SqlSelect(), $this->SqlWhere(),
			$this->SqlGroupBy(), $this->SqlHaving(), $this->SqlOrderBy(),
			$sFilter, $sSort);
	}

	// Table SQL with List page filter
	function SelectSQL() {
		$sFilter = $this->getSessionWhere();
		ew_AddFilter($sFilter, $this->CurrentFilter);
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql($this->SqlSelect(), $this->SqlWhere(), $this->SqlGroupBy(),
			$this->SqlHaving(), $this->SqlOrderBy(), $sFilter, $sSort);
	}

	// Get ORDER BY clause
	function GetOrderBy() {
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql("", "", "", "", $this->SqlOrderBy(), "", $sSort);
	}

	// Try to get record count
	function TryGetRecordCount($sSql) {
		global $conn;
		$cnt = -1;
		if ($this->TableType == 'TABLE' || $this->TableType == 'VIEW') {
			$sSql = "SELECT COUNT(*) FROM" . substr($sSql, 13);
			$sOrderBy = $this->GetOrderBy();
			if (substr($sSql, strlen($sOrderBy) * -1) == $sOrderBy)
				$sSql = substr($sSql, 0, strlen($sSql) - strlen($sOrderBy)); // Remove ORDER BY clause
		} else {
			$sSql = "SELECT COUNT(*) FROM (" . $sSql . ") EW_COUNT_TABLE";
		}
		if ($rs = $conn->Execute($sSql)) {
			if (!$rs->EOF && $rs->FieldCount() > 0) {
				$cnt = $rs->fields[0];
				$rs->Close();
			}
		}
		return intval($cnt);
	}

	// Get record count based on filter (for detail record count in master table pages)
	function LoadRecordCount($sFilter) {
		$origFilter = $this->CurrentFilter;
		$this->CurrentFilter = $sFilter;
		$this->Recordset_Selecting($this->CurrentFilter);

		//$sSql = $this->SQL();
		$sSql = $this->GetSQL($this->CurrentFilter, "");
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			if ($rs = $this->LoadRs($this->CurrentFilter)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		$this->CurrentFilter = $origFilter;
		return intval($cnt);
	}

	// Get record count (for current List page)
	function SelectRecordCount() {
		global $conn;
		$origFilter = $this->CurrentFilter;
		$this->Recordset_Selecting($this->CurrentFilter);
		$sSql = $this->SelectSQL();
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			if ($rs = $conn->Execute($sSql)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		$this->CurrentFilter = $origFilter;
		return intval($cnt);
	}

	// Update Table
	var $UpdateTable = "`entrada`";

	// INSERT statement
	function InsertSQL(&$rs) {
		global $conn;
		$names = "";
		$values = "";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]))
				continue;
			$names .= $this->fields[$name]->FldExpression . ",";
			$values .= ew_QuotedValue($value, $this->fields[$name]->FldDataType) . ",";
		}
		while (substr($names, -1) == ",")
			$names = substr($names, 0, -1);
		while (substr($values, -1) == ",")
			$values = substr($values, 0, -1);
		return "INSERT INTO " . $this->UpdateTable . " ($names) VALUES ($values)";
	}

	// Insert
	function Insert(&$rs) {
		global $conn;
		return $conn->Execute($this->InsertSQL($rs));
	}

	// UPDATE statement
	function UpdateSQL(&$rs, $where = "") {
		$sql = "UPDATE " . $this->UpdateTable . " SET ";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]))
				continue;
			$sql .= $this->fields[$name]->FldExpression . "=";
			$sql .= ew_QuotedValue($value, $this->fields[$name]->FldDataType) . ",";
		}
		while (substr($sql, -1) == ",")
			$sql = substr($sql, 0, -1);
		$filter = $this->CurrentFilter;
		ew_AddFilter($filter, $where);
		if ($filter <> "")	$sql .= " WHERE " . $filter;
		return $sql;
	}

	// Update
	function Update(&$rs, $where = "", $rsold = NULL) {
		global $conn;
		return $conn->Execute($this->UpdateSQL($rs, $where));
	}

	// DELETE statement
	function DeleteSQL(&$rs, $where = "") {
		$sql = "DELETE FROM " . $this->UpdateTable . " WHERE ";
		if ($rs) {
			if (array_key_exists('numero', $rs))
				ew_AddFilter($where, ew_QuotedName('numero') . '=' . ew_QuotedValue($rs['numero'], $this->numero->FldDataType));
		}
		$filter = $this->CurrentFilter;
		ew_AddFilter($filter, $where);
		if ($filter <> "")
			$sql .= $filter;
		else
			$sql .= "0=1"; // Avoid delete
		return $sql;
	}

	// Delete
	function Delete(&$rs, $where = "") {
		global $conn;
		return $conn->Execute($this->DeleteSQL($rs, $where));
	}

	// Key filter WHERE clause
	function SqlKeyFilter() {
		return "`numero` = @numero@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->numero->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@numero@", ew_AdjustSql($this->numero->CurrentValue), $sKeyFilter); // Replace key value
		return $sKeyFilter;
	}

	// Return page URL
	function getReturnUrl() {
		$name = EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL;

		// Get referer URL automatically
		if (ew_ServerVar("HTTP_REFERER") <> "" && ew_ReferPage() <> ew_CurrentPage() && ew_ReferPage() <> "login.php") // Referer not same page or login page
			$_SESSION[$name] = ew_ServerVar("HTTP_REFERER"); // Save to Session
		if (@$_SESSION[$name] <> "") {
			return $_SESSION[$name];
		} else {
			return "entradalist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "entradalist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("entradaview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("entradaview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl() {
		return "entradaadd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("entradaedit.php", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("entradaadd.php", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("entradadelete.php", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->numero->CurrentValue)) {
			$sUrl .= "numero=" . urlencode($this->numero->CurrentValue);
		} else {
			return "javascript:alert(ewLanguage.Phrase('InvalidRecord'));";
		}
		return $sUrl;
	}

	// Sort URL
	function SortUrl(&$fld) {
		if ($this->CurrentAction <> "" || $this->Export <> "" ||
			in_array($fld->FldType, array(128, 204, 205))) { // Unsortable data type
				return "";
		} elseif ($fld->Sortable) {
			$sUrlParm = $this->UrlParm("order=" . urlencode($fld->FldName) . "&ordertype=" . $fld->ReverseSort());
			return ew_CurrentPage() . "?" . $sUrlParm;
		} else {
			return "";
		}
	}

	// Get record keys from $_POST/$_GET/$_SESSION
	function GetRecordKeys() {
		global $EW_COMPOSITE_KEY_SEPARATOR;
		$arKeys = array();
		$arKey = array();
		if (isset($_POST["key_m"])) {
			$arKeys = ew_StripSlashes($_POST["key_m"]);
			$cnt = count($arKeys);
		} elseif (isset($_GET["key_m"])) {
			$arKeys = ew_StripSlashes($_GET["key_m"]);
			$cnt = count($arKeys);
		} elseif (isset($_GET)) {
			$arKeys[] = @$_GET["numero"]; // numero

			//return $arKeys; // Do not return yet, so the values will also be checked by the following code
		}

		// Check keys
		$ar = array();
		foreach ($arKeys as $key) {
			if (!is_numeric($key))
				continue;
			$ar[] = $key;
		}
		return $ar;
	}

	// Get key filter
	function GetKeyFilter() {
		$arKeys = $this->GetRecordKeys();
		$sKeyFilter = "";
		foreach ($arKeys as $key) {
			if ($sKeyFilter <> "") $sKeyFilter .= " OR ";
			$this->numero->CurrentValue = $key;
			$sKeyFilter .= "(" . $this->KeyFilter() . ")";
		}
		return $sKeyFilter;
	}

	// Load rows based on filter
	function &LoadRs($sFilter) {
		global $conn;

		// Set up filter (SQL WHERE clause) and get return SQL
		//$this->CurrentFilter = $sFilter;
		//$sSql = $this->SQL();

		$sSql = $this->GetSQL($sFilter, "");
		$rs = $conn->Execute($sSql);
		return $rs;
	}

	// Load row values from recordset
	function LoadListRowValues(&$rs) {
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

	// Render list row values
	function RenderListRow() {
		global $conn, $Security;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
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

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Aggregate list row values
	function AggregateListRowValues() {
	}

	// Aggregate list row (for rendering)
	function AggregateListRow() {
	}

	// Export data in HTML/CSV/Word/Excel/Email/PDF format
	function ExportDocument(&$Doc, &$Recordset, $StartRec, $StopRec, $ExportPageType = "") {
		if (!$Recordset || !$Doc)
			return;

		// Write header
		$Doc->ExportTableHeader();
		if ($Doc->Horizontal) { // Horizontal format, write header
			$Doc->BeginExportRow();
			if ($ExportPageType == "view") {
				if ($this->numero->Exportable) $Doc->ExportCaption($this->numero);
				if ($this->radicado->Exportable) $Doc->ExportCaption($this->radicado);
				if ($this->clase_documento->Exportable) $Doc->ExportCaption($this->clase_documento);
				if ($this->identificacion->Exportable) $Doc->ExportCaption($this->identificacion);
				if ($this->nombre_rem->Exportable) $Doc->ExportCaption($this->nombre_rem);
				if ($this->apellidos_rem->Exportable) $Doc->ExportCaption($this->apellidos_rem);
				if ($this->telefono->Exportable) $Doc->ExportCaption($this->telefono);
				if ($this->direccion->Exportable) $Doc->ExportCaption($this->direccion);
				if ($this->correo_electronico->Exportable) $Doc->ExportCaption($this->correo_electronico);
				if ($this->pais->Exportable) $Doc->ExportCaption($this->pais);
				if ($this->departamento->Exportable) $Doc->ExportCaption($this->departamento);
				if ($this->cuidad->Exportable) $Doc->ExportCaption($this->cuidad);
				if ($this->asunto->Exportable) $Doc->ExportCaption($this->asunto);
				if ($this->descripcion->Exportable) $Doc->ExportCaption($this->descripcion);
				if ($this->adjuntar->Exportable) $Doc->ExportCaption($this->adjuntar);
				if ($this->destino->Exportable) $Doc->ExportCaption($this->destino);
				if ($this->SecciF3n->Exportable) $Doc->ExportCaption($this->SecciF3n);
				if ($this->subseccion->Exportable) $Doc->ExportCaption($this->subseccion);
				if ($this->Fecha_De_Ingreso->Exportable) $Doc->ExportCaption($this->Fecha_De_Ingreso);
				if ($this->hora->Exportable) $Doc->ExportCaption($this->hora);
				if ($this->Fecha_Documento->Exportable) $Doc->ExportCaption($this->Fecha_Documento);
				if ($this->Tiempo_Documento->Exportable) $Doc->ExportCaption($this->Tiempo_Documento);
			} else {
				if ($this->numero->Exportable) $Doc->ExportCaption($this->numero);
				if ($this->radicado->Exportable) $Doc->ExportCaption($this->radicado);
				if ($this->clase_documento->Exportable) $Doc->ExportCaption($this->clase_documento);
				if ($this->identificacion->Exportable) $Doc->ExportCaption($this->identificacion);
				if ($this->nombre_rem->Exportable) $Doc->ExportCaption($this->nombre_rem);
				if ($this->apellidos_rem->Exportable) $Doc->ExportCaption($this->apellidos_rem);
				if ($this->telefono->Exportable) $Doc->ExportCaption($this->telefono);
				if ($this->direccion->Exportable) $Doc->ExportCaption($this->direccion);
				if ($this->correo_electronico->Exportable) $Doc->ExportCaption($this->correo_electronico);
				if ($this->pais->Exportable) $Doc->ExportCaption($this->pais);
				if ($this->departamento->Exportable) $Doc->ExportCaption($this->departamento);
				if ($this->cuidad->Exportable) $Doc->ExportCaption($this->cuidad);
				if ($this->asunto->Exportable) $Doc->ExportCaption($this->asunto);
				if ($this->descripcion->Exportable) $Doc->ExportCaption($this->descripcion);
				if ($this->adjuntar->Exportable) $Doc->ExportCaption($this->adjuntar);
				if ($this->destino->Exportable) $Doc->ExportCaption($this->destino);
				if ($this->SecciF3n->Exportable) $Doc->ExportCaption($this->SecciF3n);
				if ($this->subseccion->Exportable) $Doc->ExportCaption($this->subseccion);
				if ($this->Fecha_De_Ingreso->Exportable) $Doc->ExportCaption($this->Fecha_De_Ingreso);
				if ($this->hora->Exportable) $Doc->ExportCaption($this->hora);
				if ($this->Fecha_Documento->Exportable) $Doc->ExportCaption($this->Fecha_Documento);
				if ($this->Tiempo_Documento->Exportable) $Doc->ExportCaption($this->Tiempo_Documento);
			}
			$Doc->EndExportRow();
		}

		// Move to first record
		$RecCnt = $StartRec - 1;
		if (!$Recordset->EOF) {
			$Recordset->MoveFirst();
			if ($StartRec > 1)
				$Recordset->Move($StartRec - 1);
		}
		while (!$Recordset->EOF && $RecCnt < $StopRec) {
			$RecCnt++;
			if (intval($RecCnt) >= intval($StartRec)) {
				$RowCnt = intval($RecCnt) - intval($StartRec) + 1;

				// Page break
				if ($this->ExportPageBreakCount > 0) {
					if ($RowCnt > 1 && ($RowCnt - 1) % $this->ExportPageBreakCount == 0)
						$Doc->ExportPageBreak();
				}
				$this->LoadListRowValues($Recordset);

				// Render row
				$this->RowType = EW_ROWTYPE_VIEW; // Render view
				$this->ResetAttrs();
				$this->RenderListRow();
				$Doc->BeginExportRow($RowCnt); // Allow CSS styles if enabled
				if ($ExportPageType == "view") {
					if ($this->numero->Exportable) $Doc->ExportField($this->numero);
					if ($this->radicado->Exportable) $Doc->ExportField($this->radicado);
					if ($this->clase_documento->Exportable) $Doc->ExportField($this->clase_documento);
					if ($this->identificacion->Exportable) $Doc->ExportField($this->identificacion);
					if ($this->nombre_rem->Exportable) $Doc->ExportField($this->nombre_rem);
					if ($this->apellidos_rem->Exportable) $Doc->ExportField($this->apellidos_rem);
					if ($this->telefono->Exportable) $Doc->ExportField($this->telefono);
					if ($this->direccion->Exportable) $Doc->ExportField($this->direccion);
					if ($this->correo_electronico->Exportable) $Doc->ExportField($this->correo_electronico);
					if ($this->pais->Exportable) $Doc->ExportField($this->pais);
					if ($this->departamento->Exportable) $Doc->ExportField($this->departamento);
					if ($this->cuidad->Exportable) $Doc->ExportField($this->cuidad);
					if ($this->asunto->Exportable) $Doc->ExportField($this->asunto);
					if ($this->descripcion->Exportable) $Doc->ExportField($this->descripcion);
					if ($this->adjuntar->Exportable) $Doc->ExportField($this->adjuntar);
					if ($this->destino->Exportable) $Doc->ExportField($this->destino);
					if ($this->SecciF3n->Exportable) $Doc->ExportField($this->SecciF3n);
					if ($this->subseccion->Exportable) $Doc->ExportField($this->subseccion);
					if ($this->Fecha_De_Ingreso->Exportable) $Doc->ExportField($this->Fecha_De_Ingreso);
					if ($this->hora->Exportable) $Doc->ExportField($this->hora);
					if ($this->Fecha_Documento->Exportable) $Doc->ExportField($this->Fecha_Documento);
					if ($this->Tiempo_Documento->Exportable) $Doc->ExportField($this->Tiempo_Documento);
				} else {
					if ($this->numero->Exportable) $Doc->ExportField($this->numero);
					if ($this->radicado->Exportable) $Doc->ExportField($this->radicado);
					if ($this->clase_documento->Exportable) $Doc->ExportField($this->clase_documento);
					if ($this->identificacion->Exportable) $Doc->ExportField($this->identificacion);
					if ($this->nombre_rem->Exportable) $Doc->ExportField($this->nombre_rem);
					if ($this->apellidos_rem->Exportable) $Doc->ExportField($this->apellidos_rem);
					if ($this->telefono->Exportable) $Doc->ExportField($this->telefono);
					if ($this->direccion->Exportable) $Doc->ExportField($this->direccion);
					if ($this->correo_electronico->Exportable) $Doc->ExportField($this->correo_electronico);
					if ($this->pais->Exportable) $Doc->ExportField($this->pais);
					if ($this->departamento->Exportable) $Doc->ExportField($this->departamento);
					if ($this->cuidad->Exportable) $Doc->ExportField($this->cuidad);
					if ($this->asunto->Exportable) $Doc->ExportField($this->asunto);
					if ($this->descripcion->Exportable) $Doc->ExportField($this->descripcion);
					if ($this->adjuntar->Exportable) $Doc->ExportField($this->adjuntar);
					if ($this->destino->Exportable) $Doc->ExportField($this->destino);
					if ($this->SecciF3n->Exportable) $Doc->ExportField($this->SecciF3n);
					if ($this->subseccion->Exportable) $Doc->ExportField($this->subseccion);
					if ($this->Fecha_De_Ingreso->Exportable) $Doc->ExportField($this->Fecha_De_Ingreso);
					if ($this->hora->Exportable) $Doc->ExportField($this->hora);
					if ($this->Fecha_Documento->Exportable) $Doc->ExportField($this->Fecha_Documento);
					if ($this->Tiempo_Documento->Exportable) $Doc->ExportField($this->Tiempo_Documento);
				}
				$Doc->EndExportRow();
			}
			$Recordset->MoveNext();
		}
		$Doc->ExportTableFooter();
	}

	// Table level events
	// Recordset Selecting event
	function Recordset_Selecting(&$filter) {

		// Enter your code here	
	}

	// Recordset Selected event
	function Recordset_Selected(&$rs) {

		//echo "Recordset Selected";
	}

	// Recordset Search Validated event
	function Recordset_SearchValidated() {

		// Example:
		//$this->MyField1->AdvancedSearch->SearchValue = "your search criteria"; // Search value

	}

	// Recordset Searching event
	function Recordset_Searching(&$filter) {

		// Enter your code here	
	}

	// Row_Selecting event
	function Row_Selecting(&$filter) {

		// Enter your code here	
	}

	// Row Selected event
	function Row_Selected(&$rs) {

		//echo "Row Selected";
	}

	// Row Inserting event
	function Row_Inserting($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Inserted event
	function Row_Inserted($rsold, &$rsnew) {

		//echo "Row Inserted"
	}

	// Row Updating event
	function Row_Updating($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Updated event
	function Row_Updated($rsold, &$rsnew) {

		//echo "Row Updated";
	}

	// Row Update Conflict event
	function Row_UpdateConflict($rsold, &$rsnew) {

		// Enter your code here
		// To ignore conflict, set return value to FALSE

		return TRUE;
	}

	// Row Deleting event
	function Row_Deleting(&$rs) {

		// Enter your code here
		// To cancel, set return value to False

		return TRUE;
	}

	// Row Deleted event
	function Row_Deleted(&$rs) {

		//echo "Row Deleted";
	}

	// Email Sending event
	function Email_Sending(&$Email, &$Args) {

		//var_dump($Email); var_dump($Args); exit();
		return TRUE;
	}

	// Lookup Selecting event
	function Lookup_Selecting($fld, &$filter) {

		// Enter your code here
	}

	// Row Rendering event
	function Row_Rendering() {

		// Enter your code here	
	}

	// Row Rendered event
	function Row_Rendered() {

		// To view properties of field class, use:
		//var_dump($this-><FieldName>); 

	}

	// User ID Filtering event
	function UserID_Filtering(&$filter) {

		// Enter your code here
	}
}
?>
