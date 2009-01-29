<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'moriarty.inc.php';

/**
 * EXPERIMENTAL: Represents a path selecting nodes from a simple graph
 * Based on http://www.w3.org/2005/04/fresnel-info/fsl/
 */
class GraphPath {

  private $_path = array();
  
  function __construct($path) {
    $this->_path = $this->_parse_path($path);  
  }
    
  /**
   * Evaluate the path against a graph
   * @param SimpleGraph g
   * @return array nodes that match the path
   */
  function match(&$g, $trace = FALSE) {
    if ($trace) print "\nPath is " . $this->to_string() . "\n";
    $selected = array();
    $index = $g->get_index();
    
    $subjects = array_keys($index);
    if ($trace) print "GraphPath: Iterating through all subjects\n";
    foreach ($subjects as $subject) {
      if ($trace) print "GraphPath: Testing " . $subject . "\n";
      $candidate = $g->make_resource_array($subject);
      $selected  = array_merge($selected, $this->_path->select($candidate, $g, $trace));
      if ($trace) print "GraphPath: Number selected so far is " . count($selected) . "\n";
    }

    return $selected;
  }


  /**
   * @access private
   */
  private function _parse_path($v) {
    list($step, $v) = $this->m_locationpath($v);
    return $step;
  }

  // borrowed from ARC2
  function m($re, $v, $options = 'si') {
    return preg_match("/^\s*" . $re . "(.*)$/" . $options, $v, $m) ? $m : false;
  }

  function m_split($pattern, $v) {
    if ($r = $this->m($pattern, $v)) {
      return array($r[1], $r[2]);
    }
    return array(false, $v);
  }
  
  function m_locationpath($v) {
    $steps = array();
    if ((list($r, $v) = $this->m_step($v)) && $r) {
      $steps[] = $r;  
      
      while ((list($r, $v) = $this->m_slash($v)) && $r) {
        if ((list($r, $v) = $this->m_step($v)) && $r) {
          $steps[] = $r;  
        }
      }
    }
    return array(new Path($steps), $v);
  } 

  function m_step($v) {
    if ((list($r, $v) = $this->m_test($v)) && $r) {
      return array($r, $v);
    }
    return array(false, $v);
  }

  function m_test($v) {
    list($axis, $v) = $this->m_axis($v);
    
    $test = '';
    if ($r = $this->m('(\*)', $v)) {
      $test = '*';
      $v = $r[2];
    }
    else if ($r = $this->m('([a-z0-9_]+:[a-z0-9_]+)', $v)) {
      $test = $r[1];
      $v = $r[2];
    }
    else {
      return array(false, $v);  
    }
    
    $filters = array();
    while ((list($r, $v) = $this->m_openbracket($v)) && $r) {
      if ((list($r, $v) = $this->m_orexpr($v)) && $r) {
        $filters[] = $r;
      }   
      list($r_br, $v) = $this->m_closebracket($v);
    }
    
    return array(new TestExpr($test, $axis, $filters), $v);    
  }

  function m_orexpr($v) {
    if ((list($r, $v) = $this->m_andexpr($v)) && $r) {
      $left = $r;
      if ((list($r, $v) = $this->m_split('(\s+or\s+)', $v)) && $r) {
        if ((list($r, $v) = $this->m_andexpr($v)) && $r) {
          return array(new OrExpr($left, $r), $v);
        }
      }
      else {
        return array(new OrExpr($left), $v);
      }
    }
    return array(false, $v);
  }

  function m_andexpr($v) {
    if ((list($r, $v) = $this->m_compexpr($v)) && $r) {
      $left = $r;
      if ((list($r, $v) = $this->m_split('(\s+and\s+)', $v)) && $r) {
        if ((list($r, $v) = $this->m_andexpr($v)) && $r) {
          return array(new AndExpr($left, $r), $v);
        }
      }
      else {
        return array(new AndExpr($left), $v);
      }
    }
    return array(false, $v);
  }


  function m_compexpr($v) {
    if ((list($r, $v) = $this->m_unaryexpr($v)) && $r) {
      return array(new CompExpr($r), $v);
    }
    return array(false, $v);
  }

  function m_unaryexpr($v) {
    if ((list($r, $v) = $this->m_functioncall($v)) && $r) {
      return array($r, $v);
    }
    if ((list($r, $v) = $this->m_number($v)) && $r) {
      return array($r, $v);
    }
    if ((list($r, $v) = $this->m_locationpath($v)) && $r) {
      return array($r, $v);
    }
    return array(false, $v);
  }

  function m_functioncall($v) {
    return array(false, $v);
  }

  function m_number($v) {
    return array(false, $v);
  }


  function m_slash($v) {
    return $this->m_split('(\/)', $v);
  }

  function m_openbracket($v) {
    return $this->m_split('(\[)', $v);
  }


  function m_closebracket($v) {
    return $this->m_split('(\])', $v);
  }


  function m_axis($v) {
    return $this->m_split('(in|out)::', $v);
  }

  function to_string() {
    return $this->_path->to_string();
  }

}


class Path {
  var $_steps = array();

  function __construct($steps) {
    $this->_steps = $steps;  
  }
    
  function select(&$resource, &$g, $trace = FALSE) {
    if ($trace) print "Does " . $resource['value'] . " match " . $this->_steps[0]->to_string() . "\n";
  
    if (! $this->_steps[0]->matches($resource, $g, $trace)) {
      if ($trace) print "NO MATCH of " . $resource['value'] . " against " . $this->_steps[0]->to_string() . "\n";
      return array();
    }
    else {
      if ($trace) print "MATCH of " . $resource['value'] . " against " . $this->_steps[0]->to_string() . "\n";
    }
    
    $selected = array();
    $candidates = array($resource);
    for ($i = 1; $i < count($this->_steps); $i++) {
      $step = $this->_steps[$i];
      foreach ($candidates as $candidate) {
        if (isset($candidate['node'])) {
          if ($trace) print "Selecting nodes that are values of " . $candidate['value'] . "\n";
          $cand_resources = $this->get_nodes($candidate, $g);  
        }
        else {
          if ($trace) print "Selecting arcs that are properties of " . $candidate['value'] . "\n";
          $cand_resources = $this->get_arcs($candidate, $g); 
        }
        foreach ($cand_resources as $cand_resource) {
          if ($trace) print "Path: Testing " . $cand_resource['value'] . " against " . $step->to_string() . " so adding to selected list\n"; 
          if ($step->matches($cand_resource, $g, $trace)) {
            if ($trace) print "Path: " . $cand_resource['value'] . " matched " . $step->to_string() . " so adding to selected list\n"; 
            $selected[] = $cand_resource;  
          }
        }
      }
      $candidates = $selected;
      $selected = array();
    }    
    
    return $candidates;
  }
  
  function get_arcs(&$node, &$g) {
    $arcs = array();
    $properties = $g->get_subject_properties($node['value']);
    foreach ($properties as $property_uri) {
      $info = $g->make_resource_array($property_uri);
      $info['node'] = $node['value'];
      $arcs[] = $info;
    }
    return $arcs;
  }
  
  function get_nodes(&$arc, &$g) {
    return $g->get_subject_property_values($arc['node'], $arc['value'], $g);
  }

  function to_string() {
    $ret = $this->_steps[0]->to_string();
    for ($i = 1; $i < count($this->_steps); $i++) {
      $ret .= '/' . $this->_steps[$i]->to_string();
    }
    return $ret;
  }
}



class TestExpr {
  var $_test = '';
  var $_axis = '';
  var $_filters = array();
  function __construct($test, $axis, $filters) {
    $this->_test = $test;
    $this->_axis = $axis;
    $this->_filters = $filters;
  } 
  
  function matches(&$resource, &$g, $trace = FALSE) {
    if ($trace) print "TestExpr: testing " . $resource['value'] . " against " . $this->to_string() . "\n";
    $match = FALSE;
    
    if ($this->_test != '*') {
      if ($resource['type'] == 'literal') return FALSE;
      
      $test_uri = $g->qname_to_uri($this->_test);

      if ( $test_uri == null) return FALSE;

      if (isset($resource['node'])) {
        // We are testing an arc  
        if ($resource['value'] != $test_uri) return FALSE;
      }
      else {
        // We are testing a node
        if (! $g->has_resource_triple($resource['value'], RDF_TYPE, $test_uri) ) return FALSE;
      }
    }

    if (count($this->_filters) == 0) return TRUE;
    
    $properties = $g->get_subject_properties($resource['value']);
    $property_infos = array();
    foreach ($properties as $property_uri) {
      $property_info = $g->make_resource_array($property_uri);
      $property_info['node'] = $resource['value'];
      $property_infos[] = $property_info;
    }
    
    $filter_passes = 0;
    if ($trace) print "TestExpr: Iterating through all filters\n";
    foreach ( $this->_filters as $filter) {
      if ($trace) print "TestExpr: Trying " . $filter->to_string() . "\n";
      if ($filter->matches_one_of($property_infos, $g, $trace)) {
        $filter_passes++;
        if ($trace) print "passing [" . $filter->to_string() . "], total number passed is " . $filter_passes . "\n";
      }
    }   
    if ( $filter_passes == count($this->_filters)) {
      if ($trace) print "TestExpr: All filters matched, return true\n";
      return TRUE;
    }

    if ($trace) print "TestExpr: " . ( $match ? 'MATCHED' : 'NO MATCH') . "\n";
    return $match;        
  }
  
  function to_string() {
    $ret = '';
    if ($this->_axis && $this->_axis != 'out') {
      $ret .= $this->_axis . '::';
    } 
    $ret .= $this->_test;
    foreach ( $this->_filters as $filter) {
      $ret .= "[" . $filter->to_string() . "]";
    }
    return $ret;
  }
  
}
    


class OrExpr {
  var $_left = null;
  var $_right = null;
  function __construct($left, $right = null) {
    $this->_left = $left;
    $this->_right = $right;
  } 
  
  function matches_one_of(&$resource_list, &$g, $trace = FALSE) {
    $match = FALSE;
    
    
    if ($this->_left->matches_one_of($resource_list, $g, $trace)) {
      $match = TRUE;  
    }
    else if ($this->_right && $this->_right->matches_one_of($resource_list, $g, $trace)) {
      $match = TRUE;  
    }
    if ($trace && $this->_right) print "OrExpr: " . ( $match ? 'MATCHED' : 'NO MATCH') . "\n";
    return $match;
  }

  function to_string() {
    $ret = $this->_left->to_string();
    if ($this->_right) $ret .= ' or ' . $this->_right->to_string();
    return $ret;
  }

}

class AndExpr {
  var $_left = null;
  var $_right = null;

  function __construct($left, $right = null) {
    $this->_left = $left;
    $this->_right = $right;
  } 
  
  function matches_one_of(&$resource_list, &$g, $trace = FALSE) {
    $match = FALSE;

    if ($this->_left->matches_one_of($resource_list, $g, $trace)) {
      $match = FALSE;
      if ($this->_right) {
        if ($this->_right->matches_one_of($resource_list, $g, $trace)) {
          $match = TRUE;  
        }
      }
      else {
        $match = TRUE;  
      }
    }
    
    if ($trace && $this->_right) print "AndExpr: " . ( $match ? 'MATCHED' : 'NO MATCH') . "\n";
    return $match;
  }
  
  function to_string() {
    $ret = $this->_left->to_string();
    if ($this->_right) $ret .= ' and ' . $this->_right->to_string();
    return $ret;
  }
  
}


class CompExpr {
  var $_left = null;
  var $_operator = null;
  var $_right = null;
  function __construct($left, $operator = null, $right = null) {
    $this->_left = $left;
    $this->_operator = $operator;
    $this->_right = $right;
  } 
  
  function matches_one_of(&$resource_list, &$g, $trace = FALSE) {
    foreach ($resource_list as $resource) {
      $selected = $this->_left->select($resource, $g, $trace);
      if ($trace) print "CompExpr: selected " . count($selected) . " resources\n";
      if (count($selected) > 0 ) return TRUE;
    }
    
    return FALSE;
  }
  function to_string() {
    return $this->_left->to_string();  
  }
  
}
