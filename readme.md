# Reporting library


Reporting library is created as libphutil library (see https://secure.phabricator.com/book/libphutil/article/overview/). For installation library follow instructions below:

**1. Download reporting library from:** https://github.com/xjagos/reporting-lib.git \
&nbsp;&nbsp;&nbsp; *git clone https://github.com/xjagos/reporting-lib.git*

**2. Go to folder: reporting-lib**

**3. Create library**\
&nbsp;&nbsp;&nbsp; In reporting-lib/ run: *arc liberate src/*\
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; If you get error:\
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; *ARC: Cannot mix L and E*\
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; *UNIX: Success*\
&nbsp;&nbsp;&nbsp; run: *alias arc='/..path to../arcanist/bin/arc'* and run: *arc liberate src*/

**4. Type library name**\
&nbsp;&nbsp;&nbsp; You will get a prompt like this:\
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; *No library currently exists at that path...*\
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; *Creating new libphutil library in '/some/path/libcustom/src'.*\
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; *Choose a name for the new library.*\
\
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; *What do you want to name this library?*\
\
&nbsp;&nbsp;&nbsp; Type: *reporting-lib*

**5. Set path to phabricator/src**\
&nbsp;&nbsp;&nbsp;In file: *reporting-lib/.arcconfig* edit path: *"../phabricator/src/"* to your current path

**6. Set configuration**\
&nbsp;&nbsp;&nbsp;Copy folder: *reporting-lib/conf/custom* to: *phabricator/conf*\
&nbsp;&nbsp;&nbsp;Go to: *phabricator/conf/local*\
&nbsp;&nbsp;&nbsp;Edit (create) file: *ENVIRONMENT* and add row: *custom/libconfig*

**7. Copy CSS file**\
&nbsp;&nbsp;&nbsp;Copy file: *reporting-lib/conf/reporting-styles.css* to: *phabricator/webroot/rsrc/css*

**8. Rebuild celerity map**\
&nbsp;&nbsp;&nbsp;In: *phabricator/* run: *./bin/celerity map*


Reporting library will be added to phabricator. You will find it in Applications by name Reporting.
