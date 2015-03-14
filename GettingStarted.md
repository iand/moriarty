# Introduction #

Moriarty is a simple PHP library for accessing the [Talis Platform](http://www.talis.com/platform). It follows the Platform API very closely and wraps up many common tasks into convenient classes while remaining very lightweight. It also provides some simple classes for working with RDF in general. Moriarty is being developed by small community of developers and is in continual beta, subject to a slow stream of updates.

# Dependencies #

Moriarty requires Benjamin Novack's excellent [ARC libary for RDF processing](http://arc.semsol.org/) (later than version 2008-08-04). If you want to run the unit tests (you probably do) then you will also need [PHPUnit](http://www.phpunit.de/) version 3.3.0 or later.

# Environment #

Rather than mess around with system paths, Moriarty uses a couple of constants to locate itself and its dependencies such as ARC. You should set these to the appropriate paths before you use Moriarty like this:

```php

define('MORIARTY_DIR', '/var/www/lib/moriarty/');
define('MORIARTY_ARC_DIR', '/var/www/lib/arc_2008_11_18/');
```

**Note:** these paths should include a trailing slash

The moriarty uses a simple naming convention for files. Files containing class definitions are named as {name}.class.php and the corresponding tests for the class are in a file called {name}.test.php in the tests subdirectory. Files named like {name}.inc.php are general purpose include files containing useful functions and constants.

To include a set of useful constants for popular property and class URIs add the following to your code:

```php

require_once MORIARTY_DIR . 'moriarty.inc.php';
```

# Stores #

The main class for working with Talis Platform stores is, unimaginatively, called [Store](Store.md). It contains methods for accessing different parts of the [Platform API](http://n2.talis.com/wiki/API_Site_Map)

To create a instance of the Store class bound to your store you pass the store's URI to the constructor (without a trailing slash):

```php

require_once MORIARTY_DIR . 'store.class.php';
$store = new Store('http://api.talis.com/stores/mystore');
```

Typically to perform an action on the store, you get a reference to one of the store's services and work with that. For example to perform a describe using the store's metabox you would use the get\_metabox method like this:

```php

$store = new Store('http://api.talis.com/stores/mystore');
$mb = $store->get_metabox();
$response = $mb->describe('http://example.com/foo');
```

The describe method returns an [HTTP response](HttpResponse.md) which you can check to get information about the result of the request. Moriarty doesn't try and pretend that the network doesn't exist so most calls to store services return the raw HTTP response. This might contain valuable information about the success or failure of your request. The response contains a useful method called is\_success which returns true if the HTTP response code was in the range 200 to 299. This leads to the following common pattern:

```php

$store = new Store('http://api.talis.com/stores/mystore');
$mb = $store->get_metabox();
$response = $mb->describe('http://example.com/foo');
if ( $response->is_success() ) {
$body = $response->bodyy;
// do something...
}
else {
// try something else
}
```

Not getting direct results from method calls might take some getting used to, but we think it's really important not to hide the characteristics of the network that will always sit between your application and the Talis Platform. This kind of programming is not the same as having a single database on the same server as your application: networked applications need to be written differently to those operating on a single machine. Networks have latency, so it’s not wise to be calling these methods a thousand times a second and they are unreliable so you need to be able to handle failure gracefully and be prepared to retry (these are a couple of the [8 Fallacies of Distributed Computing](http://www.rgoarchitects.com/Files/fallacies.pdf)). Moriarty doesn’t try to hide these issues from the developer.

# SPARQL #

Every store has an associated [SPARQL service](http://n2.talis.com/wiki/Store_Sparql_Service) which can be obtained like this:

```php

$store = new Store('http://api.talis.com/stores/mystore');
$ss = $store->get_sparql_service();
```

The describe method allows you to get RDF descriptions of resources in a store.

```php

$store = new Store('http://api.talis.com/stores/mystore');
$ss = $store->get_sparql_service();
$response = $ss->describe('http://example.com/foo');
if ( $response->is_success() ) {
$graph = new SimpleGraph();
$graph->from_rdf_xml($response->body);
// do something with graph...
}
```

Whereas the metabox describe method only allows a single URI as a parameter, the sparql class allows you to pass in an array.

```php

$response = $ss->describe( array('http://example.com/foo',
'http://example.com/bar',
'http://example.com/baz' )
);
```

It also allows you to specify the type of description you want (but not with arrays of URIs):

```php

$response = $ss->describe( 'http://example.com/foo', 'scbd' );
```

The following description types are supported

  * cbd - [concise bounded description](http://n2.talis.com/wiki/Bounded_Descriptions_in_RDF#Concise_Bounded_Description)
  * scbd - [symmetric bounded description](http://n2.talis.com/wiki/Bounded_Descriptions_in_RDF#Symmetric_Concise_Bounded_Description)
  * lcbd - [labelled bounded description](http://n2.talis.com/wiki/Bounded_Descriptions_in_RDF#Labelled_Concise_Bounded_Description)
  * slcbd - symmetric labelled bounded description

You can also specify the format of the response:

```php

$response = $ss->describe( 'http://example.com/foo', 'scbd', 'json' );
```

The output format can be one of [rdf, turtle, ntriples or json](http://n2.talis.com/wiki/Store_Sparql_Service#Output_Formats). The default is rdf, i.e. RDF/XML

If you have a more general SPARQL query then you can use the [query](SparqlServiceBase#query.md) method:

```php

$query = 'select ?person where { ?person a <http://xmlns.com/foaf/0.1/Person> .} limit 5';
$store = new Store('http://api.talis.com/stores/mystore');
$ss = $store->get_sparql_service();
$response = $ss->query( $query, 'json');
if ( $response->is_success() ) {
$results = json_decode($response->body, true);
// do something with results array...
}
```



# Authentication #

Most stores require authentication before you can submit content or rdf to them. The Platform uses a capability model (see [Capabilities](http://n2.talis.com/wiki/Capabilities)) to determine what each user can do. Each service operation requires a specific capability. Moriarty handles all this with a Credentials class. Just pass a Credentials object to the Store's constructor and Moriarty will use it for subsequent interactions with that store.

```php

require_once MORIARTY_DIR . 'store.class.php';
require_once MORIARTY_DIR . 'credentials.class.php';
$credentials = new Credentials('user', 'mypassword');
$store = new Store('http://api.talis.com/stores/mystore', $credentials );
```

**Note:** there is a known problem with some versions of the cURL libraries used by PHP where the authentication credentials are used even when the Platform doesn't issue a challenge (e.g. when you are simply searching a public store). You can work around this by using one instance of the Store class without credentials for unauthenticated requests and another instance with credentials for authenticated requests such as submitting RDF. Moriarty also supports using Manuel Lemos' pure-PHP httpclient as an alternative to cURL. You can find more information in [this blog post](http://blogs.talis.com/n2/archives/89).

# Submitting RDF #

Submitting RDF to a store is done by calling one of the metabox's submit methods. You can currently use submit\_rdfxml or submit\_turtle. Other submit methods will be added over time:

```php

$store = new Store('http://api.talis.com/stores/mystore', $credentials);
$mb = $store->get_metabox();
$response = $mb->submit_rdfxml( $my_rdf );
if ( $response->is_success() ) {
// do something...
}
```

# Clearing a Store #

Now you've put some random RDF into the store, you probably want to clear it out. This is pretty simple with Moriarty, just get the store's Scheduled Job Collection and schedule a Reset Data Job:

```php

require_once MORIARTY_DIR . 'store.class.php';
require_once MORIARTY_DIR . 'credentials.class.php';

$store = new Store('http://api.talis.com/stores/mystore', new Credentials('user', 'mypassword') );
$queue = $store->get_job_queue();
$queue->schedule_reset_data();
```

If you want it to occur at a specific time then just pass in the time to the [schedule\_reset\_data](JobQueue#schedule_reset_data.md) method:

```php

$queue->schedule_reset_data( gmmktime(10, 11, 0, 12, 6, 2007) );
```

The [JobQueue](JobQueue.md) class also contains methods for scheduling [reindexes](JobQueue#schedule_reindex.md), [snapshots](JobQueue#schedule_snapshot.md) and [restores](JobQueue#schedule_restore.md)