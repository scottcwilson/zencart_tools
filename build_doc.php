<?php
/*
 Deploy this to unmodified admin folder from Github, then run from browser.
 Additional Notes: 
 - If a configuration entry has changed name (like Email Options -> Email in 1.5.8), you must update the old file (in this case, user/admin_pages/configuration/configuration_emailoptions.md) to point to the new file. 
- If a configuration entry has added html, you must handle it - see DOWN_FOR_MAINTENANCE as an example.
*/
require('includes/application_top.php'); 
$data = zen_get_admin_pages(true); 
/*
echo "<pre>"; 
print_r($data); 
echo "</pre>"; 
 */

// for config gid 1
define('TARGET_FOLDER','/Users/scott/github_files/zencart_documentation/content/user/admin_pages/configuration/'); 
// define('TARGET_FOLDER','/tmp/'); 
define('CONFIG_MENU','Configuration'); 

$weight = 10; 
foreach ($data['configuration'] as $item) { 
   build_config(TARGET_FOLDER, $item['name'], $item['params'], $weight); 
   $weight += 10; 
   echo "Done " . $item['name'] . "<br />"; 
}
 
echo "done"; 

function build_config($folder, $name, $params, $weight) {
  global $db;
  $lowername = smash($name); 
  $lowername = str_replace("'", "", $lowername); 
  $file = "configuration_" . $lowername . ".md"; 
  $file = str_replace("'","", $file); 
  $param_parts = explode("=", $params); 
  $group_id = $param_parts[1]; 

  $list = $db->Execute("SELECT * FROM " . TABLE_CONFIGURATION . " WHERE configuration_group_id = " . $group_id . " ORDER BY sort_order"); 
  $fp = fopen($folder . $file, "w");  
  write_md_header($fp, CONFIG_MENU, $name, $weight);
  fwrite($fp, PHP_EOL); 
  $extra = get_header_extra($lowername); 
  if (!empty($extra)) {
     fwrite($fp, $extra . PHP_EOL . PHP_EOL); 
  }
  while(!$list->EOF) { 

    $our_title = str_replace("'", "", strip_tags($list->fields['configuration_title']));
    // Double check no unexpected HTML 
    if ($our_title != $list->fields['configuration_title'])  {
      switch ($list->fields['configuration_key']) {
        // We know about these ones and handle them already 
      case 'DOWN_FOR_MAINTENANCE': 
      case 'DOWNLOADS_CONTROLLER_ORDERS_STATUS_END': 
      case 'EMP_LOGIN_ADMIN_ID': 
      case 'EMP_LOGIN_AUTOMATIC':
      case 'EMP_LOGIN_ADMIN_PROFILE_ID': 
      case 'POSM_OPTIONAL_OPTION_NAMES_LIST': 
      case 'POSM_OPTIONAL_OPTION_TYPES_LIST': 
      case 'POSM_ENABLE': 
      case 'POSM_STOCK_REORDER_LEVEL': 
      case 'POSM_ATTRIBUTE_IMAGE_SELECTOR':
        break; 
      default: 
       echo "*** Mismatch " . $list->fields['configuration_title'] . "<br />"; 
      }
    }

    // Now output
    $title = doc_strip_tags($list->fields['configuration_title']); 
    $title = str_replace("'","", $title); 
    fix_data($lowername, $list->fields['configuration_title'],  $list->fields['configuration_description']); 
    fwrite($fp, '<h2 id="' . id_smash($title) . '">' . $title . '</h2>' . PHP_EOL . PHP_EOL); 
    $content = get_extra_content($lowername, $list->fields['configuration_title']); 
    if (empty($list->fields['configuration_description'])) {
       $list->fields['configuration_description'] = "Default value: " . $list->fields['configuration_value']; 
    }
    $key = "Key: " . "<b>" . $list->fields['configuration_key'] . "</b>" . "<br />" . PHP_EOL;
    $path = "Path: " . "<b>" . CONFIG_MENU . " > " . $name  . "</b>" . "<br />" . PHP_EOL;
    $desc = "Description: " . $list->fields['configuration_description']; 
    if ($content == "") {
      fwrite($fp, "<div class='indent'>" . $key . $path . $desc .  "</div>" . PHP_EOL);
    } else {
      fwrite($fp, "<div class='indent'>" . $key . $path . $desc . PHP_EOL);
      fwrite($fp, PHP_EOL); 
      fwrite($fp, $content . PHP_EOL); 
      fwrite($fp, "</div>" . PHP_EOL); 
    }
    fwrite($fp, PHP_EOL . PHP_EOL);
    $list->MoveNext(); 
  }
  fclose($fp); 
}

function smash($name) {
  $name = strtolower($name);
  $name = str_replace(' ','',$name); 
  $name = str_replace('/','',$name); 
  $name = str_replace('-','',$name); 
  return $name; 
}

function id_smash($name) {
  $name = strtolower($name);
  $name = str_replace('<=','LTE',$name);
  $name = str_replace('>=','GTE',$name);
  $name = str_replace(' ','_',$name); 
  $name = preg_replace("/[^A-Za-z0-9_]/", '', $name);
  return $name; 
}

function write_md_header($fp, $top_title, $name, $weight) {
  fwrite($fp, "---" . PHP_EOL); 
  fwrite($fp, "title: " . $top_title . " ≫ " . $name . PHP_EOL); 
  fwrite($fp, "category: admin_pages" . PHP_EOL); 
  fwrite($fp, "weight: " . $weight . " " . PHP_EOL); 
  fwrite($fp, "---" . PHP_EOL); 
}


function get_extra_content($lowername, $configuration_title) {
  $content = "";
  // Additional content
  if ($lowername == "emailoptions") {
      if ($configuration_title == "Email Archiving Active?") {
        $content = 'To view the email archive that is created when this flag is set to true, use the <a href="/user/email/email_archive_manager/">Email Archive Manager</a>.'; 
      }
  }
  return $content;
}
function fix_data($lowername, &$configuration_title,  &$configuration_description) {
  if ($lowername == "ezpagessettings") {
    $configuration_description = str_replace("Admin->Tools", "Admin > Tools", $configuration_description); 
  }
}

function doc_strip_tags($title) {
  $title = str_replace('<strong>','',$title); 
  $title = str_replace('</strong>','',$title); 
  return $title;
}

function get_header_extra($lowername) {
  $extra = ""; 
  if ($lowername == "shippingpackaging") { 
    $extra = 'See also <a href="/user/shipping/">Shipping</a> for more information on shipping calculations.'; 
  } else if ($lowername == "emailoptions") { 
    $extra = 'See also <a href="/user/email/">Email</a> for more information on customizing your store\'s email.'; 
  } elseif ($lowername == "stock") { 
    $extra = 'The stock configuration settings control stock management as well as various things on the shopping cart page.'; 
  } elseif ($lowername == "layoutsettings") { 
    $extra = 'See also <a href="/user/admin_pages/catalog/product_types_edit_layout/">Admin > Catalog > Product Types > Layout Settings</a> for product type specific layout settings.'; 
  } elseif ($lowername == "attributesettings") { 
    $extra = 'See also <a href="/user/admin_pages/catalog/attribute_controller/">Admin > Catalog > Attributes Controller</a> for attribute pricing settings.'; 
  } elseif ($lowername == "newlisting") { 
    $extra = 'Controls the appearance of the New Products page.  See <a href="/user/template/new_featured_all_listing_page_configuration/">New Listing Configuration</a> for instructions on use.'; 
  } elseif ($lowername == "alllisting") { 
    $extra = 'Controls the appearance of the All Products page. See <a href="/user/template/new_featured_all_listing_page_configuration/">All Listing Configuration</a> for instructions on use.'; 
  } elseif ($lowername == "featuredlisting") { 
    $extra = 'Controls the appearance of the Featured Products page. See <a href="/user/template/new_featured_all_listing_page_configuration/">Featured Listing Configuration</a> for instructions on use.'; 
  } elseif ($lowername == "definepagestatus") { 
    $extra = 'See <a href="/user/template/define_pages/">Define Pages</a> for instructions on use.'; 
  } elseif ($lowername == "optionsstockmanager") { 
    $extra = 'See <a href="/user/running/posm/">Variant Stock</a> for details and instructions on use.'; 
  }
  return $extra; 
}
