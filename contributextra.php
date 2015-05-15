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
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function contributextra_civicrm_xmlMenu(&$files) {
  _contributextra_civix_civicrm_xmlMenu($files);
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
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function contributextra_civicrm_uninstall() {
  return _contributextra_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function contributextra_civicrm_enable() {
  return _contributextra_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function contributextra_civicrm_disable() {
  return _contributextra_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function contributextra_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _contributextra_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function contributextra_civicrm_managed(&$entities) {
  return _contributextra_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function contributextra_civicrm_caseTypes(&$caseTypes) {
  _contributextra_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function contributextra_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _contributextra_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

function contributextra_civicrm_varset($vars) {
  $version = CRM_Utils_System::version();
  if (version_compare($version, '4.5') < 0) { /// support 4.4!
    CRM_Core_Resources::singleton()->addSetting('contributextra', $vars);
  }
  else {
    CRM_Core_Resources::singleton()->addVars('contributextra', $vars);
  }
}

/**
 * Implementation of hook_civicrm_pre
 *
 * Process the is_admin_only on contribution configuration pages
 */
function contributextra_civicrm_pre($op, $objectName, $objectId, &$params) {
  // since this function gets called a lot, quickly determine if I care about the record being created
  watchdog('contributextra','hook_civicrm_pre for '.$objectName.', '.$op.', <pre>@params</pre>',array('@params' => print_r($params)));
  // i only care about recurring contributions being created or edit, of the right financial type id
  if (('create' == $op || 'edit' == $op) && ('Contribution' == $objectName) && !empty($params['contribution_status_id']) && !empty($params['contribution_recur_id'])) {
    if ($params['contribution_status_id'] != 1) { // ignore non-completed contributions
      return;
    }
    $p = array('name' => 'membership_from_contribution_type_'.$params['financial_type_id']);
    $membership_implicit = civicrm_api3('Setting', 'getvalue', $p);
    if (empty($membership_implicit)) {
      return;
    }
    // ignore if this contribution is already attached to a membership
    $p = array('contribution_id' => $params['contribution_id']);
    $count = civicrm_api3('MembershipPayment', 'getcount', $p);
    if (!empty($count)) {
      return;
    }
    // check if the contact has exactly one membership of one of these kinds, and update their renewal, and add a correspondence in the membership_payment field
    $p = array('contact_id' => $params['contact_id'],'membership_type_id' => array('IN' => $membership_implicit));
    $membership = civicrm_api3('Membership', 'getsingle', $p);
    if (empty($membership['id'])) {
      return; 
    }
    $membership_type = civicrm_api3('MembershipType','getsingle', array('id' => $membership['membership_type_id']));
    if (empty($membership_type)) {
      return; // this is actually an unexpected error
    }
    $contribution_ids = array($params['contribution_id']);
    $total_amount = floatval($params['total_amount']);
    $minimum_fee = floatval($membership_type['minimum_fee']);
    if ($minimum_fee > $total_amount) {
      // this contribution isn't enough to pay for the membership on it's own, see if we can make use of past un-connected payments within the range of the membership type
      $since = strtotime('-'.$membership_type['duration_interval'].' '.$membership_type['duration_unit']);
      $since_date = date('Y-m-d',$since);
      $p = array('sequential' => 1, 'return' => 'id,total_amount', 'contact_id' => $params['contact_id'], 'financial_type_id' => $params['financial_type_id'], 'receive_date' => array('>=' => $since_date));
      $result = civicrm_api3('Contribution', 'get', $p);
      if (empty($result['count'])) {
        return;
      }
      foreach($result['values'] as $contribution) {
        // skip those payments that are already used for a membership
        $result = civicrm_api3('MembershipPayment','getcount',array('contribution_id' => $contribution['id']));
        if (empty($result['result'])) {
          $contribution_ids[] = $contribution['id'];
          $total_amount += $contribution['total_amount'];
          if ($total_amount >= $minimum_fee) {
            break;
          }
        }
      }
      if ($total_amount < $minimum_fee) { // still failed, quit
        return; 
      }
    }
    // otherwise, we're good to renew this membership and assign the contributions
    $start_date = date('Y-m-d');
    if ($membership['status_id'] <= 3) { // new current or grace, extend the date
      $start_date = $membership['end_date'];
    }
    $end_date = strtotime('+'.$membership_type['duration_interval'].' '.$membership_type['duration_unit'],$start_date);
    civicrm_api3('Membership','create', array('id' => $membership['id'],'end_date' => $end_date));
    foreach($contribution_ids as $contribution_id) {
      civicrm_api3('MembershipPayment','create', array('contribution_id' => $contribution_id, 'membership_id' => $membership['id']));
    }
    // now see if we need to change the financial type of this contribution
    $p = array('name' => 'membership_from_contribution_type_'.$params['financial_type_id'].'_financial_type_id');
    $membership_financial_type_id = civicrm_api3('Setting', 'getvalue', $p);
    if (!empty($membership_financial_type_id)) {
      $params['financial_type_id'] = $membership_financial_type_id;
    }
    array_shift($contribution_ids);
    foreach ($contribution_ids as $contribution_id) { // and the old ones as well ..
      $p = array('contribution_id' => $contribution_id, 'financial_type_id' => $membership_financial_type_id);
      civicrm_api3('Contribution','create');
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
  else { // echo $fname; die(); 
  }
}

/*
 * hook_civicrm_postProcess
 * Do a Drupal 7 style thing so we can write smaller functions
 */
function contributextra_civicrm_postProcess($formName, &$form) {
  $fname = 'contributextra_process_'.$formName;
  if (function_exists($fname)) {
    $fname($form);
  }
 // else 
  // echo $fname; die();
}
/*
 * Enable identification of admin-only contribution pages
 */
function contributextra_CRM_Contribute_Form_ContributionPage_Settings(&$form) {
  if ($form->getVar('_id') > 0) {
    $form->addElement('checkbox','is_admin_only',ts('Is this page for administrative use only?'));
    $contribution_page_id = $form->getVar('_id');
    $sql = 'SELECT id FROM civicrm_contributextra_adminpages WHERE contribution_page_id = %1';
    $args = array(
      1 => array($contribution_page_id, 'Integer'),
    );
    $dao = CRM_Core_DAO::executeQuery($sql,$args);
    $is_admin_only = $dao->N ? TRUE : FALSE;
    $form->setDefaults(array('is_admin_only' => $is_admin_only));
    $html = '<div id="is-admin-only" style="margin: 10px 20px; padding: 10px 20px; background-color: #FFF; border: 1px solid grey; border-radius: 5px;">
<input id="is_admin_only" name="is_admin_only" type="checkbox" value="1"'.($is_admin_only ? ' checked="checked"':'').' class="form-checkbox" />
<label for="is_admin_only">Is this page for administrative use only?</label>
<div class="description"> &nbsp; When this is checked, the contribution page will be deactivated for public access and linked to from contacts\' Contribution tab for easy administrative use.</div>
</div>';
    contributextra_civicrm_varset(array('is_admin_only' => $is_admin_only, 'is_admin_html' => $html));
    CRM_Core_Resources::singleton()->addScriptFile('ca.civicrm.contributextra', 'js/contribution_page_tab.js');
  }
}

/*
 * Enable mapping of financial types to memberships
 */
function contributextra_CRM_Financial_Form_FinancialType(&$form) {
  $membership_types = array();
  if ($form->getVar('_id') > 0) {
    $financial_type_id = $form->getVar('_id');
    $params = array('version' => 3, 'sequential' => 1, 'financial_type_id' => $financial_type_id);
    $result = civicrm_api('MembershipType', 'get', $params);
    if (!empty($result['values'])) {
      $membership_types[0] = '-- none --';
      foreach($result['values'] as $value) {
        $membership_types[$value['id']] = $value['name'];
      }
    }
    $params = array('version' => 3, 'sequential' => 1);
    $result = civicrm_api('FinancialType', 'get', $params);
    $membership_financial_type = array();
    if (!empty($result['values'])) {
      $membership_financial_types[0] = '-- same --';
      foreach($result['values'] as $value) {
        if ($value['id'] != $financial_type_id) {
          $membership_financial_types[$value['id']] = $value['name'];
        }
      }
    }
  }
  if (count($membership_types)) { // we have some eligible types, see if there are any default settings
    // print_r($membership_types); die();
    $membership_implicit = $form->addElement('advmultiselect','membership_implicit',ts('Renew these membership types for contributions of this type.'),$membership_types);
    $financial_type = $form->addElement('select','membership_financial_type_id',ts('Override to use this financial type for the membership portion of the payment.'),$membership_financial_types);
    // check for and set default
    $params = array('name' => 'membership_from_contribution_type_'.$financial_type_id);
    $result = civicrm_api3('Setting', 'getvalue', $params);
    $defaults = array();
    if (!empty($result)) {
      $defaults['membership_implicit'] = $result;
    }
    $params = array('name' => 'membership_from_contribution_type_'.$financial_type_id.'_financial_type_id');
    $result = civicrm_api3('Setting', 'getvalue', $params);
    if (!empty($result)) {
      $defaults['membership_financial_type_id'] = $result;
    }
    if (count($defaults)) {
      $form->setDefaults($defaults);
    }
    // and now add the html to the form
    $myform = clone $form;
    $renderer = $myform->getRenderer();
    $myform->accept($renderer);
    $html = $renderer->toArray();
    $html = '<div id="membership-implicit" style="margin: 10px 20px; padding: 10px 20px; background-color: #FFF; border: 1px solid grey; border-radius: 5px;">
<div class="membership-implicit">'.$html['membership_implicit']['label'].'<br />'
. $html['membership_implicit']['html']
. '<div class="description"> &nbsp; When selected, contributions of this type will automatically create or renew the specified membership type.</div>
</div>
<div class="membership-financial-type-id">' . $html['membership_financial_type_id']['label'] . '<br />'
. $html['membership_financial_type_id']['html']
. '<div class="description"> &nbsp; When selected, the portion of contributions that are used for membership creation/renewal will change to this type.</div>
</div>
</div>';
    contributextra_civicrm_varset(array('membership_implicit_html' => $html));
    CRM_Core_Resources::singleton()->addScriptFile('ca.civicrm.contributextra', 'js/financial_type.js');
  }
}

/*
 * Process new field
 */
function contributextra_process_CRM_Contribute_Form_ContributionPage_Settings(&$form) {
  $contribution_page_id = $form->getVar('_id');
  $submission = $form->exportValues('is_admin_only');
  if ($submission['is_admin_only']) {
    $sql = 'INSERT IGNORE INTO civicrm_contributextra_adminpages (contribution_page_id) VALUES (%1)';
  }
  else {
    $sql = 'DELETE FROM civicrm_contributextra_adminpages  WHERE contribution_page_id = %1';
  }
  $args = array(
    1 => array($contribution_page_id, 'Integer'),
  );
  $dao = CRM_Core_DAO::executeQuery($sql,$args);
}

function contributextra_process_CRM_Financial_Form_FinancialType(&$form) {
  $financial_type_id = $form->getVar('_id');
  if (empty($financial_type_id)) return;
  $submission = $form->exportValues('membership_implicit');
  $name = 'membership_from_contribution_type_'.$financial_type_id;
  $membership_implicit = empty($submission['membership_implicit']) ? '' : $submission['membership_implicit'];
  $membership_financial_type_id = '';
  $params = array('domain_id' => 'current_domain', $name => $membership_implicit);
  $result = civicrm_api3('Setting', 'create', $params);
  // and now the financial type override seting
  $submission = $form->exportValues('membership_financial_type_id');
  $membership_financial_type_id = $submission['membership_financial_type_id'];
  $name .= '_financial_type_id';
  $params = array('domain_id' => 'current_domain', $name => $membership_financial_type_id);
  $result = civicrm_api3('Setting', 'create', $params);
}


/*
 * Handle front end forms if they are admin-only
 */
function contributextra_CRM_Contribute_Form_Contribution_Main(&$form) {
  // todo: improve permissions check
  global $user;
  $is_anon = empty($user->uid) ? TRUE : FALSE;
  $contribution_page_id = $form->getVar('_id');
  $sql = 'SELECT id FROM civicrm_contributextra_adminpages WHERE contribution_page_id = %1';
  $args = array(
    1 => array($contribution_page_id, 'Integer'),
  );
  $dao = CRM_Core_DAO::executeQuery($sql,$args);
  $is_admin_only = ($dao->N > 0) ? TRUE : FALSE;
  if ($is_anon && $is_admin_only) {
    CRM_Core_Error::fatal(ts('This form is marked for administrative use only')); 
  }
  if (empty($form->_paymentProcessors)) {
    return;
  }

  /* remove security code option if this is an authenticated user on someone else's front end */
  if (!$is_anon && $form->elementExists('cvv2')) {
    try {
      $form->removeElement('cvv2',TRUE);
      unset($form->_paymentFields['cvv2']);
      CRM_Core_Resources::singleton()->addStyleFile('ca.civicrm.contributextra', 'css/auth_front.css');
      //CRM_Core_Resources::singleton()->addScriptFile('ca.fairvote.custom', 'js/auth_front.js');
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
  $is_admin_page = $backoffice_links = array();
  $sql = 'SELECT contribution_page_id FROM civicrm_contributextra_adminpages';
  $dao = CRM_Core_DAO::executeQuery($sql);
  while ($dao->fetch()) {
    $is_admin_page[$dao->contribution_page_id] = 1;
  }
  $params = array('version' => 3, 'sequential' => 1, 'is_active' => 1);
  $result = civicrm_api('ContributionPage', 'get', $params);
  if (0 == $result['is_error'] && count($result['values']) > 0) {
    foreach($result['values'] as $page) {
      if ($is_admin_page[$page['id']]) {
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

