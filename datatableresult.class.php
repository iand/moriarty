<?php

class DataTableResult {
  var $_fields = array();
  var $_results = array();
  var $_rowdata = array();
  
  function __construct($data) {
    $results = json_decode($data, true);
    
    $this->_fields = $results['head']['vars'];
    foreach ($results['results']['bindings'] as $binding) {
      $row = array();
      $rowdata = array();
      foreach ($this->_fields as $field) {
        if (array_key_exists($field, $binding)) {
          $row[$field] = $binding[$field]['value'];
          if (array_key_exists('type', $binding[$field]) ) {
            if ($binding[$field]['type'] === 'typed-literal') {
              $rowdata[$field]['type'] = 'literal';
            }
            else {
              $rowdata[$field]['type'] = $binding[$field]['type'];
            }
          }
          else {
            $rowdata[$field]['type'] = null;
          }

          if (array_key_exists('datatype', $binding[$field]) ) {
            $rowdata[$field]['datatype'] = $binding[$field]['datatype'];
          }
          else {
            $rowdata[$field]['datatype'] = null;
          }

          if (array_key_exists('xml:lang', $binding[$field]) ) {
            $rowdata[$field]['lang'] = $binding[$field]['xml:lang'];
          }
          else {
            $rowdata[$field]['lang'] = null;
          }
        }
        else {
          $row[$field] = null;
          $rowdata[$field]['type'] = 'unknown';
        }
      }
      $this->_results[] = $row;
      $this->_rowdata[] = $rowdata;
    }  
    
    
  }
  
  function num_rows() {
    return count($this->_results);
  }
  
  function num_fields() {
    return count($this->_fields);
  }
  

  function result() {
    $results = array();
    foreach ($this->_results as $result) {
      $results[] = (object)$result;
    }
    return $results;
  }

  function result_array() {
    return $this->_results;
  }
  
  function row_array($index=0) {
    return $this->_results[$index];
  }
  function row($index=0) {
    return (object)$this->_results[$index];
  }


  function rowdata($index=0) {
    return $this->_rowdata[$index];
  }

  function to_string() {
    $ret = '';
    $col_widths = array();
    foreach ($this->_fields as $field) {
      $col_widths[$field] = strlen($field);
    }
    foreach ($this->_results as $result) {
      foreach ($this->_fields as $field) {
        if (! is_null($result[$field])) {
          if (strlen($result[$field]) > $col_widths[$field]) {
            $col_widths[$field] = strlen($result[$field]);
          }
        }
      }
    }

    $total_width = 0;
    $format_string = '';
    foreach ($this->_fields as $field) {
      $total_width += $col_widths[$field] + 2;
      $ret .= str_pad($field, $col_widths[$field]) . '  ';
    }
    $ret .= "\n";

    $ret .= str_repeat('_', $total_width) . "\n";
    foreach ($this->_results as $result) {
      foreach ($this->_fields as $field) {
        $ret .= str_pad($result[$field], $col_widths[$field]) . '  ';
      }
      $ret .= "\n";
    }
    
    return $ret;
  }

  
}
