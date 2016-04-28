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

$audittrail_delete = NULL; // Initialize page object first

class caudittrail_delete extends caudittrail {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{C2A46AD1-29DD-4049-B347-17989E75216E}";

	// Table name
	var $TableName = 'audittrail';

	// Page object name
	var $PageObjName = 'audittrail_delete';

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
			define("EW_PAGE_ID", 'delete', TRUE);

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
		if (!$Security->CanDelete()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("audittraillist.php");
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();
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
			$this->Page_Terminate("audittraillist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in audittrail class, audittrailinfo.php

		$this->CurrentFilter = $sFilter;

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
				$sThisKey .= $row['id'];
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
		} else {
			$conn->RollbackTrans(); // Rollback changes
		}

		// Call Row Deleted event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$this->Row_Deleted($row);
			}
		}
		return $DeleteRows;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "audittraillist.php", $this->TableVar, TRUE);
		$PageId = "delete";
		$Breadcrumb->Add("delete", $PageId, ew_CurrentUrl());
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
if (!isset($audittrail_delete)) $audittrail_delete = new caudittrail_delete();

// Page init
$audittrail_delete->Page_Init();

// Page main
$audittrail_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$audittrail_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var audittrail_delete = new ew_Page("audittrail_delete");
audittrail_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = audittrail_delete.PageID; // For backward compatibility

// Form object
var faudittraildelete = new ew_Form("faudittraildelete");

// Form_CustomValidate event
faudittraildelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
faudittraildelete.ValidateRequired = true;
<?php } else { ?>
faudittraildelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($audittrail_delete->Recordset = $audittrail_delete->LoadRecordset())
	$audittrail_deleteTotalRecs = $audittrail_delete->Recordset->RecordCount(); // Get record count
if ($audittrail_deleteTotalRecs <= 0) { // No record found, exit
	if ($audittrail_delete->Recordset)
		$audittrail_delete->Recordset->Close();
	$audittrail_delete->Page_Terminate("audittraillist.php"); // Return to list
}
?>
<?php $Breadcrumb->Render(); ?>
<?php $audittrail_delete->ShowPageHeader(); ?>
<?php
$audittrail_delete->ShowMessage();
?>
<form name="faudittraildelete" id="faudittraildelete" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="audittrail">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($audittrail_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_audittraildelete" class="ewTable ewTableSeparate">
<?php echo $audittrail->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($audittrail->id->Visible) { // id ?>
		<td><span id="elh_audittrail_id" class="audittrail_id"><?php echo $audittrail->id->FldCaption() ?></span></td>
<?php } ?>
<?php if ($audittrail->datetime->Visible) { // datetime ?>
		<td><span id="elh_audittrail_datetime" class="audittrail_datetime"><?php echo $audittrail->datetime->FldCaption() ?></span></td>
<?php } ?>
<?php if ($audittrail->script->Visible) { // script ?>
		<td><span id="elh_audittrail_script" class="audittrail_script"><?php echo $audittrail->script->FldCaption() ?></span></td>
<?php } ?>
<?php if ($audittrail->user->Visible) { // user ?>
		<td><span id="elh_audittrail_user" class="audittrail_user"><?php echo $audittrail->user->FldCaption() ?></span></td>
<?php } ?>
<?php if ($audittrail->action->Visible) { // action ?>
		<td><span id="elh_audittrail_action" class="audittrail_action"><?php echo $audittrail->action->FldCaption() ?></span></td>
<?php } ?>
<?php if ($audittrail->_table->Visible) { // table ?>
		<td><span id="elh_audittrail__table" class="audittrail__table"><?php echo $audittrail->_table->FldCaption() ?></span></td>
<?php } ?>
<?php if ($audittrail->_field->Visible) { // field ?>
		<td><span id="elh_audittrail__field" class="audittrail__field"><?php echo $audittrail->_field->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$audittrail_delete->RecCnt = 0;
$i = 0;
while (!$audittrail_delete->Recordset->EOF) {
	$audittrail_delete->RecCnt++;
	$audittrail_delete->RowCnt++;

	// Set row properties
	$audittrail->ResetAttrs();
	$audittrail->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$audittrail_delete->LoadRowValues($audittrail_delete->Recordset);

	// Render row
	$audittrail_delete->RenderRow();
?>
	<tr<?php echo $audittrail->RowAttributes() ?>>
<?php if ($audittrail->id->Visible) { // id ?>
		<td<?php echo $audittrail->id->CellAttributes() ?>>
<span id="el<?php echo $audittrail_delete->RowCnt ?>_audittrail_id" class="control-group audittrail_id">
<span<?php echo $audittrail->id->ViewAttributes() ?>>
<?php echo $audittrail->id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($audittrail->datetime->Visible) { // datetime ?>
		<td<?php echo $audittrail->datetime->CellAttributes() ?>>
<span id="el<?php echo $audittrail_delete->RowCnt ?>_audittrail_datetime" class="control-group audittrail_datetime">
<span<?php echo $audittrail->datetime->ViewAttributes() ?>>
<?php echo $audittrail->datetime->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($audittrail->script->Visible) { // script ?>
		<td<?php echo $audittrail->script->CellAttributes() ?>>
<span id="el<?php echo $audittrail_delete->RowCnt ?>_audittrail_script" class="control-group audittrail_script">
<span<?php echo $audittrail->script->ViewAttributes() ?>>
<?php echo $audittrail->script->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($audittrail->user->Visible) { // user ?>
		<td<?php echo $audittrail->user->CellAttributes() ?>>
<span id="el<?php echo $audittrail_delete->RowCnt ?>_audittrail_user" class="control-group audittrail_user">
<span<?php echo $audittrail->user->ViewAttributes() ?>>
<?php echo $audittrail->user->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($audittrail->action->Visible) { // action ?>
		<td<?php echo $audittrail->action->CellAttributes() ?>>
<span id="el<?php echo $audittrail_delete->RowCnt ?>_audittrail_action" class="control-group audittrail_action">
<span<?php echo $audittrail->action->ViewAttributes() ?>>
<?php echo $audittrail->action->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($audittrail->_table->Visible) { // table ?>
		<td<?php echo $audittrail->_table->CellAttributes() ?>>
<span id="el<?php echo $audittrail_delete->RowCnt ?>_audittrail__table" class="control-group audittrail__table">
<span<?php echo $audittrail->_table->ViewAttributes() ?>>
<?php echo $audittrail->_table->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($audittrail->_field->Visible) { // field ?>
		<td<?php echo $audittrail->_field->CellAttributes() ?>>
<span id="el<?php echo $audittrail_delete->RowCnt ?>_audittrail__field" class="control-group audittrail__field">
<span<?php echo $audittrail->_field->ViewAttributes() ?>>
<?php echo $audittrail->_field->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$audittrail_delete->Recordset->MoveNext();
}
$audittrail_delete->Recordset->Close();
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
faudittraildelete.Init();
</script>
<?php
$audittrail_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$audittrail_delete->Page_Terminate();
?>
