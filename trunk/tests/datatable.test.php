<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'datatable.class.php';


class DataTableTest extends PHPUnit_Framework_TestCase {
  var $_select_result1 = '{
  "head": {
    "vars": [ "var" ]
  } ,
  "results": {
    "bindings": [
      {
        "var": { "type": "literal" , "value": "foo" } 
      }
    ]
  }
}';

  function test_select_uses_map() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');
    $this->assertEquals( "select ?_uri ?name where {?_uri <http://example.org/name> ?name. }", $dt->get_sparql() );
  }  
  function test_select_with_multiple_fields() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/age', 'age');
    $dt->select('name, age');
    $this->assertEquals( "select ?_uri ?name ?age where {?_uri <http://example.org/name> ?name; <http://example.org/age> ?age. }", $dt->get_sparql() );
  }  
  function test_select_with_multiple_fields_ignores_whitespace() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/age', 'age');
    $dt->select(" name  , \tage \r");
    $this->assertEquals( "select ?_uri ?name ?age where {?_uri <http://example.org/name> ?name; <http://example.org/age> ?age. }", $dt->get_sparql() );
  }  

  function test_select_uses_from() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/Person', 'person');
    $dt->select('name');
    $dt->from('person');
    $this->assertEquals( "select ?_uri ?name where {?_uri <http://example.org/name> ?name; a <http://example.org/Person>. }", $dt->get_sparql() );
  }  

  function test_distinct() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');
    $dt->distinct();
    $this->assertEquals( "select distinct ?_uri ?name where {?_uri <http://example.org/name> ?name. }", $dt->get_sparql() );
  }

  function test_map_chains() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name')->map('http://example.org/age', 'age')->select('name, age');
    $this->assertEquals( "select ?_uri ?name ?age where {?_uri <http://example.org/name> ?name; <http://example.org/age> ?age. }", $dt->get_sparql() );
  }  

  function test_distinct_chains() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name')->distinct()->select('name');
    $this->assertEquals( "select distinct ?_uri ?name where {?_uri <http://example.org/name> ?name. }", $dt->get_sparql() );
  }
  function test_limit() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');
    $dt->limit(13);
    $this->assertEquals( "select ?_uri ?name where {?_uri <http://example.org/name> ?name. } limit 13", $dt->get_sparql() );
  }
  function test_limit_ignored_if_not_integer() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');
    $dt->limit('99.3');
    $this->assertEquals( "select ?_uri ?name where {?_uri <http://example.org/name> ?name. }", $dt->get_sparql() );
  }
  function test_limit_ignored_if_not_positive() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');
    $dt->limit('0');
    $this->assertEquals( "select ?_uri ?name where {?_uri <http://example.org/name> ?name. }", $dt->get_sparql() );
    $dt->limit('-1');
    $this->assertEquals( "select ?_uri ?name where {?_uri <http://example.org/name> ?name. }", $dt->get_sparql() );

  }
  function test_limit_allows_offset() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');
    $dt->limit(13, 20);
    $this->assertEquals( "select ?_uri ?name where {?_uri <http://example.org/name> ?name. } limit 13 offset 20", $dt->get_sparql() );
  }
  function test_offset_ignored_if_not_integer() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');
    $dt->limit(5, 1.8);
    $this->assertEquals( "select ?_uri ?name where {?_uri <http://example.org/name> ?name. } limit 5", $dt->get_sparql() );
  }
  function test_offset_ignored_if_not_positive() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');
    $dt->limit(6, -1);
    $this->assertEquals( "select ?_uri ?name where {?_uri <http://example.org/name> ?name. } limit 6", $dt->get_sparql() );
  }

  function test_get_sends_sparql_query() {
    $fake_request_factory = new FakeRequestFactory();

    $dt = new DataTable("http://example.org/store", null, $fake_request_factory);
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');

    $query = $dt->get_sparql();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('GET', "http://example.org/store/services/sparql?query=" . urlencode($query) . '&output=json', $fake_request );

    $response = $dt->get();
    $this->assertTrue( $fake_request->was_executed() );
  }


  function test_get_returns_datatableresult() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_response = new HttpResponse();
    $fake_response->status_code = 200;
    $fake_response->body = $this->_select_result1;

    $dt = new DataTable("http://example.org/store", null, $fake_request_factory);
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');

    $query = $dt->get_sparql();
    $fake_request = new FakeHttpRequest( $fake_response );
    $fake_request_factory->register('GET', "http://example.org/store/services/sparql?query=" . urlencode($query) . '&output=json', $fake_request );

    $response = $dt->get();
    $this->assertTrue( is_a($response, 'DataTableResult'));
  }

  function test_get_returns_empty_datatableresult_on_http_error() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_response = new HttpResponse();
    $fake_response->status_code = 500;
    $fake_response->body = "error";

    $dt = new DataTable("http://example.org/store", null, $fake_request_factory);
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');

    $query = $dt->get_sparql();
    $fake_request = new FakeHttpRequest( $fake_response );
    $fake_request_factory->register('GET', "http://example.org/store/services/sparql?query=" . urlencode($query) . '&output=json', $fake_request );

    $response = $dt->get();
    $this->assertTrue( is_a($response, 'DataTableResult'));
  }


  function test_optional_uses_map() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/foo', 'foo');
    $dt->map('http://example.org/name', 'name');
    $dt->select('foo');
    $dt->optional('name');
    $this->assertEquals( "select ?_uri ?foo ?name where {?_uri <http://example.org/foo> ?foo. optional {?_uri <http://example.org/name> ?name. } }", $dt->get_sparql() );
  }  
  function test_optional_with_multiple_fields() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/foo', 'foo');
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/age', 'age');
    $dt->select('foo');
    $dt->optional('name, age');
    $this->assertEquals( "select ?_uri ?foo ?name ?age where {?_uri <http://example.org/foo> ?foo. optional {?_uri <http://example.org/name> ?name; <http://example.org/age> ?age. } }", $dt->get_sparql() );
  }  
  function test_optional_with_multiple_fields_ignores_whitespace() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/foo', 'foo');
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/age', 'age');
    $dt->select('foo');
    $dt->optional(" name  , \tage \r");
    $this->assertEquals( "select ?_uri ?foo ?name ?age where {?_uri <http://example.org/foo> ?foo. optional {?_uri <http://example.org/name> ?name; <http://example.org/age> ?age. } }", $dt->get_sparql() );
  }  

  function test_multiple_optionals() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/foo', 'foo');
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/age', 'age');
    $dt->select('foo');
    $dt->optional('name');
    $dt->optional('age');
    $this->assertEquals( "select ?_uri ?foo ?name ?age where {?_uri <http://example.org/foo> ?foo. optional {?_uri <http://example.org/name> ?name. } optional {?_uri <http://example.org/age> ?age. } }", $dt->get_sparql() );
  }  

  function test_map_takes_associative_array() {
    $mappings = array();
    $mappings['http://example.org/name'] = 'name';
    $mappings['http://example.org/age'] = 'age';

    $dt = new DataTable("http://example.org/store");
    $dt->map($mappings);
    $dt->select('name');
    $dt->select('age');
    $this->assertEquals( "select ?_uri ?name ?age where {?_uri <http://example.org/name> ?name; <http://example.org/age> ?age. }", $dt->get_sparql() );
  }  

  function test_order_by() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');
    $dt->order_by('name');
    $this->assertEquals( "select ?_uri ?name where {?_uri <http://example.org/name> ?name. } order by ?name", $dt->get_sparql() );
  }  

  function test_multiple_order_by() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/foo', 'foo');
    $dt->select('name, foo');
    $dt->order_by('name');
    $dt->order_by('foo');
    $this->assertEquals( "select ?_uri ?name ?foo where {?_uri <http://example.org/name> ?name; <http://example.org/foo> ?foo. } order by ?name ?foo", $dt->get_sparql() );
  }  

  function test_order_by_desc() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');
    $dt->order_by('name', 'desc');
    $this->assertEquals( "select ?_uri ?name where {?_uri <http://example.org/name> ?name. } order by DESC(?name)", $dt->get_sparql() );
  }  

  function test_order_by_unknown_field_is_ignored() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');
    $dt->order_by('foo');
    $this->assertEquals( "select ?_uri ?name where {?_uri <http://example.org/name> ?name. }", $dt->get_sparql() );
  }  

  function select_for_unknown_field_is_ignored() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->select('name, foo');
    $this->assertEquals( "select ?_uri ?name where {?_uri <http://example.org/name> ?name. }", $dt->get_sparql() );
  }  

  function test_where() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');
    $dt->where('name', 'scooby');
    $this->assertEquals( "select ?_uri ?name where {?_uri <http://example.org/name> ?name. filter(str(?name)='scooby'). }", $dt->get_sparql() );
  }  

  function test_multiple_where() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');
    $dt->where('name', 'scooby');
    $dt->where('name', 'doo');
    $this->assertEquals( "select ?_uri ?name where {?_uri <http://example.org/name> ?name. filter(str(?name)='scooby'). filter(str(?name)='doo'). }", $dt->get_sparql() );
  }  

  function test_where_adds_pattern_to_query() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/nick', 'nick');
    $dt->select('name');
    $dt->where('nick', 'scooby');
    $this->assertEquals( "select ?_uri ?name where {?_uri <http://example.org/name> ?name; <http://example.org/nick> ?nick. filter(str(?nick)='scooby'). }", $dt->get_sparql() );
  }  


  function test_where_with_not_equals_operator() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');
    $dt->where('name !=', 'scooby');
    $this->assertEquals( "select ?_uri ?name where {?_uri <http://example.org/name> ?name. filter(str(?name)!='scooby'). }", $dt->get_sparql() );
  }  

  function test_where_with_less_than_operator() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');
    $dt->where('name <', 'scooby');
    $this->assertEquals( "select ?_uri ?name where {?_uri <http://example.org/name> ?name. filter(str(?name)<'scooby'). }", $dt->get_sparql() );
  }  

  function test_where_with_greater_than_operator() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');
    $dt->where('name >', 'scooby');
    $this->assertEquals( "select ?_uri ?name where {?_uri <http://example.org/name> ?name. filter(str(?name)>'scooby'). }", $dt->get_sparql() );
  }  
  function test_where_with_greater_than_equal_operator() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');
    $dt->where('name >=', 'scooby');
    $this->assertEquals( "select ?_uri ?name where {?_uri <http://example.org/name> ?name. filter(str(?name)>='scooby'). }", $dt->get_sparql() );
  }  
  function test_where_with_less_than_equal_operator() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');
    $dt->where('name <=', 'scooby');
    $this->assertEquals( "select ?_uri ?name where {?_uri <http://example.org/name> ?name. filter(str(?name)<='scooby'). }", $dt->get_sparql() );
  }  

  function test_where_escapes_single_quotes() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');
    $dt->where('name', "s'cooby");
    $this->assertEquals( "select ?_uri ?name where {?_uri <http://example.org/name> ?name. filter(str(?name)='s\'cooby'). }", $dt->get_sparql() );
  }  

  function test_where_includes_cast_for_integers() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');
    $dt->where('name', 15);
    $this->assertEquals( "prefix xsd: <http://www.w3.org/2001/XMLSchema#> select ?_uri ?name where {?_uri <http://example.org/name> ?name. filter(xsd:integer(?name)=15). }", $dt->get_sparql() );
  }  

  function test_where_includes_cast_for_floats() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');
    $dt->where('name', 15.6);
    $this->assertEquals( "prefix xsd: <http://www.w3.org/2001/XMLSchema#> select ?_uri ?name where {?_uri <http://example.org/name> ?name. filter(xsd:double(?name)=15.6). }", $dt->get_sparql() );
  }  

  function test_where_includes_cast_for_boolean_false() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->select('name');
    $dt->where('name', false);
    $this->assertEquals( "prefix xsd: <http://www.w3.org/2001/XMLSchema#> select ?_uri ?name where {?_uri <http://example.org/name> ?name. filter(xsd:boolean(?name)=false). }", $dt->get_sparql() );
  }  

  function test_where_uri() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/age', 'age');
    $dt->select('age');
    $dt->where_uri('name', 'http://example.org/foo');
    $this->assertEquals( "select ?_uri ?age where {?_uri <http://example.org/name> <http://example.org/foo>; <http://example.org/age> ?age. }", $dt->get_sparql() );
  }  

  function test_multiple_where_uri() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/type', 'type');
    $dt->map('http://example.org/age', 'age');
    $dt->select('age');
    $dt->where_uri('name', 'http://example.org/foo');
    $dt->where_uri('type', 'http://example.org/blah');
    $this->assertEquals( "select ?_uri ?age where {?_uri <http://example.org/name> <http://example.org/foo>; <http://example.org/type> <http://example.org/blah>; <http://example.org/age> ?age. }", $dt->get_sparql() );
  }  

  function test_select_with_joins() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/friend', 'friend');
    $dt->select('friend.name');
    $this->assertEquals( "select ?_uri ?friend_name where {?_uri <http://example.org/friend> ?friend. ?friend <http://example.org/name> ?friend_name. }", $dt->get_sparql() );
  }  

  function test_select_with_multiple_joins_on_same_property() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/nick', 'nick');
    $dt->map('http://example.org/friend', 'friend');
    $dt->select('friend.name, friend.nick');
    $this->assertEquals( "select ?_uri ?friend_name ?friend_nick where {?_uri <http://example.org/friend> ?friend. ?friend <http://example.org/name> ?friend_name; <http://example.org/nick> ?friend_nick. }", $dt->get_sparql() );
  }  

  function test_select_with_multiple_joins_on_different_properties() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/friend', 'friend');
    $dt->map('http://example.org/husband', 'husband');
    $dt->select('friend.name, husband.name');
    $this->assertEquals( "select ?_uri ?friend_name ?husband_name where {?_uri <http://example.org/friend> ?friend; <http://example.org/husband> ?husband. ?friend <http://example.org/name> ?friend_name. ?husband <http://example.org/name> ?husband_name. }", $dt->get_sparql() );
  }  

}
?>
