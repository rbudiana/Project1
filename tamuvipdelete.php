<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "tamuvipinfo.php" ?>
<?php include_once "userinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$tamuvip_delete = NULL; // Initialize page object first

class ctamuvip_delete extends ctamuvip {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{C2A46AD1-29DD-4049-B347-17989E75216E}";

	// Table name
	var $TableName = 'tamuvip';

	// Page object name
	var $PageObjName = 'tamuvip_delete';

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

		// Table object (tamuvip)
		if (!isset($GLOBALS["tamuvip"]) || get_class($GLOBALS["tamuvip"]) == "ctamuvip") {
			$GLOBALS["tamuvip"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["tamuvip"];
		}

		// Table object (user)
		if (!isset($GLOBALS['user'])) $GLOBALS['user'] = new cuser();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'delete', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'tamuvip', TRUE);

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
			$this->Page_Terminate("tamuviplist.php");
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
			$this->Page_Terminate("tamuviplist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in tamuvip class, tamuvipinfo.php

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
		$this->Company->setDbValue($rs->fields('Company'));
		$this->Person->setDbValue($rs->fields('Person'));
		$this->Tanggal->setDbValue($rs->fields('Tanggal'));
		$this->Jumlah->setDbValue($rs->fields('Jumlah'));
		$this->Status->setDbValue($rs->fields('Status'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->id->DbValue = $row['id'];
		$this->Company->DbValue = $row['Company'];
		$this->Person->DbValue = $row['Person'];
		$this->Tanggal->DbValue = $row['Tanggal'];
		$this->Jumlah->DbValue = $row['Jumlah'];
		$this->Status->DbValue = $row['Status'];
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
		// id
		// Company
		// Person
		// Tanggal
		// Jumlah
		// Status

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// id
			$this->id->ViewValue = $this->id->CurrentValue;
			$this->id->ViewCustomAttributes = "";

			// Company
			$this->Company->ViewValue = $this->Company->CurrentValue;
			$this->Company->ViewCustomAttributes = "";

			// Person
			$this->Person->ViewValue = $this->Person->CurrentValue;
			$this->Person->ViewCustomAttributes = "";

			// Tanggal
			$this->Tanggal->ViewValue = $this->Tanggal->CurrentValue;
			$this->Tanggal->ViewValue = ew_FormatDateTime($this->Tanggal->ViewValue, 7);
			$this->Tanggal->ViewCustomAttributes = "";

			// Jumlah
			$this->Jumlah->ViewValue = $this->Jumlah->CurrentValue;
			$this->Jumlah->ViewCustomAttributes = "";

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

			// id
			$this->id->LinkCustomAttributes = "";
			$this->id->HrefValue = "";
			$this->id->TooltipValue = "";

			// Company
			$this->Company->LinkCustomAttributes = "";
			$this->Company->HrefValue = "";
			$this->Company->TooltipValue = "";

			// Person
			$this->Person->LinkCustomAttributes = "";
			$this->Person->HrefValue = "";
			$this->Person->TooltipValue = "";

			// Tanggal
			$this->Tanggal->LinkCustomAttributes = "";
			$this->Tanggal->HrefValue = "";
			$this->Tanggal->TooltipValue = "";

			// Jumlah
			$this->Jumlah->LinkCustomAttributes = "";
			$this->Jumlah->HrefValue = "";
			$this->Jumlah->TooltipValue = "";

			// Status
			$this->Status->LinkCustomAttributes = "";
			$this->Status->HrefValue = "";
			$this->Status->TooltipValue = "";
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
		$Breadcrumb->Add("list", $this->TableVar, "tamuviplist.php", $this->TableVar, TRUE);
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
if (!isset($tamuvip_delete)) $tamuvip_delete = new ctamuvip_delete();

// Page init
$tamuvip_delete->Page_Init();

// Page main
$tamuvip_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tamuvip_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var tamuvip_delete = new ew_Page("tamuvip_delete");
tamuvip_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = tamuvip_delete.PageID; // For backward compatibility

// Form object
var ftamuvipdelete = new ew_Form("ftamuvipdelete");

// Form_CustomValidate event
ftamuvipdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftamuvipdelete.ValidateRequired = true;
<?php } else { ?>
ftamuvipdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($tamuvip_delete->Recordset = $tamuvip_delete->LoadRecordset())
	$tamuvip_deleteTotalRecs = $tamuvip_delete->Recordset->RecordCount(); // Get record count
if ($tamuvip_deleteTotalRecs <= 0) { // No record found, exit
	if ($tamuvip_delete->Recordset)
		$tamuvip_delete->Recordset->Close();
	$tamuvip_delete->Page_Terminate("tamuviplist.php"); // Return to list
}
?>
<?php $Breadcrumb->Render(); ?>
<?php $tamuvip_delete->ShowPageHeader(); ?>
<?php
$tamuvip_delete->ShowMessage();
?>
<form name="ftamuvipdelete" id="ftamuvipdelete" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="tamuvip">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($tamuvip_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_tamuvipdelete" class="ewTable ewTableSeparate">
<?php echo $tamuvip->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($tamuvip->id->Visible) { // id ?>
		<td><span id="elh_tamuvip_id" class="tamuvip_id"><?php echo $tamuvip->id->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tamuvip->Company->Visible) { // Company ?>
		<td><span id="elh_tamuvip_Company" class="tamuvip_Company"><?php echo $tamuvip->Company->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tamuvip->Person->Visible) { // Person ?>
		<td><span id="elh_tamuvip_Person" class="tamuvip_Person"><?php echo $tamuvip->Person->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tamuvip->Tanggal->Visible) { // Tanggal ?>
		<td><span id="elh_tamuvip_Tanggal" class="tamuvip_Tanggal"><?php echo $tamuvip->Tanggal->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tamuvip->Jumlah->Visible) { // Jumlah ?>
		<td><span id="elh_tamuvip_Jumlah" class="tamuvip_Jumlah"><?php echo $tamuvip->Jumlah->FldCaption() ?></span></td>
<?php } ?>
<?php if ($tamuvip->Status->Visible) { // Status ?>
		<td><span id="elh_tamuvip_Status" class="tamuvip_Status"><?php echo $tamuvip->Status->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$tamuvip_delete->RecCnt = 0;
$i = 0;
while (!$tamuvip_delete->Recordset->EOF) {
	$tamuvip_delete->RecCnt++;
	$tamuvip_delete->RowCnt++;

	// Set row properties
	$tamuvip->ResetAttrs();
	$tamuvip->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$tamuvip_delete->LoadRowValues($tamuvip_delete->Recordset);

	// Render row
	$tamuvip_delete->RenderRow();
?>
	<tr<?php echo $tamuvip->RowAttributes() ?>>
<?php if ($tamuvip->id->Visible) { // id ?>
		<td<?php echo $tamuvip->id->CellAttributes() ?>>
<span id="el<?php echo $tamuvip_delete->RowCnt ?>_tamuvip_id" class="control-group tamuvip_id">
<span<?php echo $tamuvip->id->ViewAttributes() ?>>
<?php echo $tamuvip->id->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tamuvip->Company->Visible) { // Company ?>
		<td<?php echo $tamuvip->Company->CellAttributes() ?>>
<span id="el<?php echo $tamuvip_delete->RowCnt ?>_tamuvip_Company" class="control-group tamuvip_Company">
<span<?php echo $tamuvip->Company->ViewAttributes() ?>>
<?php echo $tamuvip->Company->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tamuvip->Person->Visible) { // Person ?>
		<td<?php echo $tamuvip->Person->CellAttributes() ?>>
<span id="el<?php echo $tamuvip_delete->RowCnt ?>_tamuvip_Person" class="control-group tamuvip_Person">
<span<?php echo $tamuvip->Person->ViewAttributes() ?>>
<?php echo $tamuvip->Person->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tamuvip->Tanggal->Visible) { // Tanggal ?>
		<td<?php echo $tamuvip->Tanggal->CellAttributes() ?>>
<span id="el<?php echo $tamuvip_delete->RowCnt ?>_tamuvip_Tanggal" class="control-group tamuvip_Tanggal">
<span<?php echo $tamuvip->Tanggal->ViewAttributes() ?>>
<?php echo $tamuvip->Tanggal->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tamuvip->Jumlah->Visible) { // Jumlah ?>
		<td<?php echo $tamuvip->Jumlah->CellAttributes() ?>>
<span id="el<?php echo $tamuvip_delete->RowCnt ?>_tamuvip_Jumlah" class="control-group tamuvip_Jumlah">
<span<?php echo $tamuvip->Jumlah->ViewAttributes() ?>>
<?php echo $tamuvip->Jumlah->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($tamuvip->Status->Visible) { // Status ?>
		<td<?php echo $tamuvip->Status->CellAttributes() ?>>
<span id="el<?php echo $tamuvip_delete->RowCnt ?>_tamuvip_Status" class="control-group tamuvip_Status">
<span<?php echo $tamuvip->Status->ViewAttributes() ?>>
<?php echo $tamuvip->Status->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$tamuvip_delete->Recordset->MoveNext();
}
$tamuvip_delete->Recordset->Close();
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
ftamuvipdelete.Init();
</script>
<?php
$tamuvip_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$tamuvip_delete->Page_Terminate();
?>
