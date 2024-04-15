<?php

require_once 'contributextra.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function contributextra_civicrm_config(&$config) {
  _contributextra_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function contributextra_civicrm_install() {
  return _contributextra_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function contributextra_civicrm_enable() {
  return _contributextra_civix_civicrm_enable();
}

function contributextra_civicrm_varset($vars) {
  CRM_Core_Resources::singleton()->addVars('contributextra', $vars);
}

function contributextra_civicrm_navigationMenu(&$navMenu) {
  $item = array(
    'label' => 'Contribution Page Extra Settings', 
    'name' => 'Contribution Page Extra Settings', 
    'url' => 'civicrm/admin/contribute/extrasettings',
    'permission' => 'access CiviContribute,administer CiviCRM',
    'operator'   => 'AND',
    'separator'  => NULL,
    'active'     => 1
  );
  // Check that our item doesn't already exist
  $menu_item_search = array('url' => $item['url']);
  $menu_items = array();
  CRM_Core_BAO_Navigation::retrieve($menu_item_search, $menu_items);
  if (empty($menu_items)) { 
    $item['navID'] = 1 + CRM_Core_DAO::singleValueQuery("SELECT max(id) FROM civicrm_navigation");
    foreach ($navMenu as $key => $value) {
      if ('Contributions' == $value['attributes']['name']) {
        $item['parentID'] =  $key;
        $navMenu[$key]['child'][$item['navID']] = array(
          'attributes' => $item,
        );
        break;
      }
    }
  }
}

/*
 * hook_civicrm_buildForm
 * Do a Drupal 7 style thing so we can write smaller functions
 */
function contributextra_civicrm_buildForm($formName, &$form) {
  $fname = 'contributextra_'.$formName;
  if (function_exists($fname)) {
    $fname($form);
  }
  // else { echo '<pre>'.$fname.'</pre>'; }
}

/*
 * hook_civicrm_pageRun
 * Do a Drupal 7 style thing so we can write smaller functions
 */
function contributextra_civicrm_pageRun(&$page) {
  $pageName = $page->getVar('_name');
  $fname = 'contributextra_'.$pageName;
  if (function_exists($fname)) {
    $fname($page);
  }
  // else { echo '<pre>'.$fname.'</pre>'; }
}

/*
 * hook_civicrm_postProcess
 * Do a Drupal 7 style thing so we can write smaller functions
function contributextra_civicrm_postProcess($formName, &$form) {
  $fname = 'contributextra_process_'.$formName;
  if (function_exists($fname)) {
    $fname($form);
  }
  // else { echo $fname; die(); }
} 
 */

/*
 * Handle front end forms if they are admin-only
 */
function contributextra_CRM_Contribute_Form_Contribution_Main(&$form) {
  // todo: improve permissions check
  $isUserLoggedIn = CRM_Utils_System::isUserLoggedIn();
  $contribution_page_id = $form->getVar('_id');
  $settings = civicrm_api3('Setting', 'getvalue', array('name' => 'contributextra_settings'));
  $setting = empty($settings[$contribution_page_id]) ? 0 : $settings[$contribution_page_id];
  if (!$isUserLoggedIn && ($setting == 1)) {
    CRM_Core_Error::fatal(ts('This form is marked for administrative use only')); 
  }
  if (!$setting || empty($form->_paymentProcessors)) {
    return;
  }

  /* remove security code option if this is an authenticated user on someone else's front end */
  if ($isUserLoggedIn && $form->elementExists('cvv2')) {
    try {
      $form->removeElement('cvv2',TRUE);
      unset($form->_paymentFields['cvv2']);
      CRM_Core_Resources::singleton()->addStyleFile('ca.civicrm.contributextra', 'css/auth_front.css');
    }
    catch (Exception $e) {
      // ignore
    }
  }
}

/*
 *  Provide helpful links to the admin-only payment pages.
 */
function contributextra_CRM_Contribute_Form_Search(&$form) {
  // ignore invocations that aren't for a specific contact, e.g. the civicontribute dashboard
  if (empty($form->_defaultValues['contact_id'])) {
    return;
  }
  $contactID = $form->_defaultValues['contact_id'];
  $backoffice_links = array();
  $is_admin_page = civicrm_api3('Setting', 'getvalue', array('name' => 'contributextra_settings'));
  $params = array(
    'version' => 3,
    'sequential' => 1,
    'is_active' => 1,
    'options' => array(
      'limit' => 0,
    )
  );
  $result = civicrm_api('ContributionPage', 'get', $params);
  if (0 == $result['is_error'] && count($result['values']) > 0) {
    foreach($result['values'] as $page) {
      if (!empty($is_admin_page[$page['id']])) {
        $url = CRM_Utils_System::url('civicrm/contribute/transact','reset=1&cid='.$contactID.'&id='.$page['id']);
        $backoffice_links[] = array('url' => $url, 'title' => $page['title']);
      }
    }
  }
  if (count($backoffice_links)) {
    CRM_Core_Resources::singleton()->addStyleFile('ca.civicrm.contributextra', 'css/contribute_form_search.css');
    contributextra_civicrm_varset(array('backofficeLinks' => $backoffice_links));
    CRM_Core_Resources::singleton()->addScriptFile('ca.civicrm.contributextra', 'js/contribute_form_search.js');
  }
}

/*
 *  Provide helpful links to the admin-only payment pages, from the summary page, if the setting is enabled.
 */
function contributextra_CRM_Contact_Page_View_Summary(&$page) {
  // ignore invocations that aren't for a specific contact
  $contactID = $page->getVar('_contactId');
  if (empty($contactID)) {
    return;
  } 
  $is_admin_page = civicrm_api3('Setting', 'getvalue', array('name' => 'contributextra_settings'));
  if (empty($is_admin_page['summary_page_buttons'])) {
    return;
  }
  $backoffice_links = array();
  $params = array(
    'sequential' => 1,
    'is_active' => 1,
    'options' => array(
      'limit' => 0,
    )
  );
  $result = civicrm_api3('ContributionPage', 'get', $params);
  if (0 == $result['is_error'] && count($result['values']) > 0) {
    foreach($result['values'] as $contribution_page) {
      if (!empty($is_admin_page[$contribution_page['id']])) {
        $url = CRM_Utils_System::url('civicrm/contribute/transact','reset=1&cid='.$contactID.'&id='.$contribution_page['id']);
        $backoffice_links[] = array('url' => $url, 'title' => $contribution_page['title']);
      }
    }
  }
  if (count($backoffice_links)) {
    CRM_Core_Resources::singleton()->addStyleFile('ca.civicrm.contributextra', 'css/contribute_form_search.css');
    contributextra_civicrm_varset(array('backofficeLinks' => $backoffice_links));
    CRM_Core_Resources::singleton()->addScriptFile('ca.civicrm.contributextra', 'js/contact_summary.js');
  }
}
    
