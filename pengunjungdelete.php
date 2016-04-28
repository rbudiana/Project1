<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "pengunjunginfo.php" ?>
<?php include_once "userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$pengunjung_delete = NULL; // Initialize page object first

class cpengunjung_delete extends cpengunjung {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{C2A46AD1-29DD-4049-B347-17989E75216E}";

	// Table name
	var $TableName = 'pengunjung';

	// Page object name
	var $PageObjName = 'pengunjung_delete';

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
	var $AuditTrailOnDelete = TRUE;

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
		global $conn, $Language;
		$GLOBALS["Page"] = &$this;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (pengunjung)
		if (!isset($GLOBALS["pengunjung"]) || get_class($GLOBALS["pengunjung"]) == "cpengunjung") {
			$GLOBALS["pengunjung"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["pengunjung"];
		}

		// Table object (user)
		if (!isset($GLOBALS['user'])) $GLOBALS['user'] = new cuser();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'pengunjung', TRUE);

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
		$Security->TablePermission_Loading();
		$Security->LoadCurrentUserLevel($this->ProjectID . $this->TableName);
		$Security->TablePermission_Loaded();
		if (!$Security->IsLoggedIn()) {
			$Security->SaveLastUrl();
			$this->Page_Terminate("login.php");
		}
		if (!$Security->CanDelete()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("pengunjunglist.php");
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();
		if ($Security->IsLoggedIn() && strval($Security->CurrentUserID()) == "") {
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("pengunjunglist.php");
		}
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->Id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
			$this->Page_Terminate("pengunjunglist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in pengunjung class, pengunjunginfo.php

		$this->CurrentFilter = $sFilter;

		// Check if valid user id
		$sql = $this->GetSQL($this->CurrentFilter, "");
		if ($this->Recordset = ew_LoadRecordset($sql)) {
			$res = TRUE;
			while (!$this->Recordset->EOF) {
				$this->LoadRowValues($this->Recordset);
				if (!$this->ShowOptionLink('delete')) {
					$sUserIdMsg = $Language->Phrase("NoDeletePermission");
					$this->setFailureMessage($sUserIdMsg);
					$res = FALSE;
					break;
				}
				$this->Recordset->MoveNext();
			}
			$this->Recordset->Close();
			if (!$res) $this->Page_Terminate("pengunjunglist.php"); // Return to list
		}

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

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->Id->DbValue = $row['Id'];
		$this->Nama->DbValue = $row['Nama'];
		$this->Alamat->DbValue = $row['Alamat'];
		$this->Jumlah->DbValue = $row['Jumlah'];
		$this->Provinsi->DbValue = $row['Provinsi'];
		$this->Area->DbValue = $row['Area'];
		$this->CP->DbValue = $row['CP'];
		$this->NoContact->DbValue = $row['NoContact'];
		$this->Tanggal->DbValue = $row['Tanggal'];
		$this->Jam->DbValue = $row['Jam'];
		$this->Vechicle->DbValue = $row['Vechicle'];
		$this->Type->DbValue = $row['Type'];
		$this->Site->DbValue = $row['Site'];
		$this->Status->DbValue = $row['Status'];
		$this->_UserID->DbValue = $row['UserID'];
		$this->TglInput->DbValue = $row['TglInput'];
		$this->visit->DbValue = $row['visit'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Convert decimal values if posted back

		if ($this->Jumlah->FormValue == $this->Jumlah->CurrentValue && is_numeric(ew_StrToFloat($this->Jumlah->CurrentValue)))
			$this->Jumlah->CurrentValue = ew_StrToFloat($this->Jumlah->CurrentValue);

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
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

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// Id
			$this->Id->ViewValue = $this->Id->CurrentValue;
			$this->Id->ViewCustomAttributes = "";

			// Nama
			$this->Nama->ViewValue = $this->Nama->CurrentValue;
			$this->Nama->ViewCustomAttributes = "";

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

			// Jumlah
			$this->Jumlah->LinkCustomAttributes = "";
			$this->Jumlah->HrefValue = "";
			$this->Jumlah->TooltipValue = "";

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

			// visit
			$this->visit->LinkCustomAttributes = "";
			$this->visit->HrefValue = "";
			$this->visit->TooltipValue = "";
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
		if (!$Security->CanDelete()) {
			$this->setFailureMessage($Language->Phrase("NoDeletePermission")); // No delete permission
			return FALSE;
		}
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
		if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteBegin")); // Batch delete begin

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
				$sThisKey .= $row['Id'];
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
			if ($DeleteRows) {
				foreach ($rsold as $row)
					$this->WriteAuditTrailOnDelete($row);
			}
			if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteSuccess")); // Batch delete success
		} else {
			$conn->RollbackTrans(); // Rollback changes
			if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteRollback")); // Batch delete rollback
		}

		// Call Row Deleted event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$this->Row_Deleted($row);
			}
		}
		return $DeleteRows;
	}

	// Show link optionally based on User ID
	function ShowOptionLink($id = "") {
		global $Security;
		if ($Security->IsLoggedIn() && !$Security->IsAdmin() && !$this->UserIDAllow($id))
			return $Security->IsValidUserID($this->Site->CurrentValue);
		return TRUE;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "pengunjunglist.php", $this->TableVar, TRUE);
		$PageId = "delete";
		$Breadcrumb->Add("delete", $PageId, ew_CurrentUrl());
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'pengunjung';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (delete page)
	function WriteAuditTrailOnDelete(&$rs) {
		if (!$this->AuditTrailOnDelete) return;
		$table = 'pengunjung';

		// Get key value
		$key = "";
		if ($key <> "")
			$key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['Id'];

		// Write Audit Trail
		$dt = ew_StdCurrentDateTime();
		$id = ew_ScriptName();
	  $curUser = CurrentUserID();
		foreach (array_keys($rs) as $fldname) {
			if (array_key_exists($fldname, $this->fields) && $this->fields[$fldname]->FldDataType <> EW_DATATYPE_BLOB) { // Ignore BLOB fields
				if ($this->fields[$fldname]->FldDataType == EW_DATATYPE_MEMO) {
					if (EW_AUDIT_TRAIL_TO_DATABASE)
						$oldvalue = $rs[$fldname];
					else
						$oldvalue = "[MEMO]"; // Memo field
				} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_XML) {
					$oldvalue = "[XML]"; // XML field
				} else {
					$oldvalue = $rs[$fldname];
				}
				ew_WriteAuditTrail("log", $dt, $id, $curUser, "D", $table, $fldname, $key, $oldvalue, "");
			}
		}
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
<?php ew_Header(TRUE) ?>
<?php

// Create page object
if (!isset($pengunjung_delete)) $pengunjung_delete = new cpengunjung_delete();

// Page init
$pengunjung_delete->Page_Init();

// Page main
$pengunjung_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$pengunjung_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var pengunjung_delete = new ew_Page("pengunjung_delete");
pengunjung_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = pengunjung_delete.PageID; // For backward compatibility

// Form object
var fpengunjungdelete = new ew_Form("fpengunjungdelete");

// Form_CustomValidate event
fpengunjungdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fpengunjungdelete.ValidateRequired = true;
<?php } else { ?>
fpengunjungdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fpengunjungdelete.Lists["x_Area"] = {"LinkField":"x_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Kota","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fpengunjungdelete.Lists["x_Type"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($pengunjung_delete->Recordset = $pengunjung_delete->LoadRecordset())
	$pengunjung_deleteTotalRecs = $pengunjung_delete->Recordset->RecordCount(); // Get record count
if ($pengunjung_deleteTotalRecs <= 0) { // No record found, exit
	if ($pengunjung_delete->Recordset)
		$pengunjung_delete->Recordset->Close();
	$pengunjung_delete->Page_Terminate("pengunjunglist.php"); // Return to list
}
?>
<?php $Breadcrumb->Render(); ?>
<?php $pengunjung_delete->ShowPageHeader(); ?>
<?php
$pengunjung_delete->ShowMessage();
?>
<form name="fpengunjungdelete" id="fpengunjungdelete" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="pengunjung">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($pengunjung_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_pengunjungdelete" class="ewTable ewTableSeparate">
<?php echo $pengunjung->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($pengunjung->Id->Visible) { // Id ?>
		<td><span id="elh_pengunjung_Id" class="pengunjung_Id"><?php echo $pengunjung->Id->FldCaption() ?></span></td>
<?php } ?>
<?php if ($pengunjung->Nama->Visible) { // Nama ?>
		<td><span id="elh_pengunjung_Nama" class="pengunjung_Nama"><?php echo $pengunjung->Nama->FldCaption() ?></span></td>
<?php } ?>
<?php if ($pengunjung->Jumlah->Visible) { // Jumlah ?>
		<td><span id="elh_pengunjung_Jumlah" class="pengunjung_Jumlah"><?php echo $pengunjung->Jumlah->FldCaption() ?></span></td>
<?php } ?>
<?php if ($pengunjung->Area->Visible) { // Area ?>
		<td><span id="elh_pengunjung_Area" class="pengunjung_Area"><?php echo $pengunjung->Area->FldCaption() ?></span></td>
<?php } ?>
<?php if ($pengunjung->CP->Visible) { // CP ?>
		<td><span id="elh_pengunjung_CP" class="pengunjung_CP"><?php echo $pengunjung->CP->FldCaption() ?></span></td>
<?php } ?>
<?php if ($pengunjung->NoContact->Visible) { // NoContact ?>
		<td><span id="elh_pengunjung_NoContact" class="pengunjung_NoContact"><?php echo $pengunjung->NoContact->FldCaption() ?></span></td>
<?php } ?>
<?php if ($pengunjung->Tanggal->Visible) { // Tanggal ?>
		<td><span id="elh_pengunjung_Tanggal" class="pengunjung_Tanggal"><?php echo $pengunjung->Tanggal->FldCaption() ?></span></td>
<?php } ?>
<?php if ($pengunjung->Jam->Visible) { // Jam ?>
		<td><span id="elh_pengunjung_Jam" class="pengunjung_Jam"><?php echo $pengunjung->Jam->FldCaption() ?></span></td>
<?php } ?>
<?php if ($pengunjung->Type->Visible) { // Type ?>
		<td><span id="elh_pengunjung_Type" class="pengunjung_Type"><?php echo $pengunjung->Type->FldCaption() ?></span></td>
<?php } ?>
<?php if ($pengunjung->Site->Visible) { // Site ?>
		<td><span id="elh_pengunjung_Site" class="pengunjung_Site"><?php echo $pengunjung->Site->FldCaption() ?></span></td>
<?php } ?>
<?php if ($pengunjung->Status->Visible) { // Status ?>
		<td><span id="elh_pengunjung_Status" class="pengunjung_Status"><?php echo $pengunjung->Status->FldCaption() ?></span></td>
<?php } ?>
<?php if ($pengunjung->visit->Visible) { // visit ?>
		<td><span id="elh_pengunjung_visit" class="pengunjung_visit"><?php echo $pengunjung->visit->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$pengunjung_delete->RecCnt = 0;
$i = 0;
while (!$pengunjung_delete->Recordset->EOF) {
	$pengunjung_delete->RecCnt++;
	$pengunjung_delete->RowCnt++;

	// Set row properties
	$pengunjung->ResetAttrs();
	$pengunjung->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$pengunjung_delete->LoadRowValues($pengunjung_delete->Recordset);

	// Render row
	$pengunjung_delete->RenderRow();
?>
	<tr<?php echo $pengunjung->RowAttributes() ?>>
<?php if ($pengunjung->Id->Visible) { // Id ?>
		<td<?php echo $pengunjung->Id->CellAttributes() ?>>
<span id="el<?php echo $pengunjung_delete->RowCnt ?>_pengunjung_Id" class="control-group pengunjung_Id">
<span<?php echo $pengunjung->Id->ViewAttributes() ?>>
<?php echo $pengunjung->Id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($pengunjung->Nama->Visible) { // Nama ?>
		<td<?php echo $pengunjung->Nama->CellAttributes() ?>>
<span id="el<?php echo $pengunjung_delete->RowCnt ?>_pengunjung_Nama" class="control-group pengunjung_Nama">
<span<?php echo $pengunjung->Nama->ViewAttributes() ?>>
<?php echo $pengunjung->Nama->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($pengunjung->Jumlah->Visible) { // Jumlah ?>
		<td<?php echo $pengunjung->Jumlah->CellAttributes() ?>>
<span id="el<?php echo $pengunjung_delete->RowCnt ?>_pengunjung_Jumlah" class="control-group pengunjung_Jumlah">
<span<?php echo $pengunjung->Jumlah->ViewAttributes() ?>>
<?php echo $pengunjung->Jumlah->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($pengunjung->Area->Visible) { // Area ?>
		<td<?php echo $pengunjung->Area->CellAttributes() ?>>
<span id="el<?php echo $pengunjung_delete->RowCnt ?>_pengunjung_Area" class="control-group pengunjung_Area">
<span<?php echo $pengunjung->Area->ViewAttributes() ?>>
<?php echo $pengunjung->Area->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($pengunjung->CP->Visible) { // CP ?>
		<td<?php echo $pengunjung->CP->CellAttributes() ?>>
<span id="el<?php echo $pengunjung_delete->RowCnt ?>_pengunjung_CP" class="control-group pengunjung_CP">
<span<?php echo $pengunjung->CP->ViewAttributes() ?>>
<?php echo $pengunjung->CP->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($pengunjung->NoContact->Visible) { // NoContact ?>
		<td<?php echo $pengunjung->NoContact->CellAttributes() ?>>
<span id="el<?php echo $pengunjung_delete->RowCnt ?>_pengunjung_NoContact" class="control-group pengunjung_NoContact">
<span<?php echo $pengunjung->NoContact->ViewAttributes() ?>>
<?php echo $pengunjung->NoContact->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($pengunjung->Tanggal->Visible) { // Tanggal ?>
		<td<?php echo $pengunjung->Tanggal->CellAttributes() ?>>
<span id="el<?php echo $pengunjung_delete->RowCnt ?>_pengunjung_Tanggal" class="control-group pengunjung_Tanggal">
<span<?php echo $pengunjung->Tanggal->ViewAttributes() ?>>
<?php echo $pengunjung->Tanggal->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($pengunjung->Jam->Visible) { // Jam ?>
		<td<?php echo $pengunjung->Jam->CellAttributes() ?>>
<span id="el<?php echo $pengunjung_delete->RowCnt ?>_pengunjung_Jam" class="control-group pengunjung_Jam">
<span<?php echo $pengunjung->Jam->ViewAttributes() ?>>
<?php echo $pengunjung->Jam->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($pengunjung->Type->Visible) { // Type ?>
		<td<?php echo $pengunjung->Type->CellAttributes() ?>>
<span id="el<?php echo $pengunjung_delete->RowCnt ?>_pengunjung_Type" class="control-group pengunjung_Type">
<span<?php echo $pengunjung->Type->ViewAttributes() ?>>
<?php echo $pengunjung->Type->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($pengunjung->Site->Visible) { // Site ?>
		<td<?php echo $pengunjung->Site->CellAttributes() ?>>
<span id="el<?php echo $pengunjung_delete->RowCnt ?>_pengunjung_Site" class="control-group pengunjung_Site">
<span<?php echo $pengunjung->Site->ViewAttributes() ?>>
<?php echo $pengunjung->Site->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($pengunjung->Status->Visible) { // Status ?>
		<td<?php echo $pengunjung->Status->CellAttributes() ?>>
<span id="el<?php echo $pengunjung_delete->RowCnt ?>_pengunjung_Status" class="control-group pengunjung_Status">
<span<?php echo $pengunjung->Status->ViewAttributes() ?>>
<?php echo $pengunjung->Status->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($pengunjung->visit->Visible) { // visit ?>
		<td<?php echo $pengunjung->visit->CellAttributes() ?>>
<span id="el<?php echo $pengunjung_delete->RowCnt ?>_pengunjung_visit" class="control-group pengunjung_visit">
<span<?php echo $pengunjung->visit->ViewAttributes() ?>>
<?php echo $pengunjung->visit->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$pengunjung_delete->Recordset->MoveNext();
}
$pengunjung_delete->Recordset->Close();
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
fpengunjungdelete.Init();
</script>
<?php
$pengunjung_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$pengunjung_delete->Page_Terminate();
?>
