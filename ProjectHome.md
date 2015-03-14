Moriarty is a simple PHP library for accessing the [Talis Platform](http://www.talis.com/platform). It follows the Platform API very closely and wraps up many common tasks into convenient classes while remaining very lightweight. It also provides some simple RDF classes that are based on the excellent ARC2 class library. Moriarty is in continual beta, subject to occasional rapid bursts of change.

Moriarty is useful for
  * accessing core [Store](Store.md) services including [sparql](SparqlService.md), [contentbox querying](Contentbox.md), [facetting](FacetService.md) and [augmentations](AugmentService.md)
  * creating [changesets](ChangeSet.md) and [submitting data to stores](Metabox.md)
  * working with store [configuration](Config.md), [field/predicate maps](FieldPredicateMap.md) and [query profiles](QueryProfile.md)
  * managing data in stores using [batch jobs](JobQueue.md) and [snapshots](Snapshots.md)
  * manipulating generic RDF data with the [SimpleGraph](SimpleGraph.md) implementation
  * building data rich applications with simple but powerful tools like [DataTable](DataTable.md) and [GraphPath](GraphPath.md)

You may want to read the [Getting Started](GettingStarted.md) guide first.

Moriarty is being used in lots of small projects, but significantly it is also in the core of two of Talis' most important products: [Talis Prism](http://www.talis.com/prism) and [Talis Aspire](http://www.talis.com/aspire).

Recent blog postings:

  * [Moriarty DataTables: Active Record for RDF](http://blogs.talis.com/n2/archives/965)
  * [Using Moriarty for Serving Linked Data](http://blogs.talis.com/n2/archives/872)
  * [Moriarty Progress Report](http://blogs.talis.com/n2/archives/518)
  * [Alternative to CURL in Moriarty](http://blogs.talis.com/n2/archives/89)

