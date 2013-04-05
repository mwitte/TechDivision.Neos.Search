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


Use other search backend
------------------------

The search backend in the TechDivision.Search package is completely convertible. Look into it's documentations
to learn how to add an other search backend implementation.
For using your own search backend look into the Configuration/Objects.yaml


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

This package is for TYPO3 Neos. Pages in Neos are represented as Nodes with the contentType
TYPO3.Neos.ContentTypes:Page. So every search result got it's "Page Node". By default only Nodes are supported for
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