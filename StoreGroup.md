**note:** this page is automatically generated from embedded documentation in the PHP source.

# Overview #

## Constructors ##
  * [StoreGroup](#StoreGroup.md) - Create a new instance of this class
## Methods ##
  * [add\_store\_by\_uri](#add_store_by_uri.md) - Add a store to this group.
  * [get\_config](#get_config.md) - Obtain a reference to this store group's configuration
  * [get\_contentbox](#get_contentbox.md) - Obtain a reference to this store group's contentbox
  * [get\_sparql\_service](#get_sparql_service.md) - Obtain a reference to this store group's sparql service
  * [remove\_all\_stores](#remove_all_stores.md) - Remove all stores from this group.

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

## StoreGroup ##

```
public StoreGroup(mixed uri, mixed credentials)
```

Create a new instance of this class<dl>
<dt>Param:</dt>
<dd>string uri URI of the store group</dd>
<dd>Credentials credentials the credentials to use for authenticated requests (optional)</dd>
</dl>


# Method Detail #

## add\_store\_by\_uri ##

```
public void add_store_by_uri(mixed store_uri)
```

Add a store to this group. Save the changes by calling put\_to\_network.<dl>
<dt>Param:</dt>
<dd>string store_uri the URI of the store to add to this group.</dd>
</dl>


## get\_config ##

```
public void get_config()
```

Obtain a reference to this store group's configuration<dl>
<dt>See:</dt>
<dd><a href='http://n2.talis.com/wiki/Store_Configuration'>http://n2.talis.com/wiki/Store_Configuration</a></dd>
<dt>Return:</dt>
<dd>StoreGroupConfig</dd>
</dl>


## get\_contentbox ##

```
public void get_contentbox()
```

Obtain a reference to this store group's contentbox<dl>
<dt>See:</dt>
<dd><a href='http://n2.talis.com/wiki/Contentbox'>http://n2.talis.com/wiki/Contentbox</a></dd>
<dt>Return:</dt>
<dd>Contentbox</dd>
</dl>


## get\_sparql\_service ##

```
public void get_sparql_service()
```

Obtain a reference to this store group's sparql service<dl>
<dt>See:</dt>
<dd><a href='http://n2.talis.com/wiki/Store_Sparql_Service'>http://n2.talis.com/wiki/Store_Sparql_Service</a></dd>
<dt>Return:</dt>
<dd>SparqlService</dd>
</dl>


## remove\_all\_stores ##

```
public void remove_all_stores()
```

Remove all stores from this group. Save the changes by calling put\_to\_network.



Generated by [PHPDoctor 2RC2](http://phpdoctor.sourceforge.net/)