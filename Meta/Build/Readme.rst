Deployment
==========

This build script creates a TYPO3 Neos instance and installs the packages with two lines:

	ant init-instance

	ant install-packages

It works on my machine but there are some "targets" in this build file which are pretty dirty. So probably it's
not working at your machine. This comes to you without guarantees.

Be careful this script probably removes files from your filesystem. Please check the build.default.properties file
to set the correct directories.

Licence
-------

This belongs to the TYPO3 Flow package "TechDivision.Search"

It is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License,
either version 3 of the License, or (at your option) any later version.

Copyright (C) 2013 Matthias Witte
http://www.matthias-witte.net