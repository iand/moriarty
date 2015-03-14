**note:** this page is automatically generated from embedded documentation in the PHP source.

# Overview #

DataTable is Moriarty's implementation of the Active Record pattern for RDF data. It provides a very simple way to create and run SPARQL queries. See   DataTableExamples  for examples of how to use DataTable

See http://blogs.talis.com/n2/archives/965 for an introduction to DataTable.

DataTable is the class that constructs the queries. DataTableResult (in datatableresult.class.php) is a class that represents the results of a query. The interface to DataTable takes inspiration from CodeIgniter's Active Record class, adapted slightly for some RDF specifics.

DataTable uses method chaining to make the code more compact and readable. All of the following are equivalent:

```php

$dt->select('name')->from('person')->limit(5);

$dt->select('name');
$dt->from('person');
$dt->limit(5);

$dt->select('name')->limit(5);
$dt->from('person');
```

## Constructors ##
  * [DataTable](#DataTable.md) - The DataTable constructor requires the URI of the Talis Platform store as its first parameter, e.g.:   $dt = new DataTable('http://api.talis.com/stores/mystore');   Optionally a Credentials object can be supplied as the second parameter.
## Methods ##
  * [distinct](#distinct.md) - Specifies that the query results must be distinct (i.e.
  * [from](#from.md) - Specifies types of the resources you want to select in your query.
  * [get](#get.md) - Runs the constructed query and returns the results as an instance of DataTableResult
  * [get\_data\_as\_graph](#get_data_as_graph.md)
  * [get\_differences](#get_differences.md)
  * [get\_insert\_graph](#get_insert_graph.md)
  * [get\_sparql](#get_sparql.md) - Returns the generated SPARQL query
  * [get\_update\_changeset](#get_update_changeset.md) - Get the changeset that would be applied by the update method.
  * [insert](#insert.md) - Inserts data into a platform store.
  * [limit](#limit.md) - Specifies the maximum number of rows to return in the query and, optionally, an offset row number to start from.
  * [map](#map.md) - Maps a URI to a short name.
  * [optional](#optional.md) - Specifies the variables you want to optionally select in your query.
  * [order\_by](#order_by.md) - Specifies a sort order for the query results.
  * [select](#select.md) - Specifies the variables you want to select in your query.
  * [set](#set.md) - Sets the value of a field for use with the insert() method.
  * [set\_field\_defaults](#set_field_defaults.md) - Specifies default metadata for a field.
  * [update](#update.md) - Updates data in a platform store.
  * [where](#where.md) - Specifies a constraint on a literal value.
  * [where\_uri](#where_uri.md) - Specifies a constraint on a resource value.

# Constructor Detail #

## DataTable ##

```
public DataTable(mixed store_uri, mixed credentials, mixed request_factory)
```

The DataTable constructor requires the URI of the Talis Platform store as its first parameter, e.g.:

```php

$dt = new DataTable('http://api.talis.com/stores/mystore');
```

Optionally a Credentials object can be supplied as the second parameter.

Advanced: A third, optional, parameter allows an alternate HttpRequestFactory to be specified for when you need an alternate HTTP implementation to the default cURL-based one

# Method Detail #

## distinct ##

```
public void distinct()
```

Specifies that the query results must be distinct (i.e. without duplicate rows).

All of the following are valid:

```php

$dt->from('person');
$dt->from('document,book');
```

The following code will select the unique foaf:names of every resource in a store:

```php

$dt = new DataTable('http://api.talis.com/stores/mystore');
$dt->map('http://xmlns.com/foaf/0.1/name', 'name');
$dt->select('name')->distinct();
$dt->get();
```

## from ##

```
public void from(mixed type_list)
```

Specifies types of the resources you want to select in your query. It takes a single parameter which is a comma separated list of types (which must be mapped short names). If multiple types are specified then the selected resources must have an rdf:type triple for every one of the types.

All of the following are valid:

```php

$dt->from('person');
$dt->from('document,book');
```

The following code will select the foaf:names of every foaf:Person in a store:

```php

$dt = new DataTable('http://api.talis.com/stores/mystore');
$dt->map('http://xmlns.com/foaf/0.1/name', 'name');
$dt->map('http://xmlns.com/foaf/0.1/Person', 'person');
$dt->select('name')->from('person');
$dt->get();
```

## get ##

```
public void get()
```

Runs the constructed query and returns the results as an instance of DataTableResult

## get\_data\_as\_graph ##

```
public void get_data_as_graph(mixed s)
```



## get\_differences ##

```
public void get_differences(mixed query_results)
```



## get\_insert\_graph ##

```
public void get_insert_graph(mixed type_list)
```



## get\_sparql ##

```
public void get_sparql()
```

Returns the generated SPARQL query

## get\_update\_changeset ##

```
public void get_update_changeset()
```

Get the changeset that would be applied by the update method.<dl>
<dt>Return:</dt>
<dd>ChangeSet</dd>
</dl>


## insert ##

```
public void insert(mixed type_list)
```

Inserts data into a platform store. It optionally takes a single parameter which is a comma separated list of types (which must be mapped short names). These are added as rdf:type properties for the inserted resource. If multiple types are specified then multiple rdf:types will be added.

Note that this method is in beta: it has been tested but there may be unusual corner cases that could result in data corruption

Insert a new resource description for something with a name of "scooby" and a type of http://example.org/person:

```php

$dt = new DataTable('http://api.talis.com/stores/mystore');
$dt->map('http://example.org/name', 'name');
$dt->map('http://example.org/person', 'person');
$dt->set('name', 'scooby');
$response = $dt->insert('person');
```<dl>
<dt>Return:</dt>
<dd>HttpResponse</dd>
</dl>


## limit ##

```
public void limit(mixed value, mixed offset)
```

Specifies the maximum number of rows to return in the query and, optionally, an offset row number to start from. This could be used to implement a paging scheme. The default offset is zero.

Select the first five names in a store:

```php

$dt->select('name')->limit(5);
```

Select names 15 through to 19 in a store:

```php

$dt->select('name')->distinct()->limit(5, 15);
```

Note: Using offset without specifying a sort order may lead to unpredictable results.

## map ##

```
public void map(mixed uri_or_array, mixed short_name)
```

Maps a URI to a short name. The first parameter can either be a URI or an associative array of uri and shortname mappings in which case the second parameter is ignored. Short names are used by other methods to refer to property and class URIs.

The following are equivalent:

```php

$dt->map('http://xmlns.com/foaf/0.1/name', 'name');
$dt->map('http://xmlns.com/foaf/0.1/nick', 'nick');
```

```php

$dt->map( array('http://xmlns.com/foaf/0.1/name' => 'name', 'http://xmlns.com/foaf/0.1/nick' => 'nick'));
```

## optional ##

```
public void optional(mixed field_list)
```

Specifies the variables you want to optionally select in your query. It takes a single parameter which is a comma separated list of field names (which must be mapped short names). Optional variables will be returned only if there is matching data for them, otherwise they have a null value. In contrast the select method requires that all results must have values for the fields specified. At least one variable must be specified by select before any optional variables can be used.

Select the names of all the resources in a store and the nicknames of those resources that have them:

```php

$dt = new DataTable('http://api.talis.com/stores/mystore');
$dt->map('http://xmlns.com/foaf/0.1/name', 'name');
$dt->map('http://xmlns.com/foaf/0.1/nick', 'nick');
$dt->select('name')->optional('nick');
```

## order\_by ##

```
public void order_by(mixed field, mixed ordering)
```

Specifies a sort order for the query results. The first parameter is required and specifies the field name to sort by (which must be a mapped short name). The second parameter is optional and specifies the ordering of the results. It must be one of 'asc' (meaning ascending order) or 'desc' (meaning descending order). The default ordering is 'asc'.

Select names and ages in a store and return them in age order

```php

$dt->select('name,age')->order_by('age');
```

Select names in a store and return them in descending order

```php

$dt->select('name')->order_by('name', 'desc');
```

Multiple orderings can be specified by repeating this method call:

Select names and ages in a store and return them in age order. For example to sort by age and then by name descending:

```php

$dt->select('name,age')->order_by('age')->order_by('name', 'desc');
```

## select ##

```
public void select(mixed field_list)
```

Specifies the variables you want to select in your query. It takes a single parameter which is a comma separated list of field names (which must be mapped short names) or "dotted path names", explained below.

All of the following are valid:

```php

$dt->select('name');
$dt->select('name,age');
$dt->select(' name  , age');
```

The following code will select the foaf:names of every resource in a store:

```php

$dt = new DataTable('http://api.talis.com/stores/mystore');
$dt->map('http://xmlns.com/foaf/0.1/name', 'name');
$dt->select('name');
$dt->get();
```

In addition to mapped field names, DataTable supports an extended syntax for expressing traversal of the relationships in the RDF. Dotted path names are a pair of mapped names delimited by a full stop, e.g. friend.name

Both parts of a dotted path name must be mapped short names. They can be interpreted as a join between resources in the data. friend.name can be translated as "the name of the resource that is the value of the matching result's friend property". In the query resules the dotted path name is referenced by replacing the dot with an underscore, so friend.name becomes a field called friend\_name

The following code will select the foaf:names of every resource in a store and the foaf:names of everyone they know:

```php

$dt = new DataTable('http://api.talis.com/stores/mystore');
$dt->map('http://xmlns.com/foaf/0.1/name', 'name');
$dt->map('http://xmlns.com/foaf/0.1/knows', 'knows');
$dt->select('name,knows.name');
$dt->get();
$res = $dt->get();
foreach ($res->result() as $row) {
echo $row->name;
echo $row->knows_name;
}
```

## set ##

```
public void set(mixed field, mixed value, mixed type, mixed lang, mixed dt)
```

Sets the value of a field for use with the insert() method. The first parameter is required and specifies the field name to assign the value to (which must be a mapped short name). The second parameter is also required and specifies the new value for the field. Optionally a third parameter can be supplied to specify the type of the value, one of 'literal', 'uri' or 'bnode'. This will default to 'literal'. If the third parameter is 'literal', two further optional parameters may be supplied to specify the language or datatype of the value.

Set value of 'name' to be the literal 'chocolate':

```php

$dt->set('name', 'chocolate');
```

Set value of 'name' to be the literal 'chocolate' with language code 'en':

```php

$dt->set('name', 'chocolate', 'literal', 'en');
```

Set value of 'age' to be the literal '34' with datatype of xsd:integer:

```php

$dt->set('age', '34', 'literal', null, 'http://www.w3.org/2001/XMLSchema#integer');
```

Set value of 'father' to be the URI 'http://example.org/bob':

```php

$dt->set('name', 'http://example.org/bob', 'uri');
```

## set\_field\_defaults ##

```
public void set_field_defaults(mixed field, mixed type, mixed datatype)
```

Specifies default metadata for a field. These will be used by the set() method to set values for type and datatype for the specified field. The first parameter is required and specifies the field name (which must be a mapped short name). The second parameter is also required and specifies the type of the field, one of 'literal', 'uri' or 'bnode'. If the second parameter is 'literal' then a third optional parameter can be supplied which specifies a default datatype URI for the field.

Use of this method can simplify and clarify code using set() and insert()

Note: Values for type and datatype supplied via the set() method will override any default values set using this method.

Set the default type for the 'name' field to be literal:

```php

$dt->set('name', 'literal');
```

Set the default datatype for the 'created' field to be xsd:dateTime:

```php

$dt->set('created', 'literal', 'http://www.w3.org/2001/XMLSchema#dateTime');
```

## update ##

```
public void update()
```

Updates data in a platform store.

Note that this method is in beta: it has been tested but there may be unusual corner cases that could result in data corruption

Update the resource description for anything with a name of "shaggy" to have a name of "scooby":

```php

$dt = new DataTable('http://api.talis.com/stores/mystore');
$dt->map('http://example.org/name', 'name');
$dt->set('name', 'scooby');
$dt->where('name', 'shaggy');
$response = $dt->update();
```

The special variable "!_uri" can be used to refer to a specific resource._

Update the resource description for http://example.com/thing to have a name of "scooby"

```php

$dt = new DataTable('http://api.talis.com/stores/mystore');
$dt->map('http://example.org/name', 'name');
$dt->set('name', 'scooby');
$dt->where('_uri', 'http://example.com/thing');
$response = $dt->update();
```<dl>
<dt>Return:</dt>
<dd>HttpResponse</dd>
</dl>


## where ##

```
public void where(mixed field, mixed value)
```

Specifies a constraint on a literal value. Multiple calls to this method are conjunctive, i.e. all the constraints must apply to the resources.

Select all names where the person has a nickname of santa:

```php

$dt = new DataTable('http://api.talis.com/stores/mystore');
$dt->map('http://xmlns.com/foaf/0.1/name', 'name');
$dt->map('http://xmlns.com/foaf/0.1/nick', 'nick');
$dt->select('name')->where('nick', 'santa');
```

Select all names where the person has a nickname of santa and a shoe size of 9:

```php

$dt->select('name')->where('nick', 'santa')->where('shoesize', 9);
```

The field name can be suffixed by a boolean operator, one of =, >, <, !=, <=, >=

Select names of all resources that are older than 68

```php

$dt->select('name')->where('age >', 68);
```

Select names of all resources that do not have a nickname of santa:

```php

$dt->select('name')->where('nick !=', 'santa');
```

Boolean, floats and integer types are compared as those specific types, not strings:

```php

$dt->select('name')->where('jolly', TRUE);
$dt->select('name')->where('age >=', 21);
$dt->select('name')->where('shoesize <', 12.76);
```

SPARQL Note:**These constraints are implemented as filters with appropriate casts based on the type of variable supplied for the second parameter.**

## where\_uri ##

```
public void where_uri(mixed field, mixed uri)
```

Specifies a constraint on a resource value. Multiple calls to this method are conjunctive, i.e. all the constraints must apply to the resources. The first parameter is required and specifies the field name to test (which must be a mapped short name). The second parameter is also required and specifies a URI against which the field name is tested.

Select names of all resources that have a location of http://sws.geonames.org/6269203/

```php

$dt->select('name')->where('location', 'http://sws.geonames.org/6269203/');
```

SPARQL Note:**These constraints are implemented as additional graph patterns.**



Generated by [PHPDoctor 2RC2](http://phpdoctor.sourceforge.net/)