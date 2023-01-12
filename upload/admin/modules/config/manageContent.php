<?php
if (!defined("IN_MYBB")) {
  die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

//erstellen einer Konstanten (unveränderbare Variable). Wir wollen Tipparbeit sparen :D 
define("MODULE", "config-manageContent");

$page->add_breadcrumb_item($lang->manageContent, "index.php?module=" . MODULE);

/* Insert new manageContent */
if ($mybb->input['action'] == "do_addType") { //hier speichern wir neue Inhaltstypen 

  //Erst einmal Fehler abfangen.
  if (!verify_post_check($mybb->input['my_post_key'])) {
    flash_message($lang->invalid_post_verify_key2, 'error');
    admin_redirect("index.php?module=" . MODULE . "&action=addType");
  }
  if (!strlen(trim($mybb->input['active']))) {
    flash_message($lang->manageContent_active_not, 'error');
    admin_redirect("index.php?module=" . MODULE . "&action=addType");
  }
  if (!strlen(trim($mybb->input['typname']))) {
    flash_message($lang->manageContent_typname_not, 'error');
    admin_redirect("index.php?module=" . MODULE . "&action=addType");
  }
  if (!strlen(trim($mybb->input['scrollable']))) {
    flash_message($lang->manageContent_scrollable_not, 'error');
    admin_redirect("index.php?module=" . MODULE . "&action=addType");
  }
  if (!strlen(trim($mybb->input['guest']))) {
    flash_message($lang->manageContent_guest_not, 'error');
    admin_redirect("index.php?module=" . MODULE . "&action=addType");
  }
  if (!strlen(trim($mybb->input['guest_only']))) {
    flash_message($lang->manageContent_guest_only_not, 'error');
    admin_redirect("index.php?module=" . MODULE . "&action=addType");
  }
  //Array zum speichern erstellen
  $insert = array(
    "mc_active" => $mybb->input['active'],
    "mc_type" => $db->escape_string($mybb->input['typname']),
    "mc_scrollable" => $mybb->input['scrollable'],
    "mc_scrollheight" => $mybb->input['scrollheight'],
    "mc_guest" => $mybb->input['guest'],
    "mc_guest_only" => $mybb->input['guest_only'],
  );

  $db->insert_query("mc_types", $insert);
  flash_message($lang->manageContent_addType_success, 'success');
  admin_redirect("index.php?module=" . MODULE . "&action=list");
} elseif ($mybb->input['action'] == "do_add") { //Hier speichern wir globale inhalte

  //checkt post key, irgendein sicherheitsdring
  if (!verify_post_check($mybb->input['my_post_key'])) {
    flash_message($lang->invalid_post_verify_key2, 'error');
    admin_redirect("index.php?module=" . MODULE . "&action=add");
  }

  //Fehler abfangen und testen
  if (!strlen(trim($mybb->input['disporder']))) {
    //Wurde ein inhaltstyp gewählt
    flash_message($lang->manageContent_disporder_not, 'error');
    admin_redirect("index.php?module=" . MODULE . "&action=add");
  }

  if (!strlen(trim($mybb->input['managecontent']))) {
    //Wurde ein inhaltstyp gewählt
    flash_message($lang->manageContent_manageContent_not, 'error');
    admin_redirect("index.php?module=" . MODULE . "&action=add");
  }

  if (!strlen(trim($mybb->input['contenttyp']))) {
    //wurde eine Sortierung angeben
    flash_message($lang->manageContent_contenttyp_not, 'error');
    admin_redirect("index.php?module=" . MODULE . "&action=add");
  }

  if (!strlen(trim($mybb->input['showdate']))) {
    flash_message($lang->manageContent_showdate_not, 'error');
    admin_redirect("index.php?module=" . MODULE . "&action=add");
  }

  $insert = array(
    "mc_type" => $db->escape_string($mybb->input['contenttyp']),
    "mc_content" => $db->escape_string($mybb->input['managecontent']),
    "mc_sort" => intval($mybb->input['disporder']),
    "mc_showdate" => intval($mybb->input['showdate']),
    "mc_date" => date("Y.m.d H:i"),
  );
  $db->insert_query("mc_content", $insert);

  flash_message($lang->manageContent_add_success, 'success');
  admin_redirect("index.php?module=" . MODULE . "&action=list");

  /* Seite um neue Inhaltstypen hinzuzufügen */
} elseif ($mybb->input['action'] == "addType") {
  // echo date("d.m.Y");
  //Hier erstellen wir die Formulare
  //Inhaltstypen hinzufügen
  $page->add_breadcrumb_item($lang->manageContent_addType, "index.php?module=" . MODULE . "&action=addType");
  $page->output_header($lang->manageContent_addType);
  generate_tabs("addType");

  //Inhaltstypen
  $form = new Form("index.php?module=" . MODULE . "&amp;action=do_addType", "post", "addType");
  $form_container = new FormContainer($lang->manageContent_addType);

  //inhaltstyp aktiv?
  $id = "active";
  $add_active = $form->generate_yes_no_radio("active", 1, true, array("id" => $id . "_yes", "class" => $id), array("id" => $id . "_no", "class" => $id));
  $form_container->output_row($lang->manageContent_active . " <em>*</em>", '', $add_active);

  //Inhaltstypname 
  $add_typname = $form->generate_text_box("typname");
  $form_container->output_row($lang->manageContent_typname . " <em>*</em>", $lang->manageContent_typname_desc, $add_typname, '', array(), array('id' => 'thread'));

  //Inhaltstyp in Scrollbox
  $sid = "scrollable";
  $add_scrollable = $form->generate_yes_no_radio("scrollable", 1, true, array("id" => $sid . "_yes", "class" => $sid), array("id" => $sid . "_no", "class" => $sid));
  $form_container->output_row($lang->manageContent_scrollable . " <em>*</em>", '', $add_scrollable);
  if (!isset($content['scrollheight'])) {
    $content['scrollheight'] = '0';
  }
  $form_container->output_row($lang->manageContent_scrollheight, "", $form->generate_numeric_field('scrollheight', $content['scrollheight'], array('id' => 'scrollheight', 'min' => 0)), 'scrollheight');

  //Gastansicht
  $gid = "guest";
  $add_guest = $form->generate_yes_no_radio("guest", 1, true, array("id" => $gid . "_yes", "class" => $gid), array("id" => $gid . "_no", "class" => $gid));
  $form_container->output_row($lang->manageContent_guest . " <em>*</em>", '', $add_guest);

  //Gastansicht
  $goid = "guest_only";
  $add_guest_only = $form->generate_yes_no_radio("guest_only", 0, true, array("id" => $goid . "_yes", "class" => $goid), array("id" => $goid . "_no", "class" => $goid));
  $form_container->output_row($lang->manageContent_guest_only . " <em>*</em>", '', $add_guest_only);

  $form_container->end();

  $buttons[] = $form->generate_submit_button($lang->manageContent_submit);
  $buttons[] = $form->generate_reset_button($lang->reset);
  $form->output_submit_wrapper($buttons);
  $form->end();
} elseif ($mybb->input['action'] == "add") {
  //Hier fügen wir jetzt die globalen Inhalte hinzu
  //tabs/Navigation hinzufügen 
  $page->add_breadcrumb_item($lang->manageContent_add, "index.php?module=" . MODULE . "&action=add");
  $page->output_header($lang->manageContent_add);
  generate_tabs("add");

  //Hier die Formulare
  $form = new Form("index.php?module=" . MODULE . "&amp;action=do_add", "post", "add");
  $form_container = new FormContainer($lang->manageContent_add);

  //Auswahlbox für Inhaltstyp erstellen
  //Query um aus der DB die schon erstellten Typen zu bekommen.
  $query = $db->write_query("SELECT * FROM `" . TABLE_PREFIX . "mc_types`");
  $options = array();
  //Wenn es welche gibt: 
  if (mysqli_num_rows($query) > 0) {
    while ($output = $db->fetch_array($query)) {
      $options[$output['mc_type']] = $output['mc_type'];
    }
    $form_container->output_row($lang->manageContent_add_type . " <em>*</em>", '', $form->generate_select_box('contenttyp', $options, array($mybb->get_input('contenttyp', MyBB::INPUT_INT)), array('id' => 'contenttyp')), 'contenttyp');
  } else {
    echo "<h1 style=\"color:red;\"><b>Bitte erst einen Inhaltstypen erstellen</b></h1>";
  }
  if (isset($mybb->input['disporder'])) {
    $mybb->input['disporder'] =  $mybb->input['disporder'];
  } else {
    $mybb->input['disporder'] = 0;
  }
  $form_container->output_row($lang->manageContent_add_order . " <em>*</em>", $lang->manageContent_add_order_desc, $form->generate_numeric_field('disporder', $mybb->input['disporder'], array('id' => 'disporder', 'min' => 0)), 'disporder');

  //textarea erstellen
  $add_manageContent = $form->generate_text_area("managecontent"); //name des inputs
  $form_container->output_row($lang->manageContent_simple . " <em>*</em>", $lang->manageContent_desc, $add_manageContent);

  //Inhaltstyp in Scrollbox
  $did = "showdate";
  $add_showdate = $form->generate_yes_no_radio("showdate", 0, true, array("id" => $did . "_yes", "class" => $did), array("id" => $did . "_no", "class" => $did));
  $form_container->output_row($lang->manageContent_showdate . " <em>*</em>", '', $add_showdate);

  $form_container->end();

  $buttons[] = $form->generate_submit_button($lang->manageContent_submit);
  $buttons[] = $form->generate_reset_button($lang->reset);
  $form->output_submit_wrapper($buttons);
  $form->end();

  /* Delete an manageContent */
} elseif ($mybb->input['action'] == "delete") {
  // echo "test" . $mybb->input['typeid'];
  // echo "cid" . $mybb->input['cid'];
  if ($mybb->input['typeid']) {
    $name = $db->fetch_field($db->simple_select("mc_types", "mc_type", "mc_id = '{$mybb->input['typeid']}'"), "mc_type");
    // echo "name".$name;
    $db->delete_query("mc_types", "mc_id='{$mybb->input['typeid']}'");
    $db->delete_query("mc_content", "mc_type='{$name}'");
    flash_message($lang->manageContent_delete_success, 'success');
    admin_redirect("index.php?module=" . MODULE);
    //typ und content löschen
  }
  if ($mybb->input['c_id']) {
    //content löschen

    $db->delete_query("mc_content", "mc_cid='{$mybb->input['c_id']}'");
    flash_message($lang->manageContent_delete_success, 'success');
    admin_redirect("index.php?module=" . MODULE);
  }
  /* Show mask for edit an manageContent */
} elseif ($mybb->input['action'] == "edit") {

  //Typ editieren
  if (isset($mybb->input['typeid'])) {
    $query = $db->simple_select("mc_types", "*", "mc_id = '" . $mybb->get_input('typeid', MyBB::INPUT_INT) . "'");

    if ($db->num_rows($query) != 1) {
      flash_message($lang->manageContent_editcontenttype_error, 'error');
      admin_redirect("index.php?module=" . MODULE);
    }

    $type = $db->fetch_array($query);
    $mc_id = $mybb->get_input('typeid', MyBB::INPUT_INT);

    //Erstellen der Formcontainer für edit
    $page->add_breadcrumb_item($lang->manageContent_editType, "index.php?module=" . MODULE . "&amp;action=edit&amp;typedid=$mc_id");
    $page->output_header($lang->manageContent_editType);
    generate_tabs("list");

    $form = new Form("index.php?module=" . MODULE . "&amp;action=do_edit_type", "post");
    $form_container = new FormContainer("$lang->manageContent_editType");

    //Inhaltstypen
    //inhaltstyp aktiv?
    $id = "active";
    $add_active = $form->generate_yes_no_radio("active", $type['mc_active'], true, array("id" => $id . "_yes", "class" => $id), array("id" => $id . "_no", "class" => $id));
    $form_container->output_row($lang->manageContent_active . " <em>*</em>", '', $add_active);

    //Inhaltstypname 
    $add_typname = $form->generate_text_box("typname", $type['mc_type']);
    $form_container->output_row($lang->manageContent_typname . " <em>*</em>", $lang->manageContent_typname_desc, $add_typname, '', array(), array('id' => 'thread'));

    //Inhaltstyp in Scrollbox
    $sid = "scrollable";
    $add_scrollable = $form->generate_yes_no_radio("scrollable", $type['mc_scrollable'], true, array("id" => $sid . "_yes", "class" => $sid), array("id" => $sid . "_no", "class" => $sid));
    $form_container->output_row($lang->manageContent_scrollable . " <em>*</em>", '', $add_scrollable);

    $form_container->output_row($lang->manageContent_scrollheight, $lang->manageContent_add_order_desc, $form->generate_numeric_field('scrollheight', $content['scrollheight'], array('id' => 'scrollheight', 'min' => 0)), 'scrollheight');

    $gid = "guest";
    $add_guest = $form->generate_yes_no_radio("guest", $type['mc_guest'], true, array("id" => $gid . "_yes", "class" => $gid), array("id" => $gid . "_no", "class" => $gid));
    $form_container->output_row($lang->manageContent_guest . " <em>*</em>", '', $add_guest);

    $gid = "guest_only";
    $add_guest_only = $form->generate_yes_no_radio("guest_only", $type['mc_guest_only'], true, array("id" => $goid . "_yes", "class" => $goid), array("id" => $goid . "_no", "class" => $goid));
    $form_container->output_row($lang->manageContent_guest_only . " <em>*</em>", '', $add_guest_only);

    echo $form->generate_hidden_field("mc_id", $mc_id);

    $form_container->end();

    $buttons[] = $form->generate_submit_button($lang->manageContent_submit);
    $buttons[] = $form->generate_reset_button($lang->reset);
    $form->output_submit_wrapper($buttons);
    $form->end();
  }

  if ($mybb->input['cid']) {
    $query = $db->simple_select("mc_content", "*", "mc_cid = '" . $mybb->get_input('cid', MyBB::INPUT_INT) . "'");

    if ($db->num_rows($query) != 1) {
      flash_message($lang->manageContent_editcontent_error, 'error');
      admin_redirect("index.php?module=" . MODULE);
    }
    $content = $db->fetch_array($query);
    $mc_cid = $mybb->get_input('cid', MyBB::INPUT_INT);

    //Erstellen der Formcontainer für edit Content
    $page->add_breadcrumb_item($lang->manageContent_editContent, "index.php?module=" . MODULE . "&amp;action=edit&amp;cid=$mc_cid");
    $page->output_header($lang->manageContent_editContent);
    generate_tabs("list");

    $form = new Form("index.php?module=" . MODULE . "&amp;action=do_edit", "post");
    $form_container = new FormContainer($lang->manageContent_editContent);

    $query = $db->write_query("SELECT * FROM `" . TABLE_PREFIX . "mc_types`");
    $options = array();
    //Wenn es welche gibt: 
    if (mysqli_num_rows($query) > 0) {
      while ($output = $db->fetch_array($query)) {
        $options[$output['mc_type']] = $output['mc_type'];
      }
      $form_container->output_row($lang->manageContent_add_type . " <em>*</em>", '', $form->generate_select_box('contenttyp', $options, $content['mc_type'], array($mybb->get_input('contenttyp', MyBB::INPUT_INT)), array('id' => 'contenttyp')), 'contenttyp');
    }

    $form_container->output_row($lang->manageContent_add_order . " <em>*</em>", $lang->manageContent_add_order_desc, $form->generate_numeric_field('disporder', $content['mc_sort'], array('id' => 'disporder', 'min' => 0)), 'disporder');

    //textarea erstellen
    $add_manageContent = $form->generate_text_area("managecontent", $content['mc_content']); //name des inputs
    $form_container->output_row($lang->manageContent_simple . " <em>*</em>", $lang->manageContent_desc, $add_manageContent);

    //Inhaltstyp in Scrollbox
    $did = "showdate";
    $add_showdate = $form->generate_yes_no_radio("showdate", $content['mc_showdate'], true, array("id" => $did . "_yes", "class" => $did), array("id" => $did . "_no", "class" => $did));
    $form_container->output_row($lang->manageContent_showdate . " <em>*</em>", '', $add_showdate);

    echo $form->generate_hidden_field("contentid", $mc_cid);
    //Aktualisieren des Datums?
    $adid = "editdate";
    $edit_date = $form->generate_yes_no_radio("editdate", 0, true, array("id" => $adid . "_yes", "class" => $adid), array("id" => $adid . "_no", "class" => $adid));
    $form_container->output_row($lang->manageContent_editdate . " <em>*</em>", '', $edit_date);

    $form_container->end();

    $buttons[] = $form->generate_submit_button($lang->manageContent_submit);
    $buttons[] = $form->generate_reset_button($lang->reset);
    $form->output_submit_wrapper($buttons);
    $form->end();
  }
} elseif ($mybb->input['action'] == "do_edit_type") { //-> button beim editieren vom inhaltstyp wird gedrückt

  $mc_id = (int)$mybb->input['mc_id'];
  if (!verify_post_check($mybb->input['my_post_key'])) {
    flash_message($lang->manageContent_invalid_post_verify_key2, 'error');
    admin_redirect("index.php?module=" . MODULE . "&action=edit&typeid=$mc_id");
  }
  if (!strlen(trim($mybb->input['active']))) {
    flash_message($lang->manageContent_active_not, 'error');
    admin_redirect("index.php?module=" . MODULE . "&action=addType");
  }
  if (!strlen(trim($mybb->input['typname']))) {
    flash_message($lang->manageContent_typname_not, 'error');
    admin_redirect("index.php?module=" . MODULE . "&action=addType");
  }
  if (!strlen(trim($mybb->input['scrollable']))) {
    flash_message($lang->manageContent_scrollable_not, 'error');
    admin_redirect("index.php?module=" . MODULE . "&action=addType");
  }
  if (!strlen(trim($mybb->input['guest']))) {
    flash_message($lang->manageContent_guest_not, 'error');
    admin_redirect("index.php?module=" . MODULE . "&action=addType");
  }
  if (!strlen(trim($mybb->input['guest_only']))) {
    flash_message($lang->manageContent_guest_only_not, 'error');
    admin_redirect("index.php?module=" . MODULE . "&action=addType");
  }
  //Array zum speichern erstellen
  $update = array(
    "mc_active" => $mybb->input['active'],
    "mc_type" => $db->escape_string($mybb->input['typname']),
    "mc_scrollable" => $mybb->input['scrollable'],
    "mc_scrollheight" => $mybb->input['scrollheight'],
    "mc_guest" => $mybb->input['guest'],
    "mc_guest_only" => $mybb->input['guest_only'],
  );
  $db->update_query("mc_types", $update, "mc_id='{$mc_id}'");
  flash_message($lang->manageContent_addType_success, 'success');
  admin_redirect("index.php?module=" . MODULE . "&action=list");
} elseif ($mybb->input['action'] == "do_edit") { //Inhalte bearbeiten

  $cid = (int)$mybb->input['contentid'];

  if (!verify_post_check($mybb->input['my_post_key'])) {
    flash_message($lang->manageContent_invalid_post_verify_key2, 'error');
    admin_redirect("index.php?module=" . MODULE . "&action=edit&typeid=$mc_id");
  }
  //Fehler abfangen und testen
  if (!strlen(trim($mybb->input['disporder']))) {
    //Wurde ein inhaltstyp gewählt
    flash_message($lang->manageContent_disporder_not, 'error');
    admin_redirect("index.php?module=" . MODULE . "&action=add");
  }

  if (!strlen(trim($mybb->input['managecontent']))) {
    //Wurde ein inhaltstyp gewählt
    flash_message($lang->manageContent_manageContent_not, 'error');
    admin_redirect("index.php?module=" . MODULE . "&action=add");
  }

  if (!strlen(trim($mybb->input['contenttyp']))) {
    //wurde eine Sortierung angeben
    flash_message($lang->manageContent_contenttyp_not, 'error');
    admin_redirect("index.php?module=" . MODULE . "&action=add");
  }

  if (!strlen(trim($mybb->input['showdate']))) {
    flash_message($lang->manageContent_showdate_not, 'error');
    admin_redirect("index.php?module=" . MODULE . "&action=add");
  }

  $update = array(
    "mc_type" => $db->escape_string($mybb->input['contenttyp']),
    "mc_content" => $db->escape_string($mybb->input['managecontent']),
    "mc_sort" => intval($mybb->input['disporder']),
    "mc_showdate" => intval($mybb->input['showdate']),
  );
  if ($mybb->input['editdate'] == 1) {
    $update['mc_date'] = date("Y.m.d H:i");
  }

  $db->update_query("mc_content", $update, "mc_cid='{$cid}'");
  flash_message($lang->manageContent_add_success, 'success');
  admin_redirect("index.php?module=" . MODULE . "&action=list");

  //sortierung speichern
} elseif ($mybb->input['action'] == "update_order"  && $mybb->request_method == "post") {
  //Sortierung speichern 
  if (!is_array($mybb->input['disporder'])) {
    admin_redirect("index.php?module=" . MODULE);
  }
  foreach ($mybb->input['disporder'] as $cid => $order) {
    $update_query = array(
      "mc_sort" => (int)$order
    );
    $db->update_query("mc_content", $update_query, "mc_cid='" . (int)$cid . "'");
  }
  flash_message($lang->manageContent_order_success, 'success');
  admin_redirect("index.php?module=" . MODULE);
} else {
  //Übersicht anzeige

  $page->output_header($lang->manageContent);
  generate_tabs("list");
  $form = new Form("index.php?module=" . MODULE . "&amp;action=update_order", "post");
  $table = new Table;

  $table->construct_header($lang->manageContent_contenttype_list, array("width" => "60%", 'colspan' => 2));
  $table->construct_header($lang->manageContent_active_list, array('class' => "align_center",  "width" => "150"));
  $table->construct_header($lang->manageContent_scrollable_list, array('class' => "align_center",  "width" => "150"));
  $table->construct_header($lang->manageContent_controls_list, array('class' => "align_center", 'colspan' => 2, "width" => "150"));

  //Erst einmal die Ihaltstypen bekommen
  $query = $db->simple_select("mc_types", "*", "", array("order_by" => "mc_type"));

  if ($db->num_rows($query) > 0) {

    while ($contenttype = $db->fetch_array($query)) {
      $table->construct_cell("<div><strong><a href=\"index.php?module=" . MODULE . "&amp;action=edit&amp;typeid={$contenttype['mc_id']}\">{$contenttype['mc_type']}</a></strong><br /></div>", array('style' => 'background-color: rgba(0, 0, 0, 0.2); padding-top: 5px; padding-bottom: 2px;', 'colspan' => 2));

      if ($contenttype['mc_active']) {
        $table->construct_cell($lang->yes, array('class' => 'align_center', 'width' => '5%', 'style' => 'background-color: rgba(0, 0, 0, 0.2); padding-top: 5px; padding-bottom: 2px;'));
      } else {
        $table->construct_cell($lang->no, array('class' => 'align_center', 'width' => '5%', 'style' => 'background-color: rgba(0, 0, 0, 0.2); padding-top: 5px; padding-bottom: 2px;'));
      }

      if ($contenttype['mc_scrollable']) {
        $table->construct_cell($lang->yes, array('class' => 'align_center', 'width' => '5%', 'style' => 'background-color: rgba(0, 0, 0, 0.2); padding-top: 5px; padding-bottom: 2px;'));
      } else {
        $table->construct_cell($lang->no, array('class' => 'align_center', 'width' => '5%', 'style' => 'background-color: rgba(0, 0, 0, 0.2); padding-top: 5px; padding-bottom: 2px;'));
      }
      $table->construct_cell("<a href=\"index.php?module=" . MODULE . "&amp;action=edit&amp;typeid={$contenttype['mc_id']}\">{$lang->edit}</a>", array("class" => "align_center", "width" => '60', 'style' => 'background-color: rgba(0, 0, 0, 0.2); padding-top: 5px; padding-bottom: 2px;'));
      $table->construct_cell("<a href=\"index.php?module=" . MODULE . "&amp;action=delete&amp;typeid={$contenttype['mc_id']}\" onclick=\"return AdminCP.deleteConfirmation(this, '{$lang->manageContent_delete}')\">{$lang->delete}</a>", array("class" => "align_center", "width" => '90', 'style' => 'background-color: rgba(0, 0, 0, 0.2); padding-top: 5px; padding-bottom: 2px;'));
      $table->construct_row();
      $table->construct_cell("<small>Variable: &lcub;&dollar;mc_{$contenttype['mc_type']}&rcub; Klassen zum Stylen: äußere: mc_box_{$contenttype['mc_type']}, innere: mc_item_{$contenttype['mc_type']}, Datum: {$contenttype['mc_type']}_date</small>", array("colspan" => "6", "style" => "padding:10px;"));
      $table->construct_row();


      $query2 = $db->simple_select("mc_content", "*", "mc_type = '{$contenttype['mc_type']}'", array("order_by" => "mc_sort, mc_date"));
      if ($db->num_rows($query2) > 0) {
        $table->construct_cell("<small>Inhalte</small>", array('width' => '40%', 'colspan' => '1', 'style' => 'background-color: rgba(0, 0, 0, 0.15); padding-top: 3px; padding-bottom: 3px;'));
        $table->construct_cell("<small>Sortierung</small>", array('class' => 'align_center', 'width' => '10%', 'colspan' => '1', 'style' => 'background-color: rgba(0, 0, 0, 0.15); padding-top: 3px; padding-bottom: 3px;'));
        $table->construct_cell("<small>Datum</small>", array('class' => 'align_center', 'width' => '10%', 'colspan' => '1', 'style' => 'background-color: rgba(0, 0, 0, 0.15); padding-top: 3px; padding-bottom: 3px;'));
        $table->construct_cell("<small>Datum anzeigen?</small>", array('class' => 'align_center', 'width' => '10%', 'colspan' => '1', 'style' => 'background-color: rgba(0, 0, 0, 0.15); padding-top: 3px; padding-bottom: 3px;'));
        $table->construct_cell("<small>Inhalte Verwalten</small>", array('class' => 'align_center', 'width' => '40%', 'colspan' => '2', 'style' => 'background-color: rgba(0, 0, 0, 0.15); padding-top: 3px; padding-bottom: 3px;'));
        $table->construct_row();
        while ($content = $db->fetch_array($query2)) {
          $table->construct_cell("<div style=\"padding-left: 40px;\">
                                <div style=\"max-height: 100px; overflow:auto;\"><small>{$content['mc_content']}</small></div>
                                
                                </div>", array("width" => '40%'));
          $table->construct_cell($form->generate_numeric_field("disporder[{$content['mc_cid']}]", $content['mc_sort'], array('id' => 'disporder', 'style' => 'width: 70%', 'class' => 'align_center', 'min' => 0)), array('width' => 'width: 4%', 'class' => 'align_center'));

          $mc_date = date('d.m.y', strtotime($content['mc_date']));
          $table->construct_cell("<small>
          
          {$mc_date}</small>", array('class' => 'align_center', "width" => '10%'));


          if (isset($contenttype['mc_showdate'])) {
            $table->construct_cell($lang->yes, array('class' => 'align_center', 'width' => '5%'));
          } else {
            $table->construct_cell($lang->no, array('class' => 'align_center', 'width' => '5%'));
          }
          $table->construct_cell("<a href=\"index.php?module=" . MODULE . "&amp;action=edit&amp;cid={$content['mc_cid']}\">{$lang->manageContent_editcontent_list}</a>", array("class" => "align_center", "width" => '60'));
          $table->construct_cell("<a href=\"index.php?module=" . MODULE . "&amp;action=delete&amp;c_id={$content['mc_cid']}\" onclick=\"return AdminCP.deleteConfirmation(this, '{$lang->manageContent_delete}')\">{$lang->delete}</a>", array("class" => "align_center", "width" => '90'));
          $table->construct_row();
        }

        $table->construct_cell("", array("colspan" => "6", "style" => "padding:10px;"));
        $table->construct_row();
      }
    }
  }

  $table->output($lang->manageContent_listTypes);
  $buttons[] = $form->generate_submit_button($lang->manageContent_order_save);
  $form->output_submit_wrapper($buttons);
  $form->end();
}

$page->output_footer();


//Hier erstellen wir die Navigationtabs im ACP 
function generate_tabs($selected)
{
  global $lang, $page;

  $sub_tabs = array();
  $sub_tabs['list'] = array(
    'title' => $lang->manageContent_list,
    'link' => "index.php?module=" . MODULE . "&amp;action=list",
    'description' => $lang->manageContent_list_desc
  );
  $sub_tabs['addType'] = array(
    'title' => $lang->manageContent_addType,
    'link' => "index.php?module=" . MODULE . "&amp;action=addType",
    'description' => $lang->manageContent_addType_desc
  );
  $sub_tabs['add'] = array(
    'title' => $lang->manageContent_add,
    'link' => "index.php?module=" . MODULE . "&amp;action=add",
    'description' => $lang->manageContent_add_desc
  );

  $page->output_nav_tabs($sub_tabs, $selected);
}
