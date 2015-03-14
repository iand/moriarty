**note:** this page is automatically generated from embedded documentation in the PHP source.

# Overview #

Represents a changeset. Can be used to create a changeset based on the difference between two bounded descriptions. The descriptions must share the same subject URI.

<dl>
<dt>See:</dt>
<dd><a href='http://n2.talis.com/wiki/Changesets'>http://n2.talis.com/wiki/Changesets</a></dd>
</dl>
<table>
<h2>Fields inherited from Moriarty.SimpleGraph</h2>

<ul><li><a href='SimpleGraph#_ns.md'>_ns</a>
<h2>Constructors</h2>
</li><li><a href='#ChangeSet.md'>ChangeSet</a>
<h2>Methods</h2>
</li><li><a href='#addT.md'>addT</a> - adds a triple to the internal simpleIndex holding all the changesets and statements<br>
</li><li><a href='#has_changes.md'>has_changes</a>
</li><li><a href='#toRDFXML.md'>toRDFXML</a>
</li><li><a href='#to_rdfxml.md'>to_rdfxml</a> - Serialise the graph to RDF/XML</li></ul>

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
<h2>Fields</h2>
</li><li><a href='#a.md'>$a</a> - Serialise the graph to RDF/XML<br>
</li><li><a href='#after.md'>$after</a> - Serialise the graph to RDF/XML<br>
</li><li><a href='#before.md'>$before</a> - Serialise the graph to RDF/XML<br>
</li><li><a href='#subjectIndex.md'>$subjectIndex</a> - Serialise the graph to RDF/XML</li></ul>

<h1>Constructor Detail</h1>

<h2>ChangeSet</h2>

<pre><code>public ChangeSet(mixed a)<br>
</code></pre>



<h1>Method Detail</h1>

<h2>addT</h2>

<pre><code>public void addT(mixed s, mixed p, mixed o, mixed o_type)<br>
</code></pre>

adds a triple to the internal simpleIndex holding all the changesets and statements<dl>
<dt>Return:</dt>
<dd>void</dd>
<dt>Author:</dt>
<dd>Keith</dd>
</dl>


<h2>has_changes</h2>

<pre><code>public void has_changes()<br>
</code></pre>



<h2>toRDFXML</h2>

<pre><code>public void toRDFXML()<br>
</code></pre>



<h2>to_rdfxml</h2>

<pre><code>public void to_rdfxml()<br>
</code></pre>

Serialise the graph to RDF/XML<dl>
<dt>Return:</dt>
<dd>string the RDF/XML version of the graph</dd>
</dl>


<h1>Field Detail</h1>

<h2>a</h2>

<pre><code>public mixed $a<br>
</code></pre>

Create a new changeset. This will calculate the required additions and removals based on before and after versions of a bounded description. The args parameter is an associative array that may have the following fields:<br>
<ul>
<blockquote><li><em>subjectOfChange</em> => a string representing the URI of the changeset's subject of change</li>
<li><em>createdDate</em> => a string representing the date of the changeset</li>
<li><em>creatorName</em> => a string representing the creator of the changeset</li>
<li><em>changeReason</em> => a string representing the reason for the changeset</li>
<li><em>after</em> => an array of triples representing the required state of the resource description after the changeset would be applied. All subjects must be the same.</li>
<li><em>before</em> => an array of triples representing the state of the resource description before the changeset is applied. All subjects must be the same.</li>
<li><em>after_rdfxml</em> => a string of RDF/XML representing the required state of the resource description after the changeset would be applied. This is parsed and used to overwrite the 'after' parameter, if any. All subjects must be the same.</li>
<li><em>before_rdfxml</em> => a string of RDF/XML representing the state of the resource description before the changeset is applied. This is parsed and used to overwrite the 'begin' parameter, if any. All subjects must be the same.</li>
</ul>
If none of 'after', 'before', 'after_rdfxml' or 'before_rdfxml' is supplied then an empty changeset is constructed. <br />
The 'after' and 'before' arrays are simple arrays where each element is a triple array with the following structure:<br>
<ul>
<li><em>s</em> => the subject URI</li>
<li><em>p</em> => the predicate URI</li>
<li><em>o</em> => the value of the object</li>
<li><em>o_type</em> => one of 'uri', 'bnode' or 'literal'</li>
<li><em>o_lang</em> => the language of the literal if any</li>
<li><em>o_datatype</em> => the data type URI of the literal if any</li>
</ul><dl>
<dt>Param:</dt>
<dd>array args an associative array of parameters to use when constructing the changeset</dd>
</dl></blockquote>


<h2>after</h2>

<pre><code>public mixed $after = array()<br>
</code></pre>



<h2>before</h2>

<pre><code>public mixed $before = array()<br>
</code></pre>



<h2>subjectIndex</h2>

<pre><code>public mixed $subjectIndex = array()<br>
</code></pre>





Generated by <a href='http://phpdoctor.sourceforge.net/'>PHPDoctor 2RC2</a>