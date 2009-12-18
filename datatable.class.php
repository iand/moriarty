<?php
require_once MORIARTY_DIR. 'store.class.php';
require_once MORIARTY_DIR. 'datatableresult.class.php';
require_once MORIARTY_DIR. 'simplegraph.class.php';

class DataTable {
  var $_store_uri = '';
  var $_credentials = '';
  var $_request_factory = '';
  var $_sparql = '';
  var $_limit = 0;
  var $_offset = 0;
  var $_map = array();
  var $_rmap = array();

  var $_types = array();
  var $_is_distinct = FALSE;
  var $_fields = array();
  var $_optionals = array();
  var $_orders = array();
  var $_filters = array();
  var $_patterns = array();
  var $_joins = array();
  var $_selections = array();
  var $_data = array();
  var $_field_defaults = array();

  function __construct($store_uri, $credentials = null, $request_factory = null) {
    $this->_store_uri = $store_uri;
    $this->_credentials = $credentials;
    $this->_request_factory = $request_factory;
  }

  function map($uri_or_array, $short_name = null) {
    if (is_array($uri_or_array)) {
      foreach ($uri_or_array as $uri => $short_name) {
        $this->_map[$uri] = $short_name;
        $this->_rmap[$short_name] = $uri;
      }
    }
    else if ($short_name !== null) {
      $this->_map[$uri_or_array] = $short_name;
      $this->_rmap[$short_name] = $uri_or_array;
    }
    return $this;
  }
  
  function limit($value, $offset = 0) {
    $this->_limit = $value;
    $this->_offset = $offset;
    return $this;
  }
  
  function select($field_list) {
    $field_list = trim($field_list);
    $fields = explode(',', $field_list);
    foreach ($fields as $field) {
      $field = trim($field);
      if (strpos($field, '.') >0 ) {
        $parts = explode('.', $field);
        $this->_joins[] = $parts;
        $this->_selections[] = $parts[0].'_'.$parts[count($parts)-1];
      }
      else {
        $this->_fields[] = $field;
        $this->_selections[] = $field;
      }
    }
    return $this;
  }

  
  function optional($field_list) {
    $field_list = trim($field_list);
    $fields = explode(',', $field_list);
    $field_list = array();
    foreach ($fields as $field) {
      $field = trim($field);
      $field_list[] = $field;
    }
    $this->_optionals[] = $field_list;
    return $this;
  }

  function from($type_list) {
    $type_list = trim($type_list);
    $types = explode(',', $type_list);
    foreach ($types as $type) {
      $type = trim($type);
      $this->_types[] = $type;
    }
    return $this;
  }
  
  function distinct() {
    $this->_is_distinct = TRUE;
    return $this;
  }  

  function order_by($field, $ordering='ASC') {
    $this->_orders[] = array('field' => $field, 'ordering' => $ordering);
    return $this;
  }

  function where($field, $value) {
    if (preg_match('~^(.+)\s*(>|<|\!\=|<=|>=)$~', $field, $m)) {
      $op = $m[2];
      $field = trim($m[1]);
    }
    else {
      $op = '=';
    }
    $this->_filters[] = array('field'=>$field, 'op'=>$op, 'value'=>$value);
    return $this;
  }

  function where_uri($field, $uri) {
    $this->_patterns[] = array('field'=>$field, 'value'=> '<' . $uri . '>');
    return $this;
  }

  function get_sparql() {
    $prefixes  = array();
    
    $this->_sparql = 'select ';
    if ($this->_is_distinct) {
      $this->_sparql .= 'distinct ';
    }
    $this->_sparql .= '?_uri ?' . join(' ?', $this->_selections);
    foreach ($this->_optionals as $optionals) {
      $this->_sparql .= ' ?' . join(' ?', $optionals);
    }
    $this->_sparql .= ' where {?_uri';
    $done_first = FALSE;
    foreach ($this->_patterns as $pattern) {
      if ($done_first) $this->_sparql .= ';';
      $this->_sparql .= ' <' . $this->_rmap[$pattern['field']] . '> ' . $pattern['value'];
      $done_first = TRUE;
    }
    foreach ($this->_fields as $field) {
      if ($done_first) $this->_sparql .= ';';
      $this->_sparql .= ' <' . $this->_rmap[$field] . '> ?' . $field;
      $done_first = TRUE;
    }
    foreach ($this->_filters as $filter) {
      if (!in_array($filter['field'], $this->_fields)) {
        if ($done_first) $this->_sparql .= ';';
        $this->_sparql .= ' <' . $this->_rmap[$filter['field']] . '> ?' . $filter['field'];
        $done_first = TRUE;
      }
    }

    foreach ($this->_types as $type) {
      if ($done_first) $this->_sparql .= ';';
      $this->_sparql .= ' a <' . $this->_rmap[$type] . '>';
      $done_first = TRUE;
    }

    $join_groups = array();
    foreach ($this->_joins as $join) {
      if (!array_key_exists($join[0], $join_groups)) {
        $join_group_properties = array();
      }
      else {
        $join_group_properties = $join_groups[$join[0]];
      }
      $join_group_properties[] = $join[1];
      $join_groups[$join[0]] = $join_group_properties;
    }
    
    foreach ($join_groups as $join_group => $join_group_properties) {
      if ($done_first) $this->_sparql .= ';';
      $this->_sparql .= ' <' . $this->_rmap[$join_group] . '> ?' . $join_group;
      $done_first = TRUE;
    }

    $this->_sparql .= '.';

    foreach ($this->_optionals as $optionals) {
      $this->_sparql .= ' optional {?_uri';
      $done_first = FALSE;
      foreach ($optionals as $field) {
        if ($done_first) $this->_sparql .= ';';
        $this->_sparql .= ' <' . $this->_rmap[$field] . '> ?' . $field;
        $done_first = TRUE;
      }
      $this->_sparql .= '. }';
    }
    
    foreach ($this->_filters as $filter) {
      $field = $filter['field'];
      $op = $filter['op'];
      $this->_sparql .= ' filter(';
      if (is_string($filter['value'])) {
        $this->_sparql .= 'str(?'. $field . ')';
        $this->_sparql .= $op;
        $this->_sparql .= "'".str_replace("'","\\'", $filter['value'])."'";
      }
      else if (is_bool($filter['value'])) {
        $prefixes['xsd'] = 'http://www.w3.org/2001/XMLSchema#';
        $this->_sparql .= 'xsd:boolean(?'. $field . ')';
        $this->_sparql .= $op;
        if ($filter['value'] === FALSE) {
          $this->_sparql .= 'false';
        }
        else {
          $this->_sparql .= 'true';
        }
      }
      else if (is_int($filter['value'])) {
        $prefixes['xsd'] = 'http://www.w3.org/2001/XMLSchema#';
        $this->_sparql .= 'xsd:integer(?'. $field . ')';
        $this->_sparql .= $op;
        $this->_sparql .= $filter['value'];
      }
      else if (is_float($filter['value'])) {
        $prefixes['xsd'] = 'http://www.w3.org/2001/XMLSchema#';
        $this->_sparql .= 'xsd:double(?'. $field . ')';
        $this->_sparql .= $op;
        $this->_sparql .= $filter['value'];
      }
      else {
        $this->_sparql .= $field;
        $this->_sparql .= $op;
        $this->_sparql .= $filter['value'];
      }   
      $this->_sparql .= ').';
    }
    
    foreach ($join_groups as $join_group => $join_group_properties) {
      $this->_sparql .= ' ?' . $join_group;
      $done_first = FALSE;
      foreach ($join_group_properties as $join_group_property) {
        if ($done_first) $this->_sparql .= ';';
        $this->_sparql .= ' <' . $this->_rmap[$join_group_property] . '> ?' . $join_group.'_'.$join_group_property;
        $done_first = TRUE;
      }
      $this->_sparql .= '.';
    }
    
    $this->_sparql .= ' }';

    if (count($this->_orders) > 0) {
      $done_order_by_token = FALSE;
      foreach ($this->_orders as $order) {
        if (in_array($order['field'], $this->_fields)) {
          if (!$done_order_by_token) {
            $this->_sparql .= ' order by';
            $done_order_by_token = TRUE;
          }
          if (strtolower($order['ordering']) == 'desc') {
            $this->_sparql .= ' DESC(?'. $order['field'] . ')';
          }
          else {
            $this->_sparql .= ' ?' . $order['field'];
          }
        }
      }
    }
    if ($this->_limit && is_int($this->_limit) && $this->_limit > 0) {
      $this->_sparql .= ' limit ' . $this->_limit;
    }
    if ($this->_offset && is_int($this->_offset) && $this->_offset > 0) {
      $this->_sparql .= ' offset ' . $this->_offset;
    }

    $header = '';
    foreach ($prefixes as $prefix => $uri) {
      $header .= 'prefix ' . $prefix . ': <' . $uri . '> ';
    }
    $this->_sparql = $header . $this->_sparql;
    return $this->_sparql;
  }
  
  function get() {
    $store = new Store($this->_store_uri, $this->_credentials, $this->_request_factory);
    $ss = $store->get_sparql_service();
    $query = $this->get_sparql();
    $response = $ss->query($query, 'json');
    
    if ($response->is_success()) {
      return new DataTableResult($response->body);
    }
    else {
      return new DataTableResult('{"head": {"vars": [ ] } , "results": { "bindings": [] } }');
    }

  }
  
  function set($field, $value, $type=null, $lang=null, $dt=null) {
    if (is_bool($value)) {
      $field_value = $value === TRUE ? 'true' : 'false';
      $this->_data[$field] = array('type' => 'literal', 'value' => $field_value, 'lang' => null, 'datatype' => 'http://www.w3.org/2001/XMLSchema#boolean');
    }
    else {
      $this->_data[$field] = array('type' => $type, 'value' => $value, 'lang' => $lang, 'datatype' => $dt);
    }
    return $this;
  }
  
  function get_insert_graph($type_list = '') {
    $g = new SimpleGraph();
    if (array_key_exists('_uri', $this->_data)) {
      $s = $this->_data['_uri']['value'];
    }
    else {
      $s = '_:a1';
    }
    
    $type_list = trim($type_list);
    $types = explode(',', $type_list);
    foreach ($types as $type) {
      $type = trim($type);
      if (strlen($type) > 0) {
        $g->add_resource_triple($s, RDF_TYPE, $this->_rmap[$type] ); 
      }
    }
    
    foreach ($this->_data as $field => $field_info) {
      if ($field !== '_uri') {
        $type = $field_info['type'];
        if ($type === null && array_key_exists($field, $this->_field_defaults) && array_key_exists('type', $this->_field_defaults[$field]) && $this->_field_defaults[$field]['type'] !== null) {
          $type = $this->_field_defaults[$field]['type'];
        }
        if ($type === null) {
          $type = 'literal';
        }

        if ($type === 'literal') {
          
          $dt = $field_info['datatype'];
          if ($dt === null && array_key_exists($field, $this->_field_defaults) && array_key_exists('datatype', $this->_field_defaults[$field]) && $this->_field_defaults[$field]['datatype'] !== null) {
            $dt = $this->_field_defaults[$field]['datatype'];
          }
          
          $g->add_literal_triple($s, $this->_rmap[$field], $field_info['value'], $field_info['lang'], $dt ); 
        }
        else if ($type === 'uri') {
          $g->add_resource_triple($s, $this->_rmap[$field], $field_info['value'] ); 
        }
        else if ($type === 'bnode') {
          $g->add_resource_triple($s, $this->_rmap[$field], $field_info['value'] ); 
        }
      }
    }
    
    return $g;
  }

  function insert($type_list = '') {
    $store = new Store($this->_store_uri, $this->_credentials, $this->_request_factory);
    $mb = $store->get_metabox();
    $g = $this->get_insert_graph($type_list);
    $response = $mb->submit_turtle( $g->to_turtle() );
  }
 
  function set_field_defaults($field, $type, $datatype = null) {
    $this->_field_defaults[$field] = array('type' => $type, 'datatype' => $datatype);
  }
}
