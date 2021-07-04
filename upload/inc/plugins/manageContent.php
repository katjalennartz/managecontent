<?php
if (!defined("IN_MYBB")) {
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}
if (!$pluginlist)
	$pluginlist = $cache->read("plugins");

$plugins->add_hook("global_start", "manageContent_global");

$plugins->add_hook("admin_config_menu", "manageContent_admin_config_menu");
$plugins->add_hook("admin_config_action_handler", "manageContent_admin_config_action_handler");
$plugins->add_hook("admin_config_permissions", "manageContent_admin_config_permissions");

// error_reporting ( -1 );
// ini_set ( 'display_errors', true );
function manageContent_info()
{
	return array(
		"name"      => "Global Content",
		"description"  => "Verwalte und lege globale Inhalte an, wie News, Sisters etc.",
		"website"    => "https://github.com/katjalennartz",
		"author"    => "Risuena",
		"authorsite"  => "https://github.com/katjalennartz",
		"version"    => "1.0",
		"compatibility" => "18*"
	);
}

function manageContent_is_installed()
{
	global $db;
	if ($db->table_exists("mc_types")) {
		return true;
	}
	return false;
}

function manageContent_install()
{
	//
	//Variable Typ
	//Klasse zum ansprechen
	//scrollbarlang

	//Table vor manageContent Types
	global $db, $mybb, $lang;
	// $lang->load("manageContent");
	if (!$db->table_exists("mc_types")) {
		$db->write_query("CREATE TABLE `" . TABLE_PREFIX . "mc_types` (
    `mc_id` int(20) NOT NULL AUTO_INCREMENT,
    `mc_type` varchar(200) NOT NULL,
    `mc_scrollable` TINYINT(1) NOT NULL DEFAULT 0,
		`mc_active` TINYINT(1) NOT NULL DEFAULT 1,
		`mc_scrollheight` int(10),
PRIMARY KEY (`mc_id`)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	}
	//Table vor manageContent Content
	if (!$db->table_exists("mc_content")) {
		$db->write_query("CREATE TABLE `" . TABLE_PREFIX . "mc_content` (
    `mc_cid` int(20) NOT NULL AUTO_INCREMENT,
    `mc_type` varchar(200) NOT NULL,
    `mc_content` varchar(5000) NOT NULL,
    `mc_sort` int(10),
    `mc_date` datetime NOT NULL,
		`mc_showdate` TINYINT(1) NOT NULL DEFAULT 0,
PRIMARY KEY (`mc_cid`)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	}
}

function manageContent_uninstall()
{
	global $db, $mybb;
	if ($db->table_exists("mc_content")) {
		$db->drop_table("mc_content");
	}
	if ($db->table_exists("mc_types")) {
		$db->drop_table("mc_types");
	}
}

function manageContent_activate()
{
}

function manageContent_deactivate()
{
}


function manageContent_admin_config_menu($sub_menu)
{
	global $lang;

	$lang->load("config_manageContent");

	$sub_menu[] = array("id" => "manageContent", "title" => $lang->manageContent, "link" => "index.php?module=config-manageContent");

	return $sub_menu;
}

function manageContent_admin_config_action_handler($actions)
{
	$actions['manageContent'] = array(
		"active" => "manageContent",
		"file" => "manageContent.php"
	);

	return $actions;
}

function manageContent_admin_config_permissions($admin_permissions)
{
	global $lang;

	$lang->load("config_manageContent");

	$admin_permissions['manageContent'] = $lang->manageContent_permission;

	return $admin_permissions;
}

function manageContent_global()
{
	global $manageContent, $mybb, $db;
	//parser
	require_once MYBB_ROOT . "inc/class_parser.php";
	$parser = new postParser;
	$options = array(
		"allow_html" => 1,
		"allow_mycode" => 1,
		"allow_smilies" => 1,
		"allow_imgcode" => 1,
		"filter_badwords" => 0,
		"nl2br" => 1,
		"allow_videocode" => 0
	);

	//Daten aus der DB Holen
	$mc_types = $db->simple_select("mc_types", "*");

	while ($get_types = $db->fetch_array($mc_types)) {
		//typ
		$typname = $get_types['mc_type'];
		//dynamische globale variable, damit der Inhalt auch angezeigt wird.
		global ${'mc_' . $get_types['mc_type']};
		//alle dazugehörigen einträge
		$mc_content = "";
		$mc_content_query = $db->simple_select("mc_content", "*", "mc_type = '{$get_types['mc_type']}'");
		while ($get_content = $db->fetch_array($mc_content_query)) {
			$content = $parser->parse_message($get_content['mc_content'], $options);
			if ($get_content['mc_showdate'] == 1) {
				$content_date = "<div class=\"{$typname}_date\">" . date('d.m.y', strtotime($get_content['mc_date'])) . "</div>";
			}
			//wir bauen den inhalt (innere div box)
			$mc_content .= "
					<div class=\"mc_item_{$typname}\">
					{$content}
					{$content_date}
					</div>";
		}
		if ($get_types['mc_scrollable'] == 1) {
			//if the height is not set
			if ($get_types['mc_scrollheight'] == 0) {
				$get_types['mc_scrollheight'] = "200";
			}
			$scrollable = "style=\"max-height:{$get_types['mc_scrollheight']}px; overflow:auto\"";
		}
		// wir bauen die äußere div box
		if ($get_types['mc_active'] == 1){
		${'mc_' . $get_types['mc_type']} = "
		<div class=\"mc_box_{$typename}\" " . $scrollable . ">
			{$mc_content}
		</div>
		";}
	}
}
