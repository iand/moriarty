<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'labeller.class.php';

class LabellerTest extends PHPUnit_Framework_TestCase {

  function test_get_label_splits_camelcase(){

    $lab = new Labeller();
    $this->assertEquals('school:RELIGIOUSCHARACTER', $lab->get_label('http://education.data.gov.uk/ontology/school#RELIGIOUSCHARACTER'));
    $this->assertEquals('religious character', $lab->get_label('http://education.data.gov.uk/ontology/school#religiousCharacter'));
  }
  function test_get_label_splits_camelcase_and_capitalizes(){

    $lab = new Labeller();
    $this->assertEquals('Religious character', $lab->get_label('http://education.data.gov.uk/ontology/school#religiousCharacter', null, TRUE));
  }

}
?>
