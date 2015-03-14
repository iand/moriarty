**note:** this page is automatically generated from embedded documentation in the PHP source.

# Overview #

## Constructors ##
  * [SparqlServiceBase](#SparqlServiceBase.md) - Create a new instance of this class
## Methods ##
  * [ask](#ask.md) - Execute an ask sparql query
  * [construct\_to\_simple\_graph](#construct_to_simple_graph.md) -
  * [construct\_to\_triple\_list](#construct_to_triple_list.md) -
  * [describe](#describe.md) - Obtain a bounded description of a given resource.
  * [describe\_to\_simple\_graph](#describe_to_simple_graph.md) - Obtain a bounded description of a given resource as a SimpleGraph.
  * [describe\_to\_triple\_list](#describe_to_triple_list.md) -
  * [graph](#graph.md) - Execute a graph type sparql query, i.e.
  * [graph\_to\_simple\_graph](#graph_to_simple_graph.md) - Execute a graph type sparql query and obtain the result as a SimpleGraph.
  * [graph\_to\_triple\_list](#graph_to_triple_list.md) -
  * [parse\_ask\_results](#parse_ask_results.md) - Parse the SPARQL XML results format from an ask query.
  * [parse\_select\_results](#parse_select_results.md) - Parse the SPARQL XML results format into an array.
  * [query](#query.md) - Execute an arbitrary query on the sparql service.
  * [select](#select.md) - Execute a select sparql query
  * [select\_to\_array](#select_to_array.md) - Execute a select sparql query and return the results as an array.

# Constructor Detail #

## SparqlServiceBase ##

```
public SparqlServiceBase(mixed uri, mixed credentials, mixed request_factory)
```

Create a new instance of this class<dl>
<dt>Param:</dt>
<dd>string uri URI of the sparql service</dd>
<dd>Credentials credentials the credentials to use for authenticated requests (optional)</dd>
</dl>


# Method Detail #

## ask ##

```
public void ask(mixed query)
```

Execute an ask sparql query<dl>
<dt>Param:</dt>
<dd>string query the ask query to execute</dd>
<dt>Return:</dt>
<dd>HttpResponse</dd>
</dl>


## construct\_to\_simple\_graph ##

```
public void construct_to_simple_graph(mixed query)
```

<dl>
<dt>Deprecated:</dt>
<dd>use graph_to_simple_graph</dd>
</dl>


## construct\_to\_triple\_list ##

```
public void construct_to_triple_list(mixed query)
```

<dl>
<dt>Deprecated:</dt>
<dd>triple lists are deprecated</dd>
</dl>


## describe ##

```
public void describe(mixed uri, mixed type, mixed output)
```

Obtain a bounded description of a given resource. Various types of description are supported:
<ul>
<li><em>cbd</em> - concise bounded description</li>
<li><em>scbd</em> - symmetric bounded description</li>
<li><em>lcbd</em> - labelled bounded description</li>
<li><em>slcbd</em> - symmetric labelled bounded description</li>
</ul>
See http://n2.talis.com/wiki/Bounded_Descriptions_in_RDF for more information on these types of description
Only cbd type is supported for arrays of URIs<dl>
<dt>Param:</dt>
<dd>mixed uri the URI of the resource to be described or an array of URIs</dd>
<dd>string type the type of bounded description to be obtained (optional)</dd>
<dd>string output the format of the RDF to return - one of rdf, turtle, ntriples or json (optional)</dd>
<dt>Return:</dt>
<dd>HttpResponse</dd>
</dl>


## describe\_to\_simple\_graph ##

```
public void describe_to_simple_graph(mixed uri, mixed type)
```

Obtain a bounded description of a given resource as a SimpleGraph. An empty SimpleGraph is returned if any HTTP errors occur.<dl>
<dt>Param:</dt>
<dd>mixed uri the URI of the resource to be described or an array of URIs</dd>
<dt>Return:</dt>
<dd>SimpleGraph</dd>
</dl>


## describe\_to\_triple\_list ##

```
public void describe_to_triple_list(mixed uri)
```

<dl>
<dt>Deprecated:</dt>
<dd>triple lists are deprecated</dd>
</dl>


## graph ##

```
public void graph(mixed query, mixed output)
```

Execute a graph type sparql query, i.e. a describe or a construct<dl>
<dt>Param:</dt>
<dd>string query the describe or construct query to execute</dd>
<dt>Return:</dt>
<dd>HttpResponse</dd>
</dl>


## graph\_to\_simple\_graph ##

```
public void graph_to_simple_graph(mixed query)
```

Execute a graph type sparql query and obtain the result as a SimpleGraph. An empty SimpleGraph is returned if any HTTP errors occur.<dl>
<dt>Param:</dt>
<dd>string query the describe or construct query to execute</dd>
<dt>Return:</dt>
<dd>SimpleGraph</dd>
</dl>


## graph\_to\_triple\_list ##

```
public void graph_to_triple_list(mixed query)
```

<dl>
<dt>Deprecated:</dt>
<dd>triple lists are deprecated</dd>
</dl>


## parse\_ask\_results ##

```
public void parse_ask_results(mixed xml)
```

Parse the SPARQL XML results format from an ask query.<dl>
<dt>Param:</dt>
<dd>string xml the results XML to parse</dd>
<dt>Return:</dt>
<dd>array true if the query result was true, false otherwise</dd>
</dl>


## parse\_select\_results ##

```
public void parse_select_results(mixed xml)
```

Parse the SPARQL XML results format into an array. The array consist of one element per result.
Each element is an associative array where the keys correspond to the variable name and the values are
another associative array with the following keys:
<ul>
<li><em>type</em> => the type of the result binding, one of 'uri', 'literal' or 'bnode'</li>
<li><em>value</em> => the value of the result binding</li>
<li><em>lang</em> => the language code (if any) of the result binding</li>
<li><em>datatype</em> => the datatype uri (if any) of the result binding</li>
</ul>
For example: $results[2](2.md)['foo']['value'] will obtain the value of the foo variable for the third result<dl>
<dt>Param:</dt>
<dd>string xml the results XML to parse</dd>
<dt>Return:</dt>
<dd>array</dd>
</dl>


## query ##

```
public void query(mixed query, mixed mime)
```

Execute an arbitrary query on the sparql service. Will use GET for short queries to enhance cacheability.<dl>
<dt>Param:</dt>
<dd>string query the query to execute</dd>
<dd>string mime the media type of the expected response or the short name as listed at <a href='http://n2.talis.com/wiki/Store_Sparql_Service#Output_Formats'>http://n2.talis.com/wiki/Store_Sparql_Service#Output_Formats</a> (optional, defaults to RDF/XML and SPARQL results XML)</dd>
<dt>Return:</dt>
<dd>HttpResponse</dd>
</dl>


## select ##

```
public void select(mixed query)
```

Execute a select sparql query<dl>
<dt>Param:</dt>
<dd>string query the select query to execute</dd>
<dt>Return:</dt>
<dd>HttpResponse</dd>
</dl>


## select\_to\_array ##

```
public void select_to_array(mixed query)
```

Execute a select sparql query and return the results as an array. An empty array is returned if any HTTP errors occur.<dl>
<dt>Param:</dt>
<dd>string query the select query to execute</dd>
<dt>Return:</dt>
<dd>array parsed results in format returned by parse_select_results method</dd>
</dl>




Generated by [PHPDoctor 2RC2](http://phpdoctor.sourceforge.net/)