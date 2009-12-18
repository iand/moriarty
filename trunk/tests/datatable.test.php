<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'constants.inc.php';
require_once MORIARTY_DIR . 'datatable.class.php';
require_once MORIARTY_TEST_DIR . 'fakecredentials.class.php';


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

  function test_insert_posts_to_metabox_uri() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/meta", $fake_request );

    $dt = new DataTable("http://example.org/store", new FakeCredentials(), $fake_request_factory);
    $dt->map('http://example.org/name', 'name');
    $dt->set('name', 'scooby');
    $dt->insert();

    $this->assertTrue( $fake_request->was_executed() );
  }

  function test_insert_posts_generated_turtle() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/meta", $fake_request );

    $dt = new DataTable("http://example.org/store", new FakeCredentials(), $fake_request_factory);
    $dt->map('http://example.org/name', 'name');
    $dt->set('name', 'scooby');
    $dt->insert();

    $g = $dt->get_insert_graph();
    $this->assertEquals( $g->to_turtle() , $fake_request->get_body() );
  }

  function test_insert_sets_content_type() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/meta", $fake_request );

    $dt = new DataTable("http://example.org/store", new FakeCredentials(), $fake_request_factory);
    $dt->map('http://example.org/name', 'name');
    $dt->set('name', 'scooby');
    $dt->insert();

    $this->assertTrue( in_array('Content-Type: application/x-turtle', $fake_request->get_headers() ) );
  }

  function test_insert_sets_accept() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/meta", $fake_request );

    $dt = new DataTable("http://example.org/store", new FakeCredentials(), $fake_request_factory);
    $dt->map('http://example.org/name', 'name');
    $dt->set('name', 'scooby');
    $dt->insert();

    $this->assertTrue( in_array('Accept: */*', $fake_request->get_headers() ) );
  }

  function test_insert_uses_credentials() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/meta", $fake_request );

    $dt = new DataTable("http://example.org/store", new FakeCredentials(), $fake_request_factory);
    $dt->map('http://example.org/name', 'name');
    $dt->set('name', 'scooby');
    $dt->insert();

    $this->assertEquals( "user:pwd" , $fake_request->get_auth() );
  }

  function test_set_plain_literal() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/person', 'person');
    $dt->set('name', 'scooby');
    
    $g = $dt->get_insert_graph();
    
    $this->assertTrue( $g->has_literal_triple('_:a1', 'http://example.org/name', 'scooby'));
  }

  function test_set_language_literal() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/person', 'person');
    $dt->set('name', 'scooby', 'literal', 'en');
    
    $g = $dt->get_insert_graph();
    
    $this->assertTrue( $g->has_literal_triple('_:a1', 'http://example.org/name', 'scooby', 'en'));
  }

  function test_set_literal_datatype() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/person', 'person');
    $dt->set('name', 'scooby', 'literal', null, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#XMLLiteral');
    
    $g = $dt->get_insert_graph();
    
    $this->assertTrue( $g->has_literal_triple('_:a1', 'http://example.org/name', 'scooby', null, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#XMLLiteral'));
  }
  
  function test_set_uri() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/person', 'person');
    $dt->set('name', 'http://example.org/thing', 'uri');
    
    $g = $dt->get_insert_graph();
    
    $this->assertTrue( $g->has_resource_triple('_:a1', 'http://example.org/name', 'http://example.org/thing'));
  }
  
  function test_set_bnode() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/person', 'person');
    $dt->set('name', '_:b', 'bnode');
    
    $g = $dt->get_insert_graph();
    
    $this->assertTrue( $g->has_resource_triple('_:a1', 'http://example.org/name', '_:b'));
  }
  

  function test_set_detects_boolean_type() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/person', 'person');
    $dt->set('name', TRUE);
    
    $g = $dt->get_insert_graph();
    $this->assertTrue( $g->has_literal_triple('_:a1', 'http://example.org/name', 'true', null, 'http://www.w3.org/2001/XMLSchema#boolean'));
  }

  function test_set_detects_boolean_type_false() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/person', 'person');
    $dt->set('name', FALSE);
    
    $g = $dt->get_insert_graph();
    $this->assertTrue( $g->has_literal_triple('_:a1', 'http://example.org/name', 'false', null, 'http://www.w3.org/2001/XMLSchema#boolean'));
  }

  function test_set_multiple() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/surname', 'surname');
    $dt->set('name', 'scooby')->set('surname', 'doo');
    $g = $dt->get_insert_graph();
    $this->assertTrue( $g->has_literal_triple('_:a1', 'http://example.org/name', 'scooby'));
    $this->assertTrue( $g->has_literal_triple('_:a1', 'http://example.org/surname', 'doo'));
    
  }
  function test_set_subject_uri() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->set('_uri', 'http://example.org/s')->set('name', 'scooby');
    $g = $dt->get_insert_graph();
    $this->assertTrue( $g->has_literal_triple('http://example.org/s', 'http://example.org/name', 'scooby'));
  }

  function test_get_insert_graph_specifies_type() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/person', 'person');
    $dt->set('_uri', 'http://example.org/s');
    $dt->set('name', 'scooby');
    $g = $dt->get_insert_graph('person');
    $this->assertTrue( $g->has_literal_triple('http://example.org/s', 'http://example.org/name', 'scooby'));
    $this->assertTrue( $g->has_resource_triple('http://example.org/s', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://example.org/person'));
  }


  function test_get_insert_graph_specifies_multiple_types() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/person', 'person');
    $dt->map('http://example.org/employee', 'employee');
    $dt->set('_uri', 'http://example.org/s');
    $dt->set('name', 'scooby');
    $g = $dt->get_insert_graph('person,employee');
    $this->assertTrue( $g->has_resource_triple('http://example.org/s', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://example.org/person'));
    $this->assertTrue( $g->has_resource_triple('http://example.org/s', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://example.org/employee'));
  }

  function test_insert_posts_generated_turtle_with_types() {
    $fake_request_factory = new FakeRequestFactory();
    $fake_request = new FakeHttpRequest( new HttpResponse() );
    $fake_request_factory->register('POST', "http://example.org/store/meta", $fake_request );

    $dt = new DataTable("http://example.org/store", new FakeCredentials(), $fake_request_factory);
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/person', 'person');
    $dt->set('name', 'scooby');
    $dt->insert('person');

    $g = $dt->get_insert_graph('person');
    $this->assertEquals( $g->to_turtle() , $fake_request->get_body() );
  }
  
  function test_set_field_defaults_sets_datatype() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->map('http://example.org/person', 'person');
    $dt->set('name', '5');
    $dt->set_field_defaults('name', 'literal', 'http://www.w3.org/2001/XMLSchema#integer');
    
    $g = $dt->get_insert_graph();
    
    $this->assertTrue( $g->has_literal_triple('_:a1', 'http://example.org/name', '5', null,'http://www.w3.org/2001/XMLSchema#integer' ));
    
  }

  function test_set_field_defaults_datatype_is_overridden_by_set() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->set('name', '5', 'literal', null, 'http://www.w3.org/2001/XMLSchema#double');
    $dt->set_field_defaults('name', 'literal', 'http://www.w3.org/2001/XMLSchema#integer');
    
    $g = $dt->get_insert_graph();
    $this->assertTrue( $g->has_literal_triple('_:a1', 'http://example.org/name', '5', null, 'http://www.w3.org/2001/XMLSchema#double' ));
  }
  
  function test_set_field_defaults_sets_type() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->set('name', 'http://example.com/foo');
    $dt->set_field_defaults('name', 'uri');
    
    $g = $dt->get_insert_graph();
    $this->assertTrue( $g->has_resource_triple('_:a1', 'http://example.org/name', 'http://example.com/foo' ));
  }

  function test_set_field_defaults_type_is_overridden_by_set() {
    $dt = new DataTable("http://example.org/store");
    $dt->map('http://example.org/name', 'name');
    $dt->set('name', 'http://example.com/foo', 'literal');
    $dt->set_field_defaults('name', 'uri');
    
    $g = $dt->get_insert_graph();
    $this->assertTrue( $g->has_literal_triple('_:a1', 'http://example.org/name', 'http://example.com/foo' ));
  }
  
}
?>
