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

$pengunjung_list = NULL; // Initialize page object first

class cpengunjung_list extends cpengunjung {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{C2A46AD1-29DD-4049-B347-17989E75216E}";

	// Table name
	var $TableName = 'pengunjung';

	// Page object name
	var $PageObjName = 'pengunjung_list';

	// Grid form hidden field names
	var $FormName = 'fpengunjunglist';
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

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "pengunjungadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "pengunjungdelete.php";
		$this->MultiUpdateUrl = "pengunjungupdate.php";

		// Table object (user)
		if (!isset($GLOBALS['user'])) $GLOBALS['user'] = new cuser();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'pengunjung', TRUE);

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
		if ($Security->IsLoggedIn() && strval($Security->CurrentUserID()) == "") {
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate();
		}

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
		$this->Id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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

				// Switch to inline add mode
				if ($this->CurrentAction == "add" || $this->CurrentAction == "copy")
					$this->InlineAddMode();
			} else {
				if (@$_POST["a_list"] <> "") {
					$this->CurrentAction = $_POST["a_list"]; // Get action

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

			// Get and validate search values for advanced search
			$this->LoadSearchValues(); // Get search values
			if (!$this->ValidateSearch())
				$this->setFailureMessage($gsSearchError);

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

			// Get search criteria for advanced search
			if ($gsSearchError == "")
				$sSrchAdvanced = $this->AdvancedSearchWhere();
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

			// Load advanced search from default
			if ($this->LoadAdvancedSearchDefault()) {
				$sSrchAdvanced = $this->AdvancedSearchWhere();
			}
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
		$this->Jumlah->FormValue = ""; // Clear form value
		$this->LastAction = $this->CurrentAction; // Save last action
		$this->CurrentAction = ""; // Clear action
		$_SESSION[EW_SESSION_INLINE_MODE] = ""; // Clear inline mode
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
			$this->Id->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->Id->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Advanced search WHERE clause based on QueryString
	function AdvancedSearchWhere() {
		global $Security;
		$sWhere = "";
		if (!$Security->CanSearch()) return "";
		$this->BuildSearchSql($sWhere, $this->Nama, FALSE); // Nama
		$this->BuildSearchSql($sWhere, $this->Provinsi, FALSE); // Provinsi
		$this->BuildSearchSql($sWhere, $this->Area, FALSE); // Area
		$this->BuildSearchSql($sWhere, $this->CP, FALSE); // CP
		$this->BuildSearchSql($sWhere, $this->Tanggal, FALSE); // Tanggal
		$this->BuildSearchSql($sWhere, $this->Vechicle, FALSE); // Vechicle
		$this->BuildSearchSql($sWhere, $this->Type, FALSE); // Type
		$this->BuildSearchSql($sWhere, $this->Site, FALSE); // Site
		$this->BuildSearchSql($sWhere, $this->Status, FALSE); // Status
		$this->BuildSearchSql($sWhere, $this->visit, FALSE); // visit

		// Set up search parm
		if ($sWhere <> "") {
			$this->Command = "search";
		}
		if ($this->Command == "search") {
			$this->Nama->AdvancedSearch->Save(); // Nama
			$this->Provinsi->AdvancedSearch->Save(); // Provinsi
			$this->Area->AdvancedSearch->Save(); // Area
			$this->CP->AdvancedSearch->Save(); // CP
			$this->Tanggal->AdvancedSearch->Save(); // Tanggal
			$this->Vechicle->AdvancedSearch->Save(); // Vechicle
			$this->Type->AdvancedSearch->Save(); // Type
			$this->Site->AdvancedSearch->Save(); // Site
			$this->Status->AdvancedSearch->Save(); // Status
			$this->visit->AdvancedSearch->Save(); // visit
		}
		return $sWhere;
	}

	// Build search SQL
	function BuildSearchSql(&$Where, &$Fld, $MultiValue) {
		$FldParm = substr($Fld->FldVar, 2);
		$FldVal = $Fld->AdvancedSearch->SearchValue; // @$_GET["x_$FldParm"]
		$FldOpr = $Fld->AdvancedSearch->SearchOperator; // @$_GET["z_$FldParm"]
		$FldCond = $Fld->AdvancedSearch->SearchCondition; // @$_GET["v_$FldParm"]
		$FldVal2 = $Fld->AdvancedSearch->SearchValue2; // @$_GET["y_$FldParm"]
		$FldOpr2 = $Fld->AdvancedSearch->SearchOperator2; // @$_GET["w_$FldParm"]
		$sWrk = "";

		//$FldVal = ew_StripSlashes($FldVal);
		if (is_array($FldVal)) $FldVal = implode(",", $FldVal);

		//$FldVal2 = ew_StripSlashes($FldVal2);
		if (is_array($FldVal2)) $FldVal2 = implode(",", $FldVal2);
		$FldOpr = strtoupper(trim($FldOpr));
		if ($FldOpr == "") $FldOpr = "=";
		$FldOpr2 = strtoupper(trim($FldOpr2));
		if ($FldOpr2 == "") $FldOpr2 = "=";
		if (EW_SEARCH_MULTI_VALUE_OPTION == 1 || $FldOpr <> "LIKE" ||
			($FldOpr2 <> "LIKE" && $FldVal2 <> ""))
			$MultiValue = FALSE;
		if ($MultiValue) {
			$sWrk1 = ($FldVal <> "") ? ew_GetMultiSearchSql($Fld, $FldOpr, $FldVal) : ""; // Field value 1
			$sWrk2 = ($FldVal2 <> "") ? ew_GetMultiSearchSql($Fld, $FldOpr2, $FldVal2) : ""; // Field value 2
			$sWrk = $sWrk1; // Build final SQL
			if ($sWrk2 <> "")
				$sWrk = ($sWrk <> "") ? "($sWrk) $FldCond ($sWrk2)" : $sWrk2;
		} else {
			$FldVal = $this->ConvertSearchValue($Fld, $FldVal);
			$FldVal2 = $this->ConvertSearchValue($Fld, $FldVal2);
			$sWrk = ew_GetSearchSql($Fld, $FldVal, $FldOpr, $FldCond, $FldVal2, $FldOpr2);
		}
		ew_AddFilter($Where, $sWrk);
	}

	// Convert search value
	function ConvertSearchValue(&$Fld, $FldVal) {
		if ($FldVal == EW_NULL_VALUE || $FldVal == EW_NOT_NULL_VALUE)
			return $FldVal;
		$Value = $FldVal;
		if ($Fld->FldDataType == EW_DATATYPE_BOOLEAN) {
			if ($FldVal <> "") $Value = ($FldVal == "1" || strtolower(strval($FldVal)) == "y" || strtolower(strval($FldVal)) == "t") ? $Fld->TrueValue : $Fld->FalseValue;
		} elseif ($Fld->FldDataType == EW_DATATYPE_DATE) {
			if ($FldVal <> "") $Value = ew_UnFormatDateTime($FldVal, $Fld->FldDateTimeFormat);
		}
		return $Value;
	}

	// Return basic search SQL
	function BasicSearchSQL($Keyword) {
		$sKeyword = ew_AdjustSql($Keyword);
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->Nama, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->Alamat, $Keyword);
		if (is_numeric($Keyword)) $this->BuildBasicSearchSQL($sWhere, $this->Area, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->CP, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->NoContact, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->Vechicle, $Keyword);
		if (is_numeric($Keyword)) $this->BuildBasicSearchSQL($sWhere, $this->Type, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->Site, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->Status, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->_UserID, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->visit, $Keyword);
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
		if ($this->Nama->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Provinsi->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Area->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->CP->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Tanggal->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Vechicle->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Type->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Site->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Status->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->visit->AdvancedSearch->IssetSession())
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

		// Clear advanced search parameters
		$this->ResetAdvancedSearchParms();
	}

	// Load advanced search default values
	function LoadAdvancedSearchDefault() {
		return FALSE;
	}

	// Clear all basic search parameters
	function ResetBasicSearchParms() {
		$this->BasicSearch->UnsetSession();
	}

	// Clear all advanced search parameters
	function ResetAdvancedSearchParms() {
		$this->Nama->AdvancedSearch->UnsetSession();
		$this->Provinsi->AdvancedSearch->UnsetSession();
		$this->Area->AdvancedSearch->UnsetSession();
		$this->CP->AdvancedSearch->UnsetSession();
		$this->Tanggal->AdvancedSearch->UnsetSession();
		$this->Vechicle->AdvancedSearch->UnsetSession();
		$this->Type->AdvancedSearch->UnsetSession();
		$this->Site->AdvancedSearch->UnsetSession();
		$this->Status->AdvancedSearch->UnsetSession();
		$this->visit->AdvancedSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();

		// Restore advanced search values
		$this->Nama->AdvancedSearch->Load();
		$this->Provinsi->AdvancedSearch->Load();
		$this->Area->AdvancedSearch->Load();
		$this->CP->AdvancedSearch->Load();
		$this->Tanggal->AdvancedSearch->Load();
		$this->Vechicle->AdvancedSearch->Load();
		$this->Type->AdvancedSearch->Load();
		$this->Site->AdvancedSearch->Load();
		$this->Status->AdvancedSearch->Load();
		$this->visit->AdvancedSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for Ctrl pressed
		$bCtrl = (@$_GET["ctrl"] <> "");

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->Id, $bCtrl); // Id
			$this->UpdateSort($this->Nama, $bCtrl); // Nama
			$this->UpdateSort($this->Jumlah, $bCtrl); // Jumlah
			$this->UpdateSort($this->Area, $bCtrl); // Area
			$this->UpdateSort($this->CP, $bCtrl); // CP
			$this->UpdateSort($this->NoContact, $bCtrl); // NoContact
			$this->UpdateSort($this->Tanggal, $bCtrl); // Tanggal
			$this->UpdateSort($this->Jam, $bCtrl); // Jam
			$this->UpdateSort($this->Type, $bCtrl); // Type
			$this->UpdateSort($this->Site, $bCtrl); // Site
			$this->UpdateSort($this->Status, $bCtrl); // Status
			$this->UpdateSort($this->visit, $bCtrl); // visit
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
				$this->Tanggal->setSort("DESC");
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
				$this->Id->setSort("");
				$this->Nama->setSort("");
				$this->Jumlah->setSort("");
				$this->Area->setSort("");
				$this->CP->setSort("");
				$this->NoContact->setSort("");
				$this->Tanggal->setSort("");
				$this->Jam->setSort("");
				$this->Type->setSort("");
				$this->Site->setSort("");
				$this->Status->setSort("");
				$this->visit->setSort("");
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
		if ($Security->CanEdit() && $this->ShowOptionLink('edit')) {
			$oListOpt->Body = "<a class=\"ewRowLink ewEdit\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("EditLink")) . "\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" . $Language->Phrase("EditLink") . "</a>";
		} else {
			$oListOpt->Body = "";
		}

		// "delete"
		$oListOpt = &$this->ListOptions->Items["delete"];
		if ($Security->CanDelete() && $this->ShowOptionLink('delete'))
			$oListOpt->Body = "<a class=\"ewRowLink ewDelete\"" . "" . " data-caption=\"" . ew_HtmlTitle($Language->Phrase("DeleteLink")) . "\" href=\"" . ew_HtmlEncode($this->DeleteUrl) . "\">" . $Language->Phrase("DeleteLink") . "</a>";
		else
			$oListOpt->Body = "";

		// "checkbox"
		$oListOpt = &$this->ListOptions->Items["checkbox"];
		$oListOpt->Body = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->Id->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event, this);'></label>";
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
				$item->Body = "<a class=\"ewAction ewCustomAction\" href=\"\" onclick=\"ew_SubmitSelected(document.fpengunjunglist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
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
		$this->Id->CurrentValue = NULL;
		$this->Id->OldValue = $this->Id->CurrentValue;
		$this->Nama->CurrentValue = NULL;
		$this->Nama->OldValue = $this->Nama->CurrentValue;
		$this->Jumlah->CurrentValue = NULL;
		$this->Jumlah->OldValue = $this->Jumlah->CurrentValue;
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
		$this->Type->CurrentValue = NULL;
		$this->Type->OldValue = $this->Type->CurrentValue;
		$this->Site->CurrentValue = CurrentUserID();
		$this->Status->CurrentValue = "YES";
		$this->visit->CurrentValue = "NO";
	}

	// Load basic search values
	function LoadBasicSearchValues() {
		$this->BasicSearch->Keyword = @$_GET[EW_TABLE_BASIC_SEARCH];
		if ($this->BasicSearch->Keyword <> "") $this->Command = "search";
		$this->BasicSearch->Type = @$_GET[EW_TABLE_BASIC_SEARCH_TYPE];
	}

	//  Load search values for validation
	function LoadSearchValues() {
		global $objForm;

		// Load search values
		// Nama

		$this->Nama->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Nama"]);
		if ($this->Nama->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Nama->AdvancedSearch->SearchOperator = @$_GET["z_Nama"];

		// Provinsi
		$this->Provinsi->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Provinsi"]);
		if ($this->Provinsi->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Provinsi->AdvancedSearch->SearchOperator = @$_GET["z_Provinsi"];

		// Area
		$this->Area->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Area"]);
		if ($this->Area->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Area->AdvancedSearch->SearchOperator = @$_GET["z_Area"];

		// CP
		$this->CP->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_CP"]);
		if ($this->CP->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->CP->AdvancedSearch->SearchOperator = @$_GET["z_CP"];

		// Tanggal
		$this->Tanggal->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Tanggal"]);
		if ($this->Tanggal->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Tanggal->AdvancedSearch->SearchOperator = @$_GET["z_Tanggal"];
		$this->Tanggal->AdvancedSearch->SearchCondition = @$_GET["v_Tanggal"];
		$this->Tanggal->AdvancedSearch->SearchValue2 = ew_StripSlashes(@$_GET["y_Tanggal"]);
		if ($this->Tanggal->AdvancedSearch->SearchValue2 <> "") $this->Command = "search";
		$this->Tanggal->AdvancedSearch->SearchOperator2 = @$_GET["w_Tanggal"];

		// Vechicle
		$this->Vechicle->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Vechicle"]);
		if ($this->Vechicle->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Vechicle->AdvancedSearch->SearchOperator = @$_GET["z_Vechicle"];

		// Type
		$this->Type->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Type"]);
		if ($this->Type->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Type->AdvancedSearch->SearchOperator = @$_GET["z_Type"];

		// Site
		$this->Site->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Site"]);
		if ($this->Site->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Site->AdvancedSearch->SearchOperator = @$_GET["z_Site"];

		// Status
		$this->Status->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Status"]);
		if ($this->Status->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Status->AdvancedSearch->SearchOperator = @$_GET["z_Status"];

		// visit
		$this->visit->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_visit"]);
		if ($this->visit->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->visit->AdvancedSearch->SearchOperator = @$_GET["z_visit"];
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->Id->FldIsDetailKey && $this->CurrentAction <> "gridadd" && $this->CurrentAction <> "add")
			$this->Id->setFormValue($objForm->GetValue("x_Id"));
		if (!$this->Nama->FldIsDetailKey) {
			$this->Nama->setFormValue($objForm->GetValue("x_Nama"));
		}
		if (!$this->Jumlah->FldIsDetailKey) {
			$this->Jumlah->setFormValue($objForm->GetValue("x_Jumlah"));
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
		if (!$this->Type->FldIsDetailKey) {
			$this->Type->setFormValue($objForm->GetValue("x_Type"));
		}
		if (!$this->Site->FldIsDetailKey) {
			$this->Site->setFormValue($objForm->GetValue("x_Site"));
		}
		if (!$this->Status->FldIsDetailKey) {
			$this->Status->setFormValue($objForm->GetValue("x_Status"));
		}
		if (!$this->visit->FldIsDetailKey) {
			$this->visit->setFormValue($objForm->GetValue("x_visit"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		if ($this->CurrentAction <> "gridadd" && $this->CurrentAction <> "add")
			$this->Id->CurrentValue = $this->Id->FormValue;
		$this->Nama->CurrentValue = $this->Nama->FormValue;
		$this->Jumlah->CurrentValue = $this->Jumlah->FormValue;
		$this->Area->CurrentValue = $this->Area->FormValue;
		$this->CP->CurrentValue = $this->CP->FormValue;
		$this->NoContact->CurrentValue = $this->NoContact->FormValue;
		$this->Tanggal->CurrentValue = $this->Tanggal->FormValue;
		$this->Tanggal->CurrentValue = ew_UnFormatDateTime($this->Tanggal->CurrentValue, 7);
		$this->Jam->CurrentValue = $this->Jam->FormValue;
		$this->Type->CurrentValue = $this->Type->FormValue;
		$this->Site->CurrentValue = $this->Site->FormValue;
		$this->Status->CurrentValue = $this->Status->FormValue;
		$this->visit->CurrentValue = $this->visit->FormValue;
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
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// Id
			// Nama

			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->CurrentValue);
			$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

			// Jumlah
			$this->Jumlah->EditCustomAttributes = "";
			$this->Jumlah->EditValue = ew_HtmlEncode($this->Jumlah->CurrentValue);
			$this->Jumlah->PlaceHolder = ew_RemoveHtml($this->Jumlah->FldCaption());
			if (strval($this->Jumlah->EditValue) <> "" && is_numeric($this->Jumlah->EditValue)) $this->Jumlah->EditValue = ew_FormatNumber($this->Jumlah->EditValue, -2, -1, -2, 0);

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
			if (!$Security->IsAdmin() && $Security->IsLoggedIn() && !$this->UserIDAllow($this->CurrentAction)) { // Non system admin
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

			// Status
			$this->Status->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->Status->FldTagValue(1), $this->Status->FldTagCaption(1) <> "" ? $this->Status->FldTagCaption(1) : $this->Status->FldTagValue(1));
			$arwrk[] = array($this->Status->FldTagValue(2), $this->Status->FldTagCaption(2) <> "" ? $this->Status->FldTagCaption(2) : $this->Status->FldTagValue(2));
			$this->Status->EditValue = $arwrk;

			// visit
			$this->visit->EditCustomAttributes = "";
			$arwrk = array();
			$arwrk[] = array($this->visit->FldTagValue(1), $this->visit->FldTagCaption(1) <> "" ? $this->visit->FldTagCaption(1) : $this->visit->FldTagValue(1));
			$arwrk[] = array($this->visit->FldTagValue(2), $this->visit->FldTagCaption(2) <> "" ? $this->visit->FldTagCaption(2) : $this->visit->FldTagValue(2));
			$this->visit->EditValue = $arwrk;

			// Edit refer script
			// Id

			$this->Id->HrefValue = "";

			// Nama
			$this->Nama->HrefValue = "";

			// Jumlah
			$this->Jumlah->HrefValue = "";

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

			// Type
			$this->Type->HrefValue = "";

			// Site
			$this->Site->HrefValue = "";

			// Status
			$this->Status->HrefValue = "";

			// visit
			$this->visit->HrefValue = "";
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

	// Validate search
	function ValidateSearch() {
		global $gsSearchError;

		// Initialize
		$gsSearchError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return TRUE;

		// Return validate result
		$ValidateSearch = ($gsSearchError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateSearch = $ValidateSearch && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsSearchError, $sFormCustomError);
		}
		return $ValidateSearch;
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

		// Jumlah
		$this->Jumlah->SetDbValueDef($rsnew, $this->Jumlah->CurrentValue, NULL, FALSE);

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

		// Type
		$this->Type->SetDbValueDef($rsnew, $this->Type->CurrentValue, NULL, FALSE);

		// Site
		$this->Site->SetDbValueDef($rsnew, $this->Site->CurrentValue, NULL, FALSE);

		// Status
		$this->Status->SetDbValueDef($rsnew, $this->Status->CurrentValue, NULL, strval($this->Status->CurrentValue) == "");

		// visit
		$this->visit->SetDbValueDef($rsnew, $this->visit->CurrentValue, NULL, strval($this->visit->CurrentValue) == "");

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

	// Load advanced search
	function LoadAdvancedSearch() {
		$this->Nama->AdvancedSearch->Load();
		$this->Provinsi->AdvancedSearch->Load();
		$this->Area->AdvancedSearch->Load();
		$this->CP->AdvancedSearch->Load();
		$this->Tanggal->AdvancedSearch->Load();
		$this->Vechicle->AdvancedSearch->Load();
		$this->Type->AdvancedSearch->Load();
		$this->Site->AdvancedSearch->Load();
		$this->Status->AdvancedSearch->Load();
		$this->visit->AdvancedSearch->Load();
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
		$item->Body = "<a id=\"emf_pengunjung\" href=\"javascript:void(0);\" class=\"ewExportLink ewEmail\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_pengunjung',hdr:ewLanguage.Phrase('ExportToEmail'),f:document.fpengunjunglist,sel:false});\">" . $Language->Phrase("ExportToEmail") . "</a>";
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
		$url = ew_CurrentUrl();
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$Breadcrumb->Add("list", $this->TableVar, $url, $this->TableVar, TRUE);
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
if (!isset($pengunjung_list)) $pengunjung_list = new cpengunjung_list();

// Page init
$pengunjung_list->Page_Init();

// Page main
$pengunjung_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$pengunjung_list->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($pengunjung->Export == "") { ?>
<script type="text/javascript">

// Page object
var pengunjung_list = new ew_Page("pengunjung_list");
pengunjung_list.PageID = "list"; // Page ID
var EW_PAGE_ID = pengunjung_list.PageID; // For backward compatibility

// Form object
var fpengunjunglist = new ew_Form("fpengunjunglist");
fpengunjunglist.FormKeyCountName = '<?php echo $pengunjung_list->FormKeyCountName ?>';

// Validate form
fpengunjunglist.Validate = function() {
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
	return true;
}

// Form_CustomValidate event
fpengunjunglist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fpengunjunglist.ValidateRequired = true;
<?php } else { ?>
fpengunjunglist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fpengunjunglist.Lists["x_Area"] = {"LinkField":"x_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Kota","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fpengunjunglist.Lists["x_Type"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
var fpengunjunglistsrch = new ew_Form("fpengunjunglistsrch");
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($pengunjung->Export == "") { ?>
<?php $Breadcrumb->Render(); ?>
<?php } ?>
<?php if ($pengunjung_list->ExportOptions->Visible()) { ?>
<div class="ewListExportOptions"><?php $pengunjung_list->ExportOptions->Render("body") ?></div>
<?php } ?>
<?php
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$pengunjung_list->TotalRecs = $pengunjung->SelectRecordCount();
	} else {
		if ($pengunjung_list->Recordset = $pengunjung_list->LoadRecordset())
			$pengunjung_list->TotalRecs = $pengunjung_list->Recordset->RecordCount();
	}
	$pengunjung_list->StartRec = 1;
	if ($pengunjung_list->DisplayRecs <= 0 || ($pengunjung->Export <> "" && $pengunjung->ExportAll)) // Display all records
		$pengunjung_list->DisplayRecs = $pengunjung_list->TotalRecs;
	if (!($pengunjung->Export <> "" && $pengunjung->ExportAll))
		$pengunjung_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$pengunjung_list->Recordset = $pengunjung_list->LoadRecordset($pengunjung_list->StartRec-1, $pengunjung_list->DisplayRecs);
$pengunjung_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($pengunjung->Export == "" && $pengunjung->CurrentAction == "") { ?>
<form name="fpengunjunglistsrch" id="fpengunjunglistsrch" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<div class="accordion ewDisplayTable ewSearchTable" id="fpengunjunglistsrch_SearchGroup">
	<div class="accordion-group">
		<div class="accordion-heading">
<a class="accordion-toggle" data-toggle="collapse" data-parent="#fpengunjunglistsrch_SearchGroup" href="#fpengunjunglistsrch_SearchBody"><?php echo $Language->Phrase("Search") ?></a>
		</div>
		<div id="fpengunjunglistsrch_SearchBody" class="accordion-body collapse in">
			<div class="accordion-inner">
<div id="fpengunjunglistsrch_SearchPanel">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="pengunjung">
<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="btn-group ewButtonGroup">
	<div class="input-append">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="input-large" value="<?php echo ew_HtmlEncode($pengunjung_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Search")) ?>">
	<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?></button>
	</div>
	</div>
	<div class="btn-group ewButtonGroup">
	<a class="btn ewShowAll" href="<?php echo $pengunjung_list->PageUrl() ?>cmd=reset"><?php echo $Language->Phrase("ShowAll") ?></a>
	<a class="btn ewAdvancedSearch" href="pengunjungsrch.php"><?php echo $Language->Phrase("AdvancedSearch") ?></a>
	</div>
</div>
<div id="xsr_2" class="ewRow">
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="="<?php if ($pengunjung_list->BasicSearch->getType() == "=") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("ExactPhrase") ?></label>
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND"<?php if ($pengunjung_list->BasicSearch->getType() == "AND") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AllWord") ?></label>
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR"<?php if ($pengunjung_list->BasicSearch->getType() == "OR") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AnyWord") ?></label>
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
<?php $pengunjung_list->ShowPageHeader(); ?>
<?php
$pengunjung_list->ShowMessage();
?>
<table class="ewGrid"><tr><td class="ewGridContent">
<?php if ($pengunjung->Export == "") { ?>
<div class="ewGridUpperPanel">
<?php if ($pengunjung->CurrentAction <> "gridadd" && $pengunjung->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($pengunjung_list->Pager)) $pengunjung_list->Pager = new cPrevNextPager($pengunjung_list->StartRec, $pengunjung_list->DisplayRecs, $pengunjung_list->TotalRecs) ?>
<?php if ($pengunjung_list->Pager->RecordCount > 0) { ?>
<table class="ewStdTable"><tbody><tr><td>
	<?php echo $Language->Phrase("Page") ?>&nbsp;
<div class="input-prepend input-append">
<!--first page button-->
	<?php if ($pengunjung_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $pengunjung_list->PageUrl() ?>start=<?php echo $pengunjung_list->Pager->FirstButton->Start ?>"><i class="icon-step-backward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-step-backward"></i></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($pengunjung_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $pengunjung_list->PageUrl() ?>start=<?php echo $pengunjung_list->Pager->PrevButton->Start ?>"><i class="icon-prev"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-prev"></i></a>
	<?php } ?>
<!--current page number-->
	<input class="input-mini" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $pengunjung_list->Pager->CurrentPage ?>">
<!--next page button-->
	<?php if ($pengunjung_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $pengunjung_list->PageUrl() ?>start=<?php echo $pengunjung_list->Pager->NextButton->Start ?>"><i class="icon-play"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-play"></i></a>
	<?php } ?>
<!--last page button-->
	<?php if ($pengunjung_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $pengunjung_list->PageUrl() ?>start=<?php echo $pengunjung_list->Pager->LastButton->Start ?>"><i class="icon-step-forward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-step-forward"></i></a>
	<?php } ?>
</div>
	&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $pengunjung_list->Pager->PageCount ?>
</td>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $pengunjung_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $pengunjung_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $pengunjung_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Security->CanList()) { ?>
	<?php if ($pengunjung_list->SearchWhere == "0=101") { ?>
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
	foreach ($pengunjung_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
</div>
<?php } ?>
<form name="fpengunjunglist" id="fpengunjunglist" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="pengunjung">
<div id="gmp_pengunjung" class="ewGridMiddlePanel">
<?php if ($pengunjung_list->TotalRecs > 0 || $pengunjung->CurrentAction == "add" || $pengunjung->CurrentAction == "copy") { ?>
<table id="tbl_pengunjunglist" class="ewTable ewTableSeparate">
<?php echo $pengunjung->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$pengunjung_list->RenderListOptions();

// Render list options (header, left)
$pengunjung_list->ListOptions->Render("header", "left");
?>
<?php if ($pengunjung->Id->Visible) { // Id ?>
	<?php if ($pengunjung->SortUrl($pengunjung->Id) == "") { ?>
		<td><div id="elh_pengunjung_Id" class="pengunjung_Id"><div class="ewTableHeaderCaption"><?php echo $pengunjung->Id->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pengunjung->SortUrl($pengunjung->Id) ?>',2);"><div id="elh_pengunjung_Id" class="pengunjung_Id">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pengunjung->Id->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($pengunjung->Id->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pengunjung->Id->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($pengunjung->Nama->Visible) { // Nama ?>
	<?php if ($pengunjung->SortUrl($pengunjung->Nama) == "") { ?>
		<td><div id="elh_pengunjung_Nama" class="pengunjung_Nama"><div class="ewTableHeaderCaption"><?php echo $pengunjung->Nama->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pengunjung->SortUrl($pengunjung->Nama) ?>',2);"><div id="elh_pengunjung_Nama" class="pengunjung_Nama">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pengunjung->Nama->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($pengunjung->Nama->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pengunjung->Nama->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($pengunjung->Jumlah->Visible) { // Jumlah ?>
	<?php if ($pengunjung->SortUrl($pengunjung->Jumlah) == "") { ?>
		<td><div id="elh_pengunjung_Jumlah" class="pengunjung_Jumlah"><div class="ewTableHeaderCaption"><?php echo $pengunjung->Jumlah->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pengunjung->SortUrl($pengunjung->Jumlah) ?>',2);"><div id="elh_pengunjung_Jumlah" class="pengunjung_Jumlah">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pengunjung->Jumlah->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($pengunjung->Jumlah->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pengunjung->Jumlah->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($pengunjung->Area->Visible) { // Area ?>
	<?php if ($pengunjung->SortUrl($pengunjung->Area) == "") { ?>
		<td><div id="elh_pengunjung_Area" class="pengunjung_Area"><div class="ewTableHeaderCaption"><?php echo $pengunjung->Area->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pengunjung->SortUrl($pengunjung->Area) ?>',2);"><div id="elh_pengunjung_Area" class="pengunjung_Area">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pengunjung->Area->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($pengunjung->Area->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pengunjung->Area->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($pengunjung->CP->Visible) { // CP ?>
	<?php if ($pengunjung->SortUrl($pengunjung->CP) == "") { ?>
		<td><div id="elh_pengunjung_CP" class="pengunjung_CP"><div class="ewTableHeaderCaption"><?php echo $pengunjung->CP->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pengunjung->SortUrl($pengunjung->CP) ?>',2);"><div id="elh_pengunjung_CP" class="pengunjung_CP">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pengunjung->CP->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($pengunjung->CP->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pengunjung->CP->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($pengunjung->NoContact->Visible) { // NoContact ?>
	<?php if ($pengunjung->SortUrl($pengunjung->NoContact) == "") { ?>
		<td><div id="elh_pengunjung_NoContact" class="pengunjung_NoContact"><div class="ewTableHeaderCaption"><?php echo $pengunjung->NoContact->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pengunjung->SortUrl($pengunjung->NoContact) ?>',2);"><div id="elh_pengunjung_NoContact" class="pengunjung_NoContact">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pengunjung->NoContact->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($pengunjung->NoContact->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pengunjung->NoContact->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($pengunjung->Tanggal->Visible) { // Tanggal ?>
	<?php if ($pengunjung->SortUrl($pengunjung->Tanggal) == "") { ?>
		<td><div id="elh_pengunjung_Tanggal" class="pengunjung_Tanggal"><div class="ewTableHeaderCaption"><?php echo $pengunjung->Tanggal->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pengunjung->SortUrl($pengunjung->Tanggal) ?>',2);"><div id="elh_pengunjung_Tanggal" class="pengunjung_Tanggal">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pengunjung->Tanggal->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($pengunjung->Tanggal->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pengunjung->Tanggal->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($pengunjung->Jam->Visible) { // Jam ?>
	<?php if ($pengunjung->SortUrl($pengunjung->Jam) == "") { ?>
		<td><div id="elh_pengunjung_Jam" class="pengunjung_Jam"><div class="ewTableHeaderCaption"><?php echo $pengunjung->Jam->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pengunjung->SortUrl($pengunjung->Jam) ?>',2);"><div id="elh_pengunjung_Jam" class="pengunjung_Jam">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pengunjung->Jam->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($pengunjung->Jam->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pengunjung->Jam->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($pengunjung->Type->Visible) { // Type ?>
	<?php if ($pengunjung->SortUrl($pengunjung->Type) == "") { ?>
		<td><div id="elh_pengunjung_Type" class="pengunjung_Type"><div class="ewTableHeaderCaption"><?php echo $pengunjung->Type->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pengunjung->SortUrl($pengunjung->Type) ?>',2);"><div id="elh_pengunjung_Type" class="pengunjung_Type">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pengunjung->Type->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($pengunjung->Type->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pengunjung->Type->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($pengunjung->Site->Visible) { // Site ?>
	<?php if ($pengunjung->SortUrl($pengunjung->Site) == "") { ?>
		<td><div id="elh_pengunjung_Site" class="pengunjung_Site"><div class="ewTableHeaderCaption"><?php echo $pengunjung->Site->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pengunjung->SortUrl($pengunjung->Site) ?>',2);"><div id="elh_pengunjung_Site" class="pengunjung_Site">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pengunjung->Site->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($pengunjung->Site->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pengunjung->Site->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($pengunjung->Status->Visible) { // Status ?>
	<?php if ($pengunjung->SortUrl($pengunjung->Status) == "") { ?>
		<td><div id="elh_pengunjung_Status" class="pengunjung_Status"><div class="ewTableHeaderCaption"><?php echo $pengunjung->Status->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pengunjung->SortUrl($pengunjung->Status) ?>',2);"><div id="elh_pengunjung_Status" class="pengunjung_Status">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pengunjung->Status->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($pengunjung->Status->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pengunjung->Status->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($pengunjung->visit->Visible) { // visit ?>
	<?php if ($pengunjung->SortUrl($pengunjung->visit) == "") { ?>
		<td><div id="elh_pengunjung_visit" class="pengunjung_visit"><div class="ewTableHeaderCaption"><?php echo $pengunjung->visit->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $pengunjung->SortUrl($pengunjung->visit) ?>',2);"><div id="elh_pengunjung_visit" class="pengunjung_visit">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $pengunjung->visit->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($pengunjung->visit->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($pengunjung->visit->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$pengunjung_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
	if ($pengunjung->CurrentAction == "add" || $pengunjung->CurrentAction == "copy") {
		$pengunjung_list->RowIndex = 0;
		$pengunjung_list->KeyCount = $pengunjung_list->RowIndex;
		if ($pengunjung->CurrentAction == "add")
			$pengunjung_list->LoadDefaultValues();
		if ($pengunjung->EventCancelled) // Insert failed
			$pengunjung_list->RestoreFormValues(); // Restore form values

		// Set row properties
		$pengunjung->ResetAttrs();
		$pengunjung->RowAttrs = array_merge($pengunjung->RowAttrs, array('data-rowindex'=>0, 'id'=>'r0_pengunjung', 'data-rowtype'=>EW_ROWTYPE_ADD));
		$pengunjung->RowType = EW_ROWTYPE_ADD;

		// Render row
		$pengunjung_list->RenderRow();

		// Render list options
		$pengunjung_list->RenderListOptions();
		$pengunjung_list->StartRowCnt = 0;
?>
	<tr<?php echo $pengunjung->RowAttributes() ?>>
<?php

// Render list options (body, left)
$pengunjung_list->ListOptions->Render("body", "left", $pengunjung_list->RowCnt);
?>
	<?php if ($pengunjung->Id->Visible) { // Id ?>
		<td>
<input type="hidden" data-field="x_Id" name="o<?php echo $pengunjung_list->RowIndex ?>_Id" id="o<?php echo $pengunjung_list->RowIndex ?>_Id" value="<?php echo ew_HtmlEncode($pengunjung->Id->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($pengunjung->Nama->Visible) { // Nama ?>
		<td>
<span id="el<?php echo $pengunjung_list->RowCnt ?>_pengunjung_Nama" class="control-group pengunjung_Nama">
<input type="text" data-field="x_Nama" name="x<?php echo $pengunjung_list->RowIndex ?>_Nama" id="x<?php echo $pengunjung_list->RowIndex ?>_Nama" size="100" maxlength="100" placeholder="<?php echo ew_HtmlEncode($pengunjung->Nama->PlaceHolder) ?>" value="<?php echo $pengunjung->Nama->EditValue ?>"<?php echo $pengunjung->Nama->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_Nama" name="o<?php echo $pengunjung_list->RowIndex ?>_Nama" id="o<?php echo $pengunjung_list->RowIndex ?>_Nama" value="<?php echo ew_HtmlEncode($pengunjung->Nama->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($pengunjung->Jumlah->Visible) { // Jumlah ?>
		<td>
<span id="el<?php echo $pengunjung_list->RowCnt ?>_pengunjung_Jumlah" class="control-group pengunjung_Jumlah">
<input type="text" data-field="x_Jumlah" name="x<?php echo $pengunjung_list->RowIndex ?>_Jumlah" id="x<?php echo $pengunjung_list->RowIndex ?>_Jumlah" size="30" placeholder="<?php echo ew_HtmlEncode($pengunjung->Jumlah->PlaceHolder) ?>" value="<?php echo $pengunjung->Jumlah->EditValue ?>"<?php echo $pengunjung->Jumlah->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_Jumlah" name="o<?php echo $pengunjung_list->RowIndex ?>_Jumlah" id="o<?php echo $pengunjung_list->RowIndex ?>_Jumlah" value="<?php echo ew_HtmlEncode($pengunjung->Jumlah->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($pengunjung->Area->Visible) { // Area ?>
		<td>
<span id="el<?php echo $pengunjung_list->RowCnt ?>_pengunjung_Area" class="control-group pengunjung_Area">
<select data-field="x_Area" id="x<?php echo $pengunjung_list->RowIndex ?>_Area" name="x<?php echo $pengunjung_list->RowIndex ?>_Area"<?php echo $pengunjung->Area->EditAttributes() ?>>
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
</span>
<input type="hidden" data-field="x_Area" name="o<?php echo $pengunjung_list->RowIndex ?>_Area" id="o<?php echo $pengunjung_list->RowIndex ?>_Area" value="<?php echo ew_HtmlEncode($pengunjung->Area->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($pengunjung->CP->Visible) { // CP ?>
		<td>
<span id="el<?php echo $pengunjung_list->RowCnt ?>_pengunjung_CP" class="control-group pengunjung_CP">
<input type="text" data-field="x_CP" name="x<?php echo $pengunjung_list->RowIndex ?>_CP" id="x<?php echo $pengunjung_list->RowIndex ?>_CP" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($pengunjung->CP->PlaceHolder) ?>" value="<?php echo $pengunjung->CP->EditValue ?>"<?php echo $pengunjung->CP->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_CP" name="o<?php echo $pengunjung_list->RowIndex ?>_CP" id="o<?php echo $pengunjung_list->RowIndex ?>_CP" value="<?php echo ew_HtmlEncode($pengunjung->CP->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($pengunjung->NoContact->Visible) { // NoContact ?>
		<td>
<span id="el<?php echo $pengunjung_list->RowCnt ?>_pengunjung_NoContact" class="control-group pengunjung_NoContact">
<input type="text" data-field="x_NoContact" name="x<?php echo $pengunjung_list->RowIndex ?>_NoContact" id="x<?php echo $pengunjung_list->RowIndex ?>_NoContact" size="30" maxlength="20" placeholder="<?php echo ew_HtmlEncode($pengunjung->NoContact->PlaceHolder) ?>" value="<?php echo $pengunjung->NoContact->EditValue ?>"<?php echo $pengunjung->NoContact->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_NoContact" name="o<?php echo $pengunjung_list->RowIndex ?>_NoContact" id="o<?php echo $pengunjung_list->RowIndex ?>_NoContact" value="<?php echo ew_HtmlEncode($pengunjung->NoContact->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($pengunjung->Tanggal->Visible) { // Tanggal ?>
		<td>
<span id="el<?php echo $pengunjung_list->RowCnt ?>_pengunjung_Tanggal" class="control-group pengunjung_Tanggal">
<input type="text" data-field="x_Tanggal" name="x<?php echo $pengunjung_list->RowIndex ?>_Tanggal" id="x<?php echo $pengunjung_list->RowIndex ?>_Tanggal" placeholder="<?php echo ew_HtmlEncode($pengunjung->Tanggal->PlaceHolder) ?>" value="<?php echo $pengunjung->Tanggal->EditValue ?>"<?php echo $pengunjung->Tanggal->EditAttributes() ?>>
<?php if (!$pengunjung->Tanggal->ReadOnly && !$pengunjung->Tanggal->Disabled && @$pengunjung->Tanggal->EditAttrs["readonly"] == "" && @$pengunjung->Tanggal->EditAttrs["disabled"] == "") { ?>
<button id="cal_x<?php echo $pengunjung_list->RowIndex ?>_Tanggal" name="cal_x<?php echo $pengunjung_list->RowIndex ?>_Tanggal" class="btn" type="button"><img src="phpimages/calendar.png" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" style="border: 0;"></button><script type="text/javascript">
ew_CreateCalendar("fpengunjunglist", "x<?php echo $pengunjung_list->RowIndex ?>_Tanggal", "%d/%m/%Y");
</script>
<?php } ?>
</span>
<input type="hidden" data-field="x_Tanggal" name="o<?php echo $pengunjung_list->RowIndex ?>_Tanggal" id="o<?php echo $pengunjung_list->RowIndex ?>_Tanggal" value="<?php echo ew_HtmlEncode($pengunjung->Tanggal->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($pengunjung->Jam->Visible) { // Jam ?>
		<td>
<span id="el<?php echo $pengunjung_list->RowCnt ?>_pengunjung_Jam" class="control-group pengunjung_Jam">
<input type="text" data-field="x_Jam" name="x<?php echo $pengunjung_list->RowIndex ?>_Jam" id="x<?php echo $pengunjung_list->RowIndex ?>_Jam" placeholder="<?php echo ew_HtmlEncode($pengunjung->Jam->PlaceHolder) ?>" value="<?php echo $pengunjung->Jam->EditValue ?>"<?php echo $pengunjung->Jam->EditAttributes() ?>>
</span>
<input type="hidden" data-field="x_Jam" name="o<?php echo $pengunjung_list->RowIndex ?>_Jam" id="o<?php echo $pengunjung_list->RowIndex ?>_Jam" value="<?php echo ew_HtmlEncode($pengunjung->Jam->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($pengunjung->Type->Visible) { // Type ?>
		<td>
<span id="el<?php echo $pengunjung_list->RowCnt ?>_pengunjung_Type" class="control-group pengunjung_Type">
<select data-field="x_Type" id="x<?php echo $pengunjung_list->RowIndex ?>_Type" name="x<?php echo $pengunjung_list->RowIndex ?>_Type"<?php echo $pengunjung->Type->EditAttributes() ?>>
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
fpengunjunglist.Lists["x_Type"].Options = <?php echo (is_array($pengunjung->Type->EditValue)) ? ew_ArrayToJson($pengunjung->Type->EditValue, 1) : "[]" ?>;
</script>
</span>
<input type="hidden" data-field="x_Type" name="o<?php echo $pengunjung_list->RowIndex ?>_Type" id="o<?php echo $pengunjung_list->RowIndex ?>_Type" value="<?php echo ew_HtmlEncode($pengunjung->Type->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($pengunjung->Site->Visible) { // Site ?>
		<td>
<?php if (!$Security->IsAdmin() && $Security->IsLoggedIn() && !$pengunjung->UserIDAllow($pengunjung->CurrentAction)) { // Non system admin ?>
<span id="el<?php echo $pengunjung_list->RowCnt ?>_pengunjung_Site" class="control-group pengunjung_Site">
<select data-field="x_Site" id="x<?php echo $pengunjung_list->RowIndex ?>_Site" name="x<?php echo $pengunjung_list->RowIndex ?>_Site"<?php echo $pengunjung->Site->EditAttributes() ?>>
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
<span id="el<?php echo $pengunjung_list->RowCnt ?>_pengunjung_Site" class="control-group pengunjung_Site">
<select data-field="x_Site" id="x<?php echo $pengunjung_list->RowIndex ?>_Site" name="x<?php echo $pengunjung_list->RowIndex ?>_Site"<?php echo $pengunjung->Site->EditAttributes() ?>>
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
<input type="hidden" data-field="x_Site" name="o<?php echo $pengunjung_list->RowIndex ?>_Site" id="o<?php echo $pengunjung_list->RowIndex ?>_Site" value="<?php echo ew_HtmlEncode($pengunjung->Site->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($pengunjung->Status->Visible) { // Status ?>
		<td>
<span id="el<?php echo $pengunjung_list->RowCnt ?>_pengunjung_Status" class="control-group pengunjung_Status">
<div id="tp_x<?php echo $pengunjung_list->RowIndex ?>_Status" class="<?php echo EW_ITEM_TEMPLATE_CLASSNAME ?>"><input type="radio" name="x<?php echo $pengunjung_list->RowIndex ?>_Status" id="x<?php echo $pengunjung_list->RowIndex ?>_Status" value="{value}"<?php echo $pengunjung->Status->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $pengunjung_list->RowIndex ?>_Status" data-repeatcolumn="5" class="ewItemList">
<?php
$arwrk = $pengunjung->Status->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($pengunjung->Status->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " checked=\"checked\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;

		// Note: No spacing within the LABEL tag
?>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 1) ?>
<label class="radio"><input type="radio" data-field="x_Status" name="x<?php echo $pengunjung_list->RowIndex ?>_Status" id="x<?php echo $pengunjung_list->RowIndex ?>_Status_<?php echo $rowcntwrk ?>" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $pengunjung->Status->EditAttributes() ?>><?php echo $arwrk[$rowcntwrk][1] ?></label>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 2) ?>
<?php
	}
}
?>
</div>
</span>
<input type="hidden" data-field="x_Status" name="o<?php echo $pengunjung_list->RowIndex ?>_Status" id="o<?php echo $pengunjung_list->RowIndex ?>_Status" value="<?php echo ew_HtmlEncode($pengunjung->Status->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($pengunjung->visit->Visible) { // visit ?>
		<td>
<span id="el<?php echo $pengunjung_list->RowCnt ?>_pengunjung_visit" class="control-group pengunjung_visit">
<div id="tp_x<?php echo $pengunjung_list->RowIndex ?>_visit" class="<?php echo EW_ITEM_TEMPLATE_CLASSNAME ?>"><input type="radio" name="x<?php echo $pengunjung_list->RowIndex ?>_visit" id="x<?php echo $pengunjung_list->RowIndex ?>_visit" value="{value}"<?php echo $pengunjung->visit->EditAttributes() ?>></div>
<div id="dsl_x<?php echo $pengunjung_list->RowIndex ?>_visit" data-repeatcolumn="5" class="ewItemList">
<?php
$arwrk = $pengunjung->visit->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($pengunjung->visit->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " checked=\"checked\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;

		// Note: No spacing within the LABEL tag
?>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 1) ?>
<label class="radio"><input type="radio" data-field="x_visit" name="x<?php echo $pengunjung_list->RowIndex ?>_visit" id="x<?php echo $pengunjung_list->RowIndex ?>_visit_<?php echo $rowcntwrk ?>" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $pengunjung->visit->EditAttributes() ?>><?php echo $arwrk[$rowcntwrk][1] ?></label>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 2) ?>
<?php
	}
}
?>
</div>
</span>
<input type="hidden" data-field="x_visit" name="o<?php echo $pengunjung_list->RowIndex ?>_visit" id="o<?php echo $pengunjung_list->RowIndex ?>_visit" value="<?php echo ew_HtmlEncode($pengunjung->visit->OldValue) ?>">
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$pengunjung_list->ListOptions->Render("body", "right", $pengunjung_list->RowCnt);
?>
<script type="text/javascript">
fpengunjunglist.UpdateOpts(<?php echo $pengunjung_list->RowIndex ?>);
</script>
	</tr>
<?php
}
?>
<?php
if ($pengunjung->ExportAll && $pengunjung->Export <> "") {
	$pengunjung_list->StopRec = $pengunjung_list->TotalRecs;
} else {

	// Set the last record to display
	if ($pengunjung_list->TotalRecs > $pengunjung_list->StartRec + $pengunjung_list->DisplayRecs - 1)
		$pengunjung_list->StopRec = $pengunjung_list->StartRec + $pengunjung_list->DisplayRecs - 1;
	else
		$pengunjung_list->StopRec = $pengunjung_list->TotalRecs;
}

// Restore number of post back records
if ($objForm) {
	$objForm->Index = -1;
	if ($objForm->HasValue($pengunjung_list->FormKeyCountName) && ($pengunjung->CurrentAction == "gridadd" || $pengunjung->CurrentAction == "gridedit" || $pengunjung->CurrentAction == "F")) {
		$pengunjung_list->KeyCount = $objForm->GetValue($pengunjung_list->FormKeyCountName);
		$pengunjung_list->StopRec = $pengunjung_list->StartRec + $pengunjung_list->KeyCount - 1;
	}
}
$pengunjung_list->RecCnt = $pengunjung_list->StartRec - 1;
if ($pengunjung_list->Recordset && !$pengunjung_list->Recordset->EOF) {
	$pengunjung_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $pengunjung_list->StartRec > 1)
		$pengunjung_list->Recordset->Move($pengunjung_list->StartRec - 1);
} elseif (!$pengunjung->AllowAddDeleteRow && $pengunjung_list->StopRec == 0) {
	$pengunjung_list->StopRec = $pengunjung->GridAddRowCount;
}

// Initialize aggregate
$pengunjung->RowType = EW_ROWTYPE_AGGREGATEINIT;
$pengunjung->ResetAttrs();
$pengunjung_list->RenderRow();
while ($pengunjung_list->RecCnt < $pengunjung_list->StopRec) {
	$pengunjung_list->RecCnt++;
	if (intval($pengunjung_list->RecCnt) >= intval($pengunjung_list->StartRec)) {
		$pengunjung_list->RowCnt++;

		// Set up key count
		$pengunjung_list->KeyCount = $pengunjung_list->RowIndex;

		// Init row class and style
		$pengunjung->ResetAttrs();
		$pengunjung->CssClass = "";
		if ($pengunjung->CurrentAction == "gridadd") {
			$pengunjung_list->LoadDefaultValues(); // Load default values
		} else {
			$pengunjung_list->LoadRowValues($pengunjung_list->Recordset); // Load row values
		}
		$pengunjung->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$pengunjung->RowAttrs = array_merge($pengunjung->RowAttrs, array('data-rowindex'=>$pengunjung_list->RowCnt, 'id'=>'r' . $pengunjung_list->RowCnt . '_pengunjung', 'data-rowtype'=>$pengunjung->RowType));

		// Render row
		$pengunjung_list->RenderRow();

		// Render list options
		$pengunjung_list->RenderListOptions();
?>
	<tr<?php echo $pengunjung->RowAttributes() ?>>
<?php

// Render list options (body, left)
$pengunjung_list->ListOptions->Render("body", "left", $pengunjung_list->RowCnt);
?>
	<?php if ($pengunjung->Id->Visible) { // Id ?>
		<td<?php echo $pengunjung->Id->CellAttributes() ?>>
<span<?php echo $pengunjung->Id->ViewAttributes() ?>>
<?php echo $pengunjung->Id->ListViewValue() ?></span>
<a id="<?php echo $pengunjung_list->PageObjName . "_row_" . $pengunjung_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($pengunjung->Nama->Visible) { // Nama ?>
		<td<?php echo $pengunjung->Nama->CellAttributes() ?>>
<span<?php echo $pengunjung->Nama->ViewAttributes() ?>>
<?php echo $pengunjung->Nama->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($pengunjung->Jumlah->Visible) { // Jumlah ?>
		<td<?php echo $pengunjung->Jumlah->CellAttributes() ?>>
<span<?php echo $pengunjung->Jumlah->ViewAttributes() ?>>
<?php echo $pengunjung->Jumlah->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($pengunjung->Area->Visible) { // Area ?>
		<td<?php echo $pengunjung->Area->CellAttributes() ?>>
<span<?php echo $pengunjung->Area->ViewAttributes() ?>>
<?php echo $pengunjung->Area->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($pengunjung->CP->Visible) { // CP ?>
		<td<?php echo $pengunjung->CP->CellAttributes() ?>>
<span<?php echo $pengunjung->CP->ViewAttributes() ?>>
<?php echo $pengunjung->CP->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($pengunjung->NoContact->Visible) { // NoContact ?>
		<td<?php echo $pengunjung->NoContact->CellAttributes() ?>>
<span<?php echo $pengunjung->NoContact->ViewAttributes() ?>>
<?php echo $pengunjung->NoContact->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($pengunjung->Tanggal->Visible) { // Tanggal ?>
		<td<?php echo $pengunjung->Tanggal->CellAttributes() ?>>
<span<?php echo $pengunjung->Tanggal->ViewAttributes() ?>>
<?php echo $pengunjung->Tanggal->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($pengunjung->Jam->Visible) { // Jam ?>
		<td<?php echo $pengunjung->Jam->CellAttributes() ?>>
<span<?php echo $pengunjung->Jam->ViewAttributes() ?>>
<?php echo $pengunjung->Jam->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($pengunjung->Type->Visible) { // Type ?>
		<td<?php echo $pengunjung->Type->CellAttributes() ?>>
<span<?php echo $pengunjung->Type->ViewAttributes() ?>>
<?php echo $pengunjung->Type->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($pengunjung->Site->Visible) { // Site ?>
		<td<?php echo $pengunjung->Site->CellAttributes() ?>>
<span<?php echo $pengunjung->Site->ViewAttributes() ?>>
<?php echo $pengunjung->Site->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($pengunjung->Status->Visible) { // Status ?>
		<td<?php echo $pengunjung->Status->CellAttributes() ?>>
<span<?php echo $pengunjung->Status->ViewAttributes() ?>>
<?php echo $pengunjung->Status->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($pengunjung->visit->Visible) { // visit ?>
		<td<?php echo $pengunjung->visit->CellAttributes() ?>>
<span<?php echo $pengunjung->visit->ViewAttributes() ?>>
<?php echo $pengunjung->visit->ListViewValue() ?></span>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$pengunjung_list->ListOptions->Render("body", "right", $pengunjung_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($pengunjung->CurrentAction <> "gridadd")
		$pengunjung_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($pengunjung->CurrentAction == "add" || $pengunjung->CurrentAction == "copy") { ?>
<input type="hidden" name="<?php echo $pengunjung_list->FormKeyCountName ?>" id="<?php echo $pengunjung_list->FormKeyCountName ?>" value="<?php echo $pengunjung_list->KeyCount ?>">
<?php } ?>
<?php if ($pengunjung->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($pengunjung_list->Recordset)
	$pengunjung_list->Recordset->Close();
?>
</td></tr></table>
<?php if ($pengunjung->Export == "") { ?>
<script type="text/javascript">
fpengunjunglistsrch.Init();
fpengunjunglist.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php } ?>
<?php
$pengunjung_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($pengunjung->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$pengunjung_list->Page_Terminate();
?>
