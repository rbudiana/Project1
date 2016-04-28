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

$pengunjung_search = NULL; // Initialize page object first

class cpengunjung_search extends cpengunjung {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{C2A46AD1-29DD-4049-B347-17989E75216E}";

	// Table name
	var $TableName = 'pengunjung';

	// Page object name
	var $PageObjName = 'pengunjung_search';

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

		// Table object (pengunjung)
		if (!isset($GLOBALS["pengunjung"]) || get_class($GLOBALS["pengunjung"]) == "cpengunjung") {
			$GLOBALS["pengunjung"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["pengunjung"];
		}

		// Table object (user)
		if (!isset($GLOBALS['user'])) $GLOBALS['user'] = new cuser();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'search', TRUE);

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
		if (!$Security->CanSearch()) {
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

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsSearchError;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();
		if ($this->IsPageRequest()) { // Validate request

			// Get action
			$this->CurrentAction = $objForm->GetValue("a_search");
			switch ($this->CurrentAction) {
				case "S": // Get search criteria

					// Build search string for advanced search, remove blank field
					$this->LoadSearchValues(); // Get search values
					if ($this->ValidateSearch()) {
						$sSrchStr = $this->BuildAdvancedSearch();
					} else {
						$sSrchStr = "";
						$this->setFailureMessage($gsSearchError);
					}
					if ($sSrchStr <> "") {
						$sSrchStr = $this->UrlParm($sSrchStr);
						$this->Page_Terminate("pengunjunglist.php" . "?" . $sSrchStr); // Go to list page
					}
			}
		}

		// Restore search settings from Session
		if ($gsSearchError == "")
			$this->LoadAdvancedSearch();

		// Render row for search
		$this->RowType = EW_ROWTYPE_SEARCH;
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Build advanced search
	function BuildAdvancedSearch() {
		$sSrchUrl = "";
		$this->BuildSearchUrl($sSrchUrl, $this->Nama); // Nama
		$this->BuildSearchUrl($sSrchUrl, $this->Provinsi); // Provinsi
		$this->BuildSearchUrl($sSrchUrl, $this->Area); // Area
		$this->BuildSearchUrl($sSrchUrl, $this->CP); // CP
		$this->BuildSearchUrl($sSrchUrl, $this->Tanggal); // Tanggal
		$this->BuildSearchUrl($sSrchUrl, $this->Vechicle); // Vechicle
		$this->BuildSearchUrl($sSrchUrl, $this->Type); // Type
		$this->BuildSearchUrl($sSrchUrl, $this->Site); // Site
		$this->BuildSearchUrl($sSrchUrl, $this->Status); // Status
		$this->BuildSearchUrl($sSrchUrl, $this->visit); // visit
		if ($sSrchUrl <> "") $sSrchUrl .= "&";
		$sSrchUrl .= "cmd=search";
		return $sSrchUrl;
	}

	// Build search URL
	function BuildSearchUrl(&$Url, &$Fld, $OprOnly=FALSE) {
		global $objForm;
		$sWrk = "";
		$FldParm = substr($Fld->FldVar, 2);
		$FldVal = $objForm->GetValue("x_$FldParm");
		$FldOpr = $objForm->GetValue("z_$FldParm");
		$FldCond = $objForm->GetValue("v_$FldParm");
		$FldVal2 = $objForm->GetValue("y_$FldParm");
		$FldOpr2 = $objForm->GetValue("w_$FldParm");
		$FldVal = ew_StripSlashes($FldVal);
		if (is_array($FldVal)) $FldVal = implode(",", $FldVal);
		$FldVal2 = ew_StripSlashes($FldVal2);
		if (is_array($FldVal2)) $FldVal2 = implode(",", $FldVal2);
		$FldOpr = strtoupper(trim($FldOpr));
		$lFldDataType = ($Fld->FldIsVirtual) ? EW_DATATYPE_STRING : $Fld->FldDataType;
		if ($FldOpr == "BETWEEN") {
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal) && $this->SearchValueIsNumeric($Fld, $FldVal2));
			if ($FldVal <> "" && $FldVal2 <> "" && $IsValidValue) {
				$sWrk = "x_" . $FldParm . "=" . urlencode($FldVal) .
					"&y_" . $FldParm . "=" . urlencode($FldVal2) .
					"&z_" . $FldParm . "=" . urlencode($FldOpr);
			}
		} else {
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal));
			if ($FldVal <> "" && $IsValidValue && ew_IsValidOpr($FldOpr, $lFldDataType)) {
				$sWrk = "x_" . $FldParm . "=" . urlencode($FldVal) .
					"&z_" . $FldParm . "=" . urlencode($FldOpr);
			} elseif ($FldOpr == "IS NULL" || $FldOpr == "IS NOT NULL" || ($FldOpr <> "" && $OprOnly && ew_IsValidOpr($FldOpr, $lFldDataType))) {
				$sWrk = "z_" . $FldParm . "=" . urlencode($FldOpr);
			}
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal2));
			if ($FldVal2 <> "" && $IsValidValue && ew_IsValidOpr($FldOpr2, $lFldDataType)) {
				if ($sWrk <> "") $sWrk .= "&v_" . $FldParm . "=" . urlencode($FldCond) . "&";
				$sWrk .= "y_" . $FldParm . "=" . urlencode($FldVal2) .
					"&w_" . $FldParm . "=" . urlencode($FldOpr2);
			} elseif ($FldOpr2 == "IS NULL" || $FldOpr2 == "IS NOT NULL" || ($FldOpr2 <> "" && $OprOnly && ew_IsValidOpr($FldOpr2, $lFldDataType))) {
				if ($sWrk <> "") $sWrk .= "&v_" . $FldParm . "=" . urlencode($FldCond) . "&";
				$sWrk .= "w_" . $FldParm . "=" . urlencode($FldOpr2);
			}
		}
		if ($sWrk <> "") {
			if ($Url <> "") $Url .= "&";
			$Url .= $sWrk;
		}
	}

	function SearchValueIsNumeric($Fld, $Value) {
		if (ew_IsFloatFormat($Fld->FldType)) $Value = ew_StrToFloat($Value);
		return is_numeric($Value);
	}

	//  Load search values for validation
	function LoadSearchValues() {
		global $objForm;

		// Load search values
		// Nama

		$this->Nama->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Nama"));
		$this->Nama->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Nama");

		// Provinsi
		$this->Provinsi->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Provinsi"));
		$this->Provinsi->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Provinsi");

		// Area
		$this->Area->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Area"));
		$this->Area->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Area");

		// CP
		$this->CP->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_CP"));
		$this->CP->AdvancedSearch->SearchOperator = $objForm->GetValue("z_CP");

		// Tanggal
		$this->Tanggal->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Tanggal"));
		$this->Tanggal->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Tanggal");
		$this->Tanggal->AdvancedSearch->SearchCondition = $objForm->GetValue("v_Tanggal");
		$this->Tanggal->AdvancedSearch->SearchValue2 = ew_StripSlashes($objForm->GetValue("y_Tanggal"));
		$this->Tanggal->AdvancedSearch->SearchOperator2 = $objForm->GetValue("w_Tanggal");

		// Vechicle
		$this->Vechicle->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Vechicle"));
		$this->Vechicle->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Vechicle");

		// Type
		$this->Type->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Type"));
		$this->Type->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Type");

		// Site
		$this->Site->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Site"));
		$this->Site->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Site");

		// Status
		$this->Status->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Status"));
		$this->Status->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Status");

		// visit
		$this->visit->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_visit"));
		$this->visit->AdvancedSearch->SearchOperator = $objForm->GetValue("z_visit");
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
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

			// Nama
			$this->Nama->LinkCustomAttributes = "";
			$this->Nama->HrefValue = "";
			$this->Nama->TooltipValue = "";

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

			// Tanggal
			$this->Tanggal->LinkCustomAttributes = "";
			$this->Tanggal->HrefValue = "";
			$this->Tanggal->TooltipValue = "";

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

			// visit
			$this->visit->LinkCustomAttributes = "";
			$this->visit->HrefValue = "";
			$this->visit->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// Nama
			$this->Nama->EditCustomAttributes = "";
			$this->Nama->EditValue = ew_HtmlEncode($this->Nama->AdvancedSearch->SearchValue);
			$this->Nama->PlaceHolder = ew_RemoveHtml($this->Nama->FldCaption());

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
			$this->CP->EditValue = ew_HtmlEncode($this->CP->AdvancedSearch->SearchValue);
			$this->CP->PlaceHolder = ew_RemoveHtml($this->CP->FldCaption());

			// Tanggal
			$this->Tanggal->EditCustomAttributes = "";
			$this->Tanggal->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->Tanggal->AdvancedSearch->SearchValue, 7), 7));
			$this->Tanggal->PlaceHolder = ew_RemoveHtml($this->Tanggal->FldCaption());
			$this->Tanggal->EditCustomAttributes = "";
			$this->Tanggal->EditValue2 = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->Tanggal->AdvancedSearch->SearchValue2, 7), 7));
			$this->Tanggal->PlaceHolder = ew_RemoveHtml($this->Tanggal->FldCaption());

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
			if (!$Security->IsAdmin() && $Security->IsLoggedIn() && !$this->UserIDAllow("search")) { // Non system admin
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
		if (!ew_CheckEuroDate($this->Tanggal->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->Tanggal->FldErrMsg());
		}
		if (!ew_CheckEuroDate($this->Tanggal->AdvancedSearch->SearchValue2)) {
			ew_AddMessage($gsSearchError, $this->Tanggal->FldErrMsg());
		}

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

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "pengunjunglist.php", $this->TableVar, TRUE);
		$PageId = "search";
		$Breadcrumb->Add("search", $PageId, ew_CurrentUrl());
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
if (!isset($pengunjung_search)) $pengunjung_search = new cpengunjung_search();

// Page init
$pengunjung_search->Page_Init();

// Page main
$pengunjung_search->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$pengunjung_search->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var pengunjung_search = new ew_Page("pengunjung_search");
pengunjung_search.PageID = "search"; // Page ID
var EW_PAGE_ID = pengunjung_search.PageID; // For backward compatibility

// Form object
var fpengunjungsearch = new ew_Form("fpengunjungsearch");

// Form_CustomValidate event
fpengunjungsearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fpengunjungsearch.ValidateRequired = true;
<?php } else { ?>
fpengunjungsearch.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fpengunjungsearch.Lists["x_Provinsi"] = {"LinkField":"x_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_NamaProv","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};
fpengunjungsearch.Lists["x_Area"] = {"LinkField":"x_id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Kota","","",""],"ParentFields":["x_Provinsi"],"FilterFields":["x_Prov"],"Options":[]};
fpengunjungsearch.Lists["x_Type"] = {"LinkField":"x_Id","Ajax":null,"AutoFill":false,"DisplayFields":["x_Description","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
// Validate function for search

fpengunjungsearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	this.PostAutoSuggest();
	var infix = "";
	elm = this.GetElements("x" + infix + "_Tanggal");
	if (elm && !ew_CheckEuroDate(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($pengunjung->Tanggal->FldErrMsg()) ?>");

	// Set up row object
	ew_ElementsToRow(fobj);

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $pengunjung_search->ShowPageHeader(); ?>
<?php
$pengunjung_search->ShowMessage();
?>
<form name="fpengunjungsearch" id="fpengunjungsearch" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="pengunjung">
<input type="hidden" name="a_search" id="a_search" value="S">
<table class="ewGrid"><tr><td>
<table id="tbl_pengunjungsearch" class="table table-bordered table-striped">
<?php if ($pengunjung->Nama->Visible) { // Nama ?>
	<tr id="r_Nama">
		<td><span id="elh_pengunjung_Nama"><?php echo $pengunjung->Nama->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Nama" id="z_Nama" value="LIKE"></span></td>
		<td<?php echo $pengunjung->Nama->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_pengunjung_Nama" class="control-group">
<input type="text" data-field="x_Nama" name="x_Nama" id="x_Nama" size="100" maxlength="100" placeholder="<?php echo ew_HtmlEncode($pengunjung->Nama->PlaceHolder) ?>" value="<?php echo $pengunjung->Nama->EditValue ?>"<?php echo $pengunjung->Nama->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($pengunjung->Provinsi->Visible) { // Provinsi ?>
	<tr id="r_Provinsi">
		<td><span id="elh_pengunjung_Provinsi"><?php echo $pengunjung->Provinsi->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Provinsi" id="z_Provinsi" value="="></span></td>
		<td<?php echo $pengunjung->Provinsi->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_pengunjung_Provinsi" class="control-group">
<?php $pengunjung->Provinsi->EditAttrs["onchange"] = "ew_UpdateOpt.call(this, ['x_Area']); " . @$pengunjung->Provinsi->EditAttrs["onchange"]; ?>
<select data-field="x_Provinsi" id="x_Provinsi" name="x_Provinsi"<?php echo $pengunjung->Provinsi->EditAttributes() ?>>
<?php
if (is_array($pengunjung->Provinsi->EditValue)) {
	$arwrk = $pengunjung->Provinsi->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($pengunjung->Provinsi->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fpengunjungsearch.Lists["x_Provinsi"].Options = <?php echo (is_array($pengunjung->Provinsi->EditValue)) ? ew_ArrayToJson($pengunjung->Provinsi->EditValue, 1) : "[]" ?>;
</script>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($pengunjung->Area->Visible) { // Area ?>
	<tr id="r_Area">
		<td><span id="elh_pengunjung_Area"><?php echo $pengunjung->Area->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Area" id="z_Area" value="LIKE"></span></td>
		<td<?php echo $pengunjung->Area->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_pengunjung_Area" class="control-group">
<select data-field="x_Area" id="x_Area" name="x_Area"<?php echo $pengunjung->Area->EditAttributes() ?>>
<?php
if (is_array($pengunjung->Area->EditValue)) {
	$arwrk = $pengunjung->Area->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($pengunjung->Area->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fpengunjungsearch.Lists["x_Area"].Options = <?php echo (is_array($pengunjung->Area->EditValue)) ? ew_ArrayToJson($pengunjung->Area->EditValue, 1) : "[]" ?>;
</script>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($pengunjung->CP->Visible) { // CP ?>
	<tr id="r_CP">
		<td><span id="elh_pengunjung_CP"><?php echo $pengunjung->CP->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_CP" id="z_CP" value="LIKE"></span></td>
		<td<?php echo $pengunjung->CP->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_pengunjung_CP" class="control-group">
<input type="text" data-field="x_CP" name="x_CP" id="x_CP" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($pengunjung->CP->PlaceHolder) ?>" value="<?php echo $pengunjung->CP->EditValue ?>"<?php echo $pengunjung->CP->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($pengunjung->Tanggal->Visible) { // Tanggal ?>
	<tr id="r_Tanggal">
		<td><span id="elh_pengunjung_Tanggal"><?php echo $pengunjung->Tanggal->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("BETWEEN") ?><input type="hidden" name="z_Tanggal" id="z_Tanggal" value="BETWEEN"></span></td>
		<td<?php echo $pengunjung->Tanggal->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_pengunjung_Tanggal" class="control-group">
<input type="text" data-field="x_Tanggal" name="x_Tanggal" id="x_Tanggal" placeholder="<?php echo ew_HtmlEncode($pengunjung->Tanggal->PlaceHolder) ?>" value="<?php echo $pengunjung->Tanggal->EditValue ?>"<?php echo $pengunjung->Tanggal->EditAttributes() ?>>
<?php if (!$pengunjung->Tanggal->ReadOnly && !$pengunjung->Tanggal->Disabled && @$pengunjung->Tanggal->EditAttrs["readonly"] == "" && @$pengunjung->Tanggal->EditAttrs["disabled"] == "") { ?>
<button id="cal_x_Tanggal" name="cal_x_Tanggal" class="btn" type="button"><img src="phpimages/calendar.png" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" style="border: 0;"></button><script type="text/javascript">
ew_CreateCalendar("fpengunjungsearch", "x_Tanggal", "%d/%m/%Y");
</script>
<?php } ?>
</span>
				<span class="ewSearchCond btw1_Tanggal">&nbsp;<?php echo $Language->Phrase("AND") ?>&nbsp;</span>
				<span id="e2_pengunjung_Tanggal" class="control-group  btw1_Tanggal">
<input type="text" data-field="x_Tanggal" name="y_Tanggal" id="y_Tanggal" placeholder="<?php echo ew_HtmlEncode($pengunjung->Tanggal->PlaceHolder) ?>" value="<?php echo $pengunjung->Tanggal->EditValue2 ?>"<?php echo $pengunjung->Tanggal->EditAttributes() ?>>
<?php if (!$pengunjung->Tanggal->ReadOnly && !$pengunjung->Tanggal->Disabled && @$pengunjung->Tanggal->EditAttrs["readonly"] == "" && @$pengunjung->Tanggal->EditAttrs["disabled"] == "") { ?>
<button id="cal_y_Tanggal" name="cal_y_Tanggal" class="btn" type="button"><img src="phpimages/calendar.png" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" style="border: 0;"></button><script type="text/javascript">
ew_CreateCalendar("fpengunjungsearch", "y_Tanggal", "%d/%m/%Y");
</script>
<?php } ?>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($pengunjung->Vechicle->Visible) { // Vechicle ?>
	<tr id="r_Vechicle">
		<td><span id="elh_pengunjung_Vechicle"><?php echo $pengunjung->Vechicle->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Vechicle" id="z_Vechicle" value="LIKE"></span></td>
		<td<?php echo $pengunjung->Vechicle->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_pengunjung_Vechicle" class="control-group">
<select data-field="x_Vechicle" id="x_Vechicle" name="x_Vechicle"<?php echo $pengunjung->Vechicle->EditAttributes() ?>>
<?php
if (is_array($pengunjung->Vechicle->EditValue)) {
	$arwrk = $pengunjung->Vechicle->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($pengunjung->Vechicle->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($pengunjung->Type->Visible) { // Type ?>
	<tr id="r_Type">
		<td><span id="elh_pengunjung_Type"><?php echo $pengunjung->Type->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Type" id="z_Type" value="LIKE"></span></td>
		<td<?php echo $pengunjung->Type->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_pengunjung_Type" class="control-group">
<select data-field="x_Type" id="x_Type" name="x_Type"<?php echo $pengunjung->Type->EditAttributes() ?>>
<?php
if (is_array($pengunjung->Type->EditValue)) {
	$arwrk = $pengunjung->Type->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($pengunjung->Type->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fpengunjungsearch.Lists["x_Type"].Options = <?php echo (is_array($pengunjung->Type->EditValue)) ? ew_ArrayToJson($pengunjung->Type->EditValue, 1) : "[]" ?>;
</script>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($pengunjung->Site->Visible) { // Site ?>
	<tr id="r_Site">
		<td><span id="elh_pengunjung_Site"><?php echo $pengunjung->Site->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Site" id="z_Site" value="LIKE"></span></td>
		<td<?php echo $pengunjung->Site->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_pengunjung_Site" class="control-group">
<?php if (!$Security->IsAdmin() && $Security->IsLoggedIn() && !$pengunjung->UserIDAllow("search")) { // Non system admin ?>
<select data-field="x_Site" id="x_Site" name="x_Site"<?php echo $pengunjung->Site->EditAttributes() ?>>
<?php
if (is_array($pengunjung->Site->EditValue)) {
	$arwrk = $pengunjung->Site->EditValue;
	if ($arwrk[0][0] <> "") echo "<option value=\"\">" . $Language->Phrase("PleaseSelect") . "</option>";
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($pengunjung->Site->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php } else { ?>
<select data-field="x_Site" id="x_Site" name="x_Site"<?php echo $pengunjung->Site->EditAttributes() ?>>
<?php
if (is_array($pengunjung->Site->EditValue)) {
	$arwrk = $pengunjung->Site->EditValue;
	if ($arwrk[0][0] <> "") echo "<option value=\"\">" . $Language->Phrase("PleaseSelect") . "</option>";
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($pengunjung->Site->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
<?php } ?>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($pengunjung->Status->Visible) { // Status ?>
	<tr id="r_Status">
		<td><span id="elh_pengunjung_Status"><?php echo $pengunjung->Status->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Status" id="z_Status" value="LIKE"></span></td>
		<td<?php echo $pengunjung->Status->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_pengunjung_Status" class="control-group">
<div id="tp_x_Status" class="<?php echo EW_ITEM_TEMPLATE_CLASSNAME ?>"><input type="radio" name="x_Status" id="x_Status" value="{value}"<?php echo $pengunjung->Status->EditAttributes() ?>></div>
<div id="dsl_x_Status" data-repeatcolumn="5" class="ewItemList">
<?php
$arwrk = $pengunjung->Status->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($pengunjung->Status->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " checked=\"checked\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;

		// Note: No spacing within the LABEL tag
?>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 1) ?>
<label class="radio"><input type="radio" data-field="x_Status" name="x_Status" id="x_Status_<?php echo $rowcntwrk ?>" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $pengunjung->Status->EditAttributes() ?>><?php echo $arwrk[$rowcntwrk][1] ?></label>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 2) ?>
<?php
	}
}
?>
</div>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php if ($pengunjung->visit->Visible) { // visit ?>
	<tr id="r_visit">
		<td><span id="elh_pengunjung_visit"><?php echo $pengunjung->visit->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_visit" id="z_visit" value="LIKE"></span></td>
		<td<?php echo $pengunjung->visit->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_pengunjung_visit" class="control-group">
<div id="tp_x_visit" class="<?php echo EW_ITEM_TEMPLATE_CLASSNAME ?>"><input type="radio" name="x_visit" id="x_visit" value="{value}"<?php echo $pengunjung->visit->EditAttributes() ?>></div>
<div id="dsl_x_visit" data-repeatcolumn="5" class="ewItemList">
<?php
$arwrk = $pengunjung->visit->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($pengunjung->visit->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " checked=\"checked\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;

		// Note: No spacing within the LABEL tag
?>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 1) ?>
<label class="radio"><input type="radio" data-field="x_visit" name="x_visit" id="x_visit_<?php echo $rowcntwrk ?>" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $pengunjung->visit->EditAttributes() ?>><?php echo $arwrk[$rowcntwrk][1] ?></label>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 2) ?>
<?php
	}
}
?>
</div>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("Search") ?></button>
<button class="btn ewButton" name="btnReset" id="btnReset" type="button" onclick="ew_ClearForm(this.form);"><?php echo $Language->Phrase("Reset") ?></button>
</form>
<script type="text/javascript">
fpengunjungsearch.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$pengunjung_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$pengunjung_search->Page_Terminate();
?>
