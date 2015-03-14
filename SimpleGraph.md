**note:** this page is automatically generated from embedded documentation in the PHP source.

# Overview #

## Constructors ##
  * [SimpleGraph](#SimpleGraph.md)
## Methods ##
  * [add\_graph](#add_graph.md) - Add the triples in the supplied graph to the current graph
  * [add\_json](#add_json.md) - Add the triples parsed from the supplied JSON to the graph
  * [add\_labelling\_property](#add_labelling_property.md)
  * [add\_literal\_triple](#add_literal_triple.md) - Adds a triple with a literal object to the graph
  * [add\_rdf](#add_rdf.md) - Add the triples parsed from the supplied RDF to the graph - let ARC guess the input
  * [add\_rdfxml](#add_rdfxml.md) - Add the triples parsed from the supplied RDF/XML to the graph
  * [add\_resource\_triple](#add_resource_triple.md) - Adds a triple with a resource object to the graph
  * [add\_turtle](#add_turtle.md) - Add the triples parsed from the supplied Turtle to the graph
  * [diff](#diff.md) - diff returns a simpleIndex consisting of all the statements in array1 that weren't found in any of the subsequent arrays
  * [from\_json](#from_json.md) - Replace the triples in the graph with those parsed from the supplied JSON
  * [from\_rdfxml](#from_rdfxml.md) - Replace the triples in the graph with those parsed from the supplied RDF/XML
  * [from\_turtle](#from_turtle.md) - Replace the triples in the graph with those parsed from the supplied Turtle
  * [get\_description](#get_description.md)
  * [get\_first\_literal](#get_first_literal.md) - Fetch the first literal value for a given subject and predicate.
  * [get\_first\_resource](#get_first_resource.md) - Fetch the first resource value for a given subject and predicate.
  * [get\_index](#get_index.md) - Get a copy of the graph's triple index
  * [get\_label](#get_label.md)
  * [get\_literal\_triple\_values](#get_literal_triple_values.md) - Fetch the literal values for a given subject and predicate.
  * [get\_prefix](#get_prefix.md)
  * [get\_resource\_triple\_values](#get_resource_triple_values.md) - Fetch the resource values for a given subject and predicate.
  * [get\_subject\_properties](#get_subject_properties.md) - Fetch the properties of a given subject and predicate.
  * [get\_subject\_property\_values](#get_subject_property_values.md) - Fetch the values for a given subject and predicate.
  * [get\_subject\_subgraph](#get_subject_subgraph.md) - Fetch a subgraph where all triples have given subject
  * [get\_subjects](#get_subjects.md) - Fetch an array of all the subjects
  * [get\_subjects\_of\_type](#get_subjects_of_type.md) - Fetch an array of all the subject that have and rdf type that matches that given
  * [get\_subjects\_where\_literal](#get_subjects_where_literal.md) - Fetch an array of all the subjects where the predicate and object match a ?s $p $o triple in the graph and the object is a literal value
  * [get\_subjects\_where\_resource](#get_subjects_where_resource.md) - Fetch an array of all the subjects where the predicate and object match a ?s $p $o triple in the graph and the object is a resource
  * [get\_triples](#get_triples.md) -
  * [has\_literal\_triple](#has_literal_triple.md) - Tests whether the graph contains the given triple
  * [has\_resource\_triple](#has_resource_triple.md) - Tests whether the graph contains the given triple
  * [has\_triples\_about](#has_triples_about.md) - Tests whether the graph contains a triple with the given subject
  * [is\_empty](#is_empty.md) - Tests whether the graph contains any triples
  * [make\_resource\_array](#make_resource_array.md) - Constructs an array containing the type of the resource and its value
  * [merge](#merge.md) - merge merges all  rdf/json-style arrays passed as parameters
  * [qname\_to\_uri](#qname_to_uri.md) - Convert a QName to a URI using registered namespace prefixes
  * [reify](#reify.md)
  * [remove\_all\_triples](#remove_all_triples.md) - Clears all triples out of the graph
  * [remove\_literal\_triple](#remove_literal_triple.md)
  * [remove\_property\_values](#remove_property_values.md) - Removes all triples with the given subject and predicate
  * [remove\_resource\_triple](#remove_resource_triple.md) - Remove a triple with a resource object from the graph
  * [remove\_triples\_about](#remove_triples_about.md) - Remove all triples having the supplied subject
  * [replace\_resource](#replace_resource.md)
  * [set\_namespace\_mapping](#set_namespace_mapping.md) - Map a portion of a URI to a short prefix for use when serialising the graph
  * [subject\_has\_property](#subject_has_property.md) - Tests whether the graph contains a triple with the given subject and predicate
  * [to\_html](#to_html.md) - Serialise the graph to HTML
  * [to\_json](#to_json.md) - Serialise the graph to JSON
  * [to\_ntriples](#to_ntriples.md) - Serialise the graph to N-Triples
  * [to\_rdfxml](#to_rdfxml.md) - Serialise the graph to RDF/XML
  * [to\_turtle](#to_turtle.md) - Serialise the graph to Turtle
  * [update\_prefix\_mappings](#update_prefix_mappings.md)
  * [uri\_to\_qname](#uri_to_qname.md) - Convert a URI to a QName using registered namespace prefixes

## Fields ##

# Constructor Detail #

## SimpleGraph ##

```
public SimpleGraph(mixed graph)
```



# Method Detail #

## add\_graph ##

```
public void add_graph(mixed g)
```

Add the triples in the supplied graph to the current graph<dl>
<dt>Param:</dt>
<dd>SimpleGraph g the graph to read</dd>
</dl>


## add\_json ##

```
public void add_json(mixed json)
```

Add the triples parsed from the supplied JSON to the graph<dl>
<dt>See:</dt>
<dd><a href='http://n2.talis.com/wiki/RDF_JSON_Specification'>http://n2.talis.com/wiki/RDF_JSON_Specification</a></dd>
<dt>Param:</dt>
<dd>string json the JSON to parse</dd>
</dl>


## add\_labelling\_property ##

```
public void add_labelling_property(mixed p)
```



## add\_literal\_triple ##

```
public void add_literal_triple(mixed s, mixed p, mixed o, mixed lang, mixed dt)
```

Adds a triple with a literal object to the graph<dl>
<dt>Param:</dt>
<dd>string s the subject of the triple, either a URI or a blank node in the format <i>:name</dd></i><dd>string p the predicate of the triple as a URI</dd>
<dd>string o the object of the triple as a string</dd>
<dd>string lang the language code of the triple's object (optional)</dd>
<dd>string dt the datatype URI of the triple's object (optional)</dd>
<dt>Return:</dt>
<dd>boolean true if the triple was new, false if it already existed in the graph</dd>
</dl>


## add\_rdf ##

```
public void add_rdf(mixed rdf, mixed base)
```

Add the triples parsed from the supplied RDF to the graph - let ARC guess the input<dl>
<dt>Param:</dt>
<dd>string rdf the RDF to parse</dd>
<dd>string base the base URI against which relative URIs in the RDF document will be resolved</dd>
<dt>Author:</dt>
<dd>Keith Alexander</dd>
</dl>


## add\_rdfxml ##

```
public void add_rdfxml(mixed rdfxml, mixed base)
```

Add the triples parsed from the supplied RDF/XML to the graph<dl>
<dt>Param:</dt>
<dd>string rdfxml the RDF/XML to parse</dd>
<dd>string base the base URI against which relative URIs in the RDF/XML document will be resolved</dd>
</dl>


## add\_resource\_triple ##

```
public void add_resource_triple(mixed s, mixed p, mixed o)
```

Adds a triple with a resource object to the graph<dl>
<dt>Param:</dt>
<dd>string s the subject of the triple, either a URI or a blank node in the format <i>:name</dd></i><dd>string p the predicate URI of the triple</dd>
<dd>string o the object of the triple, either a URI or a blank node in the format <i>:name</dd></i><dt>Return:</dt>
<dd>boolean true if the triple was new, false if it already existed in the graph</dd>
</dl>


## add\_turtle ##

```
public void add_turtle(mixed turtle, mixed base)
```

Add the triples parsed from the supplied Turtle to the graph<dl>
<dt>See:</dt>
<dd><a href='http://www.dajobe.org/2004/01/turtle'>http://www.dajobe.org/2004/01/turtle</a></dd>
<dt>Param:</dt>
<dd>string turtle the Turtle to parse</dd>
<dd>string base the base URI against which relative URIs in the Turtle document will be resolved</dd>
</dl>


## diff ##

```
public void diff()
```

diff
returns a simpleIndex consisting of all the statements in array1 that weren't found in any of the subsequent arrays<dl>
<dt>Param:</dt>
<dd>array1, array2, [array3, ...]</dd>
<dt>Return:</dt>
<dd>array</dd>
<dt>Author:</dt>
<dd>Keith</dd>
</dl>


## from\_json ##

```
public void from_json(mixed json)
```

Replace the triples in the graph with those parsed from the supplied JSON<dl>
<dt>See:</dt>
<dd><a href='http://n2.talis.com/wiki/RDF_JSON_Specification'>http://n2.talis.com/wiki/RDF_JSON_Specification</a></dd>
<dt>Param:</dt>
<dd>string json the JSON to parse</dd>
</dl>


## from\_rdfxml ##

```
public void from_rdfxml(mixed rdfxml, mixed base)
```

Replace the triples in the graph with those parsed from the supplied RDF/XML<dl>
<dt>Param:</dt>
<dd>string rdfxml the RDF/XML to parse</dd>
<dd>string base the base URI against which relative URIs in the RDF/XML document will be resolved</dd>
</dl>


## from\_turtle ##

```
public void from_turtle(mixed turtle, mixed base)
```

Replace the triples in the graph with those parsed from the supplied Turtle<dl>
<dt>See:</dt>
<dd><a href='http://www.dajobe.org/2004/01/turtle'>http://www.dajobe.org/2004/01/turtle</a></dd>
<dt>Param:</dt>
<dd>string turtle the Turtle to parse</dd>
<dd>string base the base URI against which relative URIs in the Turtle document will be resolved</dd>
</dl>


## get\_description ##

```
public void get_description(mixed resource_uri)
```



## get\_first\_literal ##

```
public void get_first_literal(mixed s, mixed p, mixed default, mixed preferred_language)
```

Fetch the first literal value for a given subject and predicate. If there are multiple possible values then one is selected at random.<dl>
<dt>Param:</dt>
<dd>string s the subject to search for</dd>
<dd>string p the predicate to search for, or an array of predicates</dd>
<dd>string default a default value to use if no literal values are found</dd>
<dt>Return:</dt>
<dd>string the first literal value found or the supplied default if no values were found</dd>
</dl>


## get\_first\_resource ##

```
public void get_first_resource(mixed s, mixed p, mixed default)
```

Fetch the first resource value for a given subject and predicate. If there are multiple possible values then one is selected at random.<dl>
<dt>Param:</dt>
<dd>string s the subject to search for</dd>
<dd>string p the predicate to search for</dd>
<dd>string default a default value to use if no literal values are found</dd>
<dt>Return:</dt>
<dd>string the first resource value found or the supplied default if no values were found</dd>
</dl>


## get\_index ##

```
public void get_index()
```

Get a copy of the graph's triple index<dl>
<dt>See:</dt>
<dd><a href='http://n2.talis.com/wiki/RDF_PHP_Specification'>http://n2.talis.com/wiki/RDF_PHP_Specification</a></dd>
</dl>


## get\_label ##

```
public void get_label(mixed resource_uri, mixed capitalize, mixed use_qnames)
```



## get\_literal\_triple\_values ##

```
public void get_literal_triple_values(mixed s, mixed p)
```

Fetch the literal values for a given subject and predicate.<dl>
<dt>Param:</dt>
<dd>string s the subject to search for</dd>
<dd>string p the predicate to search for</dd>
<dt>Return:</dt>
<dd>array list of literals that are the objects of triples with the supplied subject and predicate</dd>
</dl>


## get\_prefix ##

```
public void get_prefix(mixed ns)
```



## get\_resource\_triple\_values ##

```
public void get_resource_triple_values(mixed s, mixed p)
```

Fetch the resource values for a given subject and predicate.<dl>
<dt>Param:</dt>
<dd>string s the subject to search for</dd>
<dd>string p the predicate to search for</dd>
<dt>Return:</dt>
<dd>array list of URIs and blank nodes that are the objects of triples with the supplied subject and predicate</dd>
</dl>


## get\_subject\_properties ##

```
public void get_subject_properties(mixed s, mixed distinct)
```

Fetch the properties of a given subject and predicate.<dl>
<dt>Param:</dt>
<dd>string s the subject to search for</dd>
<dd>boolean distinct if true then duplicate properties are included only once (optional, default is true)</dd>
<dt>Return:</dt>
<dd>array list of property URIs</dd>
</dl>


## get\_subject\_property\_values ##

```
public void get_subject_property_values(mixed s, mixed p)
```

Fetch the values for a given subject and predicate.<dl>
<dt>Param:</dt>
<dd>string s the subject to search for</dd>
<dd>string p the predicate to search for</dd>
<dt>Return:</dt>
<dd>array list of values of triples with the supplied subject and predicate</dd>
</dl>


## get\_subject\_subgraph ##

```
public void get_subject_subgraph(mixed s)
```

Fetch a subgraph where all triples have given subject<dl>
<dt>Param:</dt>
<dd>string s the subject to search for</dd>
<dt>Return:</dt>
<dd>SimpleGraph triples with the supplied subject</dd>
</dl>


## get\_subjects ##

```
public void get_subjects()
```

Fetch an array of all the subjects<dl>
<dt>Return:</dt>
<dd>array</dd>
</dl>


## get\_subjects\_of\_type ##

```
public void get_subjects_of_type(mixed t)
```

Fetch an array of all the subject that have and rdf type that matches that given<dl>
<dt>Param:</dt>
<dd>$t the type to match</dd>
<dt>Return:</dt>
<dd>array</dd>
</dl>


## get\_subjects\_where\_literal ##

```
public void get_subjects_where_literal(mixed p, mixed o)
```

Fetch an array of all the subjects where the predicate and object match a ?s $p $o triple in the graph and the object is a literal value<dl>
<dt>Param:</dt>
<dd>$p the predicate to match</dd>
<dd>$o the resource object to match</dd>
<dt>Return:</dt>
<dd>array</dd>
</dl>


## get\_subjects\_where\_resource ##

```
public void get_subjects_where_resource(mixed p, mixed o)
```

Fetch an array of all the subjects where the predicate and object match a ?s $p $o triple in the graph and the object is a resource<dl>
<dt>Param:</dt>
<dd>$p the predicate to match</dd>
<dd>$o the resource object to match</dd>
<dt>Return:</dt>
<dd>array</dd>
</dl>


## get\_triples ##

```
public void get_triples()
```

<dl>
<dt>Deprecated:</dt>
<dd>this is deprecated</dd>
</dl>


## has\_literal\_triple ##

```
public void has_literal_triple(mixed s, mixed p, mixed o, mixed lang, mixed dt)
```

Tests whether the graph contains the given triple<dl>
<dt>Param:</dt>
<dd>string s the subject of the triple, either a URI or a blank node in the format <i>:name</dd></i><dd>string p the predicate URI of the triple</dd>
<dd>string o the object of the triple as a literal value</dd>
<dt>Return:</dt>
<dd>boolean true if the triple exists in the graph, false otherwise</dd>
</dl>


## has\_resource\_triple ##

```
public void has_resource_triple(mixed s, mixed p, mixed o)
```

Tests whether the graph contains the given triple<dl>
<dt>Param:</dt>
<dd>string s the subject of the triple, either a URI or a blank node in the format <i>:name</dd></i><dd>string p the predicate URI of the triple</dd>
<dd>string o the object of the triple, either a URI or a blank node in the format <i>:name</dd></i><dt>Return:</dt>
<dd>boolean true if the triple exists in the graph, false otherwise</dd>
</dl>


## has\_triples\_about ##

```
public void has_triples_about(mixed s)
```

Tests whether the graph contains a triple with the given subject<dl>
<dt>Param:</dt>
<dd>string s the subject of the triple, either a URI or a blank node in the format <i>:name</dd></i><dt>Return:</dt>
<dd>boolean true if the graph contains any triples with the specified subject, false otherwise</dd>
</dl>


## is\_empty ##

```
public void is_empty()
```

Tests whether the graph contains any triples<dl>
<dt>Return:</dt>
<dd>boolean true if the graph contains no triples, false otherwise</dd>
</dl>


## make\_resource\_array ##

```
public void make_resource_array(mixed resource)
```

Constructs an array containing the type of the resource and its value<dl>
<dt>Param:</dt>
<dd>string resource a URI or blank node identifier (prefixed with <i>: e.g.</i>:name)</dd>
<dt>Return:</dt>
<dd>array an associative array with two keys: 'type' and 'value'. Type is either bnode or uri</dd>
</dl>


## merge ##

```
public void merge()
```

merge
merges all  rdf/json-style arrays passed as parameters<dl>
<dt>Param:</dt>
<dd>array1, array2, [array3, ...]</dd>
<dt>Return:</dt>
<dd>array</dd>
<dt>Author:</dt>
<dd>Keith</dd>
</dl>


## qname\_to\_uri ##

```
public void qname_to_uri(mixed qname)
```

Convert a QName to a URI using registered namespace prefixes<dl>
<dt>Param:</dt>
<dd>string qname the QName to convert</dd>
<dt>Return:</dt>
<dd>string the URI corresponding to the QName if a suitable prefix exists, null otherwise</dd>
</dl>


## reify ##

```
public void reify(mixed resources, mixed nodeID_prefix)
```



## remove\_all\_triples ##

```
public void remove_all_triples()
```

Clears all triples out of the graph

## remove\_literal\_triple ##

```
public void remove_literal_triple(mixed s, mixed p, mixed o)
```



## remove\_property\_values ##

```
public void remove_property_values(mixed s, mixed p)
```

Removes all triples with the given subject and predicate<dl>
<dt>Param:</dt>
<dd>string s the subject of the triple, either a URI or a blank node in the format <i>:name</dd></i><dd>string p the predicate URI of the triple</dd>
</dl>


## remove\_resource\_triple ##

```
public void remove_resource_triple(mixed s, mixed p, mixed o)
```

Remove a triple with a resource object from the graph<dl>
<dt>Param:</dt>
<dd>string s the subject of the triple, either a URI or a blank node in the format <i>:name</dd></i><dd>string p the predicate URI of the triple</dd>
<dd>string o the object of the triple, either a URI or a blank node in the format _:name</dd>
</dl>_


## remove\_triples\_about ##

```
public void remove_triples_about(mixed s)
```

Remove all triples having the supplied subject<dl>
<dt>Param:</dt>
<dd>string s the subject of the triple, either a URI or a blank node in the format _:name</dd>
</dl>_


## replace\_resource ##

```
public void replace_resource(mixed look_for, mixed replace_with)
```



## set\_namespace\_mapping ##

```
public void set_namespace_mapping(mixed prefix, mixed uri)
```

Map a portion of a URI to a short prefix for use when serialising the graph<dl>
<dt>Param:</dt>
<dd>string prefix the namespace prefix to associate with the URI</dd>
<dd>string uri the URI to associate with the prefix</dd>
</dl>


## subject\_has\_property ##

```
public void subject_has_property(mixed s, mixed p)
```

Tests whether the graph contains a triple with the given subject and predicate<dl>
<dt>Param:</dt>
<dd>string s the subject of the triple, either a URI or a blank node in the format <i>:name</dd></i><dd>string p the predicate URI of the triple</dd>
<dt>Return:</dt>
<dd>boolean true if a matching triple exists in the graph, false otherwise</dd>
</dl>


## to\_html ##

```
public void to_html(mixed s)
```

Serialise the graph to HTML<dl>
<dt>Return:</dt>
<dd>string a HTML version of the graph</dd>
</dl>


## to\_json ##

```
public void to_json()
```

Serialise the graph to JSON<dl>
<dt>See:</dt>
<dd><a href='http://n2.talis.com/wiki/RDF_JSON_Specification'>http://n2.talis.com/wiki/RDF_JSON_Specification</a></dd>
<dt>Return:</dt>
<dd>string the JSON version of the graph</dd>
</dl>


## to\_ntriples ##

```
public void to_ntriples()
```

Serialise the graph to N-Triples<dl>
<dt>See:</dt>
<dd><a href='http://www.w3.org/TR/rdf-testcases/#ntriples'>http://www.w3.org/TR/rdf-testcases/#ntriples</a></dd>
<dt>Return:</dt>
<dd>string the N-Triples version of the graph</dd>
</dl>


## to\_rdfxml ##

```
public void to_rdfxml()
```

Serialise the graph to RDF/XML<dl>
<dt>Return:</dt>
<dd>string the RDF/XML version of the graph</dd>
</dl>


## to\_turtle ##

```
public void to_turtle()
```

Serialise the graph to Turtle<dl>
<dt>See:</dt>
<dd><a href='http://www.dajobe.org/2004/01/turtle'>http://www.dajobe.org/2004/01/turtle</a></dd>
<dt>Return:</dt>
<dd>string the Turtle version of the graph</dd>
</dl>


## update\_prefix\_mappings ##

```
public void update_prefix_mappings()
```



## uri\_to\_qname ##

```
public void uri_to_qname(mixed uri)
```

Convert a URI to a QName using registered namespace prefixes<dl>
<dt>Param:</dt>
<dd>string uri the URI to convert</dd>
<dt>Return:</dt>
<dd>string the QName corresponding to the URI if a suitable prefix exists, null otherwise</dd>
</dl>


# Field Detail #



Generated by [PHPDoctor 2RC2](http://phpdoctor.sourceforge.net/)