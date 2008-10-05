<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'moriarty.inc.php';

class AugmentService {
  var $uri;
  var $request_factory;
  var $credentials;

  function __construct($uri, $credentials = null) {
    $this->uri = $uri;
    $this->credentials = $credentials;
  }

  function augment($uri) {
    if (! isset( $this->request_factory) ) {
      $this->request_factory = new HttpRequestFactory();
    }

    $uri = $this->uri . '?data-uri=' . urlencode($uri);
    $request = $this->request_factory->make( 'GET', $uri , $this->credentials );
    $request->set_accept(MIME_RSS);
    return $request->execute();
  }

  function augment_graph($graph) {
    if (! isset( $this->request_factory) ) {
      $this->request_factory = new HttpRequestFactory();
    }

    $request = $this->request_factory->make( 'POST', $this->uri , $this->credentials );
    $request->set_accept(MIME_RSS);
    $request->set_content_type(MIME_RSS);
    
    $data = new SimpleGraph();
    $data->add_turtle( $graph->to_turtle());
    
    $data->add_resource_triple( 'tag:talis.com,2008:moriarty-tmp-augment-channel', RDF_TYPE, 'http://purl.org/rss/1.0/channel');
    $data->add_resource_triple( 'tag:talis.com,2008:moriarty-tmp-augment-channel', RSS_ITEMS, 'tag:talis.com,2008:moriarty-tmp-augment-channel-items');
    $data->add_resource_triple( 'tag:talis.com,2008:moriarty-tmp-augment-channel-items', RDF_TYPE, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Seq');
    $request->set_body( $data->to_rdfxml() );
    
    return $request->execute();
  }


}
?>
