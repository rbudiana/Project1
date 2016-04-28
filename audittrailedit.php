<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "audittrailinfo.php" ?>
<?php include_once "userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$audittrail_edit = NULL; // Initialize page object first

class caudittrail_edit extends caudittrail {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{C2A46AD1-29DD-4049-B347-17989E75216E}";

	// Table name
	var $TableName = 'audittrail';

	// Page object name
	var $PageObjName = 'audittrail_edit';

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

		// Table object (audittrail)
		if (!isset($GLOBALS["audittrail"]) || get_class($GLOBALS["audittrail"]) == "caudittrail") {
			$GLOBALS["audittrail"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["audittrail"];
		}

		// Table object (user)
		if (!isset($GLOBALS['user'])) $GLOBALS['user'] = new cuser();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'audittrail', TRUE);

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
		if (!$Security->CanEdit()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("audittraillist.php");
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
	var $DbMasterFilter;
	var $DbDetailFilter;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Load key from QueryString
		if (@$_GET["id"] <> "") {
			$this->id->setQueryStringValue($_GET["id"]);
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->id->CurrentValue == "")
			$this->Page_Terminate("audittraillist.php"); // Invalid key, return to list

		// Validate form if post back
		if (@$_POST["a_edit"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = ""; // Form error, reset action
				$this->setFailureMessage($gsFormError);
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues();
			}
		}
		switch ($this->CurrentAction) {
			case "I": // Get a record to display
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("audittraillist.php"); // No matching record, return to list
				}
				break;
			Case "U": // Update
				$this->SendEmail = TRUE; // Send email on update success
				if ($this->EditRow()) { // Update record based on key
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Update success
					$sReturnUrl = $this->getReturnUrl();
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Restore form values if update failed
				}
		}

		// Render the record
		$this->RowType = EW_ROWTYPE_EDIT; // Render as Edit
		$this->ResetAttrs();
		$this->RenderRow();
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

	// Get upload files
	function GetUploadFiles() {
		global $objForm;

		// Get upload data
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->id->FldIsDetailKey)
			$this->id->setFormValue($objForm->GetValue("x_id"));
		if (!$this->datetime->FldIsDetailKey) {
			$this->datetime->setFormValue($objForm->GetValue("x_datetime"));
			$this->datetime->CurrentValue = ew_UnFormatDateTime($this->datetime->CurrentValue, 7);
		}
		if (!$this->script->FldIsDetailKey) {
			$this->script->setFormValue($objForm->GetValue("x_script"));
		}
		if (!$this->user->FldIsDetailKey) {
			$this->user->setFormValue($objForm->GetValue("x_user"));
		}
		if (!$this->action->FldIsDetailKey) {
			$this->action->setFormValue($objForm->GetValue("x_action"));
		}
		if (!$this->_table->FldIsDetailKey) {
			$this->_table->setFormValue($objForm->GetValue("x__table"));
		}
		if (!$this->_field->FldIsDetailKey) {
			$this->_field->setFormValue($objForm->GetValue("x__field"));
		}
		if (!$this->keyvalue->FldIsDetailKey) {
			$this->keyvalue->setFormValue($objForm->GetValue("x_keyvalue"));
		}
		if (!$this->oldvalue->FldIsDetailKey) {
			$this->oldvalue->setFormValue($objForm->GetValue("x_oldvalue"));
		}
		if (!$this->newvalue->FldIsDetailKey) {
			$this->newvalue->setFormValue($objForm->GetValue("x_newvalue"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->id->CurrentValue = $this->id->FormValue;
		$this->datetime->CurrentValue = $this->datetime->FormValue;
		$this->datetime->CurrentValue = ew_UnFormatDateTime($this->datetime->CurrentValue, 7);
		$this->script->CurrentValue = $this->script->FormValue;
		$this->user->CurrentValue = $this->user->FormValue;
		$this->action->CurrentValue = $this->action->FormValue;
		$this->_table->CurrentValue = $this->_table->FormValue;
		$this->_field->CurrentValue = $this->_field->FormValue;
		$this->keyvalue->CurrentValue = $this->keyvalue->FormValue;
		$this->oldvalue->CurrentValue = $this->oldvalue->FormValue;
		$this->newvalue->CurrentValue = $this->newvalue->FormValue;
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
		$this->id->setDbValue($rs->fields('id'));
		$this->datetime->setDbValue($rs->fields('datetime'));
		$this->script->setDbValue($rs->fields('script'));
		$this->user->setDbValue($rs->fields('user'));
		$this->action->setDbValue($rs->fields('action'));
		$this->_table->setDbValue($rs->fields('table'));
		$this->_field->setDbValue($rs->fields('field'));
		$this->keyvalue->setDbValue($rs->fields('keyvalue'));
		$this->oldvalue->setDbValue($rs->fields('oldvalue'));
		$this->newvalue->setDbValue($rs->fields('newvalue'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->id->DbValue = $row['id'];
		$this->datetime->DbValue = $row['datetime'];
		$this->script->DbValue = $row['script'];
		$this->user->DbValue = $row['user'];
		$this->action->DbValue = $row['action'];
		$this->_table->DbValue = $row['table'];
		$this->_field->DbValue = $row['field'];
		$this->keyvalue->DbValue = $row['keyvalue'];
		$this->oldvalue->DbValue = $row['oldvalue'];
		$this->newvalue->DbValue = $row['newvalue'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// id
		// datetime
		// script
		// user
		// action
		// table
		// field
		// keyvalue
		// oldvalue
		// newvalue

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// id
			$this->id->ViewValue = $this->id->CurrentValue;
			$this->id->ViewCustomAttributes = "";

			// datetime
			$this->datetime->ViewValue = $this->datetime->CurrentValue;
			$this->datetime->ViewValue = ew_FormatDateTime($this->datetime->ViewValue, 7);
			$this->datetime->ViewCustomAttributes = "";

			// script
			$this->script->ViewValue = $this->script->CurrentValue;
			$this->script->ViewCustomAttributes = "";

			// user
			$this->user->ViewValue = $this->user->CurrentValue;
			$this->user->ViewCustomAttributes = "";

			// action
			$this->action->ViewValue = $this->action->CurrentValue;
			$this->action->ViewCustomAttributes = "";

			// table
			$this->_table->ViewValue = $this->_table->CurrentValue;
			$this->_table->ViewCustomAttributes = "";

			// field
			$this->_field->ViewValue = $this->_field->CurrentValue;
			$this->_field->ViewCustomAttributes = "";

			// keyvalue
			$this->keyvalue->ViewValue = $this->keyvalue->CurrentValue;
			$this->keyvalue->ViewCustomAttributes = "";

			// oldvalue
			$this->oldvalue->ViewValue = $this->oldvalue->CurrentValue;
			$this->oldvalue->ViewCustomAttributes = "";

			// newvalue
			$this->newvalue->ViewValue = $this->newvalue->CurrentValue;
			$this->newvalue->ViewCustomAttributes = "";

			// id
			$this->id->LinkCustomAttributes = "";
			$this->id->HrefValue = "";
			$this->id->TooltipValue = "";

			// datetime
			$this->datetime->LinkCustomAttributes = "";
			$this->datetime->HrefValue = "";
			$this->datetime->TooltipValue = "";

			// script
			$this->script->LinkCustomAttributes = "";
			$this->script->HrefValue = "";
			$this->script->TooltipValue = "";

			// user
			$this->user->LinkCustomAttributes = "";
			$this->user->HrefValue = "";
			$this->user->TooltipValue = "";

			// action
			$this->action->LinkCustomAttributes = "";
			$this->action->HrefValue = "";
			$this->action->TooltipValue = "";

			// table
			$this->_table->LinkCustomAttributes = "";
			$this->_table->HrefValue = "";
			$this->_table->TooltipValue = "";

			// field
			$this->_field->LinkCustomAttributes = "";
			$this->_field->HrefValue = "";
			$this->_field->TooltipValue = "";

			// keyvalue
			$this->keyvalue->LinkCustomAttributes = "";
			$this->keyvalue->HrefValue = "";
			$this->keyvalue->TooltipValue = "";

			// oldvalue
			$this->oldvalue->LinkCustomAttributes = "";
			$this->oldvalue->HrefValue = "";
			$this->oldvalue->TooltipValue = "";

			// newvalue
			$this->newvalue->LinkCustomAttributes = "";
			$this->newvalue->HrefValue = "";
			$this->newvalue->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// id
			$this->id->EditCustomAttributes = "";
			$this->id->EditValue = $this->id->CurrentValue;
			$this->id->ViewCustomAttributes = "";

			// datetime
			$this->datetime->EditCustomAttributes = "";
			$this->datetime->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->datetime->CurrentValue, 7));
			$this->datetime->PlaceHolder = ew_RemoveHtml($this->datetime->FldCaption());

			// script
			$this->script->EditCustomAttributes = "";
			$this->script->EditValue = ew_HtmlEncode($this->script->CurrentValue);
			$this->script->PlaceHolder = ew_RemoveHtml($this->script->FldCaption());

			// user
			$this->user->EditCustomAttributes = "";
			$this->user->EditValue = ew_HtmlEncode($this->user->CurrentValue);
			$this->user->PlaceHolder = ew_RemoveHtml($this->user->FldCaption());

			// action
			$this->action->EditCustomAttributes = "";
			$this->action->EditValue = ew_HtmlEncode($this->action->CurrentValue);
			$this->action->PlaceHolder = ew_RemoveHtml($this->action->FldCaption());

			// table
			$this->_table->EditCustomAttributes = "";
			$this->_table->EditValue = ew_HtmlEncode($this->_table->CurrentValue);
			$this->_table->PlaceHolder = ew_RemoveHtml($this->_table->FldCaption());

			// field
			$this->_field->EditCustomAttributes = "";
			$this->_field->EditValue = ew_HtmlEncode($this->_field->CurrentValue);
			$this->_field->PlaceHolder = ew_RemoveHtml($this->_field->FldCaption());

			// keyvalue
			$this->keyvalue->EditCustomAttributes = "";
			$this->keyvalue->EditValue = $this->keyvalue->CurrentValue;
			$this->keyvalue->PlaceHolder = ew_RemoveHtml($this->keyvalue->FldCaption());

			// oldvalue
			$this->oldvalue->EditCustomAttributes = "";
			$this->oldvalue->EditValue = $this->oldvalue->CurrentValue;
			$this->oldvalue->PlaceHolder = ew_RemoveHtml($this->oldvalue->FldCaption());

			// newvalue
			$this->newvalue->EditCustomAttributes = "";
			$this->newvalue->EditValue = $this->newvalue->CurrentValue;
			$this->newvalue->PlaceHolder = ew_RemoveHtml($this->newvalue->FldCaption());

			// Edit refer script
			// id

			$this->id->HrefValue = "";

			// datetime
			$this->datetime->HrefValue = "";

			// script
			$this->script->HrefValue = "";

			// user
			$this->user->HrefValue = "";

			// action
			$this->action->HrefValue = "";

			// table
			$this->_table->HrefValue = "";

			// field
			$this->_field->HrefValue = "";

			// keyvalue
			$this->keyvalue->HrefValue = "";

			// oldvalue
			$this->oldvalue->HrefValue = "";

			// newvalue
			$this->newvalue->HrefValue = "";
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
		if (!$this->datetime->FldIsDetailKey && !is_null($this->datetime->FormValue) && $this->datetime->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->datetime->FldCaption());
		}
		if (!ew_CheckEuroDate($this->datetime->FormValue)) {
			ew_AddMessage($gsFormError, $this->datetime->FldErrMsg());
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

	// Update record based on key values
	function EditRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE)
			return FALSE;
		if ($rs->EOF) {
			$EditRow = FALSE; // Update Failed
		} else {

			// Save old values
			$rsold = &$rs->fields;
			$this->LoadDbValues($rsold);
			$rsnew = array();

			// datetime
			$this->datetime->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->datetime->CurrentValue, 7), ew_CurrentDate(), $this->datetime->ReadOnly);

			// script
			$this->script->SetDbValueDef($rsnew, $this->script->CurrentValue, NULL, $this->script->ReadOnly);

			// user
			$this->user->SetDbValueDef($rsnew, $this->user->CurrentValue, NULL, $this->user->ReadOnly);

			// action
			$this->action->SetDbValueDef($rsnew, $this->action->CurrentValue, NULL, $this->action->ReadOnly);

			// table
			$this->_table->SetDbValueDef($rsnew, $this->_table->CurrentValue, NULL, $this->_table->ReadOnly);

			// field
			$this->_field->SetDbValueDef($rsnew, $this->_field->CurrentValue, NULL, $this->_field->ReadOnly);

			// keyvalue
			$this->keyvalue->SetDbValueDef($rsnew, $this->keyvalue->CurrentValue, NULL, $this->keyvalue->ReadOnly);

			// oldvalue
			$this->oldvalue->SetDbValueDef($rsnew, $this->oldvalue->CurrentValue, NULL, $this->oldvalue->ReadOnly);

			// newvalue
			$this->newvalue->SetDbValueDef($rsnew, $this->newvalue->CurrentValue, NULL, $this->newvalue->ReadOnly);

			// Call Row Updating event
			$bUpdateRow = $this->Row_Updating($rsold, $rsnew);
			if ($bUpdateRow) {
				$conn->raiseErrorFn = 'ew_ErrorFn';
				if (count($rsnew) > 0)
					$EditRow = $this->Update($rsnew, "", $rsold);
				else
					$EditRow = TRUE; // No field to update
				$conn->raiseErrorFn = '';
				if ($EditRow) {
				}
			} else {
				if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

					// Use the message, do nothing
				} elseif ($this->CancelMessage <> "") {
					$this->setFailureMessage($this->CancelMessage);
					$this->CancelMessage = "";
				} else {
					$this->setFailureMessage($Language->Phrase("UpdateCancelled"));
				}
				$EditRow = FALSE;
			}
		}

		// Call Row_Updated event
		if ($EditRow)
			$this->Row_Updated($rsold, $rsnew);
		$rs->Close();
		return $EditRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "audittraillist.php", $this->TableVar, TRUE);
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, ew_CurrentUrl());
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
if (!isset($audittrail_edit)) $audittrail_edit = new caudittrail_edit();

// Page init
$audittrail_edit->Page_Init();

// Page main
$audittrail_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$audittrail_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var audittrail_edit = new ew_Page("audittrail_edit");
audittrail_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = audittrail_edit.PageID; // For backward compatibility

// Form object
var faudittrailedit = new ew_Form("faudittrailedit");

// Validate form
faudittrailedit.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_datetime");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($audittrail->datetime->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_datetime");
			if (elm && !ew_CheckEuroDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($audittrail->datetime->FldErrMsg()) ?>");

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
faudittrailedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
faudittrailedit.ValidateRequired = true;
<?php } else { ?>
faudittrailedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $audittrail_edit->ShowPageHeader(); ?>
<?php
$audittrail_edit->ShowMessage();
?>
<form name="faudittrailedit" id="faudittrailedit" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="audittrail">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<table class="ewGrid"><tr><td>
<table id="tbl_audittrailedit" class="table table-bordered table-striped">
<?php if ($audittrail->id->Visible) { // id ?>
	<tr id="r_id">
		<td><span id="elh_audittrail_id"><?php echo $audittrail->id->FldCaption() ?></span></td>
		<td<?php echo $audittrail->id->CellAttributes() ?>>
<span id="el_audittrail_id" class="control-group">
<span<?php echo $audittrail->id->ViewAttributes() ?>>
<?php echo $audittrail->id->EditValue ?></span>
</span>
<input type="hidden" data-field="x_id" name="x_id" id="x_id" value="<?php echo ew_HtmlEncode($audittrail->id->CurrentValue) ?>">
<?php echo $audittrail->id->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($audittrail->datetime->Visible) { // datetime ?>
	<tr id="r_datetime">
		<td><span id="elh_audittrail_datetime"><?php echo $audittrail->datetime->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $audittrail->datetime->CellAttributes() ?>>
<span id="el_audittrail_datetime" class="control-group">
<input type="text" data-field="x_datetime" name="x_datetime" id="x_datetime" placeholder="<?php echo ew_HtmlEncode($audittrail->datetime->PlaceHolder) ?>" value="<?php echo $audittrail->datetime->EditValue ?>"<?php echo $audittrail->datetime->EditAttributes() ?>>
</span>
<?php echo $audittrail->datetime->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($audittrail->script->Visible) { // script ?>
	<tr id="r_script">
		<td><span id="elh_audittrail_script"><?php echo $audittrail->script->FldCaption() ?></span></td>
		<td<?php echo $audittrail->script->CellAttributes() ?>>
<span id="el_audittrail_script" class="control-group">
<input type="text" data-field="x_script" name="x_script" id="x_script" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($audittrail->script->PlaceHolder) ?>" value="<?php echo $audittrail->script->EditValue ?>"<?php echo $audittrail->script->EditAttributes() ?>>
</span>
<?php echo $audittrail->script->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($audittrail->user->Visible) { // user ?>
	<tr id="r_user">
		<td><span id="elh_audittrail_user"><?php echo $audittrail->user->FldCaption() ?></span></td>
		<td<?php echo $audittrail->user->CellAttributes() ?>>
<span id="el_audittrail_user" class="control-group">
<input type="text" data-field="x_user" name="x_user" id="x_user" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($audittrail->user->PlaceHolder) ?>" value="<?php echo $audittrail->user->EditValue ?>"<?php echo $audittrail->user->EditAttributes() ?>>
</span>
<?php echo $audittrail->user->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($audittrail->action->Visible) { // action ?>
	<tr id="r_action">
		<td><span id="elh_audittrail_action"><?php echo $audittrail->action->FldCaption() ?></span></td>
		<td<?php echo $audittrail->action->CellAttributes() ?>>
<span id="el_audittrail_action" class="control-group">
<input type="text" data-field="x_action" name="x_action" id="x_action" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($audittrail->action->PlaceHolder) ?>" value="<?php echo $audittrail->action->EditValue ?>"<?php echo $audittrail->action->EditAttributes() ?>>
</span>
<?php echo $audittrail->action->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($audittrail->_table->Visible) { // table ?>
	<tr id="r__table">
		<td><span id="elh_audittrail__table"><?php echo $audittrail->_table->FldCaption() ?></span></td>
		<td<?php echo $audittrail->_table->CellAttributes() ?>>
<span id="el_audittrail__table" class="control-group">
<input type="text" data-field="x__table" name="x__table" id="x__table" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($audittrail->_table->PlaceHolder) ?>" value="<?php echo $audittrail->_table->EditValue ?>"<?php echo $audittrail->_table->EditAttributes() ?>>
</span>
<?php echo $audittrail->_table->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($audittrail->_field->Visible) { // field ?>
	<tr id="r__field">
		<td><span id="elh_audittrail__field"><?php echo $audittrail->_field->FldCaption() ?></span></td>
		<td<?php echo $audittrail->_field->CellAttributes() ?>>
<span id="el_audittrail__field" class="control-group">
<input type="text" data-field="x__field" name="x__field" id="x__field" size="30" maxlength="255" placeholder="<?php echo ew_HtmlEncode($audittrail->_field->PlaceHolder) ?>" value="<?php echo $audittrail->_field->EditValue ?>"<?php echo $audittrail->_field->EditAttributes() ?>>
</span>
<?php echo $audittrail->_field->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($audittrail->keyvalue->Visible) { // keyvalue ?>
	<tr id="r_keyvalue">
		<td><span id="elh_audittrail_keyvalue"><?php echo $audittrail->keyvalue->FldCaption() ?></span></td>
		<td<?php echo $audittrail->keyvalue->CellAttributes() ?>>
<span id="el_audittrail_keyvalue" class="control-group">
<textarea data-field="x_keyvalue" name="x_keyvalue" id="x_keyvalue" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($audittrail->keyvalue->PlaceHolder) ?>"<?php echo $audittrail->keyvalue->EditAttributes() ?>><?php echo $audittrail->keyvalue->EditValue ?></textarea>
</span>
<?php echo $audittrail->keyvalue->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($audittrail->oldvalue->Visible) { // oldvalue ?>
	<tr id="r_oldvalue">
		<td><span id="elh_audittrail_oldvalue"><?php echo $audittrail->oldvalue->FldCaption() ?></span></td>
		<td<?php echo $audittrail->oldvalue->CellAttributes() ?>>
<span id="el_audittrail_oldvalue" class="control-group">
<textarea data-field="x_oldvalue" name="x_oldvalue" id="x_oldvalue" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($audittrail->oldvalue->PlaceHolder) ?>"<?php echo $audittrail->oldvalue->EditAttributes() ?>><?php echo $audittrail->oldvalue->EditValue ?></textarea>
</span>
<?php echo $audittrail->oldvalue->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($audittrail->newvalue->Visible) { // newvalue ?>
	<tr id="r_newvalue">
		<td><span id="elh_audittrail_newvalue"><?php echo $audittrail->newvalue->FldCaption() ?></span></td>
		<td<?php echo $audittrail->newvalue->CellAttributes() ?>>
<span id="el_audittrail_newvalue" class="control-group">
<textarea data-field="x_newvalue" name="x_newvalue" id="x_newvalue" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($audittrail->newvalue->PlaceHolder) ?>"<?php echo $audittrail->newvalue->EditAttributes() ?>><?php echo $audittrail->newvalue->EditValue ?></textarea>
</span>
<?php echo $audittrail->newvalue->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("EditBtn") ?></button>
</form>
<script type="text/javascript">
faudittrailedit.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$audittrail_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$audittrail_edit->Page_Terminate();
?>
