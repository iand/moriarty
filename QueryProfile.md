**note:** this page is automatically generated from embedded documentation in the PHP source.

# Overview #

## Constructors ##
  * [QueryProfile](#QueryProfile.md) - Create a new instance of this class
## Methods ##
  * [add\_field\_weight](#add_field_weight.md) - Adds the field and weight to the query profile
  * [copy\_to](#copy_to.md) - Copies the field weights and other properties into new query profile.
  * [remove\_field\_weight](#remove_field_weight.md) - Removes the weight for the named field

## Methods inherited from Moriarty.NetworkResource ##

  * [delete\_from\_network](NetworkResource#delete_from_network.md)
  * [get\_comment](NetworkResource#get_comment.md)
  * [get\_from\_network](NetworkResource#get_from_network.md)
  * [get\_label](NetworkResource#get_label.md)
  * [put\_to\_network](NetworkResource#put_to_network.md)
  * [set\_comment](NetworkResource#set_comment.md)
  * [set\_label](NetworkResource#set_label.md)
## Methods inherited from Moriarty.SimpleGraph ##

  * [add\_graph](SimpleGraph#add_graph.md)
  * [add\_json](SimpleGraph#add_json.md)
  * [add\_labelling\_property](SimpleGraph#add_labelling_property.md)
  * [add\_literal\_triple](SimpleGraph#add_literal_triple.md)
  * [add\_rdf](SimpleGraph#add_rdf.md)
  * [add\_rdfxml](SimpleGraph#add_rdfxml.md)
  * [add\_resource\_triple](SimpleGraph#add_resource_triple.md)
  * [add\_turtle](SimpleGraph#add_turtle.md)
  * [diff](SimpleGraph#diff.md)
  * [from\_json](SimpleGraph#from_json.md)
  * [from\_rdfxml](SimpleGraph#from_rdfxml.md)
  * [from\_turtle](SimpleGraph#from_turtle.md)
  * [get\_description](SimpleGraph#get_description.md)
  * [get\_first\_literal](SimpleGraph#get_first_literal.md)
  * [get\_first\_resource](SimpleGraph#get_first_resource.md)
  * [get\_index](SimpleGraph#get_index.md)
  * [get\_label](SimpleGraph#get_label.md)
  * [get\_literal\_triple\_values](SimpleGraph#get_literal_triple_values.md)
  * [get\_prefix](SimpleGraph#get_prefix.md)
  * [get\_resource\_triple\_values](SimpleGraph#get_resource_triple_values.md)
  * [get\_subject\_properties](SimpleGraph#get_subject_properties.md)
  * [get\_subject\_property\_values](SimpleGraph#get_subject_property_values.md)
  * [get\_subject\_subgraph](SimpleGraph#get_subject_subgraph.md)
  * [get\_subjects](SimpleGraph#get_subjects.md)
  * [get\_subjects\_of\_type](SimpleGraph#get_subjects_of_type.md)
  * [get\_subjects\_where](SimpleGraph#get_subjects_where.md)
  * [get\_subjects\_where\_literal](SimpleGraph#get_subjects_where_literal.md)
  * [get\_subjects\_where\_resource](SimpleGraph#get_subjects_where_resource.md)
  * [get\_triples](SimpleGraph#get_triples.md)
  * [has\_literal\_triple](SimpleGraph#has_literal_triple.md)
  * [has\_resource\_triple](SimpleGraph#has_resource_triple.md)
  * [has\_triples\_about](SimpleGraph#has_triples_about.md)
  * [is\_empty](SimpleGraph#is_empty.md)
  * [make\_resource\_array](SimpleGraph#make_resource_array.md)
  * [merge](SimpleGraph#merge.md)
  * [qname\_to\_uri](SimpleGraph#qname_to_uri.md)
  * [reify](SimpleGraph#reify.md)
  * [remove\_all\_triples](SimpleGraph#remove_all_triples.md)
  * [remove\_literal\_triple](SimpleGraph#remove_literal_triple.md)
  * [remove\_property\_values](SimpleGraph#remove_property_values.md)
  * [remove\_resource\_triple](SimpleGraph#remove_resource_triple.md)
  * [remove\_triples\_about](SimpleGraph#remove_triples_about.md)
  * [replace\_resource](SimpleGraph#replace_resource.md)
  * [set\_namespace\_mapping](SimpleGraph#set_namespace_mapping.md)
  * [subject\_has\_property](SimpleGraph#subject_has_property.md)
  * [to\_html](SimpleGraph#to_html.md)
  * [to\_json](SimpleGraph#to_json.md)
  * [to\_ntriples](SimpleGraph#to_ntriples.md)
  * [to\_rdfxml](SimpleGraph#to_rdfxml.md)
  * [to\_turtle](SimpleGraph#to_turtle.md)
  * [update\_prefix\_mappings](SimpleGraph#update_prefix_mappings.md)
  * [uri\_to\_qname](SimpleGraph#uri_to_qname.md)
# Constructor Detail #

## QueryProfile ##

```
public QueryProfile(mixed uri, mixed credentials, mixed request_factory)
```

Create a new instance of this class<dl>
<dt>Param:</dt>
<dd>string uri URI of the query profile</dd>
<dd>Credentials credentials the credentials to use for authenticated requests (optional)</dd>
</dl>


# Method Detail #

## add\_field\_weight ##

```
public void add_field_weight(mixed name, mixed weight)
```

Adds the field and weight to the query profile<dl>
<dt>Return:</dt>
<dd>string URI of the added field weight</dd>
</dl>


## copy\_to ##

```
public void copy_to(mixed new_uri)
```

Copies the field weights and other properties into new query profile.
Any URIs that are prefixed by the source query profile URI will be converted to
be prefixed with this query profile's URI

For example
> http://example.org/source/queryprofile/1#name
Would become
> http://example.org/destination/queryprofile/1#name<dl>
<dt>Return:</dt>
<dd>QueryProfile</dd>
</dl>


## remove\_field\_weight ##

```
public void remove_field_weight(mixed name)
```

Removes the weight for the named field<dl>
<dt>Return:</dt>
<dd>nothing</dd>
<dt>Author:</dt>
<dd>Ian Davis</dd>
</dl>




Generated by [PHPDoctor 2RC2](http://phpdoctor.sourceforge.net/)