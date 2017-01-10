<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Contributextra_Form_ContributExtraSettings extends CRM_Core_Form {
  function buildQuickForm() {
    $result = civicrm_api3('ContributionPage', 'get', array('sequential' => 1));
    if (empty($result['values'])) {
      CRM_Core_Session::setStatus(ts('You have no contribution pages to configure'));
    }
    else {
      $pages = $result['values'];
      $result = civicrm_api3('Setting', 'getvalue', array('name' => 'contributextra_settings'));
      $defaults = (empty($result)) ? array() : $result;
      foreach($pages as $page) {
        // add form element
        $options = array('0' => 'Normal', '1' => 'Admin-only', '2' => 'Admin and Public');
        $this->add(
          'select', // field type
          $page['id'], // field name
          $page['title'],
          $options,
          true // is required
        );
      }
      $this->add(
        'checkbox', // field type
        'summary_page_buttons', // field name
        ts('Enable buttons for admin pages on contact summary page as well.')
      );

      $this->addButtons(array(
        array(
          'type' => 'submit',
          'name' => ts('Submit'),
          'isDefault' => TRUE,
        ),
      ));
      $this->setDefaults($defaults);
    }
    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  function postProcess() {
    $values = $this->exportValues();
    foreach(array('qfKey','_qf_default','_qf_ContributExtraSettings_submit') as $key) {
      if (isset($values[$key])) {
        unset($values[$key]);
      }
    }
    civicrm_api3('Setting', 'create', array('domain_id' => 'current_domain', 'contributextra_settings' => $values));
    parent::postProcess();
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
}
