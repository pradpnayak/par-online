<?php

function make_entry_in_par_log( $op, $params ) {
  if ( $op == 'Update' || $op == 'Delete' ) {
    if ( array_key_exists('external_identifier' , $params) ) {
      $where = "WHERE external_identifier = '".$params['external_identifier']."'";
    } else {
      $where = "WHERE primary_contact_id = ".$params['primary_contact_id'];
    }
    $update = "SET log_action = '".$op."', log_time = now()," ;
    foreach ( $params as $key => $value ) {
      if ('primary_contact_id' == $key ) {
        //$update .= $key." = ".$value.", ";
      } else {
        $update .= $key." = '".$value."', ";
      }
    }
    $query = "UPDATE civicrm_log_par_donor ".rtrim( $update, ", " )." ". $where;
    
  } else if ( $op == 'Insert' ) {
    if ( array_key_exists( 'primary_contact_id' , $params) ) {
      $contact_id = $params['primary_contact_id'];
      unset($params['primary_contact_id']);
    }
    $query = "INSERT INTO civicrm_log_par_donor (".implode(", " ,array_keys($params) ).", primary_contact_id, log_action ) VALUES ( '".implode("', '" ,$params )."', ".$contact_id.", 'INSERT'  )";
  }  else if ( $op == 'Delete' ) {
    $update = "SET log_action = '".$op."', log_time = now(),";
    foreach ( $params as $key => $value ) {
      if ('primary_contact_id' == $key ) {
        //$update .= $key." = ".$value.", ";
      } else {
        $update .= $key." = '".$value."', ";
      }
    }
    $query = "UPDATE civicrm_log_par_donor ".rtrim( $update, ", " )." ". $where;
  }
  
  $result = CRM_Core_DAO::singleValueQuery($query);
  } 