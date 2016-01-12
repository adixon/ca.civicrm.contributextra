<?php
// $Id$

class CRM_Contributextra_Form_Report_Contributextra extends CRM_Report_Form {

  protected $_addressField = FALSE;

  protected $_emailField = FALSE;

  protected $_summary = NULL;

  protected $_customGroupExtends = array('Contribution', 'Contact');
  // protected $_customGroupGroupBy = TRUE;

  /* debuggin, remove later */
  function buildQuery($applyLimit = TRUE) {
    $sql = parent::buildQuery($applyLimit);
    // CRM_Core_Session::setStatus("<pre>$sql</pre>",'','info',array('expires' => 0));
    return $sql;
  }
  
  function __construct() {
    $config = CRM_Core_Config::singleton();
    $campaignEnabled = in_array("CiviCampaign", $config->enableComponents);
    $this->_columns = array(
      'civicrm_financial_trxn' => array(
        'dao' => 'CRM_Financial_DAO_FinancialTrxn',
        'fields' => array(
          'crk' => array('title' => ts('CRK'),
            'default' => TRUE,
            'name' => 'payment_instrument_id',
          ),
          'payment_instrument_id' => array('title' => ts('Payment Instrument'),
            'default' => TRUE,
          ),
          'currency' => array(
             'required' => TRUE,
             'no_display' => TRUE,
          ), 
          'trxn_date' => array(
            'title' => ts('Transaction Date'),
            'default' => TRUE,
            'type' => CRM_Utils_Type::T_DATE,
          ),
          'trxn_id' => array(
            'title' => ts('Transaction String'),
          ),
        ),
        'filters' => array(
          'payment_instrument_id' => array(
            'title' => ts('Payment Instrument'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Contribute_PseudoConstant::paymentInstrument(),
          ),
          'currency' => array(
             'title' => 'Currency',
             'operatorType' => CRM_Report_Form::OP_MULTISELECT,
             'options' => CRM_Core_OptionGroup::values('currencies_enabled'),
             'default' => NULL,
             'type' => CRM_Utils_Type::T_STRING,
          ),
          'trxn_date' => array( 
            'title' => ts('Transaction Date'),
            'operatorType' => CRM_Report_Form::OP_DATE,
            'type' => CRM_Utils_Type::T_DATE,
          ),
          'trxn_id' => array(
            'title' => ts('Transaction String'),
             'type' => CRM_Utils_Type::T_STRING,
          ),
        ),
        'order_bys' => array(
          'crk' => array(
            'title' => ts('CRK'),
            'name' => 'payment_instrument_id',
          ),
          'payment_instrument_id' => array(
            'title' => ts('Payment Instrument'),
          ),
          'currency' => array(
             'title' => 'Currency',
          ),
          'trxn_date' => array( 
            'title' => ts('Transaction Date'),
          ),
        ),
      ), 
      'civicrm_financial_account' => array(
        'dao' => 'CRM_Financial_DAO_FinancialAccount',
        'fields' => array(
          'debit_accounting_code' => array(
            'title' => ts('Financial Account Code - Debit'),
            'name'  => 'accounting_code',
            'alias' => 'financial_account_civireport_debit',
            'default' => TRUE,
          ),
          'credit_accounting_code' => array(
            'title' => ts('Financial Account Code - Credit'),
            'name'  => 'accounting_code',
            'alias' => 'financial_account_civireport_credit',
            'default' => TRUE,
          ),
          'debit_name' => array(
            'title' => ts('Financial Account Name - Debit'),
            'name'  => 'name',
            'alias' => 'financial_account_civireport_debit',
            'default' => TRUE,
          ),
          'credit_name' => array(
            'title' => ts('Financial Account Name - Credit'),
            'name'  => 'name',
            'alias' => 'financial_account_civireport_credit',
            'default' => TRUE,
          ),
        ),
        'filters' => array(
          'debit_accounting_code' => array(
            'title' => ts('Financial Account Code - Debit'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Contribute_PseudoConstant::financialAccount(NULL, NULL, 'accounting_code', 'accounting_code'),
            'name'  => 'accounting_code',
            'alias' => 'financial_account_civireport_debit',
          ),
          'credit_accounting_code' => array(
            'title' => ts('Financial Account Code - Credit'), 
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Contribute_PseudoConstant::financialAccount(NULL, NULL, 'accounting_code', 'accounting_code'),
          ),
          'debit_name' => array(
            'title' => ts('Financial Account Name - Debit'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Contribute_PseudoConstant::financialAccount(),
            'name'  => 'id',
            'alias' => 'financial_account_civireport_debit',
          ),
          'credit_name' => array(
            'title' => ts('Financial Account Name - Credit'), 
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Contribute_PseudoConstant::financialAccount(),
          ),
        ),
      ),     
      'civicrm_line_item' => array(
        'dao' => 'CRM_Price_DAO_LineItem',
        'fields' => array(
          'financial_type_id' => array('title' => ts('Financial Type'),
            'default' => TRUE,
          ),
        ),
        'filters' => array(
          'financial_type_id' => array( 
            'title' => ts('Financial Type'), 
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Contribute_PseudoConstant::financialType(),
          ),
        ),
      ),  
      'civicrm_contribution' => array(
        'dao' => 'CRM_Contribute_DAO_Contribution',
        'fields' => array(
          'id' => array(
            'title' => ts('Contribution Id'),
          ),
          'contribution_status_id' => array('title' => ts('Contribution Status'),
            'default' => TRUE,
          ),
          'source' => array(
            'title' => ts('Contribution Source'),
          ),
          'contribution_page_id' => array(
            'title' => ts('Contribution Page'),
          ),
          'contribution_recur_id' => array(
            'title' => ts('Recurring Contribution Id'),
          ),
          'receive_date' => array(
            'title' => ts('Receive date'),
            'default' => TRUE,
            'type' => CRM_Utils_Type::T_DATE,
          ),
        ),
        'filters' => array(
          'contribution_status_id' => array(
            'title' => ts('Contribution Status'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Contribute_PseudoConstant::contributionStatus(),
            'default' => array(1),
          ),
          'receive_date' => array(
            'title' => ts('Receive date'),
            'type' => CRM_Utils_Type::T_DATE,
            'operatorType' => CRM_Report_Form::OP_DATE,
          ),
          'contribution_recur_id' => array(
            'title' => ts('Recurring Contribution Id'),
          ),
          'contribution_page_id' => array('title' => ts('Contribution Page'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options'  => CRM_Contribute_PseudoConstant::contributionPage(),
            'type' => CRM_Utils_Type::T_INT,
          ),
        ),
        'grouping' => 'contri-fields',
      ),
      'civicrm_contact' =>
      array(
        'dao' => 'CRM_Contact_DAO_Contact',
        'fields' =>
        array(
          'sort_name' =>
          array('title' => ts('Contact Name'),
            'no_repeat' => TRUE,
          ),
          'id' =>
          array(
            'no_display' => TRUE,
            'required' => TRUE,
          ),
          'external_identifier' =>
          array('title' => ts('External Id'),
            'no_repeat' => TRUE,
          ),
        ),
        'filters' =>
        array(
          'id' =>
          array('title' => ts('Contact ID'),
            'no_display' => TRUE,
          ),
          'sort_name' =>
          array('title' => ts('Contact Name'),
            'operator' => 'like',
          ),
        ),
        'grouping' => 'contact-fields',
      ),
      'civicrm_phone' => array(
        'dao' => 'CRM_Core_DAO_Phone',
        'fields' => array(
          'phone' => array(
            'title' => 'Telephone',
            'default' => TRUE,
          ),
        ),
      ),
      'civicrm_email' => array(
        'dao' => 'CRM_Core_DAO_Email',
        'fields' => array(
          'email' => array(
            'title' => ts('Email'),
            'no_repeat' => TRUE,
          ),
        ),
        'grouping' => 'contact-fields',
        'order_bys' => array(
          'email' => array(
            'title' => ts('Email'),
          ),
        ),
      ),
    );
    if ($campaignEnabled) {
      $getCampaigns = CRM_Campaign_BAO_Campaign::getPermissionedCampaigns(NULL, NULL, FALSE, FALSE, TRUE);
      $this->allCampaigns = $getCampaigns['campaigns'];
      asort($this->allCampaigns);
      // Add display column and filter for Campaign if CiviCampaign is enabled
      if (!empty($this->allCampaigns)) {
        $this->_columns['civicrm_contribution']['fields']['campaign_id'] = array(
          'title' => 'Campaign',
          'default' => 'false',
        );
        $this->_columns['civicrm_contribution']['filters']['campaign_id'] = array('title' => ts('Campaign'),
          'operatorType' => CRM_Report_Form::OP_MULTISELECT,
          'options' => $this->allCampaigns,
        );
      }
    }
    parent::__construct();
    // CRM_Core_Session::setStatus('<pre>'.print_r($this->_columns,TRUE).'</pre>','','info',array('expires' => 0));
    $this->_columns['civicrm_entity_financial_trxn'] = array(
        'dao' => 'CRM_Financial_DAO_EntityFinancialTrxn',
        'fields' => array(
          'amount' => array(
            'required' => TRUE,
            'title' => ts('Amount'),
            'default' => TRUE,
            'type' => CRM_Utils_Type::T_STRING,
          ),
        ),
        'filters' =>
        array(
          'amount' =>
          array('title' => ts('Amount')),
        ),
    );   
  }

  function preProcess() {
    parent::preProcess();
  }

  /*
   * copied from civi bookkeeping report */
  function select() {
    $select = array();
    /* todo: group by on all selected column headers, sum on amount */
    $this->_columnHeaders = array();
    foreach ($this->_columns as $tableName => $table) {
      // print_r($this->_params); die();
      if (array_key_exists('fields', $table)) {
        foreach ($table['fields'] as $fieldName => $field) {
          if (CRM_Utils_Array::value('required', $field) ||
            CRM_Utils_Array::value($fieldName, $this->_params['fields'])
          ) {
            switch ($fieldName) {
            // some special cases for our accounting tables 
            case 'credit_accounting_code' :
              $select[] = " CASE 
                            WHEN {$this->_aliases['civicrm_financial_trxn']}.from_financial_account_id IS NOT NULL
                            THEN  {$this->_aliases['civicrm_financial_account']}_credit_1.accounting_code
                            ELSE  {$this->_aliases['civicrm_financial_account']}_credit_2.accounting_code
                            END AS civicrm_financial_account_credit_accounting_code ";
              break;
            case 'amount' : 
              $select[] = " CASE 
                            WHEN  {$this->_aliases['civicrm_entity_financial_trxn']}_item.entity_id IS NOT NULL
                            THEN SUM({$this->_aliases['civicrm_entity_financial_trxn']}_item.amount)
                            ELSE SUM({$this->_aliases['civicrm_entity_financial_trxn']}.amount)
			    END AS civicrm_entity_financial_trxn_amount ";
	      // $select[] = " COUNT(*) as counter ";
              break;
            case 'credit_name' :
              $select[] = " CASE 
                            WHEN {$this->_aliases['civicrm_financial_trxn']}.from_financial_account_id IS NOT NULL
                            THEN  {$this->_aliases['civicrm_financial_account']}_credit_1.name
                            ELSE  {$this->_aliases['civicrm_financial_account']}_credit_2.name
                            END AS civicrm_financial_account_credit_name ";
              break;
            default :
              $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
              break; 
            }
            if ($tableName == 'civicrm_email') {
              $this->_emailField = TRUE;
            }
            elseif ($tableName == 'civicrm_phone') {
              $this->_phoneField = TRUE;
            }
            $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];
            $this->_columnHeaders["{$tableName}_{$fieldName}"]['type'] = CRM_Utils_Array::value('type', $field);
          }
        }
      }
    }

    $this->_select = 'SELECT ' . implode(', ', $select) . ' ';
  }

  static function formRule($fields, $files, $self) {
    return array();
    // todo: are there combinations that are not allowed?
  }

  function from() {
    $this->_from = NULL;

    $this->_from = "FROM  civicrm_contact {$this->_aliases['civicrm_contact']} {$this->_aclFrom}
              INNER JOIN civicrm_contribution {$this->_aliases['civicrm_contribution']}
                    ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_contribution']}.contact_id AND 
                         {$this->_aliases['civicrm_contribution']}.is_test = 0
              LEFT JOIN civicrm_entity_financial_trxn {$this->_aliases['civicrm_entity_financial_trxn']}
                    ON ({$this->_aliases['civicrm_contribution']}.id = {$this->_aliases['civicrm_entity_financial_trxn']}.entity_id AND 
                        {$this->_aliases['civicrm_entity_financial_trxn']}.entity_table = 'civicrm_contribution')
              LEFT JOIN civicrm_financial_trxn {$this->_aliases['civicrm_financial_trxn']}
                    ON {$this->_aliases['civicrm_financial_trxn']}.id = {$this->_aliases['civicrm_entity_financial_trxn']}.financial_trxn_id
              LEFT JOIN civicrm_financial_account {$this->_aliases['civicrm_financial_account']}_debit
                    ON {$this->_aliases['civicrm_financial_trxn']}.to_financial_account_id = {$this->_aliases['civicrm_financial_account']}_debit.id
              LEFT JOIN civicrm_financial_account {$this->_aliases['civicrm_financial_account']}_credit_1
                    ON {$this->_aliases['civicrm_financial_trxn']}.from_financial_account_id = {$this->_aliases['civicrm_financial_account']}_credit_1.id
              LEFT JOIN civicrm_entity_financial_trxn {$this->_aliases['civicrm_entity_financial_trxn']}_item
                    ON ({$this->_aliases['civicrm_financial_trxn']}.id = {$this->_aliases['civicrm_entity_financial_trxn']}_item.financial_trxn_id AND 
                        {$this->_aliases['civicrm_entity_financial_trxn']}_item.entity_table = 'civicrm_financial_item')
              LEFT JOIN civicrm_financial_item fitem
                    ON fitem.id = {$this->_aliases['civicrm_entity_financial_trxn']}_item.entity_id
              LEFT JOIN civicrm_financial_account {$this->_aliases['civicrm_financial_account']}_credit_2
                    ON fitem.financial_account_id = {$this->_aliases['civicrm_financial_account']}_credit_2.id
              LEFT JOIN civicrm_line_item {$this->_aliases['civicrm_line_item']}
                    ON  fitem.entity_id = {$this->_aliases['civicrm_line_item']}.id AND fitem.entity_table = 'civicrm_line_item' 
    ";
    if ($this->isTableSelected('civicrm_email')) {
      $this->_from .= "
            LEFT JOIN  civicrm_email {$this->_aliases['civicrm_email']} 
                   ON ({$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_email']}.contact_id AND
                      {$this->_aliases['civicrm_email']}.is_primary = 1) ";
    }

    if ($this->_phoneField) {
      $this->_from .= "
            LEFT JOIN civicrm_phone {$this->_aliases['civicrm_phone']} 
                   ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_phone']}.contact_id AND 
                      {$this->_aliases['civicrm_phone']}.is_primary = 1 ";
    }
  }

  function zzzorderBy() {
    // $fields = print_r($this->_params,TRUE);
    // CRM_Core_Session::setStatus("<pre>$fields</pre>",'','info',array('expires' => 0));
    // $this->_orderBy = " ORDER BY civicrm_financial_account_credit_accounting_code, civicrm_financial_account_debit_accounting_code";
 //{$this->_aliases['civicrm_contribution']}.id, {$this->_aliases['civicrm_entity_financial_trxn']}.id 
    // order in the same order as the _columns are defined
    $orderBys = array();
    foreach ($this->_columns as $tableName => $table) {
      foreach ($table['fields'] as $fieldName => $field) {
        if (CRM_Utils_Array::value($fieldName, $this->_params['fields'])) {
          switch($fieldName) {
            case 'amount': // ignore!
              break; 
            // case 'debit_accounting_code':
            case 'credit_accounting_code':
              $orderBys[] = 'civicrm_financial_account_credit_accounting_code';
              break;
            case 'credit_name':
              $orderBys[] = 'civicrm_financial_account_credit_name';
              break;
            default:
              $orderBys[] = $field['dbAlias'];
              break;
          }
        }
      }
    }
    if (count($orderBys)) {
      // $tmp = print_r($orderBys,TRUE);
      // CRM_Core_Session::setStatus("<pre>$tmp</pre>",'','info',array('expires' => 0));
      $this->_orderBy = 'ORDER BY ' . implode(', ', $orderBys) . ' ';
      // $this->_orderBy = " ORDER BY ".impode(','civicrm_financial_account_credit_accounting_code, civicrm_financial_account_debit_accounting_code";
    }
  }

  function where() {
    foreach ($this->_columns as $tableName => $table) {
      if (array_key_exists('filters', $table)) {
        foreach ($table['filters'] as $fieldName => $field) {
          $clause = NULL;
          if ($fieldName == 'credit_accounting_code') {
            $field['dbAlias'] = "CASE
              WHEN financial_trxn_civireport.from_financial_account_id IS NOT NULL
              THEN  financial_account_civireport_credit_1.accounting_code
              ELSE  financial_account_civireport_credit_2.accounting_code 
              END";
          }
          else if ($fieldName == 'credit_name') {
            $field['dbAlias'] = "CASE
              WHEN financial_trxn_civireport.from_financial_account_id IS NOT NULL
              THEN  financial_account_civireport_credit_1.id
              ELSE  financial_account_civireport_credit_2.id 
              END";
          }
          if (CRM_Utils_Array::value('type', $field) & CRM_Utils_Type::T_DATE) {
            $relative = CRM_Utils_Array::value("{$fieldName}_relative", $this->_params);
            $from     = CRM_Utils_Array::value("{$fieldName}_from", $this->_params);
            $to       = CRM_Utils_Array::value("{$fieldName}_to", $this->_params);

            $clause = $this->dateClause($field['name'], $relative, $from, $to, $field['type']);
          }
          else {
            $op = CRM_Utils_Array::value("{$fieldName}_op", $this->_params);
            if ($op) {
              $clause = $this->whereClause($field,
                $op,
                CRM_Utils_Array::value("{$fieldName}_value", $this->_params),
                CRM_Utils_Array::value("{$fieldName}_min", $this->_params),
                CRM_Utils_Array::value("{$fieldName}_max", $this->_params)
              );
            }
          }
          if (!empty($clause)) {
            $clauses[] = $clause;
          }
        }
      }
    }
    if (empty($clauses)) {
      $this->_where = 'WHERE ( 1 )';
    }
    else {
      $this->_where = 'WHERE ' . implode(' AND ', $clauses);
    }
  }

  function postProcess() {
    // get the acl clauses built before we assemble the query
    // $this->buildACLClause($this->_aliases['civicrm_contact']);
    parent::postProcess();
  }

  function groupBy() {
    // group by all selected fields except amount
    $groupBys = array();
    foreach ($this->_columns as $tableName => $table) {
      foreach ($table['fields'] as $fieldName => $field) {
        if (CRM_Utils_Array::value($fieldName, $this->_params['fields'])) {
          switch($fieldName) {
            case 'amount': // ignore!
              break; 
            // case 'debit_accounting_code':
            case 'credit_accounting_code':
              $groupBys[] = 'civicrm_financial_account_credit_accounting_code';
              break;
            case 'credit_name':
              $groupBys[] = 'civicrm_financial_account_credit_name';
              break;
            default:
              $groupBys[] = $field['dbAlias'];
              break;
          }
        }
      }
    }

    if (!empty($groupBys)) {
      $this->_groupBy = "GROUP BY " . implode(', ', $groupBys);
    }

    // parent::groupBy();
  }

  function statistics(&$rows) {
    $statistics = parent::statistics($rows);

    $select = " SELECT COUNT({$this->_aliases['civicrm_financial_trxn']}.id ) as count,
                {$this->_aliases['civicrm_contribution']}.currency,
                SUM(CASE 
                  WHEN {$this->_aliases['civicrm_entity_financial_trxn']}_item.entity_id IS NOT NULL
                  THEN {$this->_aliases['civicrm_entity_financial_trxn']}_item.amount
                  ELSE {$this->_aliases['civicrm_entity_financial_trxn']}.amount
                END) as amount 
";

    $sql = "{$select} {$this->_from} {$this->_where} 
            GROUP BY {$this->_aliases['civicrm_contribution']}.currency
";

    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $amount[] = CRM_Utils_Money::format($dao->amount, $dao->currency);
      $avg[] =  CRM_Utils_Money::format(round(($dao->amount / $dao->count), 2), $dao->currency);
    }

    $statistics['counts']['amount'] = array(
       'value' => implode(', ', $amount),
       'title' => 'Total Amount',
       'type' => CRM_Utils_Type::T_STRING,
    );
    $statistics['counts']['avg'] = array(
      'value' => implode(', ', $avg),
      'title' => 'Average',
      'type' => CRM_Utils_Type::T_STRING,
    );
    return $statistics;
  }

  function alterDisplay(&$rows) {
    $contributionTypes = CRM_Contribute_PseudoConstant::financialType();
    $paymentInstruments = CRM_Contribute_PseudoConstant::paymentInstrument();
    $contributionStatus = CRM_Contribute_PseudoConstant::contributionStatus();
    $contributionPages  = CRM_Contribute_PseudoConstant::contributionPage();
    foreach ($rows as $rowNum => $row) {
      // convert display name to links
      if (array_key_exists('civicrm_contact_sort_name', $row) &&
        CRM_Utils_Array::value('civicrm_contact_sort_name', $rows[$rowNum]) &&
        array_key_exists('civicrm_contact_id', $row)
      ) {
        $url = CRM_Utils_System::url('civicrm/contact/view',
          'reset=1&cid=' . $row['civicrm_contact_id'],
          $this->_absoluteUrl
        );
        $rows[$rowNum]['civicrm_contact_sort_name_link'] = $url;
        $rows[$rowNum]['civicrm_contact_sort_name_hover'] = ts('View Contact Summary for this Contact.');
      }

      // handle contribution status id
      if ($value = CRM_Utils_Array::value('civicrm_contribution_contribution_status_id', $row)) {
        $rows[$rowNum]['civicrm_contribution_contribution_status_id'] = $contributionStatus[$value];
      }

      // handle payment instrument id
      if ($value = CRM_Utils_Array::value('civicrm_financial_trxn_payment_instrument_id', $row)) {
        $rows[$rowNum]['civicrm_financial_trxn_payment_instrument_id'] = $paymentInstruments[$value];
      }
      // handle crk a vs. c
      if ($value = CRM_Utils_Array::value('civicrm_financial_trxn_crk', $row)) {
        $rows[$rowNum]['civicrm_financial_trxn_crk'] = (($value == 1 || $value == 6) ? 'Credit' : 'Cash');
      }
      
      // handle financial type id
      if ($value = CRM_Utils_Array::value('civicrm_line_item_financial_type_id', $row)) {
        $rows[$rowNum]['civicrm_line_item_financial_type_id'] = $contributionTypes[$value];
      }
      if ($value = CRM_Utils_Array::value('civicrm_entity_financial_trxn_amount', $row)) {
        $rows[$rowNum]['civicrm_entity_financial_trxn_amount'] = CRM_Utils_Money::format($rows[$rowNum]['civicrm_entity_financial_trxn_amount'],$rows[$rowNum]['civicrm_financial_trxn_currency']);
      }
      // handle campaigns
      if (array_key_exists('civicrm_contribution_campaign_id', $row)) {
	if ($value = $row['civicrm_contribution_campaign_id']) {
          $rows[$rowNum]['civicrm_contribution_campaign_id'] = $this->allCampaigns[$value];
        }
      } 
      // handle contribution pages
      if ($value = CRM_Utils_Array::value('civicrm_contribution_contribution_page_id', $row)) {
        $rows[$rowNum]['civicrm_contribution_contribution_page_id'] = $contributionPages[$value];
        $entryFound = TRUE;
      }

    }
  }
}

