<?php

// Global variable for table object
$pengunjung = NULL;

//
// Table class for pengunjung
//
class cpengunjung extends cTable {
	var $Id;
	var $Nama;
	var $Alamat;
	var $Jumlah;
	var $Provinsi;
	var $Area;
	var $CP;
	var $NoContact;
	var $Tanggal;
	var $Jam;
	var $Vechicle;
	var $Type;
	var $Site;
	var $Status;
	var $_UserID;
	var $TglInput;
	var $visit;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'pengunjung';
		$this->TableName = 'pengunjung';
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

		// Id
		$this->Id = new cField('pengunjung', 'pengunjung', 'x_Id', 'Id', '`Id`', '`Id`', 3, -1, FALSE, '`Id`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Id->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Id'] = &$this->Id;

		// Nama
		$this->Nama = new cField('pengunjung', 'pengunjung', 'x_Nama', 'Nama', '`Nama`', '`Nama`', 200, -1, FALSE, '`Nama`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Nama'] = &$this->Nama;

		// Alamat
		$this->Alamat = new cField('pengunjung', 'pengunjung', 'x_Alamat', 'Alamat', '`Alamat`', '`Alamat`', 201, -1, FALSE, '`Alamat`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Alamat'] = &$this->Alamat;

		// Jumlah
		$this->Jumlah = new cField('pengunjung', 'pengunjung', 'x_Jumlah', 'Jumlah', '`Jumlah`', '`Jumlah`', 4, -1, FALSE, '`Jumlah`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Jumlah->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['Jumlah'] = &$this->Jumlah;

		// Provinsi
		$this->Provinsi = new cField('pengunjung', 'pengunjung', 'x_Provinsi', 'Provinsi', '`Provinsi`', '`Provinsi`', 3, -1, FALSE, '`Provinsi`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Provinsi->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Provinsi'] = &$this->Provinsi;

		// Area
		$this->Area = new cField('pengunjung', 'pengunjung', 'x_Area', 'Area', '`Area`', '`Area`', 3, -1, FALSE, '`Area`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Area'] = &$this->Area;

		// CP
		$this->CP = new cField('pengunjung', 'pengunjung', 'x_CP', 'CP', '`CP`', '`CP`', 200, -1, FALSE, '`CP`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['CP'] = &$this->CP;

		// NoContact
		$this->NoContact = new cField('pengunjung', 'pengunjung', 'x_NoContact', 'NoContact', '`NoContact`', '`NoContact`', 200, -1, FALSE, '`NoContact`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['NoContact'] = &$this->NoContact;

		// Tanggal
		$this->Tanggal = new cField('pengunjung', 'pengunjung', 'x_Tanggal', 'Tanggal', '`Tanggal`', 'DATE_FORMAT(`Tanggal`, \'%d/%m/%Y\')', 133, 7, FALSE, '`Tanggal`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->Tanggal->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateDMY"));
		$this->fields['Tanggal'] = &$this->Tanggal;

		// Jam
		$this->Jam = new cField('pengunjung', 'pengunjung', 'x_Jam', 'Jam', '`Jam`', '`Jam`', 200, 4, FALSE, '`Jam`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Jam'] = &$this->Jam;

		// Vechicle
		$this->Vechicle = new cField('pengunjung', 'pengunjung', 'x_Vechicle', 'Vechicle', '`Vechicle`', '`Vechicle`', 200, -1, FALSE, '`Vechicle`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Vechicle'] = &$this->Vechicle;

		// Type
		$this->Type = new cField('pengunjung', 'pengunjung', 'x_Type', 'Type', '`Type`', '`Type`', 3, -1, FALSE, '`Type`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Type'] = &$this->Type;

		// Site
		$this->Site = new cField('pengunjung', 'pengunjung', 'x_Site', 'Site', '`Site`', '`Site`', 200, -1, FALSE, '`Site`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Site'] = &$this->Site;

		// Status
		$this->Status = new cField('pengunjung', 'pengunjung', 'x_Status', 'Status', '`Status`', '`Status`', 200, -1, FALSE, '`Status`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['Status'] = &$this->Status;

		// UserID
		$this->_UserID = new cField('pengunjung', 'pengunjung', 'x__UserID', 'UserID', '`UserID`', '`UserID`', 200, -1, FALSE, '`UserID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['UserID'] = &$this->_UserID;

		// TglInput
		$this->TglInput = new cField('pengunjung', 'pengunjung', 'x_TglInput', 'TglInput', '`TglInput`', 'DATE_FORMAT(`TglInput`, \'%d/%m/%Y\')', 135, 7, FALSE, '`TglInput`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->TglInput->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateDMY"));
		$this->fields['TglInput'] = &$this->TglInput;

		// visit
		$this->visit = new cField('pengunjung', 'pengunjung', 'x_visit', 'visit', '`visit`', '`visit`', 200, -1, FALSE, '`visit`', FALSE, FALSE, FALSE, 'FORMATTED TEXT');
		$this->fields['visit'] = &$this->visit;
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
		return "`pengunjung`";
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
		return "`Tanggal` DESC";
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
	var $UpdateTable = "`pengunjung`";

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
			if (array_key_exists('Id', $rs))
				ew_AddFilter($where, ew_QuotedName('Id') . '=' . ew_QuotedValue($rs['Id'], $this->Id->FldDataType));
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
		return "`Id` = @Id@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->Id->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@Id@", ew_AdjustSql($this->Id->CurrentValue), $sKeyFilter); // Replace key value
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
			return "pengunjunglist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "pengunjunglist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			return $this->KeyUrl("pengunjungview.php", $this->UrlParm($parm));
		else
			return $this->KeyUrl("pengunjungview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
	}

	// Add URL
	function GetAddUrl() {
		return "pengunjungadd.php";
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		return $this->KeyUrl("pengunjungedit.php", $this->UrlParm($parm));
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		return $this->KeyUrl("pengunjungadd.php", $this->UrlParm($parm));
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		return $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("pengunjungdelete.php", $this->UrlParm());
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->Id->CurrentValue)) {
			$sUrl .= "Id=" . urlencode($this->Id->CurrentValue);
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
			$arKeys[] = @$_GET["Id"]; // Id

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
			$this->Id->CurrentValue = $key;
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
		$this->Id->setDbValue($rs->fields('Id'));
		$this->Nama->setDbValue($rs->fields('Nama'));
		$this->Alamat->setDbValue($rs->fields('Alamat'));
		$this->Jumlah->setDbValue($rs->fields('Jumlah'));
		$this->Provinsi->setDbValue($rs->fields('Provinsi'));
		$this->Area->setDbValue($rs->fields('Area'));
		$this->CP->setDbValue($rs->fields('CP'));
		$this->NoContact->setDbValue($rs->fields('NoContact'));
		$this->Tanggal->setDbValue($rs->fields('Tanggal'));
		$this->Jam->setDbValue($rs->fields('Jam'));
		$this->Vechicle->setDbValue($rs->fields('Vechicle'));
		$this->Type->setDbValue($rs->fields('Type'));
		$this->Site->setDbValue($rs->fields('Site'));
		$this->Status->setDbValue($rs->fields('Status'));
		$this->_UserID->setDbValue($rs->fields('UserID'));
		$this->TglInput->setDbValue($rs->fields('TglInput'));
		$this->visit->setDbValue($rs->fields('visit'));
	}

	// Render list row values
	function RenderListRow() {
		global $conn, $Security, $gsLanguage;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
		// Id
		// Nama
		// Alamat
		// Jumlah
		// Provinsi
		// Area
		// CP
		// NoContact
		// Tanggal
		// Jam
		// Vechicle
		// Type
		// Site
		// Status
		// UserID
		// TglInput
		// visit
		// Id

		$this->Id->ViewValue = $this->Id->CurrentValue;
		$this->Id->ViewCustomAttributes = "";

		// Nama
		$this->Nama->ViewValue = $this->Nama->CurrentValue;
		$this->Nama->ViewCustomAttributes = "";

		// Alamat
		$this->Alamat->ViewValue = $this->Alamat->CurrentValue;
		$this->Alamat->ViewCustomAttributes = "";

		// Jumlah
		$this->Jumlah->ViewValue = $this->Jumlah->CurrentValue;
		$this->Jumlah->ViewCustomAttributes = "";

		// Provinsi
		if (strval($this->Provinsi->CurrentValue) <> "") {
			$sFilterWrk = "`id`" . ew_SearchString("=", $this->Provinsi->CurrentValue, EW_DATATYPE_NUMBER);
		$sSqlWrk = "SELECT `id`, `NamaProv` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `provinsi`";
		$sWhereWrk = "";
		if ($sFilterWrk <> "") {
			ew_AddFilter($sWhereWrk, $sFilterWrk);
		}

		// Call Lookup selecting
		$this->Lookup_Selecting($this->Provinsi, $sWhereWrk);
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->Provinsi->ViewValue = $rswrk->fields('DispFld');
				$rswrk->Close();
			} else {
				$this->Provinsi->ViewValue = $this->Provinsi->CurrentValue;
			}
		} else {
			$this->Provinsi->ViewValue = NULL;
		}
		$this->Provinsi->ViewCustomAttributes = "";

		// Area
		if (strval($this->Area->CurrentValue) <> "") {
			$sFilterWrk = "`id`" . ew_SearchString("=", $this->Area->CurrentValue, EW_DATATYPE_NUMBER);
		$sSqlWrk = "SELECT `id`, `Kota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `kota`";
		$sWhereWrk = "";
		if ($sFilterWrk <> "") {
			ew_AddFilter($sWhereWrk, $sFilterWrk);
		}

		// Call Lookup selecting
		$this->Lookup_Selecting($this->Area, $sWhereWrk);
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->Area->ViewValue = $rswrk->fields('DispFld');
				$rswrk->Close();
			} else {
				$this->Area->ViewValue = $this->Area->CurrentValue;
			}
		} else {
			$this->Area->ViewValue = NULL;
		}
		$this->Area->ViewCustomAttributes = "";

		// CP
		$this->CP->ViewValue = $this->CP->CurrentValue;
		$this->CP->ViewCustomAttributes = "";

		// NoContact
		$this->NoContact->ViewValue = $this->NoContact->CurrentValue;
		$this->NoContact->ViewCustomAttributes = "";

		// Tanggal
		$this->Tanggal->ViewValue = $this->Tanggal->CurrentValue;
		$this->Tanggal->ViewValue = ew_FormatDateTime($this->Tanggal->ViewValue, 7);
		$this->Tanggal->ViewCustomAttributes = "";

		// Jam
		$this->Jam->ViewValue = $this->Jam->CurrentValue;
		$this->Jam->ViewValue = ew_FormatDateTime($this->Jam->ViewValue, 4);
		$this->Jam->ViewCustomAttributes = "";

		// Vechicle
		if (strval($this->Vechicle->CurrentValue) <> "") {
			switch ($this->Vechicle->CurrentValue) {
				case $this->Vechicle->FldTagValue(1):
					$this->Vechicle->ViewValue = $this->Vechicle->FldTagCaption(1) <> "" ? $this->Vechicle->FldTagCaption(1) : $this->Vechicle->CurrentValue;
					break;
				case $this->Vechicle->FldTagValue(2):
					$this->Vechicle->ViewValue = $this->Vechicle->FldTagCaption(2) <> "" ? $this->Vechicle->FldTagCaption(2) : $this->Vechicle->CurrentValue;
					break;
				default:
					$this->Vechicle->ViewValue = $this->Vechicle->CurrentValue;
			}
		} else {
			$this->Vechicle->ViewValue = NULL;
		}
		$this->Vechicle->ViewCustomAttributes = "";

		// Type
		if (strval($this->Type->CurrentValue) <> "") {
			$sFilterWrk = "`Id`" . ew_SearchString("=", $this->Type->CurrentValue, EW_DATATYPE_NUMBER);
		$sSqlWrk = "SELECT `Id`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `typepengunjung`";
		$sWhereWrk = "";
		if ($sFilterWrk <> "") {
			ew_AddFilter($sWhereWrk, $sFilterWrk);
		}

		// Call Lookup selecting
		$this->Lookup_Selecting($this->Type, $sWhereWrk);
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$this->Type->ViewValue = $rswrk->fields('DispFld');
				$rswrk->Close();
			} else {
				$this->Type->ViewValue = $this->Type->CurrentValue;
			}
		} else {
			$this->Type->ViewValue = NULL;
		}
		$this->Type->ViewCustomAttributes = "";

		// Site
		if (strval($this->Site->CurrentValue) <> "") {
			switch ($this->Site->CurrentValue) {
				case $this->Site->FldTagValue(1):
					$this->Site->ViewValue = $this->Site->FldTagCaption(1) <> "" ? $this->Site->FldTagCaption(1) : $this->Site->CurrentValue;
					break;
				case $this->Site->FldTagValue(2):
					$this->Site->ViewValue = $this->Site->FldTagCaption(2) <> "" ? $this->Site->FldTagCaption(2) : $this->Site->CurrentValue;
					break;
				default:
					$this->Site->ViewValue = $this->Site->CurrentValue;
			}
		} else {
			$this->Site->ViewValue = NULL;
		}
		$this->Site->ViewCustomAttributes = "";

		// Status
		if (strval($this->Status->CurrentValue) <> "") {
			switch ($this->Status->CurrentValue) {
				case $this->Status->FldTagValue(1):
					$this->Status->ViewValue = $this->Status->FldTagCaption(1) <> "" ? $this->Status->FldTagCaption(1) : $this->Status->CurrentValue;
					break;
				case $this->Status->FldTagValue(2):
					$this->Status->ViewValue = $this->Status->FldTagCaption(2) <> "" ? $this->Status->FldTagCaption(2) : $this->Status->CurrentValue;
					break;
				default:
					$this->Status->ViewValue = $this->Status->CurrentValue;
			}
		} else {
			$this->Status->ViewValue = NULL;
		}
		$this->Status->ViewCustomAttributes = "";

		// UserID
		$this->_UserID->ViewValue = $this->_UserID->CurrentValue;
		$this->_UserID->ViewCustomAttributes = "";

		// TglInput
		$this->TglInput->ViewValue = $this->TglInput->CurrentValue;
		$this->TglInput->ViewValue = ew_FormatDateTime($this->TglInput->ViewValue, 7);
		$this->TglInput->ViewCustomAttributes = "";

		// visit
		if (strval($this->visit->CurrentValue) <> "") {
			switch ($this->visit->CurrentValue) {
				case $this->visit->FldTagValue(1):
					$this->visit->ViewValue = $this->visit->FldTagCaption(1) <> "" ? $this->visit->FldTagCaption(1) : $this->visit->CurrentValue;
					break;
				case $this->visit->FldTagValue(2):
					$this->visit->ViewValue = $this->visit->FldTagCaption(2) <> "" ? $this->visit->FldTagCaption(2) : $this->visit->CurrentValue;
					break;
				default:
					$this->visit->ViewValue = $this->visit->CurrentValue;
			}
		} else {
			$this->visit->ViewValue = NULL;
		}
		$this->visit->ViewCustomAttributes = "";

		// Id
		$this->Id->LinkCustomAttributes = "";
		$this->Id->HrefValue = "";
		$this->Id->TooltipValue = "";

		// Nama
		$this->Nama->LinkCustomAttributes = "";
		$this->Nama->HrefValue = "";
		$this->Nama->TooltipValue = "";

		// Alamat
		$this->Alamat->LinkCustomAttributes = "";
		$this->Alamat->HrefValue = "";
		$this->Alamat->TooltipValue = "";

		// Jumlah
		$this->Jumlah->LinkCustomAttributes = "";
		$this->Jumlah->HrefValue = "";
		$this->Jumlah->TooltipValue = "";

		// Provinsi
		$this->Provinsi->LinkCustomAttributes = "";
		$this->Provinsi->HrefValue = "";
		$this->Provinsi->TooltipValue = "";

		// Area
		$this->Area->LinkCustomAttributes = "";
		$this->Area->HrefValue = "";
		$this->Area->TooltipValue = "";

		// CP
		$this->CP->LinkCustomAttributes = "";
		$this->CP->HrefValue = "";
		$this->CP->TooltipValue = "";

		// NoContact
		$this->NoContact->LinkCustomAttributes = "";
		$this->NoContact->HrefValue = "";
		$this->NoContact->TooltipValue = "";

		// Tanggal
		$this->Tanggal->LinkCustomAttributes = "";
		$this->Tanggal->HrefValue = "";
		$this->Tanggal->TooltipValue = "";

		// Jam
		$this->Jam->LinkCustomAttributes = "";
		$this->Jam->HrefValue = "";
		$this->Jam->TooltipValue = "";

		// Vechicle
		$this->Vechicle->LinkCustomAttributes = "";
		$this->Vechicle->HrefValue = "";
		$this->Vechicle->TooltipValue = "";

		// Type
		$this->Type->LinkCustomAttributes = "";
		$this->Type->HrefValue = "";
		$this->Type->TooltipValue = "";

		// Site
		$this->Site->LinkCustomAttributes = "";
		$this->Site->HrefValue = "";
		$this->Site->TooltipValue = "";

		// Status
		$this->Status->LinkCustomAttributes = "";
		$this->Status->HrefValue = "";
		$this->Status->TooltipValue = "";

		// UserID
		$this->_UserID->LinkCustomAttributes = "";
		$this->_UserID->HrefValue = "";
		$this->_UserID->TooltipValue = "";

		// TglInput
		$this->TglInput->LinkCustomAttributes = "";
		$this->TglInput->HrefValue = "";
		$this->TglInput->TooltipValue = "";

		// visit
		$this->visit->LinkCustomAttributes = "";
		$this->visit->HrefValue = "";
		$this->visit->TooltipValue = "";

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
				if ($this->Id->Exportable) $Doc->ExportCaption($this->Id);
				if ($this->Nama->Exportable) $Doc->ExportCaption($this->Nama);
				if ($this->Alamat->Exportable) $Doc->ExportCaption($this->Alamat);
				if ($this->Jumlah->Exportable) $Doc->ExportCaption($this->Jumlah);
				if ($this->Provinsi->Exportable) $Doc->ExportCaption($this->Provinsi);
				if ($this->Area->Exportable) $Doc->ExportCaption($this->Area);
				if ($this->CP->Exportable) $Doc->ExportCaption($this->CP);
				if ($this->NoContact->Exportable) $Doc->ExportCaption($this->NoContact);
				if ($this->Tanggal->Exportable) $Doc->ExportCaption($this->Tanggal);
				if ($this->Jam->Exportable) $Doc->ExportCaption($this->Jam);
				if ($this->Vechicle->Exportable) $Doc->ExportCaption($this->Vechicle);
				if ($this->Type->Exportable) $Doc->ExportCaption($this->Type);
				if ($this->Site->Exportable) $Doc->ExportCaption($this->Site);
				if ($this->Status->Exportable) $Doc->ExportCaption($this->Status);
				if ($this->_UserID->Exportable) $Doc->ExportCaption($this->_UserID);
				if ($this->TglInput->Exportable) $Doc->ExportCaption($this->TglInput);
				if ($this->visit->Exportable) $Doc->ExportCaption($this->visit);
			} else {
				if ($this->Id->Exportable) $Doc->ExportCaption($this->Id);
				if ($this->Nama->Exportable) $Doc->ExportCaption($this->Nama);
				if ($this->Jumlah->Exportable) $Doc->ExportCaption($this->Jumlah);
				if ($this->Provinsi->Exportable) $Doc->ExportCaption($this->Provinsi);
				if ($this->Area->Exportable) $Doc->ExportCaption($this->Area);
				if ($this->CP->Exportable) $Doc->ExportCaption($this->CP);
				if ($this->NoContact->Exportable) $Doc->ExportCaption($this->NoContact);
				if ($this->Tanggal->Exportable) $Doc->ExportCaption($this->Tanggal);
				if ($this->Jam->Exportable) $Doc->ExportCaption($this->Jam);
				if ($this->Vechicle->Exportable) $Doc->ExportCaption($this->Vechicle);
				if ($this->Type->Exportable) $Doc->ExportCaption($this->Type);
				if ($this->Site->Exportable) $Doc->ExportCaption($this->Site);
				if ($this->Status->Exportable) $Doc->ExportCaption($this->Status);
				if ($this->_UserID->Exportable) $Doc->ExportCaption($this->_UserID);
				if ($this->TglInput->Exportable) $Doc->ExportCaption($this->TglInput);
				if ($this->visit->Exportable) $Doc->ExportCaption($this->visit);
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
					if ($this->Id->Exportable) $Doc->ExportField($this->Id);
					if ($this->Nama->Exportable) $Doc->ExportField($this->Nama);
					if ($this->Alamat->Exportable) $Doc->ExportField($this->Alamat);
					if ($this->Jumlah->Exportable) $Doc->ExportField($this->Jumlah);
					if ($this->Provinsi->Exportable) $Doc->ExportField($this->Provinsi);
					if ($this->Area->Exportable) $Doc->ExportField($this->Area);
					if ($this->CP->Exportable) $Doc->ExportField($this->CP);
					if ($this->NoContact->Exportable) $Doc->ExportField($this->NoContact);
					if ($this->Tanggal->Exportable) $Doc->ExportField($this->Tanggal);
					if ($this->Jam->Exportable) $Doc->ExportField($this->Jam);
					if ($this->Vechicle->Exportable) $Doc->ExportField($this->Vechicle);
					if ($this->Type->Exportable) $Doc->ExportField($this->Type);
					if ($this->Site->Exportable) $Doc->ExportField($this->Site);
					if ($this->Status->Exportable) $Doc->ExportField($this->Status);
					if ($this->_UserID->Exportable) $Doc->ExportField($this->_UserID);
					if ($this->TglInput->Exportable) $Doc->ExportField($this->TglInput);
					if ($this->visit->Exportable) $Doc->ExportField($this->visit);
				} else {
					if ($this->Id->Exportable) $Doc->ExportField($this->Id);
					if ($this->Nama->Exportable) $Doc->ExportField($this->Nama);
					if ($this->Jumlah->Exportable) $Doc->ExportField($this->Jumlah);
					if ($this->Provinsi->Exportable) $Doc->ExportField($this->Provinsi);
					if ($this->Area->Exportable) $Doc->ExportField($this->Area);
					if ($this->CP->Exportable) $Doc->ExportField($this->CP);
					if ($this->NoContact->Exportable) $Doc->ExportField($this->NoContact);
					if ($this->Tanggal->Exportable) $Doc->ExportField($this->Tanggal);
					if ($this->Jam->Exportable) $Doc->ExportField($this->Jam);
					if ($this->Vechicle->Exportable) $Doc->ExportField($this->Vechicle);
					if ($this->Type->Exportable) $Doc->ExportField($this->Type);
					if ($this->Site->Exportable) $Doc->ExportField($this->Site);
					if ($this->Status->Exportable) $Doc->ExportField($this->Status);
					if ($this->_UserID->Exportable) $Doc->ExportField($this->_UserID);
					if ($this->TglInput->Exportable) $Doc->ExportField($this->TglInput);
					if ($this->visit->Exportable) $Doc->ExportField($this->visit);
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
				$sFilterWrk = '`Site` IN (' . $sFilterWrk . ')';
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
		$sSql = "SELECT " . $masterfld->FldExpression . " FROM `pengunjung`";
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
