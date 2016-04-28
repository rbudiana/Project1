<?php

// Global variable for table object
$parsys = NULL;

//
// Table class for parsys
//
class cparsys extends cTable {
	var $Nama1;
	var $Alamat1;
	var $Nama2;
	var $Alamat2;
	var $Nama3;
	var $Alamat3;
	var $Nama4;
	var $Alamat4;
	var $Nama5;
	var $Alamat5;
	var $Nama6;
	var $Alamat6;
	var $ID;
	var $site;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'parsys';
		$this->TableName = 'parsys';
		$this->TableType = 'TABLE';
		$this->ExportAll = TRUE;
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

		// Nama1
		$this->Nama1 = new cField('parsys', 'parsys', 'x_Nama1', 'Nama1', '`Nama1`', '`Nama1`', 3, -1, FALSE, '`Nama1`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Nama1->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Nama1'] = &$this->Nama1;

		// Alamat1
		$this->Alamat1 = new cField('parsys', 'parsys', 'x_Alamat1', 'Alamat1', '`Alamat1`', '`Alamat1`', 3, -1, FALSE, '`Alamat1`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Alamat1->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Alamat1'] = &$this->Alamat1;

		// Nama2
		$this->Nama2 = new cField('parsys', 'parsys', 'x_Nama2', 'Nama2', '`Nama2`', '`Nama2`', 3, -1, FALSE, '`Nama2`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Nama2->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Nama2'] = &$this->Nama2;

		// Alamat2
		$this->Alamat2 = new cField('parsys', 'parsys', 'x_Alamat2', 'Alamat2', '`Alamat2`', '`Alamat2`', 3, -1, FALSE, '`Alamat2`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Alamat2->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Alamat2'] = &$this->Alamat2;

		// Nama3
		$this->Nama3 = new cField('parsys', 'parsys', 'x_Nama3', 'Nama3', '`Nama3`', '`Nama3`', 3, -1, FALSE, '`Nama3`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Nama3->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Nama3'] = &$this->Nama3;

		// Alamat3
		$this->Alamat3 = new cField('parsys', 'parsys', 'x_Alamat3', 'Alamat3', '`Alamat3`', '`Alamat3`', 3, -1, FALSE, '`Alamat3`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Alamat3->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Alamat3'] = &$this->Alamat3;

		// Nama4
		$this->Nama4 = new cField('parsys', 'parsys', 'x_Nama4', 'Nama4', '`Nama4`', '`Nama4`', 3, -1, FALSE, '`Nama4`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Nama4->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Nama4'] = &$this->Nama4;

		// Alamat4
		$this->Alamat4 = new cField('parsys', 'parsys', 'x_Alamat4', 'Alamat4', '`Alamat4`', '`Alamat4`', 3, -1, FALSE, '`Alamat4`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Alamat4->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Alamat4'] = &$this->Alamat4;

		// Nama5
		$this->Nama5 = new cField('parsys', 'parsys', 'x_Nama5', 'Nama5', '`Nama5`', '`Nama5`', 3, -1, FALSE, '`Nama5`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Nama5->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Nama5'] = &$this->Nama5;

		// Alamat5
		$this->Alamat5 = new cField('parsys', 'parsys', 'x_Alamat5', 'Alamat5', '`Alamat5`', '`Alamat5`', 3, -1, FALSE, '`Alamat5`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Alamat5->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Alamat5'] = &$this->Alamat5;

		// Nama6
		$this->Nama6 = new cField('parsys', 'parsys', 'x_Nama6', 'Nama6', '`Nama6`', '`Nama6`', 3, -1, FALSE, '`Nama6`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Nama6->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Nama6'] = &$this->Nama6;

		// Alamat6
		$this->Alamat6 = new cField('parsys', 'parsys', 'x_Alamat6', 'Alamat6', '`Alamat6`', '`Alamat6`', 3, -1, FALSE, '`Alamat6`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Alamat6->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Alamat6'] = &$this->Alamat6;

		// ID
		$this->ID = new cField('parsys', 'parsys', 'x_ID', 'ID', '`ID`', '`ID`', 3, -1, FALSE, '`ID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->ID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['ID'] = &$this->ID;

		// site
		$this->site = new cField('parsys', 'parsys', 'x_site', 'site', '`site`', '`site`', 200, -1, FALSE, '`site`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['site'] = &$this->site;
	}

	// Multiple column sort
	function UpdateSort(&$ofld, $ctrl) {
		if ($this->CurrentOrder == $ofld->FldName) {
			$sSortField = $ofld->FldExpression;
			$sLastSort = $ofld->getSort();
			if ($this->CurrentOrderType == "ASC" || $this->CurrentOrderType == "DESC") {
				$sThisSort = $this->CurrentOrderType;
			} else {
				$sThisSort = ($sLastSort == "ASC") ? "DESC" : "ASC";
			}
			$ofld->setSort($sThisSort);
			if ($ctrl) {
				$sOrderBy = $this->getSessionOrderBy();
				if (strpos($sOrderBy, $sSortField . " " . $sLastSort) !== FALSE) {
					$sOrderBy = str_replace($sSortField . " " . $sLastSort, $sSortField . " " . $sThisSort, $sOrderBy);
				} else {
					if ($sOrderBy <> "") $sOrderBy .= ", ";
					$sOrderBy .= $sSortField . " " . $sThisSort;
				}
				$this->setSessionOrderBy($sOrderBy); // Save to Session
			} else {
				$this->setSessionOrderBy($sSortField . " " . $sThisSort); // Save to Session
			}
		} else {
			if (!$ctrl) $ofld->setSort("");
		}
	}

	// Table level SQL
	function SqlFrom() { // From
		return "`parsys`";
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
		global $Security;

		// Add User ID filter
		if (!$this->AllowAnonymousUser() && $Security->CurrentUserID() <> "" && !$Security->IsAdmin()) { // Non system admin
			$sFilter = $this->AddUserIDFilter($sFilter);
		}
		return $sFilter;
	}

	// Check if User ID security allows view all
	function UserIDAllow($id = "") {
		$allow = $this->UserIDAllowSecurity;
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
	var $UpdateTable = "`parsys`";

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
			if (array_key_exists('ID', $rs))
				ew_AddFilter($where, ew_QuotedName('ID') . '=' . ew_QuotedValue($rs['ID'], $this->ID->FldDataType));
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
		return "`ID` = @ID@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->ID->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@ID@", ew_AdjustSql($this->ID->CurrentValue), $sKeyFilter); // Replace key value
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
			return "parsyslist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "parsyslist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("parsysview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("parsysview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl() {
		return "parsysadd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("parsysedit.php", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("parsysadd.php", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("parsysdelete.php", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->ID->CurrentValue)) {
			$sUrl .= "ID=" . urlencode($this->ID->CurrentValue);
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
			$sUrlParm = $this->UrlParm("order=" . urlencode($fld->FldName) . "&amp;ordertype=" . $fld->ReverseSort());
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
			$arKeys[] = @$_GET["ID"]; // ID

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
			$this->ID->CurrentValue = $key;
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
		$this->Nama1->setDbValue($rs->fields('Nama1'));
		$this->Alamat1->setDbValue($rs->fields('Alamat1'));
		$this->Nama2->setDbValue($rs->fields('Nama2'));
		$this->Alamat2->setDbValue($rs->fields('Alamat2'));
		$this->Nama3->setDbValue($rs->fields('Nama3'));
		$this->Alamat3->setDbValue($rs->fields('Alamat3'));
		$this->Nama4->setDbValue($rs->fields('Nama4'));
		$this->Alamat4->setDbValue($rs->fields('Alamat4'));
		$this->Nama5->setDbValue($rs->fields('Nama5'));
		$this->Alamat5->setDbValue($rs->fields('Alamat5'));
		$this->Nama6->setDbValue($rs->fields('Nama6'));
		$this->Alamat6->setDbValue($rs->fields('Alamat6'));
		$this->ID->setDbValue($rs->fields('ID'));
		$this->site->setDbValue($rs->fields('site'));
	}

	// Render list row values
	function RenderListRow() {
		global $conn, $Security, $gsLanguage;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
		// Nama1
		// Alamat1

		$this->Alamat1->CellCssStyle = "white-space: nowrap;";

		// Nama2
		// Alamat2

		$this->Alamat2->CellCssStyle = "white-space: nowrap;";

		// Nama3
		// Alamat3

		$this->Alamat3->CellCssStyle = "white-space: nowrap;";

		// Nama4
		// Alamat4

		$this->Alamat4->CellCssStyle = "white-space: nowrap;";

		// Nama5
		// Alamat5

		$this->Alamat5->CellCssStyle = "white-space: nowrap;";

		// Nama6
		// Alamat6

		$this->Alamat6->CellCssStyle = "white-space: nowrap;";

		// ID
		// site
		// Nama1

		$this->Nama1->ViewValue = $this->Nama1->CurrentValue;
		$this->Nama1->ViewCustomAttributes = "";

		// Alamat1
		$this->Alamat1->ViewValue = $this->Alamat1->CurrentValue;
		$this->Alamat1->ViewCustomAttributes = "";

		// Nama2
		$this->Nama2->ViewValue = $this->Nama2->CurrentValue;
		$this->Nama2->ViewCustomAttributes = "";

		// Alamat2
		$this->Alamat2->ViewValue = $this->Alamat2->CurrentValue;
		$this->Alamat2->ViewCustomAttributes = "";

		// Nama3
		$this->Nama3->ViewValue = $this->Nama3->CurrentValue;
		$this->Nama3->ViewCustomAttributes = "";

		// Alamat3
		$this->Alamat3->ViewValue = $this->Alamat3->CurrentValue;
		$this->Alamat3->ViewCustomAttributes = "";

		// Nama4
		$this->Nama4->ViewValue = $this->Nama4->CurrentValue;
		$this->Nama4->ViewCustomAttributes = "";

		// Alamat4
		$this->Alamat4->ViewValue = $this->Alamat4->CurrentValue;
		$this->Alamat4->ViewCustomAttributes = "";

		// Nama5
		$this->Nama5->ViewValue = $this->Nama5->CurrentValue;
		$this->Nama5->ViewCustomAttributes = "";

		// Alamat5
		$this->Alamat5->ViewValue = $this->Alamat5->CurrentValue;
		$this->Alamat5->ViewCustomAttributes = "";

		// Nama6
		$this->Nama6->ViewValue = $this->Nama6->CurrentValue;
		$this->Nama6->ViewCustomAttributes = "";

		// Alamat6
		$this->Alamat6->ViewValue = $this->Alamat6->CurrentValue;
		$this->Alamat6->ViewCustomAttributes = "";

		// ID
		$this->ID->ViewValue = $this->ID->CurrentValue;
		$this->ID->ViewCustomAttributes = "";

		// site
		$this->site->ViewValue = $this->site->CurrentValue;
		$this->site->ViewCustomAttributes = "";

		// Nama1
		$this->Nama1->LinkCustomAttributes = "";
		$this->Nama1->HrefValue = "";
		$this->Nama1->TooltipValue = "";

		// Alamat1
		$this->Alamat1->LinkCustomAttributes = "";
		$this->Alamat1->HrefValue = "";
		$this->Alamat1->TooltipValue = "";

		// Nama2
		$this->Nama2->LinkCustomAttributes = "";
		$this->Nama2->HrefValue = "";
		$this->Nama2->TooltipValue = "";

		// Alamat2
		$this->Alamat2->LinkCustomAttributes = "";
		$this->Alamat2->HrefValue = "";
		$this->Alamat2->TooltipValue = "";

		// Nama3
		$this->Nama3->LinkCustomAttributes = "";
		$this->Nama3->HrefValue = "";
		$this->Nama3->TooltipValue = "";

		// Alamat3
		$this->Alamat3->LinkCustomAttributes = "";
		$this->Alamat3->HrefValue = "";
		$this->Alamat3->TooltipValue = "";

		// Nama4
		$this->Nama4->LinkCustomAttributes = "";
		$this->Nama4->HrefValue = "";
		$this->Nama4->TooltipValue = "";

		// Alamat4
		$this->Alamat4->LinkCustomAttributes = "";
		$this->Alamat4->HrefValue = "";
		$this->Alamat4->TooltipValue = "";

		// Nama5
		$this->Nama5->LinkCustomAttributes = "";
		$this->Nama5->HrefValue = "";
		$this->Nama5->TooltipValue = "";

		// Alamat5
		$this->Alamat5->LinkCustomAttributes = "";
		$this->Alamat5->HrefValue = "";
		$this->Alamat5->TooltipValue = "";

		// Nama6
		$this->Nama6->LinkCustomAttributes = "";
		$this->Nama6->HrefValue = "";
		$this->Nama6->TooltipValue = "";

		// Alamat6
		$this->Alamat6->LinkCustomAttributes = "";
		$this->Alamat6->HrefValue = "";
		$this->Alamat6->TooltipValue = "";

		// ID
		$this->ID->LinkCustomAttributes = "";
		$this->ID->HrefValue = "";
		$this->ID->TooltipValue = "";

		// site
		$this->site->LinkCustomAttributes = "";
		$this->site->HrefValue = "";
		$this->site->TooltipValue = "";

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
				if ($this->Nama1->Exportable) $Doc->ExportCaption($this->Nama1);
				if ($this->Nama2->Exportable) $Doc->ExportCaption($this->Nama2);
				if ($this->Nama3->Exportable) $Doc->ExportCaption($this->Nama3);
				if ($this->Nama4->Exportable) $Doc->ExportCaption($this->Nama4);
				if ($this->Nama5->Exportable) $Doc->ExportCaption($this->Nama5);
				if ($this->Nama6->Exportable) $Doc->ExportCaption($this->Nama6);
				if ($this->ID->Exportable) $Doc->ExportCaption($this->ID);
				if ($this->site->Exportable) $Doc->ExportCaption($this->site);
			} else {
				if ($this->Nama1->Exportable) $Doc->ExportCaption($this->Nama1);
				if ($this->Nama2->Exportable) $Doc->ExportCaption($this->Nama2);
				if ($this->Nama3->Exportable) $Doc->ExportCaption($this->Nama3);
				if ($this->Nama4->Exportable) $Doc->ExportCaption($this->Nama4);
				if ($this->Nama5->Exportable) $Doc->ExportCaption($this->Nama5);
				if ($this->Nama6->Exportable) $Doc->ExportCaption($this->Nama6);
				if ($this->ID->Exportable) $Doc->ExportCaption($this->ID);
				if ($this->site->Exportable) $Doc->ExportCaption($this->site);
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
					if ($this->Nama1->Exportable) $Doc->ExportField($this->Nama1);
					if ($this->Nama2->Exportable) $Doc->ExportField($this->Nama2);
					if ($this->Nama3->Exportable) $Doc->ExportField($this->Nama3);
					if ($this->Nama4->Exportable) $Doc->ExportField($this->Nama4);
					if ($this->Nama5->Exportable) $Doc->ExportField($this->Nama5);
					if ($this->Nama6->Exportable) $Doc->ExportField($this->Nama6);
					if ($this->ID->Exportable) $Doc->ExportField($this->ID);
					if ($this->site->Exportable) $Doc->ExportField($this->site);
				} else {
					if ($this->Nama1->Exportable) $Doc->ExportField($this->Nama1);
					if ($this->Nama2->Exportable) $Doc->ExportField($this->Nama2);
					if ($this->Nama3->Exportable) $Doc->ExportField($this->Nama3);
					if ($this->Nama4->Exportable) $Doc->ExportField($this->Nama4);
					if ($this->Nama5->Exportable) $Doc->ExportField($this->Nama5);
					if ($this->Nama6->Exportable) $Doc->ExportField($this->Nama6);
					if ($this->ID->Exportable) $Doc->ExportField($this->ID);
					if ($this->site->Exportable) $Doc->ExportField($this->site);
				}
				$Doc->EndExportRow();
			}
			$Recordset->MoveNext();
		}
		$Doc->ExportTableFooter();
	}

	// Add User ID filter
	function AddUserIDFilter($sFilter) {
		global $Security;
		$sFilterWrk = "";
		$id = (CurrentPageID() == "list") ? $this->CurrentAction : CurrentPageID();
		if (!$this->UserIDAllow($id) && !$Security->IsAdmin()) {
			$sFilterWrk = $Security->UserIDList();
			if ($sFilterWrk <> "")
				$sFilterWrk = '`site` IN (' . $sFilterWrk . ')';
		}

		// Call Row Rendered event
		$this->UserID_Filtering($sFilterWrk);
		ew_AddFilter($sFilter, $sFilterWrk);
		return $sFilter;
	}

	// User ID subquery
	function GetUserIDSubquery(&$fld, &$masterfld) {
		global $conn;
		$sWrk = "";
		$sSql = "SELECT " . $masterfld->FldExpression . " FROM `parsys`";
		$sFilter = $this->AddUserIDFilter("");
		if ($sFilter <> "") $sSql .= " WHERE " . $sFilter;

		// Use subquery
		if (EW_USE_SUBQUERY_FOR_MASTER_USER_ID) {
			$sWrk = $sSql;
		} else {

			// List all values
			if ($rs = $conn->Execute($sSql)) {
				while (!$rs->EOF) {
					if ($sWrk <> "") $sWrk .= ",";
					$sWrk .= ew_QuotedValue($rs->fields[0], $masterfld->FldDataType);
					$rs->MoveNext();
				}
				$rs->Close();
			}
		}
		if ($sWrk <> "") {
			$sWrk = $fld->FldExpression . " IN (" . $sWrk . ")";
		}
		return $sWrk;
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
