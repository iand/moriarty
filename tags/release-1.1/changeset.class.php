<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'moriarty.inc.php';
require_once MORIARTY_ARC_DIR . DIRECTORY_SEPARATOR . "ARC2.php";
require_once MORIARTY_DIR. 'simplegraph.class.php';

/**
 * Represents a changeset. Can be used to create a changeset based on the difference between two bounded descriptions. The descriptions must share the same subject URI.
 * @see http://n2.talis.com/wiki/Changesets
 */
class ChangeSet extends SimpleGraph {

  protected $subjectOfChange;
  protected $before;
  protected $before_rdfxml;
  protected $after;
  protected $after_rdfxml;
  protected $createdDate = null;
  protected $creatorName = null;
  protected $changeReason = null;
  protected $has_changes = false;
  protected $cs_resource = null;
  protected $include_count = 0;

  /**
   * Create a new changeset. This will calculate the required additions and removals based on before and after versions of a bounded description. The args parameter is an associative array that may have the following fields:
   * <ul>
   *   <li><em>subjectOfChange</em> => a string representing the URI of the changeset's subject of change</li>
   *   <li><em>createdDate</em> => a string representing the date of the changeset</li>
   *   <li><em>creatorName</em> => a string representing the creator of the changeset</li>
   *   <li><em>changeReason</em> => a string representing the reason for the changeset</li>
   *   <li><em>after</em> => an array of triples representing the required state of the resource description after the changeset would be applied. All subjects must be the same.</li>
   *   <li><em>before</em> => an array of triples representing the state of the resource description before the changeset is applied. All subjects must be the same.</li>
   *   <li><em>after_rdfxml</em> => a string of RDF/XML representing the required state of the resource description after the changeset would be applied. This is parsed and used to overwrite the 'after' parameter, if any. All subjects must be the same.</li>
   *   <li><em>before_rdfxml</em> => a string of RDF/XML representing the state of the resource description before the changeset is applied. This is parsed and used to overwrite the 'begin' parameter, if any. All subjects must be the same.</li>
   * </ul>
   * If none of 'after', 'before', 'after_rdfxml' or 'before_rdfxml' is supplied then an empty changeset is constructed. <br />
   * The 'after' and 'before' arrays are simple arrays where each element is a triple array with the following structure:
   * <ul>
   *   <li><em>s</em> => the subject URI</li>
   *   <li><em>p</em> => the predicate URI</li>
   *   <li><em>o</em> => the value of the object</li>
   *   <li><em>o_type</em> => one of 'uri', 'bnode' or 'literal'</li>
   *   <li><em>o_lang</em> => the language of the literal if any</li>
   *   <li><em>o_datatype</em> => the data type URI of the literal if any</li>
   * </ul>
   * @param array args an associative array of parameters to use when constructing the changeset
   */
  function __construct($args='') {
    if(is_array($args)){foreach($args as $k=>$v){$this->$k=$v;}}/* subjectOfChange, after, createdDate, creatorName, changeReason */
    $this->init();
  }

  private function init( ) {
    if ( isset( $this->after_rdfxml) || isset( $this->before_rdfxml) ) {
      $parser_args=array(
        "bnode_prefix"=>"genid",
        "base"=>""
      );

      if ( isset( $this->after_rdfxml) ) {
        $parser = ARC2::getRDFXMLParser($parser_args);
        $parser->parse("", $this->after_rdfxml );
        $this->after = $parser->getTriples();
      }
      if ( isset( $this->before_rdfxml) ) {
        $parser = ARC2::getRDFXMLParser($parser_args);
        $parser->parse("", $this->before_rdfxml );
        $this->before = $parser->getTriples();
      }
    }

    $this->_triples = array();

    $this->cs_resource = "_:cs";

    $this->add_resource_triple($this->cs_resource, RDF_TYPE, CS_CHANGESET );

    $this->add_resource_triple($this->cs_resource, CS_SUBJECTOFCHANGE, $this->subjectOfChange );

    if ($this->createdDate != null) {
      $this->add_literal_triple($this->cs_resource, CS_CREATEDDATE, $this->createdDate );
    }
    if ($this->creatorName != null) {
      $this->add_literal_triple($this->cs_resource, CS_CREATORNAME, $this->creatorName );
    }
    if ($this->changeReason != null) {
      $this->add_literal_triple($this->cs_resource, CS_CHANGEREASON, $this->changeReason );
    }

    if ( ! isset( $this->before ) ) {
      for($i=0,$i_max=count($this->after);$i<$i_max;$i++) {
        if ( $this->after[$i]['s'] == $this->subjectOfChange ) {

          $after_triple = $this->after[$i];
          $this->include_addition( $after_triple );
        }
      }
    }
    else {
      for($i=0,$i_max=count($this->after);$i<$i_max;$i++) {
        if ( $this->after[$i]['s'] == $this->subjectOfChange) {

          $after_triple = $this->after[$i];
          if ( ! $this->triple_in_list( $after_triple, $this->before)) {
            $this->include_addition( $after_triple );
          }
        }
      }
    }

    if ( ! isset( $this->after ) ) {
      for($i=0,$i_max=count($this->before);$i<$i_max;$i++) {
        if ( $this->before[$i]['s'] == $this->subjectOfChange ) {

          $before_triple = $this->before[$i];
          $this->include_removal( $before_triple );
        }
      }
    }
    else {
      for($i=0,$i_max=count($this->before);$i<$i_max;$i++) {
        if ( $this->before[$i]['s'] == $this->subjectOfChange ) {

          $before_triple = $this->before[$i];

          if ( ! $this->triple_in_list( $before_triple, $this->after)) {
            $this->include_removal($before_triple);

          }
        }
      }
    }
  }

  /**
   * Include the supplied triple as a removal in the changeset.
   * @param array triple an array representing the triple to be removed. The structure is as documented above.
   */
  function include_removal( $triple) {
    if ( $triple['p'] == 'http://schemas.talis.com/2005/dir/schema#etag' ) return; // Platform always overrides this
    $this->has_changes = true;
    $this->include_count++;
    $removal = "_:r" . $this->include_count;

    $this->add_resource_triple($this->cs_resource, CS_REMOVAL, $removal );
    $this->add_resource_triple($removal, RDF_TYPE, RDF_STATEMENT);
    $this->add_resource_triple($removal, RDF_SUBJECT, $triple['s'] );
    $this->add_resource_triple($removal, RDF_PREDICATE, $triple['p'] );
    if ( $triple['o_type'] == 'uri' || $triple['o_type'] == 'bnode') {
      $this->add_resource_triple($removal, RDF_OBJECT, $triple['o'] );
    }
    else {
      $this->add_literal_triple($removal, RDF_OBJECT, $triple['o'], $triple['o_lang'], $triple['o_datatype'] );
    }

  }

  /**
   * Include the supplied triple as an addition in the changeset.
   * @param array triple an array representing the triple to be added. The structure is as documented above.
   */
  function include_addition( $triple) {
    if ( $triple['p'] == 'http://schemas.talis.com/2005/dir/schema#etag' ) return; // Platform always overrides this
    $this->has_changes = true;
    $this->include_count++;
    $addition = "_:a" . $this->include_count;

    $this->add_resource_triple($this->cs_resource, CS_ADDITION, $addition );
    $this->add_resource_triple($addition, RDF_TYPE, RDF_STATEMENT );
    $this->add_resource_triple($addition, RDF_SUBJECT, $triple['s'] );
    $this->add_resource_triple($addition, RDF_PREDICATE, $triple['p'] );
    if ( $triple['o_type'] == 'uri' || $triple['o_type'] == 'bnode') {
      $this->add_resource_triple($addition, RDF_OBJECT, $triple['o'] );
    }
    else {
      $this->add_literal_triple($addition, RDF_OBJECT, $triple['o'], $triple['o_lang'], $triple['o_datatype'] );
    }

  }

  /**
   * Checks whether the changeset contains any additions or removals. Can be useful when deciding whether to submit the changeset or not.
   * @return boolean true if the changeset has changes to apply
   */
  function has_changes() {
    return $this->has_changes;
  }



  protected function triple_in_list( $triple, &$list ) {
    foreach ($list as $candidate) {
      if ( $triple['s_type'] == $candidate['s_type'] && $triple['o_type'] == $candidate['o_type'] && $triple['p'] == $candidate['p'] ) {
        if ( $triple['s'] == $candidate['s'] ) {
          if ( $triple['o'] == $candidate['o']  )  {

            if ( $triple['o_type'] == 'literal' ) {
              if ( array_key_exists('o_lang', $triple) && array_key_exists('o_lang', $candidate) && $triple['o_lang'] == $candidate['o_lang']) {
                return true;
              }
              elseif (! array_key_exists('o_lang', $triple) && ! array_key_exists('o_lang', $candidate) ) {
                return true;
              }
              elseif ( array_key_exists('o_lang', $triple) && ! array_key_exists('o_lang', $candidate) && ! isset($triple['o_lang'] ) ) {
                return true;
              }
              elseif ( ! array_key_exists('o_lang', $triple) && array_key_exists('o_lang', $candidate) && ! isset($candidate['o_lang']) ) {
                return true;
              }
            }
            else {
              return true;
            }
          }
        }
      }
    }

    return false;
  }


}


?>
