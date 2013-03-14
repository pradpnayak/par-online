<?php
require_once 'CRM/Core/Page.php';
class CRM_Contact_Page_DonationSummary extends CRM_Core_Page {
    function run() {
        if( array_key_exists( 'gid', $_GET ) ){
            $groupId = $_GET[ 'gid' ];
        }
        if ( !empty( $_GET[ 'id' ] ) ) {
            $cid     = $_GET[ 'id' ];
        }
        $summary = array();
        if( empty( $_GET[ 'id' ] ) ){
            $cid = $_SESSION['CiviCRM']['userID'];
        }
        require_once 'CRM/Contribute/BAO/ContributionType.php';

        if( $cid ){
            $cids[$cid] = $cid;
            $contributionParam[ 'contact_id' ] = $cid;
            $defaults = array();
            $summary[ 'month' ]            = CRM_Contact_Page_DonationSummary::getSummary( $cid );
            $summary[ 'monthAnticipated' ] = CRM_Contact_Page_DonationSummary::getSummary( $cid, 'monthAnticipated' );
            $summary[ 'year' ]             = CRM_Contact_Page_DonationSummary::getSummary( $cid, 'year' );
            CRM_Core_Error::backtrace(); 
            CRM_Core_Error::debug( '$summary', $summary );
            $allRelations = getDenominationAdmin( $cid, true );
            CRM_Core_Error::debug( '$allRelations', $allRelations );

            if( array_key_exists( 'lower', $allRelations ) && is_array( $allRelations[ 'lower' ] ) ){
                foreach( $allRelations[ 'lower' ] as $lowerKey => $lowerValue ){
                    foreach( $lowerValue[ 'contacts' ] as $contactID => $contactName ){
                        $cids[$contactID] = $contactID;
                        if( $cid == $contactID ){
                            continue;
                        }
                        $contactSummary = CRM_Contact_Page_DonationSummary::getSummary( $contactID );
                        foreach( $contactSummary as $summaryKey => $summeryValue ){
                            if( array_key_exists( $summaryKey, $summary['month'] ) ){
                                $summary[ 'month' ][ $summaryKey ] += $summeryValue;
                            } else {
                                $summary[ 'month' ][ $summaryKey ] = $summeryValue;
                            }
                        }
                        $contactSummary = CRM_Contact_Page_DonationSummary::getSummary( $contactID, 'monthAnticipated' );
                        foreach( $contactSummary as $summaryKey => $summeryValue ){
                            if( array_key_exists( $summaryKey, $summary['monthAnticipated'] ) ){
                                $summary[ 'monthAnticipated' ][ $summaryKey ] += $summeryValue;
                            } else {
                                $summary[ 'monthAnticipated' ][ $summaryKey ] = $summeryValue;
                            }
                        }
                        $contactSummary = CRM_Contact_Page_DonationSummary::getSummary( $contactID, 'year' );
                        foreach( $contactSummary as $summaryKey => $summeryValue ){
                            if( array_key_exists( $summaryKey, $summary['year'] ) ){
                                $summary[ 'year' ][ $summaryKey ] += $summeryValue;
                            } else {
                                $summary[ 'year' ][ $summaryKey ] = $summeryValue;
                            }
                        }
                    }
                }
            }
        }
        CRM_Core_Error::backtrace(); 
        exit;
        if ( !empty( $cids ) ) {
            $summary = CRM_Contact_Page_DonationSummary::getSummaryAverage( $cids, $summary );
        }
        $smarty =  CRM_Core_Smarty::singleton( );
        
        require_once 'CRM/Contact/BAO/GroupContact.php';
        if ( CRM_Contact_BAO_GroupContact::isContactInGroup( $_SESSION['CiviCRM']['userID'] , PAR_ADMIN_GROUP_ID ) ) {
            $url = CRM_Utils_System::url('civicrm/contact/search/custom',"reset=1&csid=18&force=1");
        }
        if ( CRM_Contact_BAO_GroupContact::isContactInGroup( $_SESSION['CiviCRM']['userID'] , DENOMINATION_ADMIN_GROUP_ID ) ) {
            $url = CRM_Utils_System::url('civicrm/contact/search/custom',"reset=1&csid=16&force=1");
        } 
        $mtd = CRM_Utils_System::url('civicrm/report/instance/40',"force=1");
        $mAnti = CRM_Utils_System::url('civicrm/report/instance/39',"force=1");
        $ytd = CRM_Utils_System::url('civicrm/report/instance/38',"force=1");
        
        if ( !empty( $url ) ) {
                $smarty->_tpl_vars['url'] = $url;
        }    
        if ( !empty( $mtd  ) ) {
                $smarty->_tpl_vars['mtd'] = $mtd;
        }    
        if ( !empty( $ytd ) ) {
            $smarty->_tpl_vars['ytd'] = $ytd;
        }
        if ( !empty( $mAnti ) ) {
            $smarty->_tpl_vars['mAnti'] = $mAnti;
        }
        if( $summary ){
            $smarty->_tpl_vars['thisYear']  = $summary['year'];
            $smarty->_tpl_vars['thisMonth'] = $summary['month'];
            $smarty->_tpl_vars['thisMonthAnticipated'] = $summary['monthAnticipated'];
        }
        parent::run();
    }
    
    static function getSummary( $cid, $duration = 'month' ) {
        civicrm_initialize();
        $summary = array ('General' => 0,
                          'M&S'     => 0,
                          'Other'   => 0
                          );
        $total   = 0;
        if( $duration == 'month' ){
            $todaysDay = date('d',time());
            if( $todaysDay >= 20 ){
                $startDate = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm', time() ), 20, date( 'Y', time() ) ) );
                $endDate   = date( 'Y-m-d',strtotime("$startDate -1 day +1 month"));
            } else {
                $endDate   = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm', time() ), 19, date( 'Y', time() ) ) );
                $startDate = date( 'Y-m-d',strtotime("$endDate +1 day -1 month"));
            }            
            $query = "SELECT sum( cct.line_total ) sm, cct.label
FROM civicrm_contribution donation
LEFT JOIN civicrm_relationship org_supporter  ON org_supporter.contact_id_a = donation.contact_id
LEFT JOIN civicrm_line_item cct ON cct.entity_id = donation.id
LEFT JOIN civicrm_contact cc ON cc.id = donation.contact_id
where org_supporter.relationship_type_id = ". SUPPORTER_RELATION_TYPE_ID ." 
      AND org_supporter.contact_id_b = ".$cid."
      AND org_supporter.is_active = 1
      AND donation.receive_date BETWEEN '$startDate' AND '$endDate'
      AND donation.contribution_status_id = 1
      AND cc.is_deleted != 1
      AND donation.is_test = 0
group by cct.label";
        } else if( $duration == 'monthAnticipated' ){
            $query = "SELECT sum( cct.line_total ) sm, cct.label
FROM civicrm_contribution donation
LEFT JOIN civicrm_relationship org_supporter  ON org_supporter.contact_id_a = donation.contact_id
LEFT JOIN civicrm_line_item cct ON cct.entity_id = donation.id
LEFT JOIN civicrm_contact cc ON cc.id = donation.contact_id
where org_supporter.relationship_type_id = ". SUPPORTER_RELATION_TYPE_ID ." 
      AND org_supporter.contact_id_b = ".$cid."
      AND org_supporter.is_active = 1
      AND donation.contribution_status_id = 5
      AND cc.is_deleted != 1
      AND donation.is_test = 0
group by cct.label";
        } else {
            $query = "SELECT sum( cct.line_total ) sm, cct.label
FROM civicrm_contribution donation
LEFT JOIN civicrm_relationship org_supporter  ON org_supporter.contact_id_a = donation.contact_id
LEFT JOIN civicrm_line_item cct ON cct.entity_id = donation.id
LEFT JOIN civicrm_contact cc ON cc.id = donation.contact_id
where org_supporter.relationship_type_id = ". SUPPORTER_RELATION_TYPE_ID ." 
      AND org_supporter.contact_id_b = ".$cid."
      AND org_supporter.is_active = 1
      AND YEAR( donation.receive_date ) = YEAR(NOW()) 
      AND donation.contribution_status_id = 1
      AND cc.is_deleted != 1
      AND donation.is_test = 0
group by cct.label";
        }
        $dao = CRM_Core_DAO::executeQuery( $query );
        while( $dao->fetch() ){
            $summary[$dao->label]   = $dao->sm;
            $total                 += $dao->sm;
        }
        $summary[ 'total' ] = $total;
        $summary[ 'avg' ]   = $total;
        return $summary;
    }

    function getTemplateFileName() {
        $templateFile = "CRM/Contact/Page/DonationSummary.tpl";
        $template     = CRM_Core_Page::getTemplate( );
        if ( $template->template_exists( $templateFile ) ) {
            return $templateFile;
        }
    }
    
    static function getSummaryAverage( $cids, $summary ) { 
            CRM_Core_Error::debug( '$cids', $cids );
            exit;
        foreach ( $cids as $id ) {
            $todaysDay = date('d',time());
            if( $todaysDay >= 20 ){
                $startDate = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm', time() ), 20, date( 'Y', time() ) ) );
                $endDate   = date( 'Y-m-d',strtotime("$startDate -1 day +1 month"));
            } else {
                $endDate   = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm', time() ), 19, date( 'Y', time() ) ) );
                $startDate = date( 'Y-m-d',strtotime("$endDate +1 day -1 month"));
            }    
            $query = "SELECT donation.contact_id
FROM civicrm_contribution donation
LEFT JOIN civicrm_relationship org_supporter ON org_supporter.contact_id_a = donation.contact_id
LEFT JOIN civicrm_contact cc ON cc.id = donation.contact_id
where org_supporter.relationship_type_id = ". SUPPORTER_RELATION_TYPE_ID ." 
      AND org_supporter.contact_id_b = ".$id."
      AND org_supporter.is_active = 1
      AND donation.receive_date BETWEEN '$startDate' AND '$endDate'
      AND donation.contribution_status_id = 1
      AND cc.is_deleted != 1
      AND donation.is_test = 0";
            $dao = CRM_Core_DAO::executeQuery( $query );
            while( $dao->fetch() ) {
                $mcontact[$dao->contact_id] = $dao->contact_id;
            }  
            
            $query = "SELECT donation.contact_id
FROM civicrm_contribution donation
LEFT JOIN civicrm_relationship org_supporter ON org_supporter.contact_id_a = donation.contact_id
LEFT JOIN civicrm_contact cc ON cc.id = donation.contact_id
where org_supporter.relationship_type_id = ". SUPPORTER_RELATION_TYPE_ID ." 
      AND org_supporter.contact_id_b = ".$id."
      AND org_supporter.is_active = 1
      AND donation.contribution_status_id = 5
      AND cc.is_deleted != 1
      AND donation.is_test = 0";
            $dao = CRM_Core_DAO::executeQuery( $query );
            while( $dao->fetch() ) {
                $acontact[$dao->contact_id] = $dao->contact_id;
            }

            $query = "SELECT donation.contact_id
FROM civicrm_contribution donation
LEFT JOIN civicrm_relationship org_supporter ON org_supporter.contact_id_a = donation.contact_id
LEFT JOIN civicrm_contact cc ON cc.id = donation.contact_id
where org_supporter.relationship_type_id = ". SUPPORTER_RELATION_TYPE_ID ." 
      AND org_supporter.contact_id_b = ".$id."
      AND org_supporter.is_active = 1
      AND YEAR( donation.receive_date ) = YEAR(NOW()) 
      AND donation.contribution_status_id = 1
      AND cc.is_deleted != 1
      AND donation.is_test = 0";
            $dao = CRM_Core_DAO::executeQuery( $query );
            while( $dao->fetch() ) {
                $ycontact[$dao->contact_id] = $dao->contact_id;
            }  
        }
        
        if ( !empty($summary['month']['avg']) ) {
            $summary['month']['avg'] = $summary['month']['avg']/count($mcontact);
        }
        if ( !empty($summary['monthAnticipated']['avg']) ) {
            $summary['monthAnticipated']['avg'] = $summary['monthAnticipated']['avg']/count($acontact);
        }
        if ( !empty($summary['year']['avg']) ) {
            $summary['year']['avg'] = $summary['year']['avg']/count($ycontact);
        }
        return $summary;
    }
    
}
