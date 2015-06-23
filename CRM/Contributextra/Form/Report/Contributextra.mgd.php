<?php
// This file declares a managed database record of type "ReportTemplate".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array (
  0 => 
  array (
    'name' => 'CRM_Contributextra_Form_Report_Contributextra',
    'entity' => 'ReportTemplate',
    'params' => 
    array (
      'version' => 3,
      'label' => 'ContributExtra Bookkeeping Report',
      'description' => 'Extended Better Contribution and Bookkeeping Report',
      'class_name' => 'CRM_Contributextra_Form_Report_Contributextra',
      'report_url' => 'ca.civicrm.contributextra/contributextra',
      'component' => 'CiviContribute',
    ),
  ),
);
