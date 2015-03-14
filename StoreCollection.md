**note:** this page is automatically generated from embedded documentation in the PHP source.

# Overview #

<table>
<h2>Fields inherited from Moriarty.SimpleGraph</h2>

<ul><li><a href='SimpleGraph#_ns.md'>_ns</a>
<h2>Constructors</h2>
</li><li><a href='#StoreCollection.md'>StoreCollection</a> - Create a new instance of this class<br>
<h2>Methods</h2>
</li><li><a href='#create_store.md'>create_store</a> - Create a new store on the platform.<br>
</li><li><a href='#get_store_uris.md'>get_store_uris</a> - Obtain the list of store URIs.</li></ul>

<h2>Methods inherited from Moriarty.SimpleGraph</h2>

<ul><li><a href='SimpleGraph#add_graph.md'>add_graph</a>
</li><li><a href='SimpleGraph#add_json.md'>add_json</a>
</li><li><a href='SimpleGraph#add_labelling_property.md'>add_labelling_property</a>
</li><li><a href='SimpleGraph#add_literal_triple.md'>add_literal_triple</a>
</li><li><a href='SimpleGraph#add_rdf.md'>add_rdf</a>
</li><li><a href='SimpleGraph#add_rdfxml.md'>add_rdfxml</a>
</li><li><a href='SimpleGraph#add_resource_triple.md'>add_resource_triple</a>
</li><li><a href='SimpleGraph#add_turtle.md'>add_turtle</a>
</li><li><a href='SimpleGraph#diff.md'>diff</a>
</li><li><a href='SimpleGraph#from_json.md'>from_json</a>
</li><li><a href='SimpleGraph#from_rdfxml.md'>from_rdfxml</a>
</li><li><a href='SimpleGraph#from_turtle.md'>from_turtle</a>
</li><li><a href='SimpleGraph#get_description.md'>get_description</a>
</li><li><a href='SimpleGraph#get_first_literal.md'>get_first_literal</a>
</li><li><a href='SimpleGraph#get_first_resource.md'>get_first_resource</a>
</li><li><a href='SimpleGraph#get_index.md'>get_index</a>
</li><li><a href='SimpleGraph#get_label.md'>get_label</a>
</li><li><a href='SimpleGraph#get_literal_triple_values.md'>get_literal_triple_values</a>
</li><li><a href='SimpleGraph#get_prefix.md'>get_prefix</a>
</li><li><a href='SimpleGraph#get_resource_triple_values.md'>get_resource_triple_values</a>
</li><li><a href='SimpleGraph#get_subject_properties.md'>get_subject_properties</a>
</li><li><a href='SimpleGraph#get_subject_property_values.md'>get_subject_property_values</a>
</li><li><a href='SimpleGraph#get_subject_subgraph.md'>get_subject_subgraph</a>
</li><li><a href='SimpleGraph#get_subjects.md'>get_subjects</a>
</li><li><a href='SimpleGraph#get_subjects_of_type.md'>get_subjects_of_type</a>
</li><li><a href='SimpleGraph#get_subjects_where.md'>get_subjects_where</a>
</li><li><a href='SimpleGraph#get_subjects_where_literal.md'>get_subjects_where_literal</a>
</li><li><a href='SimpleGraph#get_subjects_where_resource.md'>get_subjects_where_resource</a>
</li><li><a href='SimpleGraph#get_triples.md'>get_triples</a>
</li><li><a href='SimpleGraph#has_literal_triple.md'>has_literal_triple</a>
</li><li><a href='SimpleGraph#has_resource_triple.md'>has_resource_triple</a>
</li><li><a href='SimpleGraph#has_triples_about.md'>has_triples_about</a>
</li><li><a href='SimpleGraph#is_empty.md'>is_empty</a>
</li><li><a href='SimpleGraph#make_resource_array.md'>make_resource_array</a>
</li><li><a href='SimpleGraph#merge.md'>merge</a>
</li><li><a href='SimpleGraph#qname_to_uri.md'>qname_to_uri</a>
</li><li><a href='SimpleGraph#reify.md'>reify</a>
</li><li><a href='SimpleGraph#remove_all_triples.md'>remove_all_triples</a>
</li><li><a href='SimpleGraph#remove_literal_triple.md'>remove_literal_triple</a>
</li><li><a href='SimpleGraph#remove_property_values.md'>remove_property_values</a>
</li><li><a href='SimpleGraph#remove_resource_triple.md'>remove_resource_triple</a>
</li><li><a href='SimpleGraph#remove_triples_about.md'>remove_triples_about</a>
</li><li><a href='SimpleGraph#replace_resource.md'>replace_resource</a>
</li><li><a href='SimpleGraph#set_namespace_mapping.md'>set_namespace_mapping</a>
</li><li><a href='SimpleGraph#subject_has_property.md'>subject_has_property</a>
</li><li><a href='SimpleGraph#to_html.md'>to_html</a>
</li><li><a href='SimpleGraph#to_json.md'>to_json</a>
</li><li><a href='SimpleGraph#to_ntriples.md'>to_ntriples</a>
</li><li><a href='SimpleGraph#to_rdfxml.md'>to_rdfxml</a>
</li><li><a href='SimpleGraph#to_turtle.md'>to_turtle</a>
</li><li><a href='SimpleGraph#update_prefix_mappings.md'>update_prefix_mappings</a>
</li><li><a href='SimpleGraph#uri_to_qname.md'>uri_to_qname</a>
<h1>Constructor Detail</h1></li></ul>

<h2>StoreCollection</h2>

<pre><code>public StoreCollection(mixed uri, mixed credentials, mixed request_factory)<br>
</code></pre>

Create a new instance of this class<dl>
<dt>Param:</dt>
<dd>string uri URI of the store collection</dd>
<dd>Credentials credentials the credentials to use for authenticated requests (optional)</dd>
</dl>


<h1>Method Detail</h1>

<h2>create_store</h2>

<pre><code>public void create_store(mixed name, mixed template_uri)<br>
</code></pre>

Create a new store on the platform. This is currently restricted to Talis administrators.<dl>
<dt>Param:</dt>
<dd>string name the name of the store</dd>
<dd>string template_uri the URI of the store template to use</dd>
<dt>Return:</dt>
<dd>HttpRequest</dd>
</dl>


<h2>get_store_uris</h2>

<pre><code>public void get_store_uris()<br>
</code></pre>

Obtain the list of store URIs. The retrieve method must be called first.<dl>
<dt>Return:</dt>
<dd>array</dd>
</dl>




Generated by <a href='http://phpdoctor.sourceforge.net/'>PHPDoctor 2RC2</a>