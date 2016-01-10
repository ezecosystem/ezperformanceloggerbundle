A bundle dedicated to measuring performances of eZ Publish 5 websites
=====================================================================

This bundle is the "eZ5 port" of the ezperformancelogger extension for eZ Publish, available at
https://github.com/gggeek/ezperformancelogger.

To work at all, it in fact depends on the ezperformancelogger extension to be installed.

The base idea is that code duplication is avoided by keeping all existing ezperformancelogger php code intact, and just
providing a thin integration layer in the bundle.
This has the nice benefit of keeping compatibility with pure-legacy and mixed-mode installations.

NB: if you have an eZ Publish 5 installation which runs in "pure legacy" mode, you should *NOT* use this bundle.

Installation instructions
-------------------------

Installation of both this bundle and the associated extension is normally handled through composer - the developer has
almost nothing to do - for some definition of 'almost nothing' ;-)

Head on to [INSTALL.md](INSTALL.md) for the quirky details.
