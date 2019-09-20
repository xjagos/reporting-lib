# Reporting library


Reporting library is created as libphutil library (see https://secure.phabricator.com/book/libphutil/article/overview/).

## Installation
For installation library follow instructions below:

**1. Download reporting library from:** https://github.com/xjagos/reporting-lib.git \
&nbsp;&nbsp;&nbsp; *git clone https://github.com/xjagos/reporting-lib.git*

**2. Go to folder: reporting-lib** \
&nbsp;&nbsp;&nbsp; *cd reporting-lib*

**3. Create library**\
&nbsp;&nbsp;&nbsp; In reporting-lib/ run: */arcanist/bin/arc liberate src/*\

**4. Set path to phabricator/src**\
&nbsp;&nbsp;&nbsp;In file: *reporting-lib/.arcconfig* edit path: *"../phabricator/src/"* to your current path

**5. Set configuration**\
&nbsp;&nbsp;&nbsp;Copy folder: *reporting-lib/conf/custom* to: *phabricator/conf*\
&nbsp;&nbsp;&nbsp;Go to: *phabricator/conf/local*\
&nbsp;&nbsp;&nbsp;Edit (create) file: *ENVIRONMENT* and add row: *custom/libconfig*

**6. Copy CSS file**\
&nbsp;&nbsp;&nbsp;Copy file: *reporting-lib/conf/css/reporting-styles.css* to: *phabricator/webroot/rsrc/css*

**7. Rebuild celerity map**\
&nbsp;&nbsp;&nbsp;In: *phabricator/* run: *./bin/celerity map*

Reporting library will be added to phabricator. You will find it in Applications by name Reporting.

## Configuration
It is necessary to create custom fields for proper working of library. For more info about custom fields see: https://secure.phabricator.com/book/phabricator/article/custom_fields/

**1. Create Custom Maniphest fields**\
&nbsp;&nbsp;&nbsp; Copy content of: *reporting-lib/conf/custom-fields/maniphest.custom-field-definitions.json* to: 
\
&nbsp;&nbsp;&nbsp; *Phabricator -> Config -> Application settings -> Maniphest -> maniphest.custom-field-definitions -> Database value*
\
\
&nbsp;&nbsp;&nbsp; Save configuration by button *Save Config Entry*.

**2. Create Custom User fields**\
&nbsp;&nbsp;&nbsp; Copy content of: *reporting-lib/conf/custom-fields/user.custom-field-definitions.json* to: 
\
&nbsp;&nbsp;&nbsp; *Phabricator -> Config -> Application settings -> User Profiles -> user.custom-field-definitions -> Database value*
\
\
&nbsp;&nbsp;&nbsp; Save configuration by button *Save Config Entry*.

## Update
For updating you need:

**1. Update Repository**\
&nbsp;&nbsp;&nbsp; In: *reporting-lib/* run: *git pull origin master* 

**2. Run: arc liberate src/**

## Usage
You will find it in *Applications* by name *Reporting*. It consists of 3 submodules:

### 1. Employee workload
#### Main features
* shows hours of planned work for selected user and interval
* lists tasks for selected user and interval
* highlights tasks, which should have been already done

#### Custom fields
Employee workload works with custom fields which extends User and Task fields.

##### User fields
Custom fields can be set by editing user profile. There are fields:
1. Employee overload ratio 
2. Employee worktime tarif
3. Acces control list

##### Task fields
Custom fields can be set by editing task. There are fields:
1. Estimated completion date
2. Estimated impl. time
3. Estimated testing time
4. Actual impl. time
5. Actual testing time
6. Tester

#### Usage

### 2. Task tree

### 3. Projects implementation chart
