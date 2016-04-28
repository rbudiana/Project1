<!-- Begin Main Menu -->
<div class="ewMenu">
<?php $RootMenu = new cMenu(EW_MENUBAR_ID) ?>
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(29, $Language->MenuPhrase("29", "MenuText"), "audittraillist.php", -1, "", AllowListMenu('{C2A46AD1-29DD-4049-B347-17989E75216E}audittrail'), FALSE);
$RootMenu->AddMenuItem(14, $Language->MenuPhrase("14", "MenuText"), "", -1, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(5, $Language->MenuPhrase("5", "MenuText"), "userlist.php", 14, "", AllowListMenu('{C2A46AD1-29DD-4049-B347-17989E75216E}user'), FALSE);
$RootMenu->AddMenuItem(6, $Language->MenuPhrase("6", "MenuText"), "userlevelpermissionslist.php", 14, "", (@$_SESSION[EW_SESSION_USER_LEVEL] & EW_ALLOW_ADMIN) == EW_ALLOW_ADMIN, FALSE);
$RootMenu->AddMenuItem(7, $Language->MenuPhrase("7", "MenuText"), "userlevelslist.php", 14, "", (@$_SESSION[EW_SESSION_USER_LEVEL] & EW_ALLOW_ADMIN) == EW_ALLOW_ADMIN, FALSE);
$RootMenu->AddMenuItem(2, $Language->MenuPhrase("2", "MenuText"), "typepengunjunglist.php", 14, "", AllowListMenu('{C2A46AD1-29DD-4049-B347-17989E75216E}typepengunjung'), FALSE);
$RootMenu->AddMenuItem(4, $Language->MenuPhrase("4", "MenuText"), "provinsilist.php", 14, "", AllowListMenu('{C2A46AD1-29DD-4049-B347-17989E75216E}provinsi'), FALSE);
$RootMenu->AddMenuItem(3, $Language->MenuPhrase("3", "MenuText"), "kotalist.php", 14, "", AllowListMenu('{C2A46AD1-29DD-4049-B347-17989E75216E}kota'), FALSE);
$RootMenu->AddMenuItem(1, $Language->MenuPhrase("1", "MenuText"), "pengunjunglist.php", -1, "", AllowListMenu('{C2A46AD1-29DD-4049-B347-17989E75216E}pengunjung'), FALSE);
$RootMenu->AddMenuItem(9, $Language->MenuPhrase("9", "MenuText"), "tamuviplist.php", -1, "", AllowListMenu('{C2A46AD1-29DD-4049-B347-17989E75216E}tamuvip'), FALSE);
$RootMenu->AddMenuItem(26, $Language->MenuPhrase("26", "MenuText"), "", -1, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(15, $Language->MenuPhrase("15", "MenuText"), "parsyslist.php", 26, "", AllowListMenu('{C2A46AD1-29DD-4049-B347-17989E75216E}parsys'), FALSE);
$RootMenu->AddMenuItem(44, $Language->MenuPhrase("44", "MenuText"), "", -1, "", TRUE, FALSE, TRUE);
$RootMenu->AddMenuItem(27, $Language->MenuPhrase("27", "MenuText"), "vrekapbytypelist.php", 44, "", AllowListMenu('{C2A46AD1-29DD-4049-B347-17989E75216E}vrekapbytype'), FALSE);
$RootMenu->AddMenuItem(45, $Language->MenuPhrase("45", "MenuText"), "vrekapbyvehiclelist.php", 44, "", AllowListMenu('{C2A46AD1-29DD-4049-B347-17989E75216E}vrekapbyvehicle'), FALSE);
$RootMenu->AddMenuItem(-2, $Language->Phrase("ChangePwd"), "changepwd.php", -1, "", IsLoggedIn() && !IsSysAdmin());
$RootMenu->AddMenuItem(-1, $Language->Phrase("Logout"), "logout.php", -1, "", IsLoggedIn());
$RootMenu->AddMenuItem(-1, $Language->Phrase("Login"), "login.php", -1, "", !IsLoggedIn() && substr(@$_SERVER["URL"], -1 * strlen("login.php")) <> "login.php");
$RootMenu->Render();
?>
</div>
<!-- End Main Menu -->
