Reporting library is created as libphutil library (see https://secure.phabricator.com/book/libphutil/article/overview/). For installation library follow instructions below:

1. Download reporting library from: https://github.com/xjagos/reporting-lib.git
git clone https://github.com/xjagos/reporting-lib.git

2. Go to folder: reporting-lib

3. Create library
reporting-lib/ $ arc liberate src/

If you get error: 

ARC: Cannot mix L and E
UNIX: Success

run: alias arc='/..path to../arcanist/bin/arc'

4. Type library name
You will get a prompt like this:

No library currently exists at that path...
Creating new libphutil library in '/var/www/html/myproject/reporting-lib/src'.
Choose a name for the new library.


    What do you want to name this library?


Type: reporting-lib

5. Set path to phabricator/src
In file: reporting-lib/.arcconfig edit path: ",,phabricator/src/" to your current path

6. Set configuration
Copy folder: custom from: reporting-lib/conf  to phabricator/conf
Go to folder: phabricator/conf/local 
Edit (create) file: ENVIRONMENT and add row: custom/libconfig

7. Copy CSS file
Copy reporting-lib/conf/reporting-styles.css to phabricator/webroot/rsrc/css

8. Rebuild celerity map
phabricator/ $ ./bin/celerity map


Reporting library will be added to phabricator. You will find it in Applications by name Reporting.
