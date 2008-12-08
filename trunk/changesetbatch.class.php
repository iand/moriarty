<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_ARC_DIR . "ARC2.php";
require_once MORIARTY_DIR . "changeset.class.php";

/**
 * Represents a batch of changesets. Can be used to create changesets based on the difference between two graphs.
 * @see http://n2.talis.com/wiki/Metabox#Batch_Updating
 */
class ChangeSetBatch {
  protected $changesets;
  protected $after;
  protected $after_rdfxml;
  protected $before;
  protected $before_rdfxml;
  protected $createdDate = null;
  protected $creatorName = null;
  protected $changeReason = null;
  protected $sparqlService;

  /**
   * Create a new changeset batch. This will calculate the required additions and removals based on before and after versions of a graph. The args parameter is an associative array that may have the following fields:
   * <ul>
   *   <li><em>subjectOfChange</em> => a string representing the URI of the changeset's subject of change</li>
   *   <li><em>createdDate</em> => a string representing the date of the changeset</li>
   *   <li><em>creatorName</em> => a string representing the creator of the changeset</li>
   *   <li><em>changeReason</em> => a string representing the reason for the changeset</li>
   *   <li><em>after</em> => an array of triples representing the required state of the graph after the changeset would be applied</li>
   *   <li><em>before</em> => an array of triples representing the state of the graph before the changeset is applied</li>
   *   <li><em>after_rdfxml</em> => a string of RDF/XML representing the required state of the graph after the changeset would be applied. This is parsed and used to overwrite the 'after' parameter, if any</li>
   *   <li><em>before_rdfxml</em> => a string of RDF/XML representing the state of the graph before the changeset is applied. This is parsed and used to overwrite the 'begin' parameter, if any.</li>
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
  function ChangeSetBatch($args='') {
    if(is_array($args)){foreach($args as $k=>$v){$this->$k=$v;}}
    $this->init();
  }

  private function init( ) {

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


    $this->changesets = array();

    $subjectIndex = array();

    for($i=0,$i_max=count($this->after);$i<$i_max;$i++) {
      if ( ! array_key_exists( $this->after[$i]['s'], $subjectIndex ) ) {
        $subjectIndex[ $this->after[$i]['s'] ] = $this->after[$i]['s'];
      }
    }
    
    if ( isset ($this->before) ) {
      $beforeSubjects = array();
      for($i=0,$i_max=count($this->before);$i<$i_max;$i++) {
     if ( ! array_key_exists( $this->before[$i]['s'], $subjectIndex ) ) {
          $subjectIndex[ $this->before[$i]['s'] ] = $this->before[$i]['s'];
        }
        if ( ! array_key_exists( $this->before[$i]['s'], $beforeSubjects ) ) {
          $beforeSubjects[ $this->before[$i]['s'] ] = array();
        }
        array_push( $beforeSubjects[ $this->before[$i]['s'] ], $this->before[$i]);
      }
    }

    $subjects = array_keys( $subjectIndex );
//    var_dump('subjects',  $subjects, 'subjectindex', $subjectIndex,  'bs', $beforeSubjects);

    for($i=0,$i_max=count($subjects);$i<$i_max;$i++) {
      if ( isset ($this->before) && array_key_exists( $subjects[$i], $beforeSubjects ) ) {
          $this->changesets[] = new ChangeSet( array( 'subjectOfChange'=>$subjects[$i], 'after'=>$this->after
                                                                            , 'before'=>$beforeSubjects[ $subjects[$i] ]
                                                                            , 'createdDate'=>$this->createdDate
                                                                            , 'creatorName'=>$this->creatorName
                                                                            , 'changeReason'=>$this->changeReason
                                                                             ) );

      }
      else {
        if ( substr($subjects[$i], 0, 2) != '_:' && isset( $this->sparqlService ) ) {
          $describe_response = $this->sparqlService->describe( $subjects[$i] );
          $before_rdfxml = $describe_response->body;
          // echo "Content-type: text/plain\r\n\r\n";
          // echo $before_rdfxml;
  
          $this->changesets[] = new ChangeSet( array( 'subjectOfChange'=>$subjects[$i], 'after'=>$this->after
                                                                            , 'before_rdfxml'=>$before_rdfxml
                                                                            , 'createdDate'=>$this->createdDate
                                                                            , 'creatorName'=>$this->creatorName
                                                                            , 'changeReason'=>$this->changeReason
                                                                             ) );
        }
        else {
          $this->changesets[] = new ChangeSet( array( 'subjectOfChange'=>$subjects[$i], 'after'=>$this->after
                                                                            , 'createdDate'=>$this->createdDate
                                                                            , 'creatorName'=>$this->creatorName
                                                                            , 'changeReason'=>$this->changeReason
                                                                             ) );
        }
      }
    }


  }

  /**
   * Returns the list of changesets constructed from the input graphs
   * @return array the list of changesets
   */
  function get_changesets() {
    return $this->changesets;
  }




}
?>
