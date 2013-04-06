========================
TechDivision.Neos.Search
========================

The TechDivision.Neos.Search package provides a frontend search interface for TYPO3 Neos. TYPO3 Neos
Nodes get indexed are search and findable. The results depends on the context. A visitor cannot find
Nodes which are not in the live workspace(default). Publishing pages with node changes automatically update
the node documents in index. There is no cron job needed to keep the index up to date. There is a command controller
to add/update/remove all nodes in index.

This package uses the TechDivision.Search package as search backend interface which requires by default the solr php
extension.


Installation
------------

This package and it's dependency the "TechDivision.Search" package are not added to packagist.org so far. So you have to
edit the composer.json of your Neos instance in the base folder to add the needed repositories.

Add the following repositories to repository section like below:

	"repositories": [
		{
			"type": "git",
			"url": "http://192.168.1.106/Neos.git"
		},
		{
			"type": "git",
			"url": "http://192.168.1.106/Search.git"
		}
	]

If you added the repositories you have to require this package with the following command in the instance folder:

	composer require techdivision/neos-search \*


Integration
-----------

To use the provided front-end plugin you have to integrate the needed TypoScript. Add the following line to the
TypoScript of your Site package. For example in the "TYPO3.NeosDemoTypo3Org" Site in the file Resources / Private /
TypoScripts / Library / Root.ts2

	include: resource://TechDivision.Neos.Search/Private/TypoScript/Root.ts2

Now you are able to add the front end plugin in the backend.

marita


Search for other NodeTypes
--------------------------

It is possible to add other NodeTypes to the search index. Look into the Settings.yaml how to configure it.


Search for other Models
-----------------------

By default this package provides a search for Nodes of the type "TYPO3/TYPO3CR/Domain/Model/Node". It is possible
to add other models. Just implement the factory interfaces and extend them in your package. The Interfaces to
implement and factories to extend are listed below:

- \TechDivision\Neos\Search\Factory\ResultFactoryInterface
- \TechDivision\Neos\Search\Factory\ResultFactory
- \TechDivision\Neos\Search\Factory\DocumentFactoryInterface
- \TechDivision\Neos\Search\Factory\DocumentFactory

For using your own factories look into the Configuration/Objects.yaml how to configure that.

Use other search backend
------------------------

The search backend in the TechDivision.Search package is completely convertible. Look into it's documentations
to learn how to add an other search backend implementation.
For using your own search backend look into the Configuration/Objects.yaml how to configure that.


Testing
-------

The TechDivision.Neos.Search is 100% test covered by unit tests. Only the command controller is not covered.
This command controller was only for simple testing and debugging and should get removed in future.

The functional tests are currently not suitable because in testing context the database is set up but not filled
with nodes. Probably i'll find a solution for this.


Design decisions
----------------

As search interface I used the TechDivision.Search package. The TechDivision.Search package provides a
generic search interface for (probably) various search backends.

This package is for TYPO3 Neos. Pages in Neos are represented as Nodes with the NodeType
TYPO3.Neos.NodeTypes:Page. So every search result got it's "Page Node". By default only Nodes are supported for
indexing and searching. Because there are various interfaces used and the implementation is selected in Objects.yaml
there is the possibility to index and search for other models by extending the existing source in your own package.

To get most possibilities every node gets discrete indexed and searched. The results are the most suitable nodes and
it's page reduced by page. In future here should be the opportunity to change this to add all nodes by page as one
document to search index by configuration.


Why this namespace?
-------------------

Until now this is a non-corporate project. I chose this namespace to participate at a company internal contest.


Licence
-------

This belongs to the TYPO3 Flow package "TechDivision.Search"

It is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License,
either version 3 of the License, or (at your option) any later version.

Copyright (C) 2013 Matthias Witte
http://www.matthias-witte.net