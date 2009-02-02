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
    if ($trace) print "ResourceSelector: Selecting all subjects in graph\n";
    $candidates = array();
    $index = $g->get_index();
    foreach (array_keys($index) as $subject) {
      $candidates[] = $g->make_resource_array($subject);
    }
    return $this->_path->select($candidates, $g, $trace);
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
    if ((list($r, $v) = $this->m_literal($v)) && $r) {
      return array($r, $v);
    }
    if ((list($r, $v) = $this->m_textfunction($v)) && $r) {
      return array($r, $v);
    }   
    return array(false, $v);
  }

  function m_test($v) {
    list($axis, $v) = $this->m_axis($v);
    
    $selector= '';
    if ($r = $this->m('(\*)', $v)) {
      $selector = new WildCardMatcher();
      $v = $r[2];
    }
    else if ($r = $this->m('([a-z0-9_]+:[a-z0-9_]+)', $v)) {
      $selector = new TypeMatcher($r[1]);
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
    
    return array(new StepMatcher($selector, $axis, $filters), $v);    
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
      $left = $r;
      if ((list($r, $v) = $this->m_operator($v)) && $r) {
        $op = $r;
        if ((list($r, $v) = $this->m_unaryexpr($v)) && $r) {
          return array(new CompExpr($left, $op, $r), $v);
        }
      }
      else {
        return array(new CompExpr($left), $v);
      }
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
    if ((list($r, $v) = $this->m_LiteralGenerator($v)) && $r) {
      return array($r, $v);
    }
    if ((list($r, $v) = $this->m_locationpath($v)) && $r) {
      return array($r, $v);
    }
    return array(false, $v);
  }


  function m_LiteralGenerator($v) {
    if ((list($r, $v) = $this->m_split('"(.*)"', $v)) && $r) {
      return array(new LiteralGenerator($r), $v);
    }
    if ((list($r, $v) = $this->m_split('\\\'(.*)\\\'', $v)) && $r) {
      return array(new LiteralGenerator($r), $v);
    }
    return array(false, $v);
   }


  function m_literal($v) {
    if ((list($r, $v) = $this->m_split('"(.*)"', $v)) && $r) {
      return array(new LiteralMatcher($r), $v);
    }
    if ((list($r, $v) = $this->m_split('\\\'(.*)\\\'', $v)) && $r) {
      return array(new LiteralMatcher($r), $v);
    }
    return array(false, $v);
   }

  function m_textfunction($v) {
    if ((list($r, $v) = $this->m_split('(text\(\))', $v)) && $r) {
      return array(new AnyLiteralMatcher($r), $v);
    }
    return array(false, $v);
   }

  function m_operator($v) {
    return $this->m_split('(=)', $v);
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




class TypeMatcher {
  var $_type = null;
  function __construct($type) {
    $this->_type = $type; 
  }
  
  function matches($candidate, &$g, $trace = FALSE) {
    if ($trace) print "TypeMatcher: Testing " . $candidate['value'] . " using "  . $this->to_string() . "\n";

    $matches = FALSE;

    $test_uri = $g->qname_to_uri($this->_type);
    if ( $test_uri != null) {

      if (isset($candidate['node'])) {
        // We are testing an arc  
        if ($trace) print "TypeMatcher: Testing to see if " . $candidate['value'] . " is same as " . $test_uri . "\n";
        if ($candidate['value'] == $test_uri) {
          $matches = TRUE;
        }
      }
      else {
        // We are testing a node
        if ($trace) print "TypeMatcher: Testing to see if " . $candidate['value'] . " has type of " . $test_uri . "\n";
        if ($g->has_resource_triple($candidate['value'], RDF_TYPE, $test_uri) ) {
          $matches = TRUE;
        }
      }
    }
    if ($trace) print "TypeMatcher: " . ( $matches ? 'MATCHED' : 'NO MATCH') . " using " . $this->to_string() . "\n";

    return $matches;

  }
  

  function to_string() {
    return $this->_type; 
  } 
  
}

class WildCardMatcher {

  function matches($candidate, &$g, $trace = FALSE) {
    if ($trace) print "WildCardMatcher: Testing " . $candidate['value'] . " using "  . $this->to_string() . "\n";
    return TRUE;
  }
  
  function to_string() {
    return '*'; 
  } 
}

class LiteralMatcher {
  var $_text = '';
  var $_dt = '';
  function __construct($text,$dt = null) {
    $this->_text = $text;
    $this->_dt = $dt;
  } 
  
  function matches($candidate, &$g, $trace = FALSE) {
    if ($trace) print "LiteralMatcher: Testing " . $candidate['value'] . " using "  . $this->to_string() . "\n";
    $matches = FALSE;

    if ($trace) print "LiteralMatcher: Testing to see if " . $candidate['value'] . " is same as " . $this->_text . "\n";
    if ($candidate['type'] == 'literal' && $candidate['value'] == $this->_text) {
      if ($trace) print "LiteralMatcher: It is, adding " . $candidate['value'] . " to selected queue\n";
      $matches = TRUE; 
    }

    if ($trace) print "LiteralMatcher: " . ( $matches ? 'MATCHED' : 'NO MATCH') . " using " . $this->to_string() . "\n";
    return $matches;
  }

  function to_string() {
    $ret = "'" . $this->_text. "'"; 
    return $ret;
  }
}

class AnyLiteralMatcher {
  function __construct() {

  } 
  
  function matches($candidate, &$g, $trace = FALSE) {
    if ($trace) print "AnyLiteralMatcher: Testing " . $candidate['value'] . " using "  . $this->to_string() . "\n";
    $matches = FALSE;

    if ($candidate['type'] == 'literal') {
      $matches = TRUE; 
    }

    if ($trace) print "AnyLiteralMatcher: " . ( $matches ? 'MATCHED' : 'NO MATCH') . " using " . $this->to_string() . "\n";
    return $matches;
  }

  function to_string() {
    $ret = "text()"; 
    return $ret;
  }
}


class LiteralGenerator {
  var $_text = '';
  var $_dt = '';
  function __construct($text,$dt = null) {
    $this->_text = $text;
    $this->_dt = $dt;
  } 
  
  function select($candidates, &$g, $trace = FALSE) {
    return array( array('type'=>'literal', 'value'=>$this->_text));
  }

  function to_string() {
    $ret = "'" . $this->_text. "'"; 
    return $ret;
  }
}


class Path {
  var $_steps = array();

  function __construct($steps) {
    $this->_steps = $steps;  
  }
    
  function select($candidates, &$g, $trace = FALSE) {
    if ($trace) print "Path: " . $this->to_string() ."\n";
   
    for ($i = 0; $i < count($this->_steps); $i++) {
      $selected = array();
      $step = $this->_steps[$i];
      if ($trace) print "Path: Filtering " . count($candidates) . " candidates using " . $step->to_string() . "\n";
      foreach ($candidates as $candidate) {
        if ( $step->matches($candidate, $g, $trace) ) {
          $selected[] = $candidate; 
        }
      }
      if ($trace) print "Path: " . count($selected) . " resources passed the filter\n";
      $candidates = $this->get_candidates($selected, $g, $trace);
    }    
    
    return $selected;
  }
  
  function get_candidates($resources, &$g, $trace = FALSE) {
    $candidates = array();
    foreach ($resources as $resource) {
      if ($resource['type'] != 'literal') {
        if (isset($resource['node'])) {
          if ($trace) print "Path: Selecting nodes that are values of " . $resource['value'] . "\n";
          $candidates = array_merge($candidates, $this->get_nodes($resource, $g));  
        }
        else {
          if ($trace) print "Path: Selecting arcs that are properties of " . $resource['value'] . "\n";
          $candidates = array_merge($candidates, $this->get_arcs($resource, $g)); 
        }
      }
    }

    if ($trace) print "Path: Selected " . count($candidates) . " candidates\n";
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




class StepMatcher {
  var $_selector = '';
  var $_axis = '';
  var $_filters = array();
  function __construct($selector, $axis, $filters) {
    $this->_selector = $selector;
    $this->_axis = $axis;
    $this->_filters = $filters;
  } 
  
  function matches($candidate, &$g, $trace = FALSE) {
    if ($trace) print "StepMatcher: Matching " . ( $candidate ? $candidate['value'] : 'null') . " using " . $this->to_string() . "\n";
    $matches = FALSE;
    if ( $this->_selector->matches($candidate, $g, $trace) ) {
      if (count($this->_filters) == 0) {
        $matches = TRUE;  
      }
      else {
        if ($trace) print "StepMatcher: Iterating through all filters\n";
        $filter_passes = 0;
        
        $filter_resources = $this->get_candidates(array($candidate), $g, $trace);
        foreach ( $this->_filters as $filter) {
          if ($trace) print "StepMatcher: Trying " . $filter->to_string() . "\n";
          if ($filter->matches($filter_resources, $g, $trace)) {
            $filter_passes++;
          }
        }   
        if ( $filter_passes == count($this->_filters)) {
          $matches = TRUE;  
        }
      }
    }

    if ($trace) print "StepMatcher: " . ( $matches ? 'MATCHED' : 'NO MATCH') . " using " . $this->to_string() . "\n";
    return $matches;        
  }

  function get_candidates($resources, &$g, $trace = FALSE) {
    $candidates = array();
    foreach ($resources as $resource) {
      if ($resource['type'] != 'literal') {
        if (isset($resource['node'])) {
          if ($trace) print "Path: Selecting nodes that are values of " . $resource['value'] . "\n";
          $candidates = array_merge($candidates, $this->get_nodes($resource, $g));  
        }
        else {
          if ($trace) print "Path: Selecting arcs that are properties of " . $resource['value'] . "\n";
          $candidates = array_merge($candidates, $this->get_arcs($resource, $g)); 
        }
      }
    }

    if ($trace) print "Path: Selected " . count($candidates) . " candidates\n";
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
    $ret = '';
    if ($this->_axis && $this->_axis != 'out') {
      $ret .= $this->_axis . '::';
    } 
    $ret .= $this->_selector->to_string();
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
  
  function matches($candidates, &$g, $trace = FALSE) {
    $match = FALSE;
    
    
    if ($this->_left->matches($candidates, $g, $trace)) {
      $match = TRUE;  
    }
    else if ($this->_right && $this->_right->matches($candidates, $g, $trace)) {
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
  
  function matches($candidates, &$g, $trace = FALSE) {
    $match = FALSE;

    if ($this->_left->matches($candidates, $g, $trace)) {
      $match = FALSE;
      if ($this->_right) {
        if ($this->_right->matches($candidates, $g, $trace)) {
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
  
  function matches($candidates, &$g, $trace = FALSE) {
    $matches = FALSE;
    
    if ($trace) print "CompExpr: Testing using left of " . $this->_left->to_string() . "\n";
    $selected = $this->_left->select($candidates, $g, $trace);
    if ($trace) print "CompExpr: Selected " . count($selected) . " resources\n";
    
    if ( $this->_operator && $this->_right) {
      if ($trace) print "CompExpr: Testing using left of " . $this->_right->to_string() . "\n";
      $selected_right = $this->_right->select($candidates, $g, $trace);
      if ($trace) print "CompExpr: Selected " . count($selected_right) . " resources\n";
      if (count($selected_right) > 0 ) {
      
        if ($this->_operator == '=') {
          // Naieve for now
          foreach ($selected as $selected_resource) {
            if (in_array($selected_resource, $selected_right)) {
              $matches = TRUE;
              break;
            }
          }
        }       
      }
    }
    else {
      if (count($selected) > 0 ) {
        $matches = TRUE;
      }
      
    }
    
    if ($trace) print "CompExpr: " . ( $matches ? 'MATCHED' : 'NO MATCH') . " using " . $this->to_string() . "\n";
    return $matches;
  }

  function to_string() {
    $ret = $this->_left->to_string();  
    if ( $this->_operator && $this->_right) {
      $ret .= ' ' . $this->_operator . ' ' . $this->_right->to_string();  
    } 
    return $ret;
  }
  
}
