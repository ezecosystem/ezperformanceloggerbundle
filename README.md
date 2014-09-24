A bundle dedicated to measuring performances of eZ Publish 5 websites
=====================================================================

This bundle is the "eZ5 port" of the ezperformancelogger extension for eZ Publish, available at
http://projects.ez.no/ezperformancelogger.

To work at all, it in fact depends on the ezperformancelogger extension to be installed.

The base idea is that code duplication is avoided by keeping all existing ezperformancelogger php code intact, and just
providing a thin integration layer in the bundle.
This has the nice benefit of keeping compatibility with pure-legacy and mixed-mode installations.

Installation of both this bundle and the associated extension is normally handled through composer - the developer has
almost nothing to do - for some definition of 'almost nothing' ;-)

!! IMPORTANT !!

Once you have installed this bundle, make sure ypu read the INSTALL_5x document found inside
ezpublish_legacy/extension/ezperformancelogger/, and carry out all the actions described there.
