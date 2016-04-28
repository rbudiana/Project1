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

$tamuvip_list = NULL; // Initialize page object first

class ctamuvip_list extends ctamuvip {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{C2A46AD1-29DD-4049-B347-17989E75216E}";

	// Table name
	var $TableName = 'tamuvip';

	// Page object name
	var $PageObjName = 'tamuvip_list';

	// Grid form hidden field names
	var $FormName = 'ftamuviplist';
	var $FormActionName = 'k_action';
	var $FormKeyName = 'k_key';
	var $FormOldKeyName = 'k_oldkey';
	var $FormBlankRowName = 'k_blankrow';
	var $FormKeyCountName = 'key_count';

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

	// Page URLs
	var $AddUrl;
	var $EditUrl;
	var $CopyUrl;
	var $DeleteUrl;
	var $ViewUrl;
	var $ListUrl;

	// Export URLs
	var $ExportPrintUrl;
	var $ExportHtmlUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportXmlUrl;
	var $ExportCsvUrl;
	var $ExportPdfUrl;

	// Update URLs
	var $InlineAddUrl;
	var $InlineCopyUrl;
	var $InlineEditUrl;
	var $GridAddUrl;
	var $GridEditUrl;
	var $MultiDeleteUrl;
	var $MultiUpdateUrl;

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

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "tamuvipadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "tamuvipdelete.php";
		$this->MultiUpdateUrl = "tamuvipupdate.php";

		// Table object (user)
		if (!isset($GLOBALS['user'])) $GLOBALS['user'] = new cuser();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'tamuvip', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

		// List options
		$this->ListOptions = new cListOptions();
		$this->ListOptions->TableVar = $this->TableVar;

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['addedit'] = new cListOptions();
		$this->OtherOptions['addedit']->Tag = "div";
		$this->OtherOptions['addedit']->TagClassName = "ewAddEditOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "div";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "div";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";
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
		if (!$Security->CanList()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate("login.php");
		}
		$Security->UserID_Loading();
		if ($Security->IsLoggedIn()) $Security->LoadUserID();
		$Security->UserID_Loaded();

		// Create form object
		$objForm = new cFormObj();

		// Get export parameters
		if (@$_GET["export"] <> "") {
			$this->Export = $_GET["export"];
		} elseif (ew_IsHttpPost()) {
			if (@$_POST["exporttype"] <> "")
				$this->Export = $_POST["exporttype"];
		} else {
			$this->setExportReturnUrl(ew_CurrentUrl());
		}
		$gsExport = $this->Export; // Get export parameter, used in header
		$gsExportFile = $this->TableVar; // Get export file, used in header
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

		// Get grid add count
		$gridaddcnt = @$_GET[EW_TABLE_GRID_ADD_ROW_COUNT];
		if (is_numeric($gridaddcnt) && $gridaddcnt > 0)
			$this->GridAddRowCount = $gridaddcnt;

		// Set up list options
		$this->SetupListOptions();

		// Setup export options
		$this->SetupExportOptions();
		$this->id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Setup other options
		$this->SetupOtherOptions();

		// Set "checkbox" visible
		if (count($this->CustomActions) > 0)
			$this->ListOptions->Items["checkbox"]->Visible = TRUE;

		// Update url if printer friendly for Pdf
		if ($this->PrinterFriendlyForPdf)
			$this->ExportOptions->Items["pdf"]->Body = str_replace($this->ExportPdfUrl, $this->ExportPrintUrl . "&pdf=1", $this->ExportOptions->Items["pdf"]->Body);
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $conn;

		// Page Unload event
		$this->Page_Unload();
		if ($this->Export == "print" && @$_GET["pdf"] == "1") { // Printer friendly version and with pdf=1 in URL parameters
			$pdf = new cExportPdf($GLOBALS["Table"]);
			$pdf->Text = ob_get_contents(); // Set the content as the HTML of current page (printer friendly version)
			ob_end_clean();
			$pdf->Export();
		}

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

	// Class variables
	var $ListOptions; // List options
	var $ExportOptions; // Export options
	var $OtherOptions = array(); // Other options
	var $DisplayRecs = 20;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $Pager;
	var $SearchWhere = ""; // Search WHERE clause
	var $RecCnt = 0; // Record count
	var $EditRowCnt;
	var $StartRowCnt = 1;
	var $RowCnt = 0;
	var $Attrs = array(); // Row attributes and cell attributes
	var $RowIndex = 0; // Row index
	var $KeyCount = 0; // Key count
	var $RowAction = ""; // Row action
	var $RowOldKey = ""; // Row old key (for copy)
	var $RecPerRow = 0;
	var $ColCnt = 0;
	var $DbMasterFilter = ""; // Master filter
	var $DbDetailFilter = ""; // Detail filter
	var $MasterRecordExists;	
	var $MultiSelectKey;
	var $Command;
	var $RestoreSearch = FALSE;
	var $Recordset;
	var $OldRecordset;

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError, $gsSearchError, $Security;

		// Search filters
		$sSrchAdvanced = ""; // Advanced search filter
		$sSrchBasic = ""; // Basic search filter
		$sFilter = "";

		// Get command
		$this->Command = strtolower(@$_GET["cmd"]);
		if ($this->IsPageRequest()) { // Validate request

			// Process custom action first
			$this->ProcessCustomAction();

			// Handle reset command
			$this->ResetCmd();

			// Set up Breadcrumb
			$this->SetupBreadcrumb();

			// Check QueryString parameters
			if (@$_GET["a"] <> "") {
				$this->CurrentAction = $_GET["a"];

				// Clear inline mode
				if ($this->CurrentAction == "cancel")
					$this->ClearInlineMode();

				// Switch to inline edit mode
				if ($this->CurrentAction == "edit")
					$this->InlineEditMode();

				// Switch to inline add mode
				if ($this->CurrentAction == "add" || $this->CurrentAction == "copy")
					$this->InlineAddMode();
			} else {
				if (@$_POST["a_list"] <> "") {
					$this->CurrentAction = $_POST["a_list"]; // Get action

					// Inline Update
					if (($this->CurrentAction == "update" || $this->CurrentAction == "overwrite") && @$_SESSION[EW_SESSION_INLINE_MODE] == "edit")
						$this->InlineUpdate();

					// Insert Inline
					if ($this->CurrentAction == "insert" && @$_SESSION[EW_SESSION_INLINE_MODE] == "add")
						$this->InlineInsert();
				}
			}

			// Hide list options
			if ($this->Export <> "") {
				$this->ListOptions->HideAllOptions(array("sequence"));
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			} elseif ($this->CurrentAction == "gridadd" || $this->CurrentAction == "gridedit") {
				$this->ListOptions->HideAllOptions();
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			}

			// Hide export options
			if ($this->Export <> "" || $this->CurrentAction <> "")
				$this->ExportOptions->HideAllOptions();

			// Hide other options
			if ($this->Export <> "") {
				foreach ($this->OtherOptions as &$option)
					$option->HideAllOptions();
			}

			// Get basic search values
			$this->LoadBasicSearchValues();

			// Restore search parms from Session if not searching / reset
			if ($this->Command <> "search" && $this->Command <> "reset" && $this->Command <> "resetall" && $this->CheckSearchParms())
				$this->RestoreSearchParms();

			// Call Recordset SearchValidated event
			$this->Recordset_SearchValidated();

			// Set up sorting order
			$this->SetUpSortOrder();

			// Get basic search criteria
			if ($gsSearchError == "")
				$sSrchBasic = $this->BasicSearchWhere();
		}

		// Restore display records
		if ($this->getRecordsPerPage() <> "") {
			$this->DisplayRecs = $this->getRecordsPerPage(); // Restore from Session
		} else {
			$this->DisplayRecs = 20; // Load default
		}

		// Load Sorting Order
		$this->LoadSortOrder();

		// Load search default if no existing search criteria
		if (!$this->CheckSearchParms()) {

			// Load basic search from default
			$this->BasicSearch->LoadDefault();
			if ($this->BasicSearch->Keyword != "")
				$sSrchBasic = $this->BasicSearchWhere();
		}

		// Build search criteria
		ew_AddFilter($this->SearchWhere, $sSrchAdvanced);
		ew_AddFilter($this->SearchWhere, $sSrchBasic);

		// Call Recordset_Searching event
		$this->Recordset_Searching($this->SearchWhere);

		// Save search criteria
		if ($this->Command == "search" && !$this->RestoreSearch) {
			$this->setSearchWhere($this->SearchWhere); // Save to Session
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} else {
			$this->SearchWhere = $this->getSearchWhere();
		}

		// Build filter
		$sFilter = "";
		if (!$Security->CanList())
			$sFilter = "(0=1)"; // Filter all records
		ew_AddFilter($sFilter, $this->DbDetailFilter);
		ew_AddFilter($sFilter, $this->SearchWhere);

		// Set up filter in session
		$this->setSessionWhere($sFilter);
		$this->CurrentFilter = "";

		// Export data only
		if (in_array($this->Export, array("html","word","excel","xml","csv","email","pdf"))) {
			$this->ExportData();
			$this->Page_Terminate(); // Terminate response
			exit();
		}
	}

	//  Exit inline mode
	function ClearInlineMode() {
		$this->setKey("id", ""); // Clear inline edit key
		$this->Jumlah->FormValue = ""; // Clear form value
		$this->LastAction = $this->CurrentAction; // Save last action
		$this->CurrentAction = ""; // Clear action
		$_SESSION[EW_SESSION_INLINE_MODE] = ""; // Clear inline mode
	}

	// Switch to Inline Edit mode
	function InlineEditMode() {
		global $Security, $Language;
		if (!$Security->CanEdit())
			$this->Page_Terminate("login.php"); // Go to login page
		$bInlineEdit = TRUE;
		if (@$_GET["id"] <> "") {
			$this->id->setQueryStringValue($_GET["id"]);
		} else {
			$bInlineEdit = FALSE;
		}
		if ($bInlineEdit) {
			if ($this->LoadRow()) {
				$this->setKey("id", $this->id->CurrentValue); // Set up inline edit key
				$_SESSION[EW_SESSION_INLINE_MODE] = "edit"; // Enable inline edit
			}
		}
	}

	// Perform update to Inline Edit record
	function InlineUpdate() {
		global $Language, $objForm, $gsFormError;
		$objForm->Index = 1; 
		$this->LoadFormValues(); // Get form values

		// Validate form
		$bInlineUpdate = TRUE;
		if (!$this->ValidateForm()) {	
			$bInlineUpdate = FALSE; // Form error, reset action
			$this->setFailureMessage($gsFormError);
		} else {
			$bInlineUpdate = FALSE;
			$rowkey = strval($objForm->GetValue($this->FormKeyName));
			if ($this->SetupKeyValues($rowkey)) { // Set up key values
				if ($this->CheckInlineEditKey()) { // Check key
					$this->SendEmail = TRUE; // Send email on update success
					$bInlineUpdate = $this->EditRow(); // Update record
				} else {
					$bInlineUpdate = FALSE;
				}
			}
		}
		if ($bInlineUpdate) { // Update success
			if ($this->getSuccessMessage() == "")
				$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Set up success message
			$this->ClearInlineMode(); // Clear inline edit mode
		} else {
			if ($this->getFailureMessage() == "")
				$this->setFailureMessage($Language->Phrase("UpdateFailed")); // Set update failed message
			$this->EventCancelled = TRUE; // Cancel event
			$this->CurrentAction = "edit"; // Stay in edit mode
		}
	}

	// Check Inline Edit key
	function CheckInlineEditKey() {

		//CheckInlineEditKey = True
		if (strval($this->getKey("id")) <> strval($this->id->CurrentValue))
			return FALSE;
		return TRUE;
	}

	// Switch to Inline Add mode
	function InlineAddMode() {
		global $Security, $Language;
		if (!$Security->CanAdd())
			$this->Page_Terminate("login.php"); // Return to login page
		$this->CurrentAction = "add";
		$_SESSION[EW_SESSION_INLINE_MODE] = "add"; // Enable inline add
	}

	// Perform update to Inline Add/Copy record
	function InlineInsert() {
		global $Language, $objForm, $gsFormError;
		$this->LoadOldRecord(); // Load old recordset
		$objForm->Index = 0;
		$this->LoadFormValues(); // Get form values

		// Validate form
		if (!$this->ValidateForm()) {
			$this->setFailureMessage($gsFormError); // Set validation error message
			$this->EventCancelled = TRUE; // Set event cancelled
			$this->CurrentAction = "add"; // Stay in add mode
			return;
		}
		$this->SendEmail = TRUE; // Send email on add success
		if ($this->AddRow($this->OldRecordset)) { // Add record
			if ($this->getSuccessMessage() == "")
				$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up add success message
			$this->ClearInlineMode(); // Clear inline add mode
		} else { // Add failed
			$this->EventCancelled = TRUE; // Set event cancelled
			$this->CurrentAction = "add"; // Stay in add mode
		}
	}

	// Build filter for all keys
	function BuildKeyFilter() {
		global $objForm;
		$sWrkFilter = "";

		// Update row index and get row key
		$rowindex = 1;
		$objForm->Index = $rowindex;
		$sThisKey = strval($objForm->GetValue($this->FormKeyName));
		while ($sThisKey <> "") {
			if ($this->SetupKeyValues($sThisKey)) {
				$sFilter = $this->KeyFilter();
				if ($sWrkFilter <> "") $sWrkFilter .= " OR ";
				$sWrkFilter .= $sFilter;
			} else {
				$sWrkFilter = "0=1";
				break;
			}

			// Update row index and get row key
			$rowindex++; // Next row
			$objForm->Index = $rowindex;
			$sThisKey = strval($objForm->GetValue($this->FormKeyName));
		}
		return $sWrkFilter;
	}

	// Set up key values
	function SetupKeyValues($key) {
		$arrKeyFlds = explode($GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"], $key);
		if (count($arrKeyFlds) >= 1) {
			$this->id->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->id->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Return basic search SQL
	function BasicSearchSQL($Keyword) {
		$sKeyword = ew_AdjustSql($Keyword);
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->Company, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->Person, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->Status, $Keyword);
		return $sWhere;
	}

	// Build basic search SQL
	function BuildBasicSearchSql(&$Where, &$Fld, $Keyword) {
		if ($Keyword == EW_NULL_VALUE) {
			$sWrk = $Fld->FldExpression . " IS NULL";
		} elseif ($Keyword == EW_NOT_NULL_VALUE) {
			$sWrk = $Fld->FldExpression . " IS NOT NULL";
		} else {
			$sFldExpression = ($Fld->FldVirtualExpression <> $Fld->FldExpression) ? $Fld->FldVirtualExpression : $Fld->FldBasicSearchExpression;
			$sWrk = $sFldExpression . ew_Like(ew_QuotedValue("%" . $Keyword . "%", EW_DATATYPE_STRING));
		}
		if ($Where <> "") $Where .= " OR ";
		$Where .= $sWrk;
	}

	// Return basic search WHERE clause based on search keyword and type
	function BasicSearchWhere() {
		global $Security;
		$sSearchStr = "";
		if (!$Security->CanSearch()) return "";
		$sSearchKeyword = $this->BasicSearch->Keyword;
		$sSearchType = $this->BasicSearch->Type;
		if ($sSearchKeyword <> "") {
			$sSearch = trim($sSearchKeyword);
			if ($sSearchType <> "=") {
				while (strpos($sSearch, "  ") !== FALSE)
					$sSearch = str_replace("  ", " ", $sSearch);
				$arKeyword = explode(" ", trim($sSearch));
				foreach ($arKeyword as $sKeyword) {
					if ($sSearchStr <> "") $sSearchStr .= " " . $sSearchType . " ";
					$sSearchStr .= "(" . $this->BasicSearchSQL($sKeyword) . ")";
				}
			} else {
				$sSearchStr = $this->BasicSearchSQL($sSearch);
			}
			$this->Command = "search";
		}
		if ($this->Command == "search") {
			$this->BasicSearch->setKeyword($sSearchKeyword);
			$this->BasicSearch->setType($sSearchType);
		}
		return $sSearchStr;
	}

	// Check if search parm exists
	function CheckSearchParms() {

		// Check basic search
		if ($this->BasicSearch->IssetSession())
			return TRUE;
		return FALSE;
	}

	// Clear all search parameters
	function ResetSearchParms() {

		// Clear search WHERE clause
		$this->SearchWhere = "";
		$this->setSearchWhere($this->SearchWhere);

		// Clear basic search parameters
		$this->ResetBasicSearchParms();
	}

	// Load advanced search default values
	function LoadAdvancedSearchDefault() {
		return FALSE;
	}

	// Clear all basic search parameters
	function ResetBasicSearchParms() {
		$this->BasicSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for Ctrl pressed
		$bCtrl = (@$_GET["ctrl"] <> "");

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->id, $bCtrl); // id
			$this->UpdateSort($this->Company, $bCtrl); // Company
			$this->UpdateSort($this->Person, $bCtrl); // Person
			$this->UpdateSort($this->Tanggal, $bCtrl); // Tanggal
			$this->UpdateSort($this->Jumlah, $bCtrl); // Jumlah
			$this->UpdateSort($this->Status, $bCtrl); // Status
			$this->setStartRecordNumber(1); // Reset start position
		}
	}

	// Load sort order parameters
	function LoadSortOrder() {
		$sOrderBy = $this->getSessionOrderBy(); // Get ORDER BY from Session
		if ($sOrderBy == "") {
			if ($this->SqlOrderBy() <> "") {
				$sOrderBy = $this->SqlOrderBy();
				$this->setSessionOrderBy($sOrderBy);
			}
		}
	}

	// Reset command
	// - cmd=reset (Reset search parameters)
	// - cmd=resetall (Reset search and master/detail parameters)
	// - cmd=resetsort (Reset sort parameters)
	function ResetCmd() {

		// Check if reset command
		if (substr($this->Command,0,5) == "reset") {

			// Reset search criteria
			if ($this->Command == "reset" || $this->Command == "resetall")
				$this->ResetSearchParms();

			// Reset sorting order
			if ($this->Command == "resetsort") {
				$sOrderBy = "";
				$this->setSessionOrderBy($sOrderBy);
				$this->id->setSort("");
				$this->Company->setSort("");
				$this->Person->setSort("");
				$this->Tanggal->setSort("");
				$this->Jumlah->setSort("");
				$this->Status->setSort("");
			}

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Set up list options
	function SetupListOptions() {
		global $Security, $Language;

		// Add group option item
		$item = &$this->ListOptions->Add($this->ListOptions->GroupOptionName);
		$item->Body = "";
		$item->OnLeft = TRUE;
		$item->Visible = FALSE;

		// "view"
		$item = &$this->ListOptions->Add("view");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanView();
		$item->OnLeft = TRUE;

		// "edit"
		$item = &$this->ListOptions->Add("edit");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanEdit();
		$item->OnLeft = TRUE;

		// "copy"
		$item = &$this->ListOptions->Add("copy");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanAdd() && ($this->CurrentAction == "add");
		$item->OnLeft = TRUE;

		// "delete"
		$item = &$this->ListOptions->Add("delete");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanDelete();
		$item->OnLeft = TRUE;

		// "checkbox"
		$item = &$this->ListOptions->Add("checkbox");
		$item->Visible = FALSE;
		$item->OnLeft = TRUE;
		$item->Header = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key\" id=\"key\" onclick=\"ew_SelectAllKey(this);\"></label>";
		$item->MoveTo(0);
		$item->ShowInDropDown = FALSE;
		$item->ShowInButtonGroup = FALSE;

		// Drop down button for ListOptions
		$this->ListOptions->UseDropDownButton = FALSE;
		$this->ListOptions->DropDownButtonPhrase = $Language->Phrase("ButtonListOptions");
		$this->ListOptions->UseButtonGroup = FALSE;
		$this->ListOptions->ButtonClass = "btn-small"; // Class for button group

		// Call ListOptions_Load event
		$this->ListOptions_Load();
		$item = &$this->ListOptions->GetItem($this->ListOptions->GroupOptionName);
		$item->Visible = $this->ListOptions->GroupOptionVisible();
	}

	// Render list options
	function RenderListOptions() {
		global $Security, $Language, $objForm;
		$this->ListOptions->LoadDefault();

		// Set up row action and key
		if (is_numeric($this->RowIndex) && $this->CurrentMode <> "view") {
			$objForm->Index = $this->RowIndex;
			$ActionName = str_replace("k_", "k" . $this->RowIndex . "_", $this->FormActionName);
			$OldKeyName = str_replace("k_", "k" . $this->RowIndex . "_", $this->FormOldKeyName);
			$KeyName = str_replace("k_", "k" . $this->RowIndex . "_", $this->FormKeyName);
			$BlankRowName = str_replace("k_", "k" . $this->RowIndex . "_", $this->FormBlankRowName);
			if ($this->RowAction <> "")
				$this->MultiSelectKey .= "<input type=\"hidden\" name=\"" . $ActionName . "\" id=\"" . $ActionName . "\" value=\"" . $this->RowAction . "\">";
			if ($this->RowAction == "delete") {
				$rowkey = $objForm->GetValue($this->FormKeyName);
				$this->SetupKeyValues($rowkey);
			}
			if ($this->RowAction == "insert" && $this->CurrentAction == "F" && $this->EmptyRow())
				$this->MultiSelectKey .= "<input type=\"hidden\" name=\"" . $BlankRowName . "\" id=\"" . $BlankRowName . "\" value=\"1\">";
		}

		// "copy"
		$oListOpt = &$this->ListOptions->Items["copy"];
		if (($this->CurrentAction == "add" || $this->CurrentAction == "copy") && $this->RowType == EW_ROWTYPE_ADD) { // Inline Add/Copy
			$this->ListOptions->CustomItem = "copy"; // Show copy column only
			$oListOpt->Body = "<div" . (($oListOpt->OnLeft) ? " style=\"text-align: right\"" : "") . ">" .
				"<a class=\"ewGridLink ewInlineInsert\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("InsertLink")) . "\" href=\"\" onclick=\"return ewForms(this).Submit();\">" . $Language->Phrase("InsertLink") . "</a>&nbsp;" .
				"<a class=\"ewGridLink ewInlineCancel\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("CancelLink")) . "\" href=\"" . $this->PageUrl() . "a=cancel\">" . $Language->Phrase("CancelLink") . "</a>" .
				"<input type=\"hidden\" name=\"a_list\" id=\"a_list\" value=\"insert\"></div>";
			return;
		}

		// "edit"
		$oListOpt = &$this->ListOptions->Items["edit"];
		if ($this->CurrentAction == "edit" && $this->RowType == EW_ROWTYPE_EDIT) { // Inline-Edit
			$this->ListOptions->CustomItem = "edit"; // Show edit column only
				$oListOpt->Body = "<div" . (($oListOpt->OnLeft) ? " style=\"text-align: right\"" : "") . ">" .
					"<a class=\"ewGridLink ewInlineUpdate\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("UpdateLink")) . "\" href=\"\" onclick=\"return ewForms(this).Submit('" . ew_GetHashUrl($this->PageName(), $this->PageObjName . "_row_" . $this->RowCnt) . "');\">" . $Language->Phrase("UpdateLink") . "</a>&nbsp;" .
					"<a class=\"ewGridLink ewInlineCancel\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("CancelLink")) . "\" href=\"" . $this->PageUrl() . "a=cancel\">" . $Language->Phrase("CancelLink") . "</a>" .
					"<input type=\"hidden\" name=\"a_list\" id=\"a_list\" value=\"update\"></div>";
			$oListOpt->Body .= "<input type=\"hidden\" name=\"k" . $this->RowIndex . "_key\" id=\"k" . $this->RowIndex . "_key\" value=\"" . ew_HtmlEncode($this->id->CurrentValue) . "\">";
			return;
		}

		// "view"
		$oListOpt = &$this->ListOptions->Items["view"];
		if ($Security->CanView())
			$oListOpt->Body = "<a class=\"ewRowLink ewView\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewLink")) . "\" href=\"" . ew_HtmlEncode($this->ViewUrl) . "\">" . $Language->Phrase("ViewLink") . "</a>";
		else
			$oListOpt->Body = "";

		// "edit"
		$oListOpt = &$this->ListOptions->Items["edit"];
		if ($Security->CanEdit()) {
			$oListOpt->Body = "<a class=\"ewRowLink ewEdit\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("EditLink")) . "\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" . $Language->Phrase("EditLink") . "</a>";
			$oListOpt->Body .= "<span class=\"ewSeparator\">&nbsp;|&nbsp;</span>";
			$oListOpt->Body .= "<a class=\"ewRowLink ewInlineEdit\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("InlineEditLink")) . "\" href=\"" . ew_HtmlEncode(ew_GetHashUrl($this->InlineEditUrl, $this->PageObjName . "_row_" . $this->RowCnt)) . "\">" . $Language->Phrase("InlineEditLink") . "</a>";
		} else {
			$oListOpt->Body = "";
		}

		// "delete"
		$oListOpt = &$this->ListOptions->Items["delete"];
		if ($Security->CanDelete())
			$oListOpt->Body = "<a class=\"ewRowLink ewDelete\"" . "" . " data-caption=\"" . ew_HtmlTitle($Language->Phrase("DeleteLink")) . "\" href=\"" . ew_HtmlEncode($this->DeleteUrl) . "\">" . $Language->Phrase("DeleteLink") . "</a>";
		else
			$oListOpt->Body = "";

		// "checkbox"
		$oListOpt = &$this->ListOptions->Items["checkbox"];
		$oListOpt->Body = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->id->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event, this);'></label>";
		$this->RenderListOptionsExt();

		// Call ListOptions_Rendered event
		$this->ListOptions_Rendered();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = $options["addedit"];

		// Add
		$item = &$option->Add("add");
		$item->Body = "<a class=\"ewAddEdit ewAdd\" href=\"" . ew_HtmlEncode($this->AddUrl) . "\">" . $Language->Phrase("AddLink") . "</a>";
		$item->Visible = ($this->AddUrl <> "" && $Security->CanAdd());

		// Inline Add
		$item = &$option->Add("inlineadd");
		$item->Body = "<a class=\"ewAddEdit ewInlineAdd\" href=\"" . ew_HtmlEncode($this->InlineAddUrl) . "\">" .$Language->Phrase("InlineAddLink") . "</a>";
		$item->Visible = ($this->InlineAddUrl <> "" && $Security->CanAdd());
		$option = $options["action"];

		// Set up options default
		foreach ($options as &$option) {
			$option->UseDropDownButton = FALSE;
			$option->UseButtonGroup = TRUE;
			$option->ButtonClass = "btn-small"; // Class for button group
			$item = &$option->Add($option->GroupOptionName);
			$item->Body = "";
			$item->Visible = FALSE;
		}
		$options["addedit"]->DropDownButtonPhrase = $Language->Phrase("ButtonAddEdit");
		$options["detail"]->DropDownButtonPhrase = $Language->Phrase("ButtonDetails");
		$options["action"]->DropDownButtonPhrase = $Language->Phrase("ButtonActions");
	}

	// Render other options
	function RenderOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
			$option = &$options["action"];
			foreach ($this->CustomActions as $action => $name) {

				// Add custom action
				$item = &$option->Add("custom_" . $action);
				$item->Body = "<a class=\"ewAction ewCustomAction\" href=\"\" onclick=\"ew_SubmitSelected(document.ftamuviplist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
			}

			// Hide grid edit, multi-delete and multi-update
			if ($this->TotalRecs <= 0) {
				$option = &$options["addedit"];
				$item = &$option->GetItem("gridedit");
				if ($item) $item->Visible = FALSE;
				$option = &$options["action"];
				$item = &$option->GetItem("multidelete");
				if ($item) $item->Visible = FALSE;
				$item = &$option->GetItem("multiupdate");
				if ($item) $item->Visible = FALSE;
			}
	}

	// Process custom action
	function ProcessCustomAction() {
		global $conn, $Language, $Security;
		$sFilter = $this->GetKeyFilter();
		$UserAction = @$_POST["useraction"];
		if ($sFilter <> "" && $UserAction <> "") {
			$this->CurrentFilter = $sFilter;
			$sSql = $this->SQL();
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$rs = $conn->Execute($sSql);
			$conn->raiseErrorFn = '';
			$rsuser = ($rs) ? $rs->GetRows() : array();
			if ($rs)
				$rs->Close();

			// Call row custom action event
			if (count($rsuser) > 0) {
				$conn->BeginTrans();
				foreach ($rsuser as $row) {
					$Processed = $this->Row_CustomAction($UserAction, $row);
					if (!$Processed) break;
				}
				if ($Processed) {
					$conn->CommitTrans(); // Commit the changes
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage(str_replace('%s', $UserAction, $Language->Phrase("CustomActionCompleted"))); // Set up success message
				} else {
					$conn->RollbackTrans(); // Rollback changes

					// Set up error message
					if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

						// Use the message, do nothing
					} elseif ($this->CancelMessage <> "") {
						$this->setFailureMessage($this->CancelMessage);
						$this->CancelMessage = "";
					} else {
						$this->setFailureMessage(str_replace('%s', $UserAction, $Language->Phrase("CustomActionCancelled")));
					}
				}
			}
		}
	}

	function RenderListOptionsExt() {
		global $Security, $Language;
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

	// Load default values
	function LoadDefaultValues() {
		$this->id->CurrentValue = NULL;
		$this->id->OldValue = $this->id->CurrentValue;
		$this->Company->CurrentValue = NULL;
		$this->Company->OldValue = $this->Company->CurrentValue;
		$this->Person->CurrentValue = NULL;
		$this->Person->OldValue = $this->Person->CurrentValue;
		$this->Tanggal->CurrentValue = NULL;
		$this->Tanggal->OldValue = $this->Tanggal->CurrentValue;
		$this->Jumlah->CurrentValue = NULL;
		$this->Jumlah->OldValue = $this->Jumlah->CurrentValue;
		$this->Status->CurrentValue = "YES";
	}

	// Load basic search values
	function LoadBasicSearchValues() {
		$this->BasicSearch->Keyword = @$_GET[EW_TABLE_BASIC_SEARCH];
		if ($this->BasicSearch->Keyword <> "") $this->Command = "search";
		$this->BasicSearch->Type = @$_GET[EW_TABLE_BASIC_SEARCH_TYPE];
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->id->FldIsDetailKey && $this->CurrentAction <> "gridadd" && $this->CurrentAction <> "add")
			$this->id->setFormValue($objForm->GetValue("x_id"));
		if (!$this->Company->FldIsDetailKey) {
			$this->Company->setFormValue($objForm->GetValue("x_Company"));
		}
		if (!$this->Person->FldIsDetailKey) {
			$this->Person->setFormValue($objForm->GetValue("x_Person"));
		}
		if (!$this->Tanggal->FldIsDetailKey) {
			$this->Tanggal->setFormValue($objForm->GetValue("x_Tanggal"));
			$this->Tanggal->CurrentValue = ew_UnFormatDateTime($this->Tanggal->CurrentValue, 7);
		}
		if (!$this->Jumlah->FldIsDetailKey) {
			$this->Jumlah->setFormValue($objForm->GetValue("x_Jumlah"));
		}
		if (!$this->Status->FldIsDetailKey) {
			$this->Status->setFormValue($objForm->GetValue("x_Status"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		if ($this->CurrentAction <> "gridadd" && $this->CurrentAction <> "add")
			$this->id->CurrentValue = $this->id->FormValue;
		$this->Company->CurrentValue = $this->Company->FormValue;
		$this->Person->CurrentValue = $this->Person->FormValue;
		$this->Tanggal->CurrentValue = $this->Tanggal->FormValue;
		$this->Tanggal->CurrentValue = ew_UnFormatDateTime($this->Tanggal->CurrentValue, 7);
		$this->Jumlah->CurrentValue = $this->Jumlah->FormValue;
		$this->Status->CurrentValue = $this->Status->FormValue;
	}

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

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("id")) <> "")
			$this->id->CurrentValue = $this->getKey("id"); // id
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
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

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
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// id
			// Company

			$this->Company->EditCustomAttributes = "";
			$this->Company->EditValue = ew_HtmlEncode($this->Company->CurrentValue);
			$this->Company->PlaceHolder = ew_RemoveHtml($this->Company->FldCaption());

			// Person
			$this->Person->EditCustomAttributes = "";
			$this->Person->EditValue = ew_HtmlEncode($this->Person->CurrentValue);
			$this->Person->PlaceHolder = ew_RemoveHtml($this->Person->FldCaption());

			// Tanggal
			$this->Tanggal->EditCustomAttributes = "";
			$this->Tanggal->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->Tanggal->CurrentValue, 7));
			$this->Tanggal->PlaceHolder = ew_RemoveHtml($this->Tanggal->FldCaption());

			// Jumlah
			$this->Jumlah->EditCustomAttributes = "";
			$this->Jumlah->EditValue = ew_HtmlEncode($this->Jumlah->CurrentValue);
			$this->Jumlah->PlaceHolder = ew_RemoveHtml($this->Jumlah->FldCaption());
			if (strval($this->Jumlah->EditValue) <> "" && is_numeric($this->Jumlah->EditValue)) $this->Jumlah->EditValue = ew_FormatNumber($this->Jumlah->EditValue, -2, -1, -2, 0);

			// Status
			$this->Status->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->Status->FldTagValue(1), $this->Status->FldTagCaption(1) <> "" ? $this->Status->FldTagCaption(1) : $this->Status->FldTagValue(1));
			$arwrk[] = array($this->Status->FldTagValue(2), $this->Status->FldTagCaption(2) <> "" ? $this->Status->FldTagCaption(2) : $this->Status->FldTagValue(2));
			$this->Status->EditValue = $arwrk;

			// Edit refer script
			// id

			$this->id->HrefValue = "";

			// Company
			$this->Company->HrefValue = "";

			// Person
			$this->Person->HrefValue = "";

			// Tanggal
			$this->Tanggal->HrefValue = "";

			// Jumlah
			$this->Jumlah->HrefValue = "";

			// Status
			$this->Status->HrefValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// id
			$this->id->EditCustomAttributes = "";
			$this->id->EditValue = $this->id->CurrentValue;
			$this->id->ViewCustomAttributes = "";

			// Company
			$this->Company->EditCustomAttributes = "";
			$this->Company->EditValue = ew_HtmlEncode($this->Company->CurrentValue);
			$this->Company->PlaceHolder = ew_RemoveHtml($this->Company->FldCaption());

			// Person
			$this->Person->EditCustomAttributes = "";
			$this->Person->EditValue = ew_HtmlEncode($this->Person->CurrentValue);
			$this->Person->PlaceHolder = ew_RemoveHtml($this->Person->FldCaption());

			// Tanggal
			$this->Tanggal->EditCustomAttributes = "";
			$this->Tanggal->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->Tanggal->CurrentValue, 7));
			$this->Tanggal->PlaceHolder = ew_RemoveHtml($this->Tanggal->FldCaption());

			// Jumlah
			$this->Jumlah->EditCustomAttributes = "";
			$this->Jumlah->EditValue = ew_HtmlEncode($this->Jumlah->CurrentValue);
			$this->Jumlah->PlaceHolder = ew_RemoveHtml($this->Jumlah->FldCaption());
			if (strval($this->Jumlah->EditValue) <> "" && is_numeric($this->Jumlah->EditValue)) $this->Jumlah->EditValue = ew_FormatNumber($this->Jumlah->EditValue, -2, -1, -2, 0);

			// Status
			$this->Status->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->Status->FldTagValue(1), $this->Status->FldTagCaption(1) <> "" ? $this->Status->FldTagCaption(1) : $this->Status->FldTagValue(1));
			$arwrk[] = array($this->Status->FldTagValue(2), $this->Status->FldTagCaption(2) <> "" ? $this->Status->FldTagCaption(2) : $this->Status->FldTagValue(2));
			$this->Status->EditValue = $arwrk;

			// Edit refer script
			// id

			$this->id->HrefValue = "";

			// Company
			$this->Company->HrefValue = "";

			// Person
			$this->Person->HrefValue = "";

			// Tanggal
			$this->Tanggal->HrefValue = "";

			// Jumlah
			$this->Jumlah->HrefValue = "";

			// Status
			$this->Status->HrefValue = "";
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
		if (!ew_CheckEuroDate($this->Tanggal->FormValue)) {
			ew_AddMessage($gsFormError, $this->Tanggal->FldErrMsg());
		}
		if (!$this->Jumlah->FldIsDetailKey && !is_null($this->Jumlah->FormValue) && $this->Jumlah->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->Jumlah->FldCaption());
		}
		if (!ew_CheckNumber($this->Jumlah->FormValue)) {
			ew_AddMessage($gsFormError, $this->Jumlah->FldErrMsg());
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

			// Company
			$this->Company->SetDbValueDef($rsnew, $this->Company->CurrentValue, NULL, $this->Company->ReadOnly);

			// Person
			$this->Person->SetDbValueDef($rsnew, $this->Person->CurrentValue, NULL, $this->Person->ReadOnly);

			// Tanggal
			$this->Tanggal->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->Tanggal->CurrentValue, 7), NULL, $this->Tanggal->ReadOnly);

			// Jumlah
			$this->Jumlah->SetDbValueDef($rsnew, $this->Jumlah->CurrentValue, NULL, $this->Jumlah->ReadOnly);

			// Status
			$this->Status->SetDbValueDef($rsnew, $this->Status->CurrentValue, NULL, $this->Status->ReadOnly);

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

	// Add record
	function AddRow($rsold = NULL) {
		global $conn, $Language, $Security;

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// Company
		$this->Company->SetDbValueDef($rsnew, $this->Company->CurrentValue, NULL, FALSE);

		// Person
		$this->Person->SetDbValueDef($rsnew, $this->Person->CurrentValue, NULL, FALSE);

		// Tanggal
		$this->Tanggal->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->Tanggal->CurrentValue, 7), NULL, FALSE);

		// Jumlah
		$this->Jumlah->SetDbValueDef($rsnew, $this->Jumlah->CurrentValue, NULL, FALSE);

		// Status
		$this->Status->SetDbValueDef($rsnew, $this->Status->CurrentValue, NULL, strval($this->Status->CurrentValue) == "");

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
			$this->id->setDbValue($conn->Insert_ID());
			$rsnew['id'] = $this->id->DbValue;
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
		}
		return $AddRow;
	}

	// Set up export options
	function SetupExportOptions() {
		global $Language;

		// Printer friendly
		$item = &$this->ExportOptions->Add("print");
		$item->Body = "<a href=\"" . $this->ExportPrintUrl . "\" class=\"ewExportLink ewPrint\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("PrinterFriendlyText")) . "\">" . $Language->Phrase("PrinterFriendly") . "</a>";
		$item->Visible = TRUE;

		// Export to Excel
		$item = &$this->ExportOptions->Add("excel");
		$item->Body = "<a href=\"" . $this->ExportExcelUrl . "\" class=\"ewExportLink ewExcel\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToExcelText")) . "\">" . $Language->Phrase("ExportToExcel") . "</a>";
		$item->Visible = TRUE;

		// Export to Word
		$item = &$this->ExportOptions->Add("word");
		$item->Body = "<a href=\"" . $this->ExportWordUrl . "\" class=\"ewExportLink ewWord\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToWordText")) . "\">" . $Language->Phrase("ExportToWord") . "</a>";
		$item->Visible = FALSE;

		// Export to Html
		$item = &$this->ExportOptions->Add("html");
		$item->Body = "<a href=\"" . $this->ExportHtmlUrl . "\" class=\"ewExportLink ewHtml\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToHtmlText")) . "\">" . $Language->Phrase("ExportToHtml") . "</a>";
		$item->Visible = FALSE;

		// Export to Xml
		$item = &$this->ExportOptions->Add("xml");
		$item->Body = "<a href=\"" . $this->ExportXmlUrl . "\" class=\"ewExportLink ewXml\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToXmlText")) . "\">" . $Language->Phrase("ExportToXml") . "</a>";
		$item->Visible = FALSE;

		// Export to Csv
		$item = &$this->ExportOptions->Add("csv");
		$item->Body = "<a href=\"" . $this->ExportCsvUrl . "\" class=\"ewExportLink ewCsv\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToCsvText")) . "\">" . $Language->Phrase("ExportToCsv") . "</a>";
		$item->Visible = FALSE;

		// Export to Pdf
		$item = &$this->ExportOptions->Add("pdf");
		$item->Body = "<a href=\"" . $this->ExportPdfUrl . "\" class=\"ewExportLink ewPdf\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToPDFText")) . "\">" . $Language->Phrase("ExportToPDF") . "</a>";
		$item->Visible = FALSE;

		// Export to Email
		$item = &$this->ExportOptions->Add("email");
		$item->Body = "<a id=\"emf_tamuvip\" href=\"javascript:void(0);\" class=\"ewExportLink ewEmail\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_tamuvip',hdr:ewLanguage.Phrase('ExportToEmail'),f:document.ftamuviplist,sel:false});\">" . $Language->Phrase("ExportToEmail") . "</a>";
		$item->Visible = FALSE;

		// Drop down button for export
		$this->ExportOptions->UseDropDownButton = TRUE;
		$this->ExportOptions->DropDownButtonPhrase = $Language->Phrase("ButtonExport");

		// Add group option item
		$item = &$this->ExportOptions->Add($this->ExportOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;
	}

	// Export data in HTML/CSV/Word/Excel/XML/Email/PDF format
	function ExportData() {
		$utf8 = (strtolower(EW_CHARSET) == "utf-8");
		$bSelectLimit = EW_SELECT_LIMIT;

		// Load recordset
		if ($bSelectLimit) {
			$this->TotalRecs = $this->SelectRecordCount();
		} else {
			if ($rs = $this->LoadRecordset())
				$this->TotalRecs = $rs->RecordCount();
		}
		$this->StartRec = 1;

		// Export all
		if ($this->ExportAll) {
			set_time_limit(EW_EXPORT_ALL_TIME_LIMIT);
			$this->DisplayRecs = $this->TotalRecs;
			$this->StopRec = $this->TotalRecs;
		} else { // Export one page only
			$this->SetUpStartRec(); // Set up start record position

			// Set the last record to display
			if ($this->DisplayRecs <= 0) {
				$this->StopRec = $this->TotalRecs;
			} else {
				$this->StopRec = $this->StartRec + $this->DisplayRecs - 1;
			}
		}
		if ($bSelectLimit)
			$rs = $this->LoadRecordset($this->StartRec-1, $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs);
		if (!$rs) {
			header("Content-Type:"); // Remove header
			header("Content-Disposition:");
			$this->ShowMessage();
			return;
		}
		$ExportDoc = ew_ExportDocument($this, "h");
		$ParentTable = "";
		if ($bSelectLimit) {
			$StartRec = 1;
			$StopRec = $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs;
		} else {
			$StartRec = $this->StartRec;
			$StopRec = $this->StopRec;
		}
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		$ExportDoc->Text .= $sHeader;
		$this->ExportDocument($ExportDoc, $rs, $StartRec, $StopRec, "");
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		$ExportDoc->Text .= $sFooter;

		// Close recordset
		$rs->Close();

		// Export header and footer
		$ExportDoc->ExportHeaderAndFooter();

		// Clean output buffer
		if (!EW_DEBUG_ENABLED && ob_get_length())
			ob_end_clean();

		// Write debug message if enabled
		if (EW_DEBUG_ENABLED)
			echo ew_DebugMsg();

		// Output data
		$ExportDoc->Export();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = ew_CurrentUrl();
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$Breadcrumb->Add("list", $this->TableVar, $url, $this->TableVar, TRUE);
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

	// ListOptions Load event
	function ListOptions_Load() {

		// Example:
		//$opt = &$this->ListOptions->Add("new");
		//$opt->Header = "xxx";
		//$opt->OnLeft = TRUE; // Link on left
		//$opt->MoveTo(0); // Move to first column

	}

	// ListOptions Rendered event
	function ListOptions_Rendered() {

		// Example: 
		//$this->ListOptions->Items["new"]->Body = "xxx";

	}

	// Row Custom Action event
	function Row_CustomAction($action, $row) {

		// Return FALSE to abort
		return TRUE;
	}
}
?>
<?php ew_Header(TRUE) ?>
<?php

// Create page object
if (!isset($tamuvip_list)) $tamuvip_list = new ctamuvip_list();

// Page init
$tamuvip_list->Page_Init();

// Page main
$tamuvip_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$tamuvip_list->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($tamuvip->Export == "") { ?>
<script type="text/javascript">

// Page object
var tamuvip_list = new ew_Page("tamuvip_list");
tamuvip_list.PageID = "list"; // Page ID
var EW_PAGE_ID = tamuvip_list.PageID; // For backward compatibility

// Form object
var ftamuviplist = new ew_Form("ftamuviplist");
ftamuviplist.FormKeyCountName = '<?php echo $tamuvip_list->FormKeyCountName ?>';

// Validate form
ftamuviplist.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_Tanggal");
			if (elm && !ew_CheckEuroDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($tamuvip->Tanggal->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Jumlah");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($tamuvip->Jumlah->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_Jumlah");
			if (elm && !ew_CheckNumber(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($tamuvip->Jumlah->FldErrMsg()) ?>");

			// Set up row object
			ew_ElementsToRow(fobj);

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}
	return true;
}

// Form_CustomValidate event
ftamuviplist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
ftamuviplist.ValidateRequired = true;
<?php } else { ?>
ftamuviplist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

var ftamuviplistsrch = new ew_Form("ftamuviplistsrch");
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($tamuvip->Export == "") { ?>
<?php $Breadcrumb->Render(); ?>
<?php } ?>
<?php if ($tamuvip_list->ExportOptions->Visible()) { ?>
<div class="ewListExportOptions"><?php $tamuvip_list->ExportOptions->Render("body") ?></div>
<?php } ?>
<?php
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$tamuvip_list->TotalRecs = $tamuvip->SelectRecordCount();
	} else {
		if ($tamuvip_list->Recordset = $tamuvip_list->LoadRecordset())
			$tamuvip_list->TotalRecs = $tamuvip_list->Recordset->RecordCount();
	}
	$tamuvip_list->StartRec = 1;
	if ($tamuvip_list->DisplayRecs <= 0 || ($tamuvip->Export <> "" && $tamuvip->ExportAll)) // Display all records
		$tamuvip_list->DisplayRecs = $tamuvip_list->TotalRecs;
	if (!($tamuvip->Export <> "" && $tamuvip->ExportAll))
		$tamuvip_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$tamuvip_list->Recordset = $tamuvip_list->LoadRecordset($tamuvip_list->StartRec-1, $tamuvip_list->DisplayRecs);
$tamuvip_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($tamuvip->Export == "" && $tamuvip->CurrentAction == "") { ?>
<form name="ftamuviplistsrch" id="ftamuviplistsrch" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<div class="accordion ewDisplayTable ewSearchTable" id="ftamuviplistsrch_SearchGroup">
	<div class="accordion-group">
		<div class="accordion-heading">
<a class="accordion-toggle" data-toggle="collapse" data-parent="#ftamuviplistsrch_SearchGroup" href="#ftamuviplistsrch_SearchBody"><?php echo $Language->Phrase("Search") ?></a>
		</div>
		<div id="ftamuviplistsrch_SearchBody" class="accordion-body collapse in">
			<div class="accordion-inner">
<div id="ftamuviplistsrch_SearchPanel">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="tamuvip">
<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="btn-group ewButtonGroup">
	<div class="input-append">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="input-large" value="<?php echo ew_HtmlEncode($tamuvip_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Search")) ?>">
	<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?></button>
	</div>
	</div>
	<div class="btn-group ewButtonGroup">
	<a class="btn ewShowAll" href="<?php echo $tamuvip_list->PageUrl() ?>cmd=reset"><?php echo $Language->Phrase("ShowAll") ?></a>
	</div>
</div>
<div id="xsr_2" class="ewRow">
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="="<?php if ($tamuvip_list->BasicSearch->getType() == "=") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("ExactPhrase") ?></label>
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND"<?php if ($tamuvip_list->BasicSearch->getType() == "AND") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AllWord") ?></label>
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR"<?php if ($tamuvip_list->BasicSearch->getType() == "OR") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AnyWord") ?></label>
</div>
</div>
</div>
			</div>
		</div>
	</div>
</div>
</form>
<?php } ?>
<?php } ?>
<?php $tamuvip_list->ShowPageHeader(); ?>
<?php
$tamuvip_list->ShowMessage();
?>
<table class="ewGrid"><tr><td class="ewGridContent">
<?php if ($tamuvip->Export == "") { ?>
<div class="ewGridUpperPanel">
<?php if ($tamuvip->CurrentAction <> "gridadd" && $tamuvip->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($tamuvip_list->Pager)) $tamuvip_list->Pager = new cPrevNextPager($tamuvip_list->StartRec, $tamuvip_list->DisplayRecs, $tamuvip_list->TotalRecs) ?>
<?php if ($tamuvip_list->Pager->RecordCount > 0) { ?>
<table class="ewStdTable"><tbody><tr><td>
	<?php echo $Language->Phrase("Page") ?>&nbsp;
<div class="input-prepend input-append">
<!--first page button-->
	<?php if ($tamuvip_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $tamuvip_list->PageUrl() ?>start=<?php echo $tamuvip_list->Pager->FirstButton->Start ?>"><i class="icon-step-backward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-step-backward"></i></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($tamuvip_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $tamuvip_list->PageUrl() ?>start=<?php echo $tamuvip_list->Pager->PrevButton->Start ?>"><i class="icon-prev"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-prev"></i></a>
	<?php } ?>
<!--current page number-->
	<input class="input-mini" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $tamuvip_list->Pager->CurrentPage ?>">
<!--next page button-->
	<?php if ($tamuvip_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $tamuvip_list->PageUrl() ?>start=<?php echo $tamuvip_list->Pager->NextButton->Start ?>"><i class="icon-play"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-play"></i></a>
	<?php } ?>
<!--last page button-->
	<?php if ($tamuvip_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $tamuvip_list->PageUrl() ?>start=<?php echo $tamuvip_list->Pager->LastButton->Start ?>"><i class="icon-step-forward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-step-forward"></i></a>
	<?php } ?>
</div>
	&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $tamuvip_list->Pager->PageCount ?>
</td>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $tamuvip_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $tamuvip_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $tamuvip_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($tamuvip_list->SearchWhere == "0=101") { ?>
	<p><?php echo $Language->Phrase("EnterSearchCriteria") ?></p>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoRecord") ?></p>
	<?php } ?>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoPermission") ?></p>
	<?php } ?>
<?php } ?>
</td>
</tr></table>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($tamuvip_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
</div>
<?php } ?>
<form name="ftamuviplist" id="ftamuviplist" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="tamuvip">
<div id="gmp_tamuvip" class="ewGridMiddlePanel">
<?php if ($tamuvip_list->TotalRecs > 0 || $tamuvip->CurrentAction == "add" || $tamuvip->CurrentAction == "copy") { ?>
<table id="tbl_tamuviplist" class="ewTable ewTableSeparate">
<?php echo $tamuvip->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$tamuvip_list->RenderListOptions();

// Render list options (header, left)
$tamuvip_list->ListOptions->Render("header", "left");
?>
<?php if ($tamuvip->id->Visible) { // id ?>
	<?php if ($tamuvip->SortUrl($tamuvip->id) == "") { ?>
		<td><div id="elh_tamuvip_id" class="tamuvip_id"><div class="ewTableHeaderCaption"><?php echo $tamuvip->id->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tamuvip->SortUrl($tamuvip->id) ?>',2);"><div id="elh_tamuvip_id" class="tamuvip_id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tamuvip->id->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tamuvip->id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tamuvip->id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tamuvip->Company->Visible) { // Company ?>
	<?php if ($tamuvip->SortUrl($tamuvip->Company) == "") { ?>
		<td><div id="elh_tamuvip_Company" class="tamuvip_Company"><div class="ewTableHeaderCaption"><?php echo $tamuvip->Company->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tamuvip->SortUrl($tamuvip->Company) ?>',2);"><div id="elh_tamuvip_Company" class="tamuvip_Company">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tamuvip->Company->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tamuvip->Company->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tamuvip->Company->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tamuvip->Person->Visible) { // Person ?>
	<?php if ($tamuvip->SortUrl($tamuvip->Person) == "") { ?>
		<td><div id="elh_tamuvip_Person" class="tamuvip_Person"><div class="ewTableHeaderCaption"><?php echo $tamuvip->Person->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tamuvip->SortUrl($tamuvip->Person) ?>',2);"><div id="elh_tamuvip_Person" class="tamuvip_Person">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tamuvip->Person->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($tamuvip->Person->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tamuvip->Person->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tamuvip->Tanggal->Visible) { // Tanggal ?>
	<?php if ($tamuvip->SortUrl($tamuvip->Tanggal) == "") { ?>
		<td><div id="elh_tamuvip_Tanggal" class="tamuvip_Tanggal"><div class="ewTableHeaderCaption"><?php echo $tamuvip->Tanggal->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tamuvip->SortUrl($tamuvip->Tanggal) ?>',2);"><div id="elh_tamuvip_Tanggal" class="tamuvip_Tanggal">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tamuvip->Tanggal->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tamuvip->Tanggal->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tamuvip->Tanggal->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tamuvip->Jumlah->Visible) { // Jumlah ?>
	<?php if ($tamuvip->SortUrl($tamuvip->Jumlah) == "") { ?>
		<td><div id="elh_tamuvip_Jumlah" class="tamuvip_Jumlah"><div class="ewTableHeaderCaption"><?php echo $tamuvip->Jumlah->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tamuvip->SortUrl($tamuvip->Jumlah) ?>',2);"><div id="elh_tamuvip_Jumlah" class="tamuvip_Jumlah">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tamuvip->Jumlah->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tamuvip->Jumlah->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tamuvip->Jumlah->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($tamuvip->Status->Visible) { // Status ?>
	<?php if ($tamuvip->SortUrl($tamuvip->Status) == "") { ?>
		<td><div id="elh_tamuvip_Status" class="tamuvip_Status"><div class="ewTableHeaderCaption"><?php echo $tamuvip->Status->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $tamuvip->SortUrl($tamuvip->Status) ?>',2);"><div id="elh_tamuvip_Status" class="tamuvip_Status">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $tamuvip->Status->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($tamuvip->Status->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($tamuvip->Status->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$tamuvip_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
	if ($tamuvip->CurrentAction == "add" || $tamuvip->CurrentAction == "copy") {
		$tamuvip_list->RowIndex = 0;
		$tamuvip_list->KeyCount = $tamuvip_list->RowIndex;
		if ($tamuvip->CurrentAction == "add")
			$tamuvip_list->LoadDefaultValues();
		if ($tamuvip->EventCancelled) // Insert failed
			$tamuvip_list->RestoreFormValues(); // Restore form values

		// Set row properties
		$tamuvip->ResetAttrs();
		$tamuvip->RowAttrs = array_merge($tamuvip->RowAttrs, array('data-rowindex'=>0, 'id'=>'r0_tamuvip', 'data-rowtype'=>EW_ROWTYPE_ADD));
		$tamuvip->RowType = EW_ROWTYPE_ADD;

		// Render row
		$tamuvip_list->RenderRow();

		// Render list options
		$tamuvip_list->RenderListOptions();
		$tamuvip_list->StartRowCnt = 0;
?>
	<tr<?php echo $tamuvip->RowAttributes() ?>>
<?php

// Render list options (body, left)
$tamuvip_list->ListOptions->Render("body", "left", $tamuvip_list->RowCnt);
?>
	<?php if ($tamuvip->id->Visible) { // id ?>
		<td>
<input type="hidden" data-field="x_id" name="o<?php echo $tamuvip_list->RowIndex ?>_id" id="o<?php echo $tamuvip_list->RowIndex ?>_id" value="<?php echo ew_HtmlEncode($tamuvip->id->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tamuvip->Company->Visible) { // Company ?>
		<td>
<span id="el<?php echo $tamuvip_list->RowCnt ?>_tamuvip_Company" class="control-group tamuvip_Company">
<input type="text" data-field="x_Company" name="x<?php echo $tamuvip_list->RowIndex ?>_Company" id="x<?php echo $tamuvip_list->RowIndex ?>_Company" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($tamuvip->Company->PlaceHolder) ?>" value="<?php echo $tamuvip->Company->EditValue ?>"<?php echo $tamuvip->Company->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_Company" name="o<?php echo $tamuvip_list->RowIndex ?>_Company" id="o<?php echo $tamuvip_list->RowIndex ?>_Company" value="<?php echo ew_HtmlEncode($tamuvip->Company->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tamuvip->Person->Visible) { // Person ?>
		<td>
<span id="el<?php echo $tamuvip_list->RowCnt ?>_tamuvip_Person" class="control-group tamuvip_Person">
<input type="text" data-field="x_Person" name="x<?php echo $tamuvip_list->RowIndex ?>_Person" id="x<?php echo $tamuvip_list->RowIndex ?>_Person" size="30" maxlength="200" placeholder="<?php echo ew_HtmlEncode($tamuvip->Person->PlaceHolder) ?>" value="<?php echo $tamuvip->Person->EditValue ?>"<?php echo $tamuvip->Person->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_Person" name="o<?php echo $tamuvip_list->RowIndex ?>_Person" id="o<?php echo $tamuvip_list->RowIndex ?>_Person" value="<?php echo ew_HtmlEncode($tamuvip->Person->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tamuvip->Tanggal->Visible) { // Tanggal ?>
		<td>
<span id="el<?php echo $tamuvip_list->RowCnt ?>_tamuvip_Tanggal" class="control-group tamuvip_Tanggal">
<input type="text" data-field="x_Tanggal" name="x<?php echo $tamuvip_list->RowIndex ?>_Tanggal" id="x<?php echo $tamuvip_list->RowIndex ?>_Tanggal" placeholder="<?php echo ew_HtmlEncode($tamuvip->Tanggal->PlaceHolder) ?>" value="<?php echo $tamuvip->Tanggal->EditValue ?>"<?php echo $tamuvip->Tanggal->EditAttributes() ?>>
<?php if (!$tamuvip->Tanggal->ReadOnly && !$tamuvip->Tanggal->Disabled && @$tamuvip->Tanggal->EditAttrs["readonly"] == "" && @$tamuvip->Tanggal->EditAttrs["disabled"] == "") { ?>
<button id="cal_x<?php echo $tamuvip_list->RowIndex ?>_Tanggal" name="cal_x<?php echo $tamuvip_list->RowIndex ?>_Tanggal" class="btn" type="button"><img src="phpimages/calendar.png" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" style="border: 0;"></button><script type="text/javascript">
ew_CreateCalendar("ftamuviplist", "x<?php echo $tamuvip_list->RowIndex ?>_Tanggal", "%d/%m/%Y");
</script>
<?php } ?>
</span>
<input type="hidden" data-field="x_Tanggal" name="o<?php echo $tamuvip_list->RowIndex ?>_Tanggal" id="o<?php echo $tamuvip_list->RowIndex ?>_Tanggal" value="<?php echo ew_HtmlEncode($tamuvip->Tanggal->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tamuvip->Jumlah->Visible) { // Jumlah ?>
		<td>
<span id="el<?php echo $tamuvip_list->RowCnt ?>_tamuvip_Jumlah" class="control-group tamuvip_Jumlah">
<input type="text" data-field="x_Jumlah" name="x<?php echo $tamuvip_list->RowIndex ?>_Jumlah" id="x<?php echo $tamuvip_list->RowIndex ?>_Jumlah" size="30" placeholder="<?php echo ew_HtmlEncode($tamuvip->Jumlah->PlaceHolder) ?>" value="<?php echo $tamuvip->Jumlah->EditValue ?>"<?php echo $tamuvip->Jumlah->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_Jumlah" name="o<?php echo $tamuvip_list->RowIndex ?>_Jumlah" id="o<?php echo $tamuvip_list->RowIndex ?>_Jumlah" value="<?php echo ew_HtmlEncode($tamuvip->Jumlah->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($tamuvip->Status->Visible) { // Status ?>
		<td>
<span id="el<?php echo $tamuvip_list->RowCnt ?>_tamuvip_Status" class="control-group tamuvip_Status">
<div id="tp_x<?php echo $tamuvip_list->RowIndex ?>_Status" class="<?php echo EW_ITEM_TEMPLATE_CLASSNAME ?>"><input type="radio" name="x<?php echo $tamuvip_list->RowIndex ?>_Status" id="x<?php echo $tamuvip_list->RowIndex ?>_Status" value="{value}"<?php echo $tamuvip->Status->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $tamuvip_list->RowIndex ?>_Status" data-repeatcolumn="5" class="ewItemList">
<?php
$arwrk = $tamuvip->Status->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tamuvip->Status->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " checked=\"checked\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;

		// Note: No spacing within the LABEL tag
?>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 1) ?>
<label class="radio"><input type="radio" data-field="x_Status" name="x<?php echo $tamuvip_list->RowIndex ?>_Status" id="x<?php echo $tamuvip_list->RowIndex ?>_Status_<?php echo $rowcntwrk ?>" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $tamuvip->Status->EditAttributes() ?>><?php echo $arwrk[$rowcntwrk][1] ?></label>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 2) ?>
<?php
	}
}
?>
</div>
</span>
<input type="hidden" data-field="x_Status" name="o<?php echo $tamuvip_list->RowIndex ?>_Status" id="o<?php echo $tamuvip_list->RowIndex ?>_Status" value="<?php echo ew_HtmlEncode($tamuvip->Status->OldValue) ?>">
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$tamuvip_list->ListOptions->Render("body", "right", $tamuvip_list->RowCnt);
?>
<script type="text/javascript">
ftamuviplist.UpdateOpts(<?php echo $tamuvip_list->RowIndex ?>);
</script>
	</tr>
<?php
}
?>
<?php
if ($tamuvip->ExportAll && $tamuvip->Export <> "") {
	$tamuvip_list->StopRec = $tamuvip_list->TotalRecs;
} else {

	// Set the last record to display
	if ($tamuvip_list->TotalRecs > $tamuvip_list->StartRec + $tamuvip_list->DisplayRecs - 1)
		$tamuvip_list->StopRec = $tamuvip_list->StartRec + $tamuvip_list->DisplayRecs - 1;
	else
		$tamuvip_list->StopRec = $tamuvip_list->TotalRecs;
}

// Restore number of post back records
if ($objForm) {
	$objForm->Index = -1;
	if ($objForm->HasValue($tamuvip_list->FormKeyCountName) && ($tamuvip->CurrentAction == "gridadd" || $tamuvip->CurrentAction == "gridedit" || $tamuvip->CurrentAction == "F")) {
		$tamuvip_list->KeyCount = $objForm->GetValue($tamuvip_list->FormKeyCountName);
		$tamuvip_list->StopRec = $tamuvip_list->StartRec + $tamuvip_list->KeyCount - 1;
	}
}
$tamuvip_list->RecCnt = $tamuvip_list->StartRec - 1;
if ($tamuvip_list->Recordset && !$tamuvip_list->Recordset->EOF) {
	$tamuvip_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $tamuvip_list->StartRec > 1)
		$tamuvip_list->Recordset->Move($tamuvip_list->StartRec - 1);
} elseif (!$tamuvip->AllowAddDeleteRow && $tamuvip_list->StopRec == 0) {
	$tamuvip_list->StopRec = $tamuvip->GridAddRowCount;
}

// Initialize aggregate
$tamuvip->RowType = EW_ROWTYPE_AGGREGATEINIT;
$tamuvip->ResetAttrs();
$tamuvip_list->RenderRow();
$tamuvip_list->EditRowCnt = 0;
if ($tamuvip->CurrentAction == "edit")
	$tamuvip_list->RowIndex = 1;
while ($tamuvip_list->RecCnt < $tamuvip_list->StopRec) {
	$tamuvip_list->RecCnt++;
	if (intval($tamuvip_list->RecCnt) >= intval($tamuvip_list->StartRec)) {
		$tamuvip_list->RowCnt++;

		// Set up key count
		$tamuvip_list->KeyCount = $tamuvip_list->RowIndex;

		// Init row class and style
		$tamuvip->ResetAttrs();
		$tamuvip->CssClass = "";
		if ($tamuvip->CurrentAction == "gridadd") {
			$tamuvip_list->LoadDefaultValues(); // Load default values
		} else {
			$tamuvip_list->LoadRowValues($tamuvip_list->Recordset); // Load row values
		}
		$tamuvip->RowType = EW_ROWTYPE_VIEW; // Render view
		if ($tamuvip->CurrentAction == "edit") {
			if ($tamuvip_list->CheckInlineEditKey() && $tamuvip_list->EditRowCnt == 0) { // Inline edit
				$tamuvip->RowType = EW_ROWTYPE_EDIT; // Render edit
			}
		}
		if ($tamuvip->CurrentAction == "edit" && $tamuvip->RowType == EW_ROWTYPE_EDIT && $tamuvip->EventCancelled) { // Update failed
			$objForm->Index = 1;
			$tamuvip_list->RestoreFormValues(); // Restore form values
		}
		if ($tamuvip->RowType == EW_ROWTYPE_EDIT) // Edit row
			$tamuvip_list->EditRowCnt++;

		// Set up row id / data-rowindex
		$tamuvip->RowAttrs = array_merge($tamuvip->RowAttrs, array('data-rowindex'=>$tamuvip_list->RowCnt, 'id'=>'r' . $tamuvip_list->RowCnt . '_tamuvip', 'data-rowtype'=>$tamuvip->RowType));

		// Render row
		$tamuvip_list->RenderRow();

		// Render list options
		$tamuvip_list->RenderListOptions();
?>
	<tr<?php echo $tamuvip->RowAttributes() ?>>
<?php

// Render list options (body, left)
$tamuvip_list->ListOptions->Render("body", "left", $tamuvip_list->RowCnt);
?>
	<?php if ($tamuvip->id->Visible) { // id ?>
		<td<?php echo $tamuvip->id->CellAttributes() ?>>
<?php if ($tamuvip->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tamuvip_list->RowCnt ?>_tamuvip_id" class="control-group tamuvip_id">
<span<?php echo $tamuvip->id->ViewAttributes() ?>>
<?php echo $tamuvip->id->EditValue ?></span>
</span>
<input type="hidden" data-field="x_id" name="x<?php echo $tamuvip_list->RowIndex ?>_id" id="x<?php echo $tamuvip_list->RowIndex ?>_id" value="<?php echo ew_HtmlEncode($tamuvip->id->CurrentValue) ?>">
<?php } ?>
<?php if ($tamuvip->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tamuvip->id->ViewAttributes() ?>>
<?php echo $tamuvip->id->ListViewValue() ?></span>
<?php } ?>
<a id="<?php echo $tamuvip_list->PageObjName . "_row_" . $tamuvip_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($tamuvip->Company->Visible) { // Company ?>
		<td<?php echo $tamuvip->Company->CellAttributes() ?>>
<?php if ($tamuvip->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tamuvip_list->RowCnt ?>_tamuvip_Company" class="control-group tamuvip_Company">
<input type="text" data-field="x_Company" name="x<?php echo $tamuvip_list->RowIndex ?>_Company" id="x<?php echo $tamuvip_list->RowIndex ?>_Company" size="30" maxlength="100" placeholder="<?php echo ew_HtmlEncode($tamuvip->Company->PlaceHolder) ?>" value="<?php echo $tamuvip->Company->EditValue ?>"<?php echo $tamuvip->Company->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($tamuvip->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tamuvip->Company->ViewAttributes() ?>>
<?php echo $tamuvip->Company->ListViewValue() ?></span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($tamuvip->Person->Visible) { // Person ?>
		<td<?php echo $tamuvip->Person->CellAttributes() ?>>
<?php if ($tamuvip->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tamuvip_list->RowCnt ?>_tamuvip_Person" class="control-group tamuvip_Person">
<input type="text" data-field="x_Person" name="x<?php echo $tamuvip_list->RowIndex ?>_Person" id="x<?php echo $tamuvip_list->RowIndex ?>_Person" size="30" maxlength="200" placeholder="<?php echo ew_HtmlEncode($tamuvip->Person->PlaceHolder) ?>" value="<?php echo $tamuvip->Person->EditValue ?>"<?php echo $tamuvip->Person->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($tamuvip->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tamuvip->Person->ViewAttributes() ?>>
<?php echo $tamuvip->Person->ListViewValue() ?></span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($tamuvip->Tanggal->Visible) { // Tanggal ?>
		<td<?php echo $tamuvip->Tanggal->CellAttributes() ?>>
<?php if ($tamuvip->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tamuvip_list->RowCnt ?>_tamuvip_Tanggal" class="control-group tamuvip_Tanggal">
<input type="text" data-field="x_Tanggal" name="x<?php echo $tamuvip_list->RowIndex ?>_Tanggal" id="x<?php echo $tamuvip_list->RowIndex ?>_Tanggal" placeholder="<?php echo ew_HtmlEncode($tamuvip->Tanggal->PlaceHolder) ?>" value="<?php echo $tamuvip->Tanggal->EditValue ?>"<?php echo $tamuvip->Tanggal->EditAttributes() ?>>
<?php if (!$tamuvip->Tanggal->ReadOnly && !$tamuvip->Tanggal->Disabled && @$tamuvip->Tanggal->EditAttrs["readonly"] == "" && @$tamuvip->Tanggal->EditAttrs["disabled"] == "") { ?>
<button id="cal_x<?php echo $tamuvip_list->RowIndex ?>_Tanggal" name="cal_x<?php echo $tamuvip_list->RowIndex ?>_Tanggal" class="btn" type="button"><img src="phpimages/calendar.png" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" style="border: 0;"></button><script type="text/javascript">
ew_CreateCalendar("ftamuviplist", "x<?php echo $tamuvip_list->RowIndex ?>_Tanggal", "%d/%m/%Y");
</script>
<?php } ?>
</span>
<?php } ?>
<?php if ($tamuvip->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tamuvip->Tanggal->ViewAttributes() ?>>
<?php echo $tamuvip->Tanggal->ListViewValue() ?></span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($tamuvip->Jumlah->Visible) { // Jumlah ?>
		<td<?php echo $tamuvip->Jumlah->CellAttributes() ?>>
<?php if ($tamuvip->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tamuvip_list->RowCnt ?>_tamuvip_Jumlah" class="control-group tamuvip_Jumlah">
<input type="text" data-field="x_Jumlah" name="x<?php echo $tamuvip_list->RowIndex ?>_Jumlah" id="x<?php echo $tamuvip_list->RowIndex ?>_Jumlah" size="30" placeholder="<?php echo ew_HtmlEncode($tamuvip->Jumlah->PlaceHolder) ?>" value="<?php echo $tamuvip->Jumlah->EditValue ?>"<?php echo $tamuvip->Jumlah->EditAttributes() ?>>
</span>
<?php } ?>
<?php if ($tamuvip->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tamuvip->Jumlah->ViewAttributes() ?>>
<?php echo $tamuvip->Jumlah->ListViewValue() ?></span>
<?php } ?>
</td>
	<?php } ?>
	<?php if ($tamuvip->Status->Visible) { // Status ?>
		<td<?php echo $tamuvip->Status->CellAttributes() ?>>
<?php if ($tamuvip->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $tamuvip_list->RowCnt ?>_tamuvip_Status" class="control-group tamuvip_Status">
<div id="tp_x<?php echo $tamuvip_list->RowIndex ?>_Status" class="<?php echo EW_ITEM_TEMPLATE_CLASSNAME ?>"><input type="radio" name="x<?php echo $tamuvip_list->RowIndex ?>_Status" id="x<?php echo $tamuvip_list->RowIndex ?>_Status" value="{value}"<?php echo $tamuvip->Status->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $tamuvip_list->RowIndex ?>_Status" data-repeatcolumn="5" class="ewItemList">
<?php
$arwrk = $tamuvip->Status->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($tamuvip->Status->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " checked=\"checked\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;

		// Note: No spacing within the LABEL tag
?>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 1) ?>
<label class="radio"><input type="radio" data-field="x_Status" name="x<?php echo $tamuvip_list->RowIndex ?>_Status" id="x<?php echo $tamuvip_list->RowIndex ?>_Status_<?php echo $rowcntwrk ?>" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $tamuvip->Status->EditAttributes() ?>><?php echo $arwrk[$rowcntwrk][1] ?></label>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 2) ?>
<?php
	}
}
?>
</div>
</span>
<?php } ?>
<?php if ($tamuvip->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span<?php echo $tamuvip->Status->ViewAttributes() ?>>
<?php echo $tamuvip->Status->ListViewValue() ?></span>
<?php } ?>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$tamuvip_list->ListOptions->Render("body", "right", $tamuvip_list->RowCnt);
?>
	</tr>
<?php if ($tamuvip->RowType == EW_ROWTYPE_ADD || $tamuvip->RowType == EW_ROWTYPE_EDIT) { ?>
<script type="text/javascript">
ftamuviplist.UpdateOpts(<?php echo $tamuvip_list->RowIndex ?>);
</script>
<?php } ?>
<?php
	}
	if ($tamuvip->CurrentAction <> "gridadd")
		$tamuvip_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($tamuvip->CurrentAction == "add" || $tamuvip->CurrentAction == "copy") { ?>
<input type="hidden" name="<?php echo $tamuvip_list->FormKeyCountName ?>" id="<?php echo $tamuvip_list->FormKeyCountName ?>" value="<?php echo $tamuvip_list->KeyCount ?>">
<?php } ?>
<?php if ($tamuvip->CurrentAction == "edit") { ?>
<input type="hidden" name="<?php echo $tamuvip_list->FormKeyCountName ?>" id="<?php echo $tamuvip_list->FormKeyCountName ?>" value="<?php echo $tamuvip_list->KeyCount ?>">
<?php } ?>
<?php if ($tamuvip->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($tamuvip_list->Recordset)
	$tamuvip_list->Recordset->Close();
?>
</td></tr></table>
<?php if ($tamuvip->Export == "") { ?>
<script type="text/javascript">
ftamuviplistsrch.Init();
ftamuviplist.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php } ?>
<?php
$tamuvip_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($tamuvip->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$tamuvip_list->Page_Terminate();
?>
