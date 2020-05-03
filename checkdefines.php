<?php
require("includes/application_top.php"); 

// Pull in all language files here 
if (IS_ADMIN_FLAG) { 
  // Pull in all admin files 
  $files= getfiles("includes/languages"); 
  $skip_list = array("includes/languages/english.php"); 
} else {
  $files= getfiles("includes/languages"); 
  $skip_list = array("includes/languages/english.php"); 
  // Pull in all catalog files 
}

foreach ($files as $file) {
  if (in_array($file, $skip_list)) continue; 
  if (strpos($file, "html_includes") !== false) continue; 
  require($file); 
}

$list = get_defined_constants(true) ;

if (IS_ADMIN_FLAG) { 
   echo "Run catalog_find_define on all of these to determine which should be removed."; 
} else {
   echo "All these should be removed."; 
}
echo "<br />";

$db_keys = get_db_keys(); 
if (!IS_ADMIN_FLAG) { 
  chdir("includes"); 
}
$admin_kgl = admin_known_good_list(); 
$catalog_kgl = catalog_known_good_list(); 
foreach ($list['user'] as $key => $value) {
  if (in_array($key, $db_keys)) continue; 
  if (IS_ADMIN_FLAG) { 
     if (in_array($key, $admin_kgl)) continue; 
  }
  if (in_array($key, $catalog_kgl)) continue; 
  if (strpos($key, 'TEXT_MAX_ADMIN_') === 0) continue; 
  if (strpos($key, 'TEXT_MIN_ADMIN_') === 0) continue; 
  if (strpos($key, 'TEXT_CC_ENABLED') === 0) continue; 
  $parts = explode("_", $key); 
  if (!empty($parts[0])) {
    if ($parts[0] == "FILENAME") continue; 
    if ($parts[0] == "TABLE") continue; 
  }
  $rc = 0;
  system('grep -l ' . $key . ' `find . -type f -not -iwholename "*.git*"` | grep -s -v "/languages/" 1>/dev/null 2>/dev/null', $rc); 
  if ($rc != 0) {
     echo $key . "<br />"; 
  }
}
echo "Complete!";

function get_db_keys() {
  global $db; 
  $keys = array(); 

  $query = $db->Execute("SELECT configuration_key FROM " . TABLE_CONFIGURATION); 
  $config_keys = get_all_keys($query, 'configuration_key'); 
  $keys = array_merge($keys, $config_keys); 

  $query = $db->Execute("SELECT configuration_key FROM " . TABLE_PRODUCT_TYPE_LAYOUT); 
  $layout_keys = get_all_keys($query, 'configuration_key'); 
  $keys = array_merge($keys, $layout_keys); 

  $query = $db->Execute("SELECT language_key FROM " . TABLE_ADMIN_PAGES); 
  $admin_keys = get_all_keys($query, 'language_key'); 
  $keys = array_merge($keys, $admin_keys); 


  return $keys; 
}

function get_all_keys($query, $key) {
  $keys = array(); 

  while (!$query->EOF) {
    $keys[] = $query->fields[$key];
    $query->MoveNext(); 
  }
  return $keys; 
}

// Why known good lists? Some constants are: 
// - used in admin but defined on storefront side
// - required by plugins 
// - used by install
// - used to "build" other defines (and thus not used outside language files)
function catalog_known_good_list() {
  return array(
    'TEXT_GV_NAMES', 
    'MODULE_PAYMENT_PAYPAL_MARK_BUTTON_IMG', 
    'MODULE_PAYMENT_PAYPAL_MARK_BUTTON_ALT',
    'MODULE_PAYMENT_PAYPAL_ACCEPTANCE_MARK_TEXT', 
    'TEXT_SEE_ORDERS', 

    // These are kept intentionally in case someone wants to use them.
    'MODULE_PAYMENT_AUTHORIZENET_TEXT_TYPE',
    'MODULE_PAYMENT_PAYPALDP_TEXT_ADMIN_TITLE_PRO20',
    'MODULE_PAYMENT_PAYPALDP_ERROR_HEADING',
    'MODULE_PAYMENT_PAYPALDP_TEXT_CARD_ERROR',
    'MODULE_PAYMENT_PAYPALDP_TEXT_CREDIT_CARD_OWNER',
    'MODULE_PAYMENT_PAYPALDP_TEXT_GEN_ERROR',
    'MODULE_PAYMENT_PAYPALDP_TEXT_INSUFFICIENT_FUNDS_ERROR',
    'MODULE_PAYMENT_PAYPALDP_TEXT_ERROR',
    'MODULE_PAYMENT_PAYPALDP_TEXT_BAD_LOGIN',
    'MODULE_PAYMENT_PAYPALDP_ERROR_AVS_FAILURE_TEXT',
    'MODULE_PAYMENT_PAYPALDP_ERROR_CVV_FAILURE_TEXT',
    'MODULE_PAYMENT_PAYPALDP_ERROR_AVSCVV_PROBLEM_TEXT',
    'MODULE_PAYMENT_PAYPALDP_TEXT_NOT_CONFIGURED',
    'MODULE_PAYMENT_PAYPALDP_TEXT_INVALID_AUTH_AMOUNT',
    'MODULE_PAYMENT_PAYPALDP_TEXT_AUTH_CONFIRM_ERROR',
    'MODULE_PAYMENT_PAYPALDP_TEXT_AUTH_INITIATED',
    'MODULE_PAYMENT_PAYPALDP_TEXT_INVALID_ZONE_ERROR',
    'MODULE_PAYMENT_PAYPAL_ENTRY_ADDRESS_ZIP',
    'MODULE_PAYMENT_PAYPALDP_TEXT_CAPTURE_FULL_CONFIRM_CHECK',
    'MODULES_PAYMENT_PAYPALWPP_LINEITEM_TEXT_SURCHARGES_SHORT',
    'MODULES_PAYMENT_PAYPALWPP_LINEITEM_TEXT_DISCOUNTS_SHORT',
    'MODULE_PAYMENT_PAYPALWPP_TEXT_TITLE',
    'MODULE_PAYMENT_PAYPALWPP_TEXT_EC_HEADER',
    'MODULE_PAYMENT_PAYPALWPP_DP_TEXT_TYPE',
    'MODULE_PAYMENT_PAYPALWPP_PF_TEXT_TYPE',
    'MODULE_PAYMENT_PAYPALWPP_ERROR_HEADING',
    'MODULE_PAYMENT_PAYPALWPP_TEXT_CARD_ERROR',
    'MODULE_PAYMENT_PAYPALWPP_TEXT_CREDIT_CARD_FIRSTNAME',
    'MODULE_PAYMENT_PAYPALWPP_TEXT_CREDIT_CARD_LASTNAME',
    'MODULE_PAYMENT_PAYPALWPP_TEXT_CREDIT_CARD_OWNER',
    'MODULE_PAYMENT_PAYPALWPP_TEXT_CREDIT_CARD_TYPE',
    'MODULE_PAYMENT_PAYPALWPP_TEXT_CREDIT_CARD_NUMBER',
    'MODULE_PAYMENT_PAYPALWPP_TEXT_CREDIT_CARD_EXPIRES',
    'MODULE_PAYMENT_PAYPALWPP_TEXT_CREDIT_CARD_ISSUE',
    'MODULE_PAYMENT_PAYPALWPP_TEXT_CREDIT_CARD_CHECKNUMBER',
    'MODULE_PAYMENT_PAYPALWPP_TEXT_CREDIT_CARD_CHECKNUMBER_LOCATION',
    'MODULE_PAYMENT_PAYPALWPP_TEXT_CONFIRMEDADDR_ERROR',
    'MODULE_PAYMENT_PAYPALWPP_TEXT_ERROR',
    'MODULE_PAYMENT_PAYPALWPP_TEXT_BAD_CARD',
    'MODULE_PAYMENT_PAYPALWPP_TEXT_JS_CC_OWNER',
    'MODULE_PAYMENT_PAYPALWPP_TEXT_JS_CC_NUMBER',
    'MODULE_PAYMENT_PAYPALWPP_ERROR_AVS_FAILURE_TEXT',
    'MODULE_PAYMENT_PAYPALWPP_ERROR_CVV_FAILURE_TEXT',
    'MODULE_PAYMENT_PAYPALWPP_ERROR_AVSCVV_PROBLEM_TEXT',
    'MODULE_PAYMENT_PAYPALWPP_TEXT_NOT_CONFIGURED',
    'MODULE_PAYMENT_PAYPALWPP_EC_BUTTON_SM_IMG',
    'MODULE_PAYMENT_PAYPALWPP_TEXT_CAPTURE_FULL_CONFIRM_CHECK',
    'MODULES_PAYMENT_PAYPALSTD_LINEITEM_TEXT_SURCHARGES_LONG',
    'MODULES_PAYMENT_PAYPALSTD_LINEITEM_TEXT_DISCOUNTS_LONG',
    'MODULE_PAYMENT_SQUARE_ENTRY_TRANSACTION_ACTIONS',
    'MODULE_PAYMENT_SQUARE_TEXT_TRANS_ID_REQUIRED_ERROR',
   
  );
}

function admin_known_good_list() {
  return array(
    'EMAIL_ORDER_UPDATE_MESSAGE', 
    'OTHER_IMAGE_CUSTOMERS_AUTHORIZATION_ALT', 
    'OTHER_REVIEWS_RATING_STARS_FIVE_ALT',
    'OTHER_REVIEWS_RATING_STARS_FOUR_ALT',
    'OTHER_REVIEWS_RATING_STARS_THREE_ALT',
    'OTHER_REVIEWS_RATING_STARS_TWO_ALT',
    'OTHER_REVIEWS_RATING_STARS_ONE_ALT',
    'TEXT_EMAIL_ADDRESS_VALIDATE', 
    'TEXT_CALL_FOR_PRICE', 
    'TEXT_MAX_PREVIEW', 
    'ERROR_GV_AMOUNT', 
    'TABLE_HEADING_VOUCHER_CODE', 
    'PHP_DATE_TIME_FORMAT', 
    'TEXT_GV_NAME', 
    'TEXT_GV_NAMES', 
    'EMAIL_LOGO_FILENAME',
    'EMAIL_LOGO_WIDTH',
    'EMAIL_LOGO_HEIGHT',
    'EMAIL_LOGO_ALT_TITLE_TEXT',

    'OFFICE_FROM',
    'OFFICE_EMAIL',
    'OFFICE_USE',
    'OFFICE_LOGIN_NAME',
    'OFFICE_LOGIN_EMAIL',
    'OFFICE_LOGIN_PHONE',
    'OFFICE_IP_ADDRESS',
    'OFFICE_HOST_ADDRESS',
    'OFFICE_DATE_TIME',

    /* Required by SQL */
    'BOX_HEADING_CONFIGURATION',
    'BOX_HEADING_MODULES',
    'BOX_HEADING_CUSTOMERS',
    'BOX_HEADING_LOCATION_AND_TAXES',
    'BOX_HEADING_REPORTS',
    'BOX_HEADING_EXTRAS',
    'BOX_HEADING_LOCALIZATION',
    'BOX_HEADING_GV_ADMIN',
    'BOX_HEADING_ADMIN_ACCESS',
    'BOX_TOOLS_DEFINE_PAGES_EDITOR',

    'OTHER_IMAGE_REVIEWS_RATING_STARS_FIVE',
    'OTHER_IMAGE_REVIEWS_RATING_STARS_FOUR',
    'OTHER_IMAGE_REVIEWS_RATING_STARS_THREE',
    'OTHER_IMAGE_REVIEWS_RATING_STARS_TWO',
    'OTHER_IMAGE_REVIEWS_RATING_STARS_ONE',
    'OTHER_IMAGE_BLACK_SEPARATOR', 
    'OTHER_IMAGE_BOX_NOTIFY_REMOVE', 
    'OTHER_IMAGE_BOX_NOTIFY_YES', 
    'OTHER_IMAGE_BOX_WRITE_REVIEW', 
    'OTHER_IMAGE_CALL_FOR_PRICE', 
    'OTHER_IMAGE_DOWN_FOR_MAINTENANCE', 
    'OTHER_IMAGE_PRICE_IS_FREE',
    'OTHER_IMAGE_TRANPARENT', 
    'OTHER_IMAGE_CUSTOMERS_AUTHORIZATION', 
    
    'ICON_ERROR',
    'ICON_SUCCESS',
    'ICON_WARNING',
    'TEXT_COMMENTS_NO',
    'ERROR_FILE_TOO_BIG',
    'ERROR_FILETYPE_NOT_ALLOWED', 
    'ERROR_FILE_NOT_SAVED',
    'ERROR_DESTINATION_NOT_WRITEABLE',
    'ERROR_DESTINATION_DOES_NOT_EXIST', 
    'WARNING_NO_FILE_UPLOADED', 
    'SUCCESS_FILE_SAVED_SUCCESSFULLY',
    'UPLOAD_FILENAME_EXTENSIONS_LIST',
    'EMAIL_SYSTEM_DEBUG',
    'EMAIL_ATTACH_EMBEDDED_IMAGES',
    'SMTPAUTH_EMAIL_PROTOCOL',
    'ENABLE_PLUGIN_VERSION_CHECKING',
    'LOG_PLUGIN_VERSIONCHECK_FAILURES',
    'TEXT_DOCUMENT_AVAILABLE',
    'OFFICE_IP_TO_HOST_ADDRESS', 
    'EMAIL_SEND_FAILED', 
    'TEXT_IMAGE_OVERWRITE_WARNING',
    'EMAIL_EXTRA_HEADER_INFO', 
    'EMAIL_FOOTER_COPYRIGHT',
    'SEND_EXTRA_ORDERS_STATUS_ADMIN_EMAILS_TO_SUBJECT', 
    'TEXT_UNSUBSCRIBE',
    'OTHER_BOX_NOTIFY_REMOVE_ALT',
    'OTHER_BOX_NOTIFY_YES_ALT',
    'OTHER_BOX_WRITE_REVIEW_ALT',
    'OTHER_DOWN_FOR_MAINTENANCE_ALT', 
    'WARNING_COULD_NOT_LOCATE_LANG_FILE', 
    'ERROR_MODULE_REMOVAL_PROHIBITED', 
    'NEW_VERSION_CHECKUP_URL', 
    'CONNECTION_TYPE_UNKNOWN',

    'OSH_EMAIL_SEPARATOR', 
    'OSH_EMAIL_TEXT_SUBJECT',
    'OSH_EMAIL_TEXT_ORDER_NUMBER',
    'OSH_EMAIL_TEXT_INVOICE_URL',
    'OSH_EMAIL_TEXT_DATE_ORDERED',
    'OSH_EMAIL_TEXT_COMMENTS_UPDATE',
    'OSH_EMAIL_TEXT_STATUS_UPDATED',
    'OSH_EMAIL_TEXT_STATUS_NO_CHANGE',
    'OSH_EMAIL_TEXT_STATUS_LABEL',
    'OSH_EMAIL_TEXT_STATUS_CHANGE',
    'OSH_EMAIL_TEXT_STATUS_PLEASE_REPLY',
  ); 
}

function getfiles($dir_name) { 
     $subdirectories = array();
     $files = array();
     if (is_dir($dir_name) && is_readable($dir_name)) {
        $d = dir($dir_name); 
        while (false != ($f = $d->read())) {
           if ( ("." == $f) || (".." == $f) ) continue;
           if (is_dir("$dir_name/$f")) {
             array_push($subdirectories, "$dir_name/$f");
           } else {
             $extension = end(explode(".", $f));
             if (!empty($extension) && $extension == "php") { 
                array_push($files, "$dir_name/$f");
             }
           }
        }
        $d->close(); 
        foreach ($subdirectories as $subdirectory) {
           $files = array_merge($files, getfiles($subdirectory));
        }
     }
     return $files;
}
