**note:** this page is automatically generated from embedded documentation in the PHP source.

# Overview #

## Constructors ##
  * [Graph](#Graph.md) - Create a new instance of this class
## Methods ##
  * [apply\_changeset](#apply_changeset.md) - Apply a changeset to the graph
  * [apply\_changeset\_rdfxml](#apply_changeset_rdfxml.md) - Apply a changeset to the graph
  * [apply\_versioned\_changeset](#apply_versioned_changeset.md) - Apply a changeset in a versioned manner to the graph
  * [apply\_versioned\_changeset\_rdfxml](#apply_versioned_changeset_rdfxml.md) - Apply a changeset in a versioned manner to the graph
  * [describe](#describe.md) - Obtain the graph's bounded description of a given resource
  * [describe\_to\_simple\_graph](#describe_to_simple_graph.md) - Obtain the graph's bounded description of a given resource.
  * [describe\_to\_triple\_list](#describe_to_triple_list.md) - Obtain the graph's bounded description of a given resource
  * [has\_description](#has_description.md) - Tests whether the graph contains a bounded description of a given resource.
  * [submit\_rdfxml](#submit_rdfxml.md) - Submit some RDF/XML to be added to the graph
  * [submit\_turtle](#submit_turtle.md) - Submit some Turtle to be added to the graph

# Constructor Detail #

## Graph ##

```
public Graph(mixed uri, mixed credentials, mixed request_factory)
```

Create a new instance of this class<dl>
<dt>Param:</dt>
<dd>string uri URI of the graph</dd>
<dd>Credentials credentials the credentials to use for authenticated requests (optional)</dd>
</dl>


# Method Detail #

## apply\_changeset ##

```
public void apply_changeset(mixed cs)
```

Apply a changeset to the graph<dl>
<dt>Param:</dt>
<dd>ChangeSet cs the changeset to apply</dd>
<dt>Return:</dt>
<dd>HttpResponse</dd>
</dl>


## apply\_changeset\_rdfxml ##

```
public void apply_changeset_rdfxml(mixed rdfxml)
```

Apply a changeset to the graph<dl>
<dt>Param:</dt>
<dd>string rdfxml the changeset to apply, serialised as RDF/XML</dd>
<dt>Return:</dt>
<dd>HttpResponse</dd>
</dl>


## apply\_versioned\_changeset ##

```
public void apply_versioned_changeset(mixed cs)
```

Apply a changeset in a versioned manner to the graph<dl>
<dt>Param:</dt>
<dd>ChangeSet cs the changeset to apply</dd>
<dt>Return:</dt>
<dd>HttpResponse</dd>
</dl>


## apply\_versioned\_changeset\_rdfxml ##

```
public void apply_versioned_changeset_rdfxml(mixed rdfxml)
```

Apply a changeset in a versioned manner to the graph<dl>
<dt>Param:</dt>
<dd>string rdfxml the changeset to apply, serialised as RDF/XML</dd>
<dt>Return:</dt>
<dd>HttpResponse</dd>
</dl>


## describe ##

```
public void describe(mixed uri, mixed output)
```

Obtain the graph's bounded description of a given resource<dl>
<dt>See:</dt>
<dd><a href='http://n2.talis.com/wiki/Metabox#Describing_a_Resource'>http://n2.talis.com/wiki/Metabox#Describing_a_Resource</a></dd>
<dt>Param:</dt>
<dd>string uri the URI of the resource to be described</dd>
<dd>string output the desired output format of the response (e.g. rdf, xml, json, ntriples, turtle)</dd>
<dt>Return:</dt>
<dd>HttpResponse</dd>
</dl>


## describe\_to\_simple\_graph ##

```
public void describe_to_simple_graph(mixed uri)
```

Obtain the graph's bounded description of a given resource. This is designed to be fast since it uses RDF/JSON which requires no parsing by the SimpleGraph class. This method always returns a SimpleGraph, which will be empty if any HTTP errors occured.<dl>
<dt>See:</dt>
<dd><a href='http://n2.talis.com/wiki/Metabox#Describing_a_Resource'>http://n2.talis.com/wiki/Metabox#Describing_a_Resource</a></dd>
<dt>Param:</dt>
<dd>string uri the URI of the resource to be described</dd>
<dt>Return:</dt>
<dd>SimpleGraph</dd>
</dl>


## describe\_to\_triple\_list ##

```
public void describe_to_triple_list(mixed uri)
```

Obtain the graph's bounded description of a given resource<dl>
<dt>Deprecated:</dt>
<dd>triple lists are deprecated</dd>
</dl>


## has\_description ##

```
public void has_description(mixed uri)
```

Tests whether the graph contains a bounded description of a given resource. This uses a conditional GET.<dl>
<dt>See:</dt>
<dd><a href='http://n2.talis.com/wiki/Metabox#Describing_a_Resource'>http://n2.talis.com/wiki/Metabox#Describing_a_Resource</a></dd>
<dt>Param:</dt>
<dd>string uri the URI of the resource to be described</dd>
<dt>Return:</dt>
<dd>boolean true if the graph contains triples with the resource as a subject, false otherwise</dd>
</dl>


## submit\_rdfxml ##

```
public void submit_rdfxml(mixed rdfxml)
```

Submit some RDF/XML to be added to the graph<dl>
<dt>Param:</dt>
<dd>string rdfxml the RDF to be submitted, serialised as RDF/XML</dd>
<dt>Return:</dt>
<dd>HttpResponse</dd>
</dl>


## submit\_turtle ##

```
public void submit_turtle(mixed turtle)
```

Submit some Turtle to be added to the graph<dl>
<dt>Param:</dt>
<dd>string turtle the RDF to be submitted, serialised as Turtle</dd>
<dt>Return:</dt>
<dd>HttpResponse</dd>
</dl>




Generated by [PHPDoctor 2RC2](http://phpdoctor.sourceforge.net/)