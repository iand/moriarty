<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'moriarty.inc.php';
require_once MORIARTY_ARC_DIR . "ARC2.php";

/**
 * Represents an RDF graph and provides some simple functions for traversing and manipulating it
 */
class SimpleGraph {
  protected $_index = array();
  protected $_ns = array (
                    'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#',
                    'owl' => 'http://www.w3.org/2002/07/owl#',
                    'cs' => 'http://purl.org/vocab/changeset/schema#',
                    'bf' => 'http://schemas.talis.com/2006/bigfoot/configuration#',
                    'frm' => 'http://schemas.talis.com/2006/frame/schema#',

                    'dc' => 'http://purl.org/dc/elements/1.1/',
                    'dct' => 'http://purl.org/dc/terms/',
                    'dctype' => 'http://purl.org/dc/dcmitype/',

                    'foaf' => 'http://xmlns.com/foaf/0.1/',
                    'bio' => 'http://purl.org/vocab/bio/0.1/',
                    'geo' => 'http://www.w3.org/2003/01/geo/wgs84_pos#',
                    'rel' => 'http://purl.org/vocab/relationship/',
                    'rss' => 'http://purl.org/rss/1.0/',
                    'wn' => 'http://xmlns.com/wordnet/1.6/',
                    'air' => 'http://www.daml.org/2001/10/html/airport-ont#',
                    'contact' => 'http://www.w3.org/2000/10/swap/pim/contact#',
                    'ical' => 'http://www.w3.org/2002/12/cal/ical#',
                    'icaltzd' => 'http://www.w3.org/2002/12/cal/icaltzd#',
                    'frbr' => 'http://purl.org/vocab/frbr/core#',

                    'ad' => 'http://schemas.talis.com/2005/address/schema#',
                    'lib' => 'http://schemas.talis.com/2005/library/schema#',
                    'dir' => 'http://schemas.talis.com/2005/dir/schema#',
                    'user' => 'http://schemas.talis.com/2005/user/schema#',
                    'sv' => 'http://schemas.talis.com/2005/service/schema#',
                    'mo' => 'http://purl.org/ontology/mo/',
                    'status' => 'http://www.w3.org/2003/06/sw-vocab-status/ns#',
                    'label' => 'http://purl.org/net/vocab/2004/03/label#',
                    'skos' => 'http://www.w3.org/2004/02/skos/core#',
                  );
                  
  function __destruct(){
    unset($this->_index);
    unset($this);
  }


  /**
   * Map a portion of a URI to a short prefix for use when serialising the graph
   * @param string prefix the namespace prefix to associate with the URI
   * @param string uri the URI to associate with the prefix
   */                  
  function set_namespace_mapping($prefix, $uri) {
    $this->_ns[$prefix] = $uri;
  }


  /**
   * Adds a triple with a resource object to the graph
   * @param string s the subject of the triple, either a URI or a blank node in the format _:name
   * @param string p the predicate URI of the triple
   * @param string o the object of the triple, either a URI or a blank node in the format _:name
   * @return boolean true if the triple was new, false if it already existed in the graph
   */
  function add_resource_triple($s, $p, $o) {
    $o_type = strpos($o, '_:' ) === 0 ? 'bnode' : 'uri';
    $o_info = array('type' => $o_type, 'value' => $o);
    return $this->_add_triple($s, $p, $o_info);
  }

  /**
   * Adds a triple with a literal object to the graph
   * @param string s the subject of the triple, either a URI or a blank node in the format _:name
   * @param string p the predicate of the triple as a URI
   * @param string o the object of the triple as a string
   * @param string lang the language code of the triple's object (optional)
   * @param string dt the datatype URI of the triple's object (optional)
   * @return boolean true if the triple was new, false if it already existed in the graph
   */
  function add_literal_triple($s, $p, $o, $lang = null, $dt = null) {
    $o_info = array('type' => 'literal', 'value' => $o);
    if ( $lang != null ) {
      $o_info['lang'] = $lang;
    }
    if ( $dt != null ) {
      $o_info['datatype'] = $dt;
    }
    return $this->_add_triple($s, $p, $o_info);
  }

  private function _add_triple($s, $p, $o_info) {
    if (!isset($this->_index[$s])) { 
      $this->_index[$s] = array();
      $this->_index[$s][$p] = array( $o_info );
      return true;
    }
    elseif (!isset($this->_index[$s][$p])) {
      $this->_index[$s][$p] = array( $o_info);
      return true;
    }
    else {          
      if ( ! in_array( $o_info, $this->_index[$s][$p] ) ) {
        $this->_index[$s][$p][] = $o_info;       
        return true;
      }
    }
    return false;  
  }

  /**
   * @deprecated this is deprecated
   */
  function get_triples() {
    return ARC2::getTriplesFromIndex($this->_to_arc_index($this->_index));
  }

  /**
   * Get a copy of the graph's triple index
   * @see http://n2.talis.com/wiki/RDF_PHP_Specification
   */
  function get_index() {
    return $this->_index;
  }


  /**
   * Serialise the graph to RDF/XML
   * @return string the RDF/XML version of the graph
   */
  function to_rdfxml() {
    $serializer = ARC2::getRDFXMLSerializer(
        array(
          'ns' => $this->_ns,
        )
      );
    return $serializer->getSerializedIndex($this->_to_arc_index($this->_index));
 }

  /**
   * Serialise the graph to Turtle
   * @see http://www.dajobe.org/2004/01/turtle/
   * @return string the Turtle version of the graph
   */
  function to_turtle() {
    $serializer = ARC2::getTurtleSerializer(
        array(
          'ns' => $this->_ns,
        )
      );
    return $serializer->getSerializedIndex($this->_to_arc_index($this->_index));
  }

  /**
   * Serialise the graph to N-Triples
   * @see http://www.w3.org/TR/rdf-testcases/#ntriples
   * @return string the N-Triples version of the graph
   */
  function to_ntriples() {
    $serializer = ARC2::getComponent('NTriplesSerializer', array());
    return $serializer->getSerializedIndex($this->_to_arc_index($this->_index));
  }


  /**
   * Serialise the graph to JSON
   * @see http://n2.talis.com/wiki/RDF_JSON_Specification
   * @return string the JSON version of the graph
   */
  function to_json() {
    $serializer = ARC2::getRDFJSONSerializer(
        array(
          'ns' => $this->_ns,
        )
      );
    return $serializer->getSerializedIndex($this->_to_arc_index($this->_index));
  }
  
  /**
   * Fetch the first literal value for a given subject and predicate. If there are multiple possible values then one is selected at random. 
   * @param string s the subject to search for
   * @param string p the predicate to search for
   * @param string default a default value to use if no literal values are found
   * @return string the first literal value found or the supplied default if no values were found
   */
  function get_first_literal($s, $p, $default = null) {
    if ( array_key_exists($s, $this->_index)) {
      if (is_array($p)) {
        foreach($p as $p_uri) {
          if(array_key_exists($p_uri, $this->_index[$s]) ) {
            foreach ($this->_index[$s][$p_uri] as $value) {
              if ($value['type'] == 'literal') {
                return $value['value'];
              }
            }
          }         
        }       
      }
      else if(array_key_exists($p, $this->_index[$s]) ) {
        foreach ($this->_index[$s][$p] as $value) {
          if ($value['type'] == 'literal') {
            return $value['value'];
          }
        }
      }
    }

    return $default;
  }

  /**
   * Fetch the first resource value for a given subject and predicate. If there are multiple possible values then one is selected at random. 
   * @param string s the subject to search for
   * @param string p the predicate to search for
   * @param string default a default value to use if no literal values are found
   * @return string the first resource value found or the supplied default if no values were found
   */
  function get_first_resource($s, $p, $default = null) {
    if ( array_key_exists($s, $this->_index) && array_key_exists($p, $this->_index[$s]) ) {
      foreach ($this->_index[$s][$p] as $value) {
        if ($value['type'] == 'uri' || $value['type'] == 'bnode' ) {
          return $value['value'];
        }
      }
    }
    else {
      return $default;
    }
  }

  /**
   * Remove a triple with a resource object from the graph
   * @param string s the subject of the triple, either a URI or a blank node in the format _:name
   * @param string p the predicate URI of the triple
   * @param string o the object of the triple, either a URI or a blank node in the format _:name
   */
  function remove_resource_triple( $s, $p, $o) {
    for ($i = count($this->_index[$s][$p]) - 1; $i >= 0; $i--) {
      if (($this->_index[$s][$p][$i]['type'] == 'uri' || $this->_index[$s][$p][$i]['type'] == 'bnode') && $this->_index[$s][$p][$i]['value'] == $o)  {
        array_splice($this->_index[$s][$p], $i, 1);
      }
    }

    if (count($this->_index[$s][$p]) == 0) {
      unset($this->_index[$s][$p]);
    }
    if (count($this->_index[$s]) == 0) {
      unset($this->_index[$s]);
    }

  }

  /**
   * Remove all triples having the supplied subject
   * @param string s the subject of the triple, either a URI or a blank node in the format _:name
   */
  function remove_triples_about($s) {
    unset($this->_index[$s]);
  }


  /**
   * Replace the triples in the graph with those parsed from the supplied RDF/XML
   * @param string rdfxml the RDF/XML to parse
   * @param string base the base URI against which relative URIs in the RDF/XML document will be resolved
   */
  function from_rdfxml($rdfxml, $base='') {
    if ($rdfxml) {
      $this->remove_all_triples();
      $this->add_rdfxml($rdfxml, $base);
    }
  }
  
  /**
   * Replace the triples in the graph with those parsed from the supplied JSON
   * @see http://n2.talis.com/wiki/RDF_JSON_Specification
   * @param string json the JSON to parse
   */
  function from_json($json) {
    if ($json) {
      $this->remove_all_triples();
      $this->_index = json_decode($json, true);
    }
  }

  /**
   * Add the triples parsed from the supplied RDF/XML to the graph
   * @param string rdfxml the RDF/XML to parse
   * @param string base the base URI against which relative URIs in the RDF/XML document will be resolved
   */
  function add_rdfxml($rdfxml, $base='') {
    if ($rdfxml) {
      $parser = ARC2::getRDFXMLParser();
      $parser->parse($base, $rdfxml );
      $this->_add_arc2_triple_list($parser->getTriples());
      unset($parser);
    }
  }

  /**
   * Replace the triples in the graph with those parsed from the supplied Turtle
   * @see http://www.dajobe.org/2004/01/turtle/
   * @param string turtle the Turtle to parse
   * @param string base the base URI against which relative URIs in the Turtle document will be resolved
   */
  function from_turtle($turtle, $base='') {
    if ($turtle) {
      $this->remove_all_triples();
      $this->add_turtle($turtle, $base);
    }
  }

  /**
   * Add the triples parsed from the supplied Turtle to the graph
   * @see http://www.dajobe.org/2004/01/turtle/
   * @param string turtle the Turtle to parse
   * @param string base the base URI against which relative URIs in the Turtle document will be resolved
   */
  function add_turtle($turtle, $base='') {
    if ($turtle) {
      $parser = ARC2::getTurtleParser();
      $parser->parse($base, $turtle );
      $this->_add_arc2_triple_list($parser->getTriples());
      unset($parser);
    }
  }


  /**
   * Add the triples in the supplied graph to the current graph
   * @param SimpleGraph g the graph to read
   */
  function add_graph($g) {
    $triples_were_added = false;
    $index = $g->get_index();
    foreach ($index as $s => $p_list) {
      foreach ($p_list as $p => $o_list) {
        foreach ($o_list as $o_info) {
          if ($this->_add_triple($s, $p, $o_info) ) {
            $triples_were_added = true;
          }
        } 
      } 
    } 
    return $triples_were_added;
  }

  private function _add_arc2_triple_list(&$triples) {
    foreach ($triples as $t) {
      $obj = array();
      $obj['value'] = $t['o'];
      if ($t['o_type'] === 'iri' ) {
        $obj['type'] = 'uri';
      }
      elseif ($t['o_type'] === 'literal1' ||  
              $t['o_type'] === 'literal2' || 
              $t['o_type'] === 'long_literal1' || 
              $t['o_type'] === 'long_literal2' 
      ) {
        $obj['type'] = 'literal';
      }
      else {
        $obj['type'] = $t['o_type'];
      }
      
      if ($obj['type'] == 'literal') {
        if ( isset( $t['o_dt'] ) && $t['o_dt'] ) {
          $obj['datatype'] = $t['o_dt'];
        }
        else if ( isset( $t['o_datatype'] ) && $t['o_datatype'] ) {
          $obj['datatype'] = $t['o_datatype'];
        }
        if ( isset( $t['o_lang']) && $t['o_lang'])  {
          $obj['lang'] = $t['o_lang'];
        }
      }         

      if (!isset($this->_index[$t['s']])) { 
        $this->_index[$t['s']] = array();
        $this->_index[$t['s']][$t['p']] = array($obj);
      }
      elseif (!isset($this->_index[$t['s']][$t['p']])) {
        $this->_index[$t['s']][$t['p']] = array($obj);
      }
      else {          
        if ( ! in_array( $obj, $this->_index[$t['s']][$t['p']] ) ) {
          $this->_index[$t['s']][$t['p']][] = $obj;       
        }
      }
    }
  }


  // until ARC2 upgrades to support RDF/PHP we need to rename all types of "uri" to "iri"
  private function _to_arc_index(&$index) {
    $ret = array();

    foreach ($index as $s => $s_info) {
      $ret[$s] = array();
      foreach ($s_info as $p => $p_info) {
        $ret[$s][$p] = array();
        foreach ($p_info as $o) {
          $o_new = array();
          foreach ($o as $key => $value) {
            if ( $key == 'type' && $value == 'uri' ) {
              $o_new['type'] = 'iri';
            }
            else {
              $o_new[$key] = $value;
            }
          }
          $ret[$s][$p][] = $o_new;
        }
      }
    }
    return $ret;
  }

  /**
   * Tests whether the graph contains the given triple
   * @param string s the subject of the triple, either a URI or a blank node in the format _:name
   * @param string p the predicate URI of the triple
   * @param string o the object of the triple, either a URI or a blank node in the format _:name
   * @return boolean true if the triple exists in the graph, false otherwise
   */
  function has_resource_triple($s, $p, $o) {
    if (array_key_exists($s, $this->_index) ) {
      if (array_key_exists($p, $this->_index[$s]) ) {
        foreach ($this->_index[$s][$p] as $value) {
          if ( ( $value['type'] == 'uri' || $value['type'] == 'bnode') && $value['value'] == $o) {
            return true;
          }
        }
      }
    }

    return false;
  }

  /**
   * Tests whether the graph contains the given triple
   * @param string s the subject of the triple, either a URI or a blank node in the format _:name
   * @param string p the predicate URI of the triple
   * @param string o the object of the triple as a literal value
   * @return boolean true if the triple exists in the graph, false otherwise
   */
  function has_literal_triple($s, $p, $o) {
    if (array_key_exists($s, $this->_index) ) {
      if (array_key_exists($p, $this->_index[$s]) ) {
        foreach ($this->_index[$s][$p] as $value) {
          if ( ( $value['type'] == 'literal') && $value['value'] == $o) {
            return true;
          }
        }
      }
    }

    return false;
  }

  /**
   * Fetch the resource values for a given subject and predicate. 
   * @param string s the subject to search for
   * @param string p the predicate to search for
   * @return array list of URIs and blank nodes that are the objects of triples with the supplied subject and predicate
   */
  function get_resource_triple_values($s, $p) {
    $values = array();
    if (array_key_exists($s, $this->_index) ) {
      if (array_key_exists($p, $this->_index[$s]) ) {
        foreach ($this->_index[$s][$p] as $value) {
          if ( ( $value['type'] == 'uri' || $value['type'] == 'bnode')) {
            $values[] = $value['value'];
          }
        }
      }
    }
    return $values;
  }
  
  /**
   * Fetch the literal values for a given subject and predicate. 
   * @param string s the subject to search for
   * @param string p the predicate to search for
   * @return array list of literals that are the objects of triples with the supplied subject and predicate
   */
  function get_literal_triple_values($s, $p) {
    $values = array();
    if ( array_key_exists($s, $this->_index)) {
      if (is_array($p)) {
        foreach($p as $p_uri) {
          if(array_key_exists($p_uri, $this->_index[$s]) ) {
            foreach ($this->_index[$s][$p_uri] as $value) {
              if ($value['type'] == 'literal') {
                $values[] = $value['value'];
              }
            }
          }         
        }       
      }
      else if(array_key_exists($p, $this->_index[$s]) ) {
        foreach ($this->_index[$s][$p] as $value) {
          if ($value['type'] == 'literal') {
            $values[] = $value['value'];
          }
        }
      }
    }

    return $values;
  }

  
  /**
   * Fetch the values for a given subject and predicate. 
   * @param string s the subject to search for
   * @param string p the predicate to search for
   * @return array list of values of triples with the supplied subject and predicate
   */
  function get_subject_property_values($s, $p) {
    $values = array();
    if (array_key_exists($s, $this->_index) ) {
      if (array_key_exists($p, $this->_index[$s]) ) {
        foreach ($this->_index[$s][$p] as $value) {
          $values[] = $value;
        }
      }
    }
    return $values;
  }      
  
  /**
   * Tests whether the graph contains a triple with the given subject and predicate
   * @param string s the subject of the triple, either a URI or a blank node in the format _:name
   * @param string p the predicate URI of the triple
   * @return boolean true if a matching triple exists in the graph, false otherwise
   */
  function subject_has_property($s, $p) {
    if (array_key_exists($s, $this->_index) ) {
      return (array_key_exists($p, $this->_index[$s]) );
    }
    return false;
  }  
  
  /**
   * Removes all triples with the given subject and predicate
   * @param string s the subject of the triple, either a URI or a blank node in the format _:name
   * @param string p the predicate URI of the triple
   */ 
  function remove_property_values($s, $p) {
    unset($this->_index[$s][$p]);
  }

  /**
   * Clears all triples out of the graph
   */
  function remove_all_triples() {
    $this->_index = array();
  }

  /**
   * Tests whether the graph contains any triples
   * @return boolean true if the graph contains no triples, false otherwise
   */
  function is_empty() {
    return ( count($this->_index) == 0);
  }

}

