<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "parsysinfo.php" ?>
<?php include_once "userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$parsys_edit = NULL; // Initialize page object first

class cparsys_edit extends cparsys {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{C2A46AD1-29DD-4049-B347-17989E75216E}";

	// Table name
	var $TableName = 'parsys';

	// Page object name
	var $PageObjName = 'parsys_edit';

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

		// Table object (parsys)
		if (!isset($GLOBALS["parsys"]) || get_class($GLOBALS["parsys"]) == "cparsys") {
			$GLOBALS["parsys"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["parsys"];
		}

		// Table object (user)
		if (!isset($GLOBALS['user'])) $GLOBALS['user'] = new cuser();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'parsys', TRUE);

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
			$this->Page_Terminate("parsyslist.php");
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();
		if ($Security->IsLoggedIn() && strval($Security->CurrentUserID()) == "") {
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("parsyslist.php");
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->ID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
		if (@$_GET["ID"] <> "") {
			$this->ID->setQueryStringValue($_GET["ID"]);
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
		if ($this->ID->CurrentValue == "")
			$this->Page_Terminate("parsyslist.php"); // Invalid key, return to list

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
					$this->Page_Terminate("parsyslist.php"); // No matching record, return to list
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
		if (!$this->Nama1->FldIsDetailKey) {
			$this->Nama1->setFormValue($objForm->GetValue("x_Nama1"));
		}
		if (!$this->Nama2->FldIsDetailKey) {
			$this->Nama2->setFormValue($objForm->GetValue("x_Nama2"));
		}
		if (!$this->Nama3->FldIsDetailKey) {
			$this->Nama3->setFormValue($objForm->GetValue("x_Nama3"));
		}
		if (!$this->Nama4->FldIsDetailKey) {
			$this->Nama4->setFormValue($objForm->GetValue("x_Nama4"));
		}
		if (!$this->Nama5->FldIsDetailKey) {
			$this->Nama5->setFormValue($objForm->GetValue("x_Nama5"));
		}
		if (!$this->Nama6->FldIsDetailKey) {
			$this->Nama6->setFormValue($objForm->GetValue("x_Nama6"));
		}
		if (!$this->ID->FldIsDetailKey)
			$this->ID->setFormValue($objForm->GetValue("x_ID"));
		if (!$this->site->FldIsDetailKey) {
			$this->site->setFormValue($objForm->GetValue("x_site"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->Nama1->CurrentValue = $this->Nama1->FormValue;
		$this->Nama2->CurrentValue = $this->Nama2->FormValue;
		$this->Nama3->CurrentValue = $this->Nama3->FormValue;
		$this->Nama4->CurrentValue = $this->Nama4->FormValue;
		$this->Nama5->CurrentValue = $this->Nama5->FormValue;
		$this->Nama6->CurrentValue = $this->Nama6->FormValue;
		$this->ID->CurrentValue = $this->ID->FormValue;
		$this->site->CurrentValue = $this->site->FormValue;
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
			$res = $this->ShowOptionLink('edit');
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

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->Nama1->DbValue = $row['Nama1'];
		$this->Alamat1->DbValue = $row['Alamat1'];
		$this->Nama2->DbValue = $row['Nama2'];
		$this->Alamat2->DbValue = $row['Alamat2'];
		$this->Nama3->DbValue = $row['Nama3'];
		$this->Alamat3->DbValue = $row['Alamat3'];
		$this->Nama4->DbValue = $row['Nama4'];
		$this->Alamat4->DbValue = $row['Alamat4'];
		$this->Nama5->DbValue = $row['Nama5'];
		$this->Alamat5->DbValue = $row['Alamat5'];
		$this->Nama6->DbValue = $row['Nama6'];
		$this->Alamat6->DbValue = $row['Alamat6'];
		$this->ID->DbValue = $row['ID'];
		$this->site->DbValue = $row['site'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// Nama1
		// Alamat1
		// Nama2
		// Alamat2
		// Nama3
		// Alamat3
		// Nama4
		// Alamat4
		// Nama5
		// Alamat5
		// Nama6
		// Alamat6
		// ID
		// site

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// Nama1
			$this->Nama1->ViewValue = $this->Nama1->CurrentValue;
			$this->Nama1->ViewCustomAttributes = "";

			// Nama2
			$this->Nama2->ViewValue = $this->Nama2->CurrentValue;
			$this->Nama2->ViewCustomAttributes = "";

			// Nama3
			$this->Nama3->ViewValue = $this->Nama3->CurrentValue;
			$this->Nama3->ViewCustomAttributes = "";

			// Nama4
			$this->Nama4->ViewValue = $this->Nama4->CurrentValue;
			$this->Nama4->ViewCustomAttributes = "";

			// Nama5
			$this->Nama5->ViewValue = $this->Nama5->CurrentValue;
			$this->Nama5->ViewCustomAttributes = "";

			// Nama6
			$this->Nama6->ViewValue = $this->Nama6->CurrentValue;
			$this->Nama6->ViewCustomAttributes = "";

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

			// Nama2
			$this->Nama2->LinkCustomAttributes = "";
			$this->Nama2->HrefValue = "";
			$this->Nama2->TooltipValue = "";

			// Nama3
			$this->Nama3->LinkCustomAttributes = "";
			$this->Nama3->HrefValue = "";
			$this->Nama3->TooltipValue = "";

			// Nama4
			$this->Nama4->LinkCustomAttributes = "";
			$this->Nama4->HrefValue = "";
			$this->Nama4->TooltipValue = "";

			// Nama5
			$this->Nama5->LinkCustomAttributes = "";
			$this->Nama5->HrefValue = "";
			$this->Nama5->TooltipValue = "";

			// Nama6
			$this->Nama6->LinkCustomAttributes = "";
			$this->Nama6->HrefValue = "";
			$this->Nama6->TooltipValue = "";

			// ID
			$this->ID->LinkCustomAttributes = "";
			$this->ID->HrefValue = "";
			$this->ID->TooltipValue = "";

			// site
			$this->site->LinkCustomAttributes = "";
			$this->site->HrefValue = "";
			$this->site->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// Nama1
			$this->Nama1->EditCustomAttributes = "";
			$this->Nama1->EditValue = ew_HtmlEncode($this->Nama1->CurrentValue);
			$this->Nama1->PlaceHolder = ew_RemoveHtml($this->Nama1->FldCaption());

			// Nama2
			$this->Nama2->EditCustomAttributes = "";
			$this->Nama2->EditValue = ew_HtmlEncode($this->Nama2->CurrentValue);
			$this->Nama2->PlaceHolder = ew_RemoveHtml($this->Nama2->FldCaption());

			// Nama3
			$this->Nama3->EditCustomAttributes = "";
			$this->Nama3->EditValue = ew_HtmlEncode($this->Nama3->CurrentValue);
			$this->Nama3->PlaceHolder = ew_RemoveHtml($this->Nama3->FldCaption());

			// Nama4
			$this->Nama4->EditCustomAttributes = "";
			$this->Nama4->EditValue = ew_HtmlEncode($this->Nama4->CurrentValue);
			$this->Nama4->PlaceHolder = ew_RemoveHtml($this->Nama4->FldCaption());

			// Nama5
			$this->Nama5->EditCustomAttributes = "";
			$this->Nama5->EditValue = ew_HtmlEncode($this->Nama5->CurrentValue);
			$this->Nama5->PlaceHolder = ew_RemoveHtml($this->Nama5->FldCaption());

			// Nama6
			$this->Nama6->EditCustomAttributes = "";
			$this->Nama6->EditValue = ew_HtmlEncode($this->Nama6->CurrentValue);
			$this->Nama6->PlaceHolder = ew_RemoveHtml($this->Nama6->FldCaption());

			// ID
			$this->ID->EditCustomAttributes = "";
			$this->ID->EditValue = $this->ID->CurrentValue;
			$this->ID->ViewCustomAttributes = "";

			// site
			$this->site->EditCustomAttributes = "";
			if (!$Security->IsAdmin() && $Security->IsLoggedIn() && !$this->UserIDAllow("edit")) { // Non system admin
			$sFilterWrk = "";
			$sFilterWrk = $GLOBALS["user"]->AddUserIDFilter("");
			$sSqlWrk = "SELECT `site`, `site` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `user`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->site, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->site->EditValue = $arwrk;
			} else {
			$this->site->EditValue = ew_HtmlEncode($this->site->CurrentValue);
			$this->site->PlaceHolder = ew_RemoveHtml($this->site->FldCaption());
			}

			// Edit refer script
			// Nama1

			$this->Nama1->HrefValue = "";

			// Nama2
			$this->Nama2->HrefValue = "";

			// Nama3
			$this->Nama3->HrefValue = "";

			// Nama4
			$this->Nama4->HrefValue = "";

			// Nama5
			$this->Nama5->HrefValue = "";

			// Nama6
			$this->Nama6->HrefValue = "";

			// ID
			$this->ID->HrefValue = "";

			// site
			$this->site->HrefValue = "";
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
		if (!ew_CheckInteger($this->Nama1->FormValue)) {
			ew_AddMessage($gsFormError, $this->Nama1->FldErrMsg());
		}
		if (!ew_CheckInteger($this->Nama2->FormValue)) {
			ew_AddMessage($gsFormError, $this->Nama2->FldErrMsg());
		}
		if (!ew_CheckInteger($this->Nama3->FormValue)) {
			ew_AddMessage($gsFormError, $this->Nama3->FldErrMsg());
		}
		if (!ew_CheckInteger($this->Nama4->FormValue)) {
			ew_AddMessage($gsFormError, $this->Nama4->FldErrMsg());
		}
		if (!ew_CheckInteger($this->Nama5->FormValue)) {
			ew_AddMessage($gsFormError, $this->Nama5->FldErrMsg());
		}
		if (!ew_CheckInteger($this->Nama6->FormValue)) {
			ew_AddMessage($gsFormError, $this->Nama6->FldErrMsg());
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

			// Nama1
			$this->Nama1->SetDbValueDef($rsnew, $this->Nama1->CurrentValue, NULL, $this->Nama1->ReadOnly);

			// Nama2
			$this->Nama2->SetDbValueDef($rsnew, $this->Nama2->CurrentValue, NULL, $this->Nama2->ReadOnly);

			// Nama3
			$this->Nama3->SetDbValueDef($rsnew, $this->Nama3->CurrentValue, NULL, $this->Nama3->ReadOnly);

			// Nama4
			$this->Nama4->SetDbValueDef($rsnew, $this->Nama4->CurrentValue, NULL, $this->Nama4->ReadOnly);

			// Nama5
			$this->Nama5->SetDbValueDef($rsnew, $this->Nama5->CurrentValue, NULL, $this->Nama5->ReadOnly);

			// Nama6
			$this->Nama6->SetDbValueDef($rsnew, $this->Nama6->CurrentValue, NULL, $this->Nama6->ReadOnly);

			// site
			$this->site->SetDbValueDef($rsnew, $this->site->CurrentValue, NULL, $this->site->ReadOnly);

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

	// Show link optionally based on User ID
	function ShowOptionLink($id = "") {
		global $Security;
		if ($Security->IsLoggedIn() && !$Security->IsAdmin() && !$this->UserIDAllow($id))
			return $Security->IsValidUserID($this->site->CurrentValue);
		return TRUE;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "parsyslist.php", $this->TableVar, TRUE);
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
if (!isset($parsys_edit)) $parsys_edit = new cparsys_edit();

// Page init
$parsys_edit->Page_Init();

// Page main
$parsys_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$parsys_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var parsys_edit = new ew_Page("parsys_edit");
parsys_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = parsys_edit.PageID; // For backward compatibility

// Form object
var fparsysedit = new ew_Form("fparsysedit");

// Validate form
fparsysedit.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_Nama1");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($parsys->Nama1->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Nama2");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($parsys->Nama2->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Nama3");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($parsys->Nama3->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Nama4");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($parsys->Nama4->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Nama5");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($parsys->Nama5->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Nama6");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($parsys->Nama6->FldErrMsg()) ?>");

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
fparsysedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fparsysedit.ValidateRequired = true;
<?php } else { ?>
fparsysedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $parsys_edit->ShowPageHeader(); ?>
<?php
$parsys_edit->ShowMessage();
?>
<form name="fparsysedit" id="fparsysedit" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="parsys">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<table class="ewGrid"><tr><td>
<table id="tbl_parsysedit" class="table table-bordered table-striped">
<?php if ($parsys->Nama1->Visible) { // Nama1 ?>
	<tr id="r_Nama1">
		<td><span id="elh_parsys_Nama1"><?php echo $parsys->Nama1->FldCaption() ?></span></td>
		<td<?php echo $parsys->Nama1->CellAttributes() ?>>
<span id="el_parsys_Nama1" class="control-group">
<input type="text" data-field="x_Nama1" name="x_Nama1" id="x_Nama1" size="30" placeholder="<?php echo ew_HtmlEncode($parsys->Nama1->PlaceHolder) ?>" value="<?php echo $parsys->Nama1->EditValue ?>"<?php echo $parsys->Nama1->EditAttributes() ?>>
</span>
<?php echo $parsys->Nama1->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($parsys->Nama2->Visible) { // Nama2 ?>
	<tr id="r_Nama2">
		<td><span id="elh_parsys_Nama2"><?php echo $parsys->Nama2->FldCaption() ?></span></td>
		<td<?php echo $parsys->Nama2->CellAttributes() ?>>
<span id="el_parsys_Nama2" class="control-group">
<input type="text" data-field="x_Nama2" name="x_Nama2" id="x_Nama2" size="30" placeholder="<?php echo ew_HtmlEncode($parsys->Nama2->PlaceHolder) ?>" value="<?php echo $parsys->Nama2->EditValue ?>"<?php echo $parsys->Nama2->EditAttributes() ?>>
</span>
<?php echo $parsys->Nama2->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($parsys->Nama3->Visible) { // Nama3 ?>
	<tr id="r_Nama3">
		<td><span id="elh_parsys_Nama3"><?php echo $parsys->Nama3->FldCaption() ?></span></td>
		<td<?php echo $parsys->Nama3->CellAttributes() ?>>
<span id="el_parsys_Nama3" class="control-group">
<input type="text" data-field="x_Nama3" name="x_Nama3" id="x_Nama3" size="30" placeholder="<?php echo ew_HtmlEncode($parsys->Nama3->PlaceHolder) ?>" value="<?php echo $parsys->Nama3->EditValue ?>"<?php echo $parsys->Nama3->EditAttributes() ?>>
</span>
<?php echo $parsys->Nama3->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($parsys->Nama4->Visible) { // Nama4 ?>
	<tr id="r_Nama4">
		<td><span id="elh_parsys_Nama4"><?php echo $parsys->Nama4->FldCaption() ?></span></td>
		<td<?php echo $parsys->Nama4->CellAttributes() ?>>
<span id="el_parsys_Nama4" class="control-group">
<input type="text" data-field="x_Nama4" name="x_Nama4" id="x_Nama4" size="30" placeholder="<?php echo ew_HtmlEncode($parsys->Nama4->PlaceHolder) ?>" value="<?php echo $parsys->Nama4->EditValue ?>"<?php echo $parsys->Nama4->EditAttributes() ?>>
</span>
<?php echo $parsys->Nama4->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($parsys->Nama5->Visible) { // Nama5 ?>
	<tr id="r_Nama5">
		<td><span id="elh_parsys_Nama5"><?php echo $parsys->Nama5->FldCaption() ?></span></td>
		<td<?php echo $parsys->Nama5->CellAttributes() ?>>
<span id="el_parsys_Nama5" class="control-group">
<input type="text" data-field="x_Nama5" name="x_Nama5" id="x_Nama5" size="30" placeholder="<?php echo ew_HtmlEncode($parsys->Nama5->PlaceHolder) ?>" value="<?php echo $parsys->Nama5->EditValue ?>"<?php echo $parsys->Nama5->EditAttributes() ?>>
</span>
<?php echo $parsys->Nama5->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($parsys->Nama6->Visible) { // Nama6 ?>
	<tr id="r_Nama6">
		<td><span id="elh_parsys_Nama6"><?php echo $parsys->Nama6->FldCaption() ?></span></td>
		<td<?php echo $parsys->Nama6->CellAttributes() ?>>
<span id="el_parsys_Nama6" class="control-group">
<input type="text" data-field="x_Nama6" name="x_Nama6" id="x_Nama6" size="30" placeholder="<?php echo ew_HtmlEncode($parsys->Nama6->PlaceHolder) ?>" value="<?php echo $parsys->Nama6->EditValue ?>"<?php echo $parsys->Nama6->EditAttributes() ?>>
</span>
<?php echo $parsys->Nama6->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($parsys->ID->Visible) { // ID ?>
	<tr id="r_ID">
		<td><span id="elh_parsys_ID"><?php echo $parsys->ID->FldCaption() ?></span></td>
		<td<?php echo $parsys->ID->CellAttributes() ?>>
<span id="el_parsys_ID" class="control-group">
<span<?php echo $parsys->ID->ViewAttributes() ?>>
<?php echo $parsys->ID->EditValue ?></span>
</span>
<input type="hidden" data-field="x_ID" name="x_ID" id="x_ID" value="<?php echo ew_HtmlEncode($parsys->ID->CurrentValue) ?>">
<?php echo $parsys->ID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($parsys->site->Visible) { // site ?>
	<tr id="r_site">
		<td><span id="elh_parsys_site"><?php echo $parsys->site->FldCaption() ?></span></td>
		<td<?php echo $parsys->site->CellAttributes() ?>>
<?php if (!$Security->IsAdmin() && $Security->IsLoggedIn() && !$parsys->UserIDAllow("edit")) { // Non system admin ?>
<span id="el_parsys_site" class="control-group">
<select data-field="x_site" id="x_site" name="x_site"<?php echo $parsys->site->EditAttributes() ?>>
<?php
if (is_array($parsys->site->EditValue)) {
	$arwrk = $parsys->site->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($parsys->site->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<span id="el_parsys_site" class="control-group">
<input type="text" data-field="x_site" name="x_site" id="x_site" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($parsys->site->PlaceHolder) ?>" value="<?php echo $parsys->site->EditValue ?>"<?php echo $parsys->site->EditAttributes() ?>>
</span>
<?php } ?>
<?php echo $parsys->site->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("EditBtn") ?></button>
</form>
<script type="text/javascript">
fparsysedit.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$parsys_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$parsys_edit->Page_Terminate();
?>
