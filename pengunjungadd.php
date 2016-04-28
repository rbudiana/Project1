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

$pengunjung_add = NULL; // Initialize page object first

class cpengunjung_add extends cpengunjung {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{C2A46AD1-29DD-4049-B347-17989E75216E}";

	// Table name
	var $TableName = 'pengunjung';

	// Page object name
	var $PageObjName = 'pengunjung_add';

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
	var $AuditTrailOnAdd = TRUE;

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
			define("EW_PAGE_ID", 'add', TRUE);

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
		if (!$Security->CanAdd()) {
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

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

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
			if (@$_GET["Id"] != "") {
				$this->Id->setQueryStringValue($_GET["Id"]);
				$this->setKey("Id", $this->Id->CurrentValue); // Set up key
			} else {
				$this->setKey("Id", ""); // Clear key
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
					$this->Page_Terminate("pengunjunglist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "pengunjungview.php")
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
	}

	// Load default values
	function LoadDefaultValues() {
		$this->Nama->CurrentValue = NULL;
		$this->Nama->OldValue = $this->Nama->CurrentValue;
		$this->Alamat->CurrentValue = NULL;
		$this->Alamat->OldValue = $this->Alamat->CurrentValue;
		$this->Jumlah->CurrentValue = NULL;
		$this->Jumlah->OldValue = $this->Jumlah->CurrentValue;
		$this->Provinsi->CurrentValue = NULL;
		$this->Provinsi->OldValue = $this->Provinsi->CurrentValue;
		$this->Area->CurrentValue = NULL;
		$this->Area->OldValue = $this->Area->CurrentValue;
		$this->CP->CurrentValue = NULL;
		$this->CP->OldValue = $this->CP->CurrentValue;
		$this->NoContact->CurrentValue = NULL;
		$this->NoContact->OldValue = $this->NoContact->CurrentValue;
		$this->Tanggal->CurrentValue = NULL;
		$this->Tanggal->OldValue = $this->Tanggal->CurrentValue;
		$this->Jam->CurrentValue = NULL;
		$this->Jam->OldValue = $this->Jam->CurrentValue;
		$this->Vechicle->CurrentValue = NULL;
		$this->Vechicle->OldValue = $this->Vechicle->CurrentValue;
		$this->Type->CurrentValue = NULL;
		$this->Type->OldValue = $this->Type->CurrentValue;
		$this->Site->CurrentValue = CurrentUserID();
		$this->_UserID->CurrentValue = NULL;
		$this->_UserID->OldValue = $this->_UserID->CurrentValue;
		$this->TglInput->CurrentValue = NULL;
		$this->TglInput->OldValue = $this->TglInput->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->Nama->FldIsDetailKey) {
			$this->Nama->setFormValue($objForm->GetValue("x_Nama"));
		}
		if (!$this->Alamat->FldIsDetailKey) {
			$this->Alamat->setFormValue($objForm->GetValue("x_Alamat"));
		}
		if (!$this->Jumlah->FldIsDetailKey) {
			$this->Jumlah->setFormValue($objForm->GetValue("x_Jumlah"));
		}
		if (!$this->Provinsi->FldIsDetailKey) {
			$this->Provinsi->setFormValue($objForm->GetValue("x_Provinsi"));
		}
		if (!$this->Area->FldIsDetailKey) {
			$this->Area->setFormValue($objForm->GetValue("x_Area"));
		}
		if (!$this->CP->FldIsDetailKey) {
			$this->CP->setFormValue($objForm->GetValue("x_CP"));
		}
		if (!$this->NoContact->FldIsDetailKey) {
			$this->NoContact->setFormValue($objForm->GetValue("x_NoContact"));
		}
		if (!$this->Tanggal->FldIsDetailKey) {
			$this->Tanggal->setFormValue($objForm->GetValue("x_Tanggal"));
			$this->Tanggal->CurrentValue = ew_UnFormatDateTime($this->Tanggal->CurrentValue, 7);
		}
		if (!$this->Jam->FldIsDetailKey) {
			$this->Jam->setFormValue($objForm->GetValue("x_Jam"));
		}
		if (!$this->Vechicle->FldIsDetailKey) {
			$this->Vechicle->setFormValue($objForm->GetValue("x_Vechicle"));
		}
		if (!$this->Type->FldIsDetailKey) {
			$this->Type->setFormValue($objForm->GetValue("x_Type"));
		}
		if (!$this->Site->FldIsDetailKey) {
			$this->Site->setFormValue($objForm->GetValue("x_Site"));
		}
		if (!$this->_UserID->FldIsDetailKey) {
			$this->_UserID->setFormValue($objForm->GetValue("x__UserID"));
		}
		if (!$this->TglInput->FldIsDetailKey) {
			$this->TglInput->setFormValue($objForm->GetValue("x_TglInput"));
			$this->TglInput->CurrentValue = ew_UnFormatDateTime($this->TglInput->CurrentValue, 7);
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->Nama->CurrentValue = $this->Nama->FormValue;
		$this->Alamat->CurrentValue = $this->Alamat->FormValue;
		$this->Jumlah->CurrentValue = $this->Jumlah->FormValue;
		$this->Provinsi->CurrentValue = $this->Provinsi->FormValue;
		$this->Area->CurrentValue = $this->Area->FormValue;
		$this->CP->CurrentValue = $this->CP->FormValue;
		$this->NoContact->CurrentValue = $this->NoContact->FormValue;
		$this->Tanggal->CurrentValue = $this->Tanggal->FormValue;
		$this->Tanggal->CurrentValue = ew_UnFormatDateTime($this->Tanggal->CurrentValue, 7);
		$this->Jam->CurrentValue = $this->Jam->FormValue;
		$this->Vechicle->CurrentValue = $this->Vechicle->FormValue;
		$this->Type->CurrentValue = $this->Type->FormValue;
		$this->Site->CurrentValue = $this->Site->FormValue;
		$this->_UserID->CurrentValue = $this->_UserID->FormValue;
		$this->TglInput->CurrentValue = $this->TglInput->FormValue;
		$this->TglInput->CurrentValue = ew_UnFormatDateTime($this->TglInput->CurrentValue, 7);
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

		// Check if valid user id
		if ($res) {
			$res = $this->ShowOptionLink('add');
			if (!$res) {
				$sUserIdMsg = $Language->Phrase("NoPermission");
				$this->setFailureMessage($sUserIdMsg);
			}
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

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("Id")) <> "")
			$this->Id->CurrentValue = $this->getKey("Id"); // Id
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

			// UserID
			$this->_UserID->LinkCustomAttributes = "";
			$this->_UserID->HrefValue = "";
			$this->_UserID->TooltipValue = "";

			// TglInput
			$this->TglInput->LinkCustomAttributes = "";
			$this->TglInput->HrefValue = "";
			$this->TglInput->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// Nama
			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->CurrentValue);
			$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

			// Alamat
			$this->Alamat->EditCustomAttributes = "";
			$this->Alamat->EditValue = $this->Alamat->CurrentValue;
			$this->Alamat->PlaceHolder = ew_RemoveHtml($this->Alamat->FldCaption());

			// Jumlah
			$this->Jumlah->EditCustomAttributes = "";
			$this->Jumlah->EditValue = ew_HtmlEncode($this->Jumlah->CurrentValue);
			$this->Jumlah->PlaceHolder = ew_RemoveHtml($this->Jumlah->FldCaption());
			if (strval($this->Jumlah->EditValue) <> "" && is_numeric($this->Jumlah->EditValue)) $this->Jumlah->EditValue = ew_FormatNumber($this->Jumlah->EditValue, -2, -1, -2, 0);

			// Provinsi
			$this->Provinsi->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `id`, `NamaProv` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `provinsi`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Provinsi, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->Provinsi->EditValue = $arwrk;

			// Area
			$this->Area->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `id`, `Kota` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, `Prov` AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `kota`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Area, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->Area->EditValue = $arwrk;

			// CP
			$this->CP->EditCustomAttributes = "";
			$this->CP->EditValue = ew_HtmlEncode($this->CP->CurrentValue);
			$this->CP->PlaceHolder = ew_RemoveHtml($this->CP->FldCaption());

			// NoContact
			$this->NoContact->EditCustomAttributes = "";
			$this->NoContact->EditValue = ew_HtmlEncode($this->NoContact->CurrentValue);
			$this->NoContact->PlaceHolder = ew_RemoveHtml($this->NoContact->FldCaption());

			// Tanggal
			$this->Tanggal->EditCustomAttributes = "";
			$this->Tanggal->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->Tanggal->CurrentValue, 7));
			$this->Tanggal->PlaceHolder = ew_RemoveHtml($this->Tanggal->FldCaption());

			// Jam
			$this->Jam->EditCustomAttributes = "";
			$this->Jam->EditValue = ew_HtmlEncode($this->Jam->CurrentValue);
			$this->Jam->PlaceHolder = ew_RemoveHtml($this->Jam->FldCaption());

			// Vechicle
			$this->Vechicle->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->Vechicle->FldTagValue(1), $this->Vechicle->FldTagCaption(1) <> "" ? $this->Vechicle->FldTagCaption(1) : $this->Vechicle->FldTagValue(1));
			$arwrk[] = array($this->Vechicle->FldTagValue(2), $this->Vechicle->FldTagCaption(2) <> "" ? $this->Vechicle->FldTagCaption(2) : $this->Vechicle->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->Vechicle->EditValue = $arwrk;

			// Type
			$this->Type->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `Id`, `Description` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `typepengunjung`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Type, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->Type->EditValue = $arwrk;

			// Site
			$this->Site->EditCustomAttributes = "";
			if (!$Security->IsAdmin() && $Security->IsLoggedIn() && !$this->UserIDAllow("add")) { // Non system admin
			$sFilterWrk = "";
			$sFilterWrk = $GLOBALS["user"]->AddUserIDFilter("");
			$sSqlWrk = "SELECT `site`, `site` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `user`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->Site, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->Site->EditValue = $arwrk;
			} else {
			$arwrk = array();
			$arwrk[] = array($this->Site->FldTagValue(1), $this->Site->FldTagCaption(1) <> "" ? $this->Site->FldTagCaption(1) : $this->Site->FldTagValue(1));
			$arwrk[] = array($this->Site->FldTagValue(2), $this->Site->FldTagCaption(2) <> "" ? $this->Site->FldTagCaption(2) : $this->Site->FldTagValue(2));
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect")));
			$this->Site->EditValue = $arwrk;
			}

			// UserID
			// TglInput
			// Edit refer script
			// Nama

			$this->Nama->HrefValue = "";

			// Alamat
			$this->Alamat->HrefValue = "";

			// Jumlah
			$this->Jumlah->HrefValue = "";

			// Provinsi
			$this->Provinsi->HrefValue = "";

			// Area
			$this->Area->HrefValue = "";

			// CP
			$this->CP->HrefValue = "";

			// NoContact
			$this->NoContact->HrefValue = "";

			// Tanggal
			$this->Tanggal->HrefValue = "";

			// Jam
			$this->Jam->HrefValue = "";

			// Vechicle
			$this->Vechicle->HrefValue = "";

			// Type
			$this->Type->HrefValue = "";

			// Site
			$this->Site->HrefValue = "";

			// UserID
			$this->_UserID->HrefValue = "";

			// TglInput
			$this->TglInput->HrefValue = "";
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
		if (!$this->Jumlah->FldIsDetailKey && !is_null($this->Jumlah->FormValue) && $this->Jumlah->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->Jumlah->FldCaption());
		}
		if (!ew_CheckNumber($this->Jumlah->FormValue)) {
			ew_AddMessage($gsFormError, $this->Jumlah->FldErrMsg());
		}
		if (!ew_CheckEuroDate($this->Tanggal->FormValue)) {
			ew_AddMessage($gsFormError, $this->Tanggal->FldErrMsg());
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

		// Check if valid User ID
		$bValidUser = FALSE;
		if ($Security->CurrentUserID() <> "" && !$Security->IsAdmin()) { // Non system admin
			$bValidUser = $Security->IsValidUserID($this->Site->CurrentValue);
			if (!$bValidUser) {
				$sUserIdMsg = str_replace("%c", CurrentUserID(), $Language->Phrase("UnAuthorizedUserID"));
				$sUserIdMsg = str_replace("%u", $this->Site->CurrentValue, $sUserIdMsg);
				$this->setFailureMessage($sUserIdMsg);
				return FALSE;
			}
		}

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// Nama
		$this->Nama->SetDbValueDef($rsnew, $this->Nama->CurrentValue, NULL, FALSE);

		// Alamat
		$this->Alamat->SetDbValueDef($rsnew, $this->Alamat->CurrentValue, NULL, FALSE);

		// Jumlah
		$this->Jumlah->SetDbValueDef($rsnew, $this->Jumlah->CurrentValue, NULL, FALSE);

		// Provinsi
		$this->Provinsi->SetDbValueDef($rsnew, $this->Provinsi->CurrentValue, NULL, FALSE);

		// Area
		$this->Area->SetDbValueDef($rsnew, $this->Area->CurrentValue, NULL, FALSE);

		// CP
		$this->CP->SetDbValueDef($rsnew, $this->CP->CurrentValue, NULL, FALSE);

		// NoContact
		$this->NoContact->SetDbValueDef($rsnew, $this->NoContact->CurrentValue, NULL, FALSE);

		// Tanggal
		$this->Tanggal->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->Tanggal->CurrentValue, 7), NULL, FALSE);

		// Jam
		$this->Jam->SetDbValueDef($rsnew, $this->Jam->CurrentValue, NULL, FALSE);

		// Vechicle
		$this->Vechicle->SetDbValueDef($rsnew, $this->Vechicle->CurrentValue, NULL, FALSE);

		// Type
		$this->Type->SetDbValueDef($rsnew, $this->Type->CurrentValue, NULL, FALSE);

		// Site
		$this->Site->SetDbValueDef($rsnew, $this->Site->CurrentValue, NULL, FALSE);

		// UserID
		$this->_UserID->SetDbValueDef($rsnew, CurrentUserName(), NULL);
		$rsnew['UserID'] = &$this->_UserID->DbValue;

		// TglInput
		$this->TglInput->SetDbValueDef($rsnew, ew_CurrentDateTime(), NULL);
		$rsnew['TglInput'] = &$this->TglInput->DbValue;

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);
		if ($bInsertRow) {
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {
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
			$this->Id->setDbValue($conn->Insert_ID());
			$rsnew['Id'] = $this->Id->DbValue;
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
			$this->WriteAuditTrailOnAdd($rsnew);
		}
		return $AddRow;
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
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, ew_CurrentUrl());
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'pengunjung';
	  $usr = CurrentUserID();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (add page)
	function WriteAuditTrailOnAdd(&$rs) {
		if (!$this->AuditTrailOnAdd) return;
		$table = 'pengunjung';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['Id'];

		// Write Audit Trail
		$dt = ew_StdCurrentDateTime();
		$id = ew_ScriptName();
	  $usr = CurrentUserID();
		foreach (array_keys($rs) as $fldname) {
			if ($this->fields[$fldname]->FldDataType <> EW_DATATYPE_BLOB) { // Ignore BLOB fields
				if ($this->fields[$fldname]->FldDataType == EW_DATATYPE_MEMO) {
					if (EW_AUDIT_TRAIL_TO_DATABASE)
						$newvalue = $rs[$fldname];
					else
						$newvalue = "[MEMO]"; // Memo Field
				} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_XML) {
					$newvalue = "[XML]"; // XML Field
				} else {
					$newvalue = $rs[$fldname];
				}
				ew_WriteAuditTrail("log", $dt, $id, $usr, "A", $table, $fldname, $key, "", $newvalue);
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

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ew_Header(TRUE) ?>
<?php

// Create page object
if (!isset($pengunjung_add)) $pengunjung_add = new cpengunjung_add();

// Page init
$pengunjung_add->Page_Init();

// Page main
$pengunjung_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$pengunjung_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var pengunjung_add = new ew_Page("pengunjung_add");
pengunjung_add.PageID = "add"; // Page ID
var EW_PAGE_ID = pengunjung_add.PageID; // For backward compatibility

// Form object
var fpengunjungadd = new ew_Form("fpengunjungadd");

// Validate form
fpengunjungadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_Jumlah");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($pengunjung->Jumlah->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_Jumlah");
			if (elm && !ew_CheckNumber(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($pengunjung->Jumlah->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Tanggal");
			if (elm && !ew_CheckEuroDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($pengunjung->Tanggal->FldErrMsg()) ?>");

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
fpengunjungadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fpengunjungadd.ValidateRequired = true;
<?php } else { ?>
fpengunjungadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fpengunjungadd.Lists["x_Provinsi"] = {"LinkField":"x_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_NamaProv","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fpengunjungadd.Lists["x_Area"] = {"LinkField":"x_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Kota","","",""],"ParentFields":["x_Provinsi"],"FilterFields":["x_Prov"],"Options":[]};
fpengunjungadd.Lists["x_Type"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $pengunjung_add->ShowPageHeader(); ?>
<?php
$pengunjung_add->ShowMessage();
?>
<form name="fpengunjungadd" id="fpengunjungadd" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="pengunjung">
<input type="hidden" name="a_add" id="a_add" value="A">
<table class="ewGrid"><tr><td>
<table id="tbl_pengunjungadd" class="table table-bordered table-striped">
<?php if ($pengunjung->Nama->Visible) { // Nama ?>
	<tr id="r_Nama">
		<td><span id="elh_pengunjung_Nama"><?php echo $pengunjung->Nama->FldCaption() ?></span></td>
		<td<?php echo $pengunjung->Nama->CellAttributes() ?>>
<span id="el_pengunjung_Nama" class="control-group">
<input type="text" data-field="x_Nama" name="x_Nama" id="x_Nama" size="100" maxlength="100" placeholder="<?php echo ew_HtmlEncode($pengunjung->Nama->PlaceHolder) ?>" value="<?php echo $pengunjung->Nama->EditValue ?>"<?php echo $pengunjung->Nama->EditAttributes() ?>>
</span>
<?php echo $pengunjung->Nama->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($pengunjung->Alamat->Visible) { // Alamat ?>
	<tr id="r_Alamat">
		<td><span id="elh_pengunjung_Alamat"><?php echo $pengunjung->Alamat->FldCaption() ?></span></td>
		<td<?php echo $pengunjung->Alamat->CellAttributes() ?>>
<span id="el_pengunjung_Alamat" class="control-group">
<textarea data-field="x_Alamat" name="x_Alamat" id="x_Alamat" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($pengunjung->Alamat->PlaceHolder) ?>"<?php echo $pengunjung->Alamat->EditAttributes() ?>><?php echo $pengunjung->Alamat->EditValue ?></textarea>
</span>
<?php echo $pengunjung->Alamat->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($pengunjung->Jumlah->Visible) { // Jumlah ?>
	<tr id="r_Jumlah">
		<td><span id="elh_pengunjung_Jumlah"><?php echo $pengunjung->Jumlah->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $pengunjung->Jumlah->CellAttributes() ?>>
<span id="el_pengunjung_Jumlah" class="control-group">
<input type="text" data-field="x_Jumlah" name="x_Jumlah" id="x_Jumlah" size="30" placeholder="<?php echo ew_HtmlEncode($pengunjung->Jumlah->PlaceHolder) ?>" value="<?php echo $pengunjung->Jumlah->EditValue ?>"<?php echo $pengunjung->Jumlah->EditAttributes() ?>>
</span>
<?php echo $pengunjung->Jumlah->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($pengunjung->Provinsi->Visible) { // Provinsi ?>
	<tr id="r_Provinsi">
		<td><span id="elh_pengunjung_Provinsi"><?php echo $pengunjung->Provinsi->FldCaption() ?></span></td>
		<td<?php echo $pengunjung->Provinsi->CellAttributes() ?>>
<span id="el_pengunjung_Provinsi" class="control-group">
<?php $pengunjung->Provinsi->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x_Area']); " . @$pengunjung->Provinsi->EditAttrs["onchange"]; ?>
<select data-field="x_Provinsi" id="x_Provinsi" name="x_Provinsi"<?php echo $pengunjung->Provinsi->EditAttributes() ?>>
<?php
if (is_array($pengunjung->Provinsi->EditValue)) {
	$arwrk = $pengunjung->Provinsi->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($pengunjung->Provinsi->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fpengunjungadd.Lists["x_Provinsi"].Options = <?php echo (is_array($pengunjung->Provinsi->EditValue)) ? ew_ArrayToJson($pengunjung->Provinsi->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $pengunjung->Provinsi->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($pengunjung->Area->Visible) { // Area ?>
	<tr id="r_Area">
		<td><span id="elh_pengunjung_Area"><?php echo $pengunjung->Area->FldCaption() ?></span></td>
		<td<?php echo $pengunjung->Area->CellAttributes() ?>>
<span id="el_pengunjung_Area" class="control-group">
<select data-field="x_Area" id="x_Area" name="x_Area"<?php echo $pengunjung->Area->EditAttributes() ?>>
<?php
if (is_array($pengunjung->Area->EditValue)) {
	$arwrk = $pengunjung->Area->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($pengunjung->Area->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fpengunjungadd.Lists["x_Area"].Options = <?php echo (is_array($pengunjung->Area->EditValue)) ? ew_ArrayToJson($pengunjung->Area->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $pengunjung->Area->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($pengunjung->CP->Visible) { // CP ?>
	<tr id="r_CP">
		<td><span id="elh_pengunjung_CP"><?php echo $pengunjung->CP->FldCaption() ?></span></td>
		<td<?php echo $pengunjung->CP->CellAttributes() ?>>
<span id="el_pengunjung_CP" class="control-group">
<input type="text" data-field="x_CP" name="x_CP" id="x_CP" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($pengunjung->CP->PlaceHolder) ?>" value="<?php echo $pengunjung->CP->EditValue ?>"<?php echo $pengunjung->CP->EditAttributes() ?>>
</span>
<?php echo $pengunjung->CP->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($pengunjung->NoContact->Visible) { // NoContact ?>
	<tr id="r_NoContact">
		<td><span id="elh_pengunjung_NoContact"><?php echo $pengunjung->NoContact->FldCaption() ?></span></td>
		<td<?php echo $pengunjung->NoContact->CellAttributes() ?>>
<span id="el_pengunjung_NoContact" class="control-group">
<input type="text" data-field="x_NoContact" name="x_NoContact" id="x_NoContact" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($pengunjung->NoContact->PlaceHolder) ?>" value="<?php echo $pengunjung->NoContact->EditValue ?>"<?php echo $pengunjung->NoContact->EditAttributes() ?>>
</span>
<?php echo $pengunjung->NoContact->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($pengunjung->Tanggal->Visible) { // Tanggal ?>
	<tr id="r_Tanggal">
		<td><span id="elh_pengunjung_Tanggal"><?php echo $pengunjung->Tanggal->FldCaption() ?></span></td>
		<td<?php echo $pengunjung->Tanggal->CellAttributes() ?>>
<span id="el_pengunjung_Tanggal" class="control-group">
<input type="text" data-field="x_Tanggal" name="x_Tanggal" id="x_Tanggal" placeholder="<?php echo ew_HtmlEncode($pengunjung->Tanggal->PlaceHolder) ?>" value="<?php echo $pengunjung->Tanggal->EditValue ?>"<?php echo $pengunjung->Tanggal->EditAttributes() ?>>
<?php if (!$pengunjung->Tanggal->ReadOnly && !$pengunjung->Tanggal->Disabled && @$pengunjung->Tanggal->EditAttrs["readonly"] == "" && @$pengunjung->Tanggal->EditAttrs["disabled"] == "") { ?>
<button id="cal_x_Tanggal" name="cal_x_Tanggal" class="btn" type="button"><img src="phpimages/calendar.png" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" style="border: 0;"></button><script type="text/javascript">
ew_CreateCalendar("fpengunjungadd", "x_Tanggal", "%d/%m/%Y");
</script>
<?php } ?>
</span>
<?php echo $pengunjung->Tanggal->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($pengunjung->Jam->Visible) { // Jam ?>
	<tr id="r_Jam">
		<td><span id="elh_pengunjung_Jam"><?php echo $pengunjung->Jam->FldCaption() ?></span></td>
		<td<?php echo $pengunjung->Jam->CellAttributes() ?>>
<span id="el_pengunjung_Jam" class="control-group">
<input type="text" data-field="x_Jam" name="x_Jam" id="x_Jam" placeholder="<?php echo ew_HtmlEncode($pengunjung->Jam->PlaceHolder) ?>" value="<?php echo $pengunjung->Jam->EditValue ?>"<?php echo $pengunjung->Jam->EditAttributes() ?>>
</span>
<?php echo $pengunjung->Jam->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($pengunjung->Vechicle->Visible) { // Vechicle ?>
	<tr id="r_Vechicle">
		<td><span id="elh_pengunjung_Vechicle"><?php echo $pengunjung->Vechicle->FldCaption() ?></span></td>
		<td<?php echo $pengunjung->Vechicle->CellAttributes() ?>>
<span id="el_pengunjung_Vechicle" class="control-group">
<select data-field="x_Vechicle" id="x_Vechicle" name="x_Vechicle"<?php echo $pengunjung->Vechicle->EditAttributes() ?>>
<?php
if (is_array($pengunjung->Vechicle->EditValue)) {
	$arwrk = $pengunjung->Vechicle->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($pengunjung->Vechicle->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
</span>
<?php echo $pengunjung->Vechicle->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($pengunjung->Type->Visible) { // Type ?>
	<tr id="r_Type">
		<td><span id="elh_pengunjung_Type"><?php echo $pengunjung->Type->FldCaption() ?></span></td>
		<td<?php echo $pengunjung->Type->CellAttributes() ?>>
<span id="el_pengunjung_Type" class="control-group">
<select data-field="x_Type" id="x_Type" name="x_Type"<?php echo $pengunjung->Type->EditAttributes() ?>>
<?php
if (is_array($pengunjung->Type->EditValue)) {
	$arwrk = $pengunjung->Type->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($pengunjung->Type->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fpengunjungadd.Lists["x_Type"].Options = <?php echo (is_array($pengunjung->Type->EditValue)) ? ew_ArrayToJson($pengunjung->Type->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $pengunjung->Type->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($pengunjung->Site->Visible) { // Site ?>
	<tr id="r_Site">
		<td><span id="elh_pengunjung_Site"><?php echo $pengunjung->Site->FldCaption() ?></span></td>
		<td<?php echo $pengunjung->Site->CellAttributes() ?>>
<?php if (!$Security->IsAdmin() && $Security->IsLoggedIn() && !$pengunjung->UserIDAllow("add")) { // Non system admin ?>
<span id="el_pengunjung_Site" class="control-group">
<select data-field="x_Site" id="x_Site" name="x_Site"<?php echo $pengunjung->Site->EditAttributes() ?>>
<?php
if (is_array($pengunjung->Site->EditValue)) {
	$arwrk = $pengunjung->Site->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($pengunjung->Site->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
</span>
<?php } else { ?>
<span id="el_pengunjung_Site" class="control-group">
<select data-field="x_Site" id="x_Site" name="x_Site"<?php echo $pengunjung->Site->EditAttributes() ?>>
<?php
if (is_array($pengunjung->Site->EditValue)) {
	$arwrk = $pengunjung->Site->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($pengunjung->Site->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
</span>
<?php } ?>
<?php echo $pengunjung->Site->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
</form>
<script type="text/javascript">
fpengunjungadd.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$pengunjung_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$pengunjung_add->Page_Terminate();
?>
