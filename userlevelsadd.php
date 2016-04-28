<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "userlevelsinfo.php" ?>
<?php include_once "userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$userlevels_add = NULL; // Initialize page object first

class cuserlevels_add extends cuserlevels {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{C2A46AD1-29DD-4049-B347-17989E75216E}";

	// Table name
	var $TableName = 'userlevels';

	// Page object name
	var $PageObjName = 'userlevels_add';

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
		global $conn, $Language;
		$GLOBALS["Page"] = &$this;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (userlevels)
		if (!isset($GLOBALS["userlevels"]) || get_class($GLOBALS["userlevels"]) == "cuserlevels") {
			$GLOBALS["userlevels"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["userlevels"];
		}

		// Table object (user)
		if (!isset($GLOBALS['user'])) $GLOBALS['user'] = new cuser();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'userlevels', TRUE);

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
		if (!$Security->CanAdmin()) {
			$Security->SaveLastUrl();
			$this->Page_Terminate("login.php");
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();

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

			// Load values for user privileges
			$AllowAdd = @$_POST["x__AllowAdd"];
			if ($AllowAdd == "") $AllowAdd = 0;
			$AllowEdit = @$_POST["x__AllowEdit"];
			if ($AllowEdit == "") $AllowEdit = 0;
			$AllowDelete = @$_POST["x__AllowDelete"];
			if ($AllowDelete == "") $AllowDelete = 0;
			$AllowList = @$_POST["x__AllowList"];
			if ($AllowList == "") $AllowList = 0;
			if (defined("EW_USER_LEVEL_COMPAT")) {
				$this->Priv = intval($AllowAdd) + intval($AllowEdit) +
					intval($AllowDelete) + intval($AllowList);
			} else {
				$AllowView = @$_POST["x__AllowView"];
				if ($AllowView == "") $AllowView = 0;
				$AllowSearch = @$_POST["x__AllowSearch"];
				if ($AllowSearch == "") $AllowSearch = 0;
				$this->Priv = intval($AllowAdd) + intval($AllowEdit) +
					intval($AllowDelete) + intval($AllowList) +
					intval($AllowView) + intval($AllowSearch);
			}
		} else { // Not post back

			// Load key values from QueryString
			$this->CopyRecord = TRUE;
			if (@$_GET["userlevelid"] != "") {
				$this->userlevelid->setQueryStringValue($_GET["userlevelid"]);
				$this->setKey("userlevelid", $this->userlevelid->CurrentValue); // Set up key
			} else {
				$this->setKey("userlevelid", ""); // Clear key
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
					$this->Page_Terminate("userlevelslist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "userlevelsview.php")
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
		$this->userlevelid->CurrentValue = NULL;
		$this->userlevelid->OldValue = $this->userlevelid->CurrentValue;
		$this->userlevelname->CurrentValue = NULL;
		$this->userlevelname->OldValue = $this->userlevelname->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->userlevelid->FldIsDetailKey) {
			$this->userlevelid->setFormValue($objForm->GetValue("x_userlevelid"));
		}
		if (!$this->userlevelname->FldIsDetailKey) {
			$this->userlevelname->setFormValue($objForm->GetValue("x_userlevelname"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->userlevelid->CurrentValue = $this->userlevelid->FormValue;
		$this->userlevelname->CurrentValue = $this->userlevelname->FormValue;
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
		$this->userlevelid->setDbValue($rs->fields('userlevelid'));
		if (is_null($this->userlevelid->CurrentValue)) {
			$this->userlevelid->CurrentValue = 0;
		} else {
			$this->userlevelid->CurrentValue = intval($this->userlevelid->CurrentValue);
		}
		$this->userlevelname->setDbValue($rs->fields('userlevelname'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->userlevelid->DbValue = $row['userlevelid'];
		$this->userlevelname->DbValue = $row['userlevelname'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("userlevelid")) <> "")
			$this->userlevelid->CurrentValue = $this->getKey("userlevelid"); // userlevelid
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
		// userlevelid
		// userlevelname

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// userlevelid
			$this->userlevelid->ViewValue = $this->userlevelid->CurrentValue;
			$this->userlevelid->ViewCustomAttributes = "";

			// userlevelname
			$this->userlevelname->ViewValue = $this->userlevelname->CurrentValue;
			$this->userlevelname->ViewCustomAttributes = "";

			// userlevelid
			$this->userlevelid->LinkCustomAttributes = "";
			$this->userlevelid->HrefValue = "";
			$this->userlevelid->TooltipValue = "";

			// userlevelname
			$this->userlevelname->LinkCustomAttributes = "";
			$this->userlevelname->HrefValue = "";
			$this->userlevelname->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// userlevelid
			$this->userlevelid->EditCustomAttributes = "";
			$this->userlevelid->EditValue = ew_HtmlEncode($this->userlevelid->CurrentValue);
			$this->userlevelid->PlaceHolder = ew_RemoveHtml($this->userlevelid->FldCaption());

			// userlevelname
			$this->userlevelname->EditCustomAttributes = "";
			$this->userlevelname->EditValue = ew_HtmlEncode($this->userlevelname->CurrentValue);
			$this->userlevelname->PlaceHolder = ew_RemoveHtml($this->userlevelname->FldCaption());

			// Edit refer script
			// userlevelid

			$this->userlevelid->HrefValue = "";

			// userlevelname
			$this->userlevelname->HrefValue = "";
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
		if (!$this->userlevelid->FldIsDetailKey && !is_null($this->userlevelid->FormValue) && $this->userlevelid->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->userlevelid->FldCaption());
		}
		if (!ew_CheckInteger($this->userlevelid->FormValue)) {
			ew_AddMessage($gsFormError, $this->userlevelid->FldErrMsg());
		}
		if (!$this->userlevelname->FldIsDetailKey && !is_null($this->userlevelname->FormValue) && $this->userlevelname->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->userlevelname->FldCaption());
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
		if (trim(strval($this->userlevelid->CurrentValue)) == "") {
			$this->setFailureMessage($Language->Phrase("MissingUserLevelID"));
		} elseif (trim($this->userlevelname->CurrentValue) == "") {
			$this->setFailureMessage($Language->Phrase("MissingUserLevelName"));
		} elseif (!is_numeric($this->userlevelid->CurrentValue)) {
			$this->setFailureMessage($Language->Phrase("UserLevelIDInteger"));
		} elseif (intval($this->userlevelid->CurrentValue) < -1) {
			$this->setFailureMessage($Language->Phrase("UserLevelIDIncorrect"));
		} elseif (intval($this->userlevelid->CurrentValue) == 0 && strtolower(trim($this->userlevelname->CurrentValue)) <> "default") {
			$this->setFailureMessage($Language->Phrase("UserLevelDefaultName"));
		} elseif (intval($this->userlevelid->CurrentValue) == -1 && strtolower(trim($this->userlevelname->CurrentValue)) <> "administrator") {
			$this->setFailureMessage($Language->Phrase("UserLevelAdministratorName"));
		} elseif (intval($this->userlevelid->CurrentValue) > 0 && (strtolower(trim($this->userlevelname->CurrentValue)) == "administrator" || strtolower(trim($this->userlevelname->CurrentValue)) == "default")) {
			$this->setFailureMessage($Language->Phrase("UserLevelNameIncorrect"));
		}
		if ($this->getFailureMessage() <> "")
			return FALSE;

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// userlevelid
		$this->userlevelid->SetDbValueDef($rsnew, $this->userlevelid->CurrentValue, 0, FALSE);

		// userlevelname
		$this->userlevelname->SetDbValueDef($rsnew, $this->userlevelname->CurrentValue, "", FALSE);

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);

		// Check if key value entered
		if ($bInsertRow && $this->ValidateKey && $this->userlevelid->CurrentValue == "" && $this->userlevelid->getSessionValue() == "") {
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

		// Add User Level priv
		if ($this->Priv > 0) {
			$UserLevelList = array();
			$UserLevelPrivList = array();
			$TableList = array();
			$GLOBALS["Security"]->LoadUserLevelFromConfigFile($UserLevelList, $UserLevelPrivList, $TableList, TRUE);
			$TableNameCount = count($TableList);
			for ($i = 0; $i < $TableNameCount; $i++) {
				$sSql = "INSERT INTO " . EW_USER_LEVEL_PRIV_TABLE . " (" .
					EW_USER_LEVEL_PRIV_TABLE_NAME_FIELD . ", " .
					EW_USER_LEVEL_PRIV_USER_LEVEL_ID_FIELD . ", " .
					EW_USER_LEVEL_PRIV_PRIV_FIELD . ") VALUES ('" .
					ew_AdjustSql($TableList[$i][4] . $TableList[$i][0]) .
					"', " . $this->userlevelid->CurrentValue . ", " . $this->Priv . ")";
				$conn->Execute($sSql);
			}
		}
		return $AddRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "userlevelslist.php", $this->TableVar, TRUE);
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, ew_CurrentUrl());
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
if (!isset($userlevels_add)) $userlevels_add = new cuserlevels_add();

// Page init
$userlevels_add->Page_Init();

// Page main
$userlevels_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$userlevels_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var userlevels_add = new ew_Page("userlevels_add");
userlevels_add.PageID = "add"; // Page ID
var EW_PAGE_ID = userlevels_add.PageID; // For backward compatibility

// Form object
var fuserlevelsadd = new ew_Form("fuserlevelsadd");

// Validate form
fuserlevelsadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_userlevelid");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($userlevels->userlevelid->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_userlevelid");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($userlevels->userlevelid->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_userlevelname");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($userlevels->userlevelname->FldCaption()) ?>");
			var elId = fobj.elements["x" + infix + "_userlevelid"];
			var elName = fobj.elements["x" + infix + "_userlevelname"];
			if (elId && elName) {
				elId.value = $.trim(elId.value);
				elName.value = $.trim(elName.value);
				if (elId && !ew_CheckInteger(elId.value))
					return this.OnError(elId, ewLanguage.Phrase("UserLevelIDInteger"));
				var level = parseInt(elId.value, 10);
				if (level == 0) {
					if (!ew_SameText(elName.value, "default"))
						return this.OnError(elName, ewLanguage.Phrase("UserLevelDefaultName"));
				} else if (level == -1) {
					if (!ew_SameText(elName.value, "administrator"))
						return this.OnError(elName, ewLanguage.Phrase("UserLevelAdministratorName"));
				} else if (level < -1) {
					return this.OnError(elId, ewLanguage.Phrase("UserLevelIDIncorrect"));
				} else if (level > 0) { 
					if (ew_SameText(elName.value, "administrator") || ew_SameText(elName.value, "default"))
						return this.OnError(elName, ewLanguage.Phrase("UserLevelNameIncorrect"));
				}
			}

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
fuserlevelsadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fuserlevelsadd.ValidateRequired = true;
<?php } else { ?>
fuserlevelsadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $userlevels_add->ShowPageHeader(); ?>
<?php
$userlevels_add->ShowMessage();
?>
<form name="fuserlevelsadd" id="fuserlevelsadd" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="userlevels">
<input type="hidden" name="a_add" id="a_add" value="A">
<table class="ewGrid"><tr><td>
<table id="tbl_userlevelsadd" class="table table-bordered table-striped">
<?php if ($userlevels->userlevelid->Visible) { // userlevelid ?>
	<tr id="r_userlevelid">
		<td><span id="elh_userlevels_userlevelid"><?php echo $userlevels->userlevelid->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $userlevels->userlevelid->CellAttributes() ?>>
<span id="el_userlevels_userlevelid" class="control-group">
<input type="text" data-field="x_userlevelid" name="x_userlevelid" id="x_userlevelid" size="30" placeholder="<?php echo ew_HtmlEncode($userlevels->userlevelid->PlaceHolder) ?>" value="<?php echo $userlevels->userlevelid->EditValue ?>"<?php echo $userlevels->userlevelid->EditAttributes() ?>>
</span>
<?php echo $userlevels->userlevelid->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($userlevels->userlevelname->Visible) { // userlevelname ?>
	<tr id="r_userlevelname">
		<td><span id="elh_userlevels_userlevelname"><?php echo $userlevels->userlevelname->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $userlevels->userlevelname->CellAttributes() ?>>
<span id="el_userlevels_userlevelname" class="control-group">
<input type="text" data-field="x_userlevelname" name="x_userlevelname" id="x_userlevelname" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($userlevels->userlevelname->PlaceHolder) ?>" value="<?php echo $userlevels->userlevelname->EditValue ?>"<?php echo $userlevels->userlevelname->EditAttributes() ?>>
</span>
<?php echo $userlevels->userlevelname->CustomMsg ?></td>
	</tr>
<?php } ?>
	<!-- row for permission values -->
	<tr id="rp_permission">
		<td><?php echo $Language->Phrase("Permission") ?></td>
		<td>
<label class="checkbox"><input type="checkbox" name="x__AllowAdd" id="Add" value="<?php echo EW_ALLOW_ADD ?>"><?php echo $Language->Phrase("PermissionAddCopy") ?></label>
<label class="checkbox"><input type="checkbox" name="x__AllowDelete" id="Delete" value="<?php echo EW_ALLOW_DELETE ?>"><?php echo $Language->Phrase("PermissionDelete") ?></label>
<label class="checkbox"><input type="checkbox" name="x__AllowEdit" id="Edit" value="<?php echo EW_ALLOW_EDIT ?>"><?php echo $Language->Phrase("PermissionEdit") ?></label>
<?php if (defined("EW_USER_LEVEL_COMPAT")) { ?>
<label class="checkbox"><input type="checkbox" name="x__AllowList" id="List" value="<?php echo EW_ALLOW_LIST ?>"><?php echo $Language->Phrase("PermissionListSearchView") ?></label>
<?php } else { ?>
<label class="checkbox"><input type="checkbox" name="x__AllowList" id="List" value="<?php echo EW_ALLOW_LIST ?>"><?php echo $Language->Phrase("PermissionList") ?></label>
<label class="checkbox"><input type="checkbox" name="x__AllowView" id="View" value="<?php echo EW_ALLOW_VIEW ?>"><?php echo $Language->Phrase("PermissionView") ?></label>
<label class="checkbox"><input type="checkbox" name="x__AllowSearch" id="Search" value="<?php echo EW_ALLOW_SEARCH ?>"><?php echo $Language->Phrase("PermissionSearch") ?></label>
<?php } ?>
		</td>
	</tr>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
</form>
<script type="text/javascript">
fuserlevelsadd.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$userlevels_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$userlevels_add->Page_Terminate();
?>
