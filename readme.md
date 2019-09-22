# Reporting library


Reporting library is created as libphutil library (see https://secure.phabricator.com/book/libphutil/article/overview/).

## Installation
For installation library follow instructions below:

**1. Download reporting library from:** https://github.com/xjagos/reporting-lib.git \
&nbsp;&nbsp;&nbsp; *git clone https://github.com/xjagos/reporting-lib.git*

**2. Go to folder: reporting-lib** \
&nbsp;&nbsp;&nbsp; *cd reporting-lib*

**3. Create library**\
&nbsp;&nbsp;&nbsp; In reporting-lib/ run: */arcanist/bin/arc liberate src/*

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

## Uninstallation
For uninstallation library follow instructions below:

**1. Remove reporting-lib directory**
&nbsp;&nbsp;&nbsp; *rm -r reporting-lib*

**2. Remove configuration file** \
&nbsp;&nbsp;&nbsp; Remove *phabricator/conf/custom/libconfig.conf.php* \
&nbsp;&nbsp;&nbsp; In: *phabricator/conf/local/ENVIRONMENT* remove row: custom/libconfig

**3. Remove CSS file**\
&nbsp;&nbsp;&nbsp;Remove *phabricator/webroot/rsrc/css/reporting-styles.css*

**4. Rebuild celerity map**\
&nbsp;&nbsp;&nbsp;Run: *./phabricator/bin/celerity map*

**5. Remove custom fields**\
&nbsp;&nbsp;&nbsp;See chapter Configuration

## Usage
You will find it in *Applications* by name *Reporting*. It consists of 2 submodules:

### 1. Employee workload
#### Main features
* shows hours of planned work for selected user and interval
* lists tasks for selected user and interval
* highlights tasks, which should have been already done

#### Custom fields
Employee workload works with custom fields which extends User and Task fields.

##### User fields
Custom fields can be set by editing user profile. There are fields:
1. overload-ratio - Determines part of work time spended on meetings, business trips etc.
2. worktime-tarif - Value between 0.0 - 1.0 for part-time or full-time work (0.5 = 4 hours, 1.0 = 8 hours).
3. access-control-list - List of users, who can view employee workload of this user.

##### Task fields
Custom fields can be set by editing task. There are fields:
1. estimated-completion-date - When task should be finished
2. estimated-time - Time estimated for implementation
3. estimated-time-testing - Time estimated for testing
4. actual-time - Time already spended by task implementation
5. actual-time-testing - Time already spended by task testing
6. tester

#### Usage
You can run it by Employee workload in Reporting module.
Select user and interval by Start and End date for which statistics should be shown.

### 2. Task tree
#### Main features
* shows estimated hours broken down into groups by status of selected task
* shows estimated/actual/estimated-testing/actual-testing time of selected task and total estimated/actual/estimated-testing/actual-testing time of task and its children

#### Custom fields
Task tree works with custom fields which extends Task object.

##### Task fields
Custom fields can be set by editing task. There are fields:
1. estimated-time - Time estimated for implementation
2. estimated-time-testing - Time estimated for testing
3. actual-time - Time already spended by task implementation
4. actual-time-testing - Time already spended by task testing

#### Usage
You can run it by Task tree in Reporting module.
Select task for which statistics should be shown. 
In the top table you can watch estimated hours broken down into groups by status of selected task. Under the table is graph showing selected task and it's children. There are 4 columns for each task in row:
1. Estimated: estimated time of selected task / total estimated time of task and all its children
2. Actual:  actual time of selected task / total actual time of task and all its children
3. Estimated - testing: estimated testing time of selected task / total estimated testing time of task and all its children
4. Actual - testing: actual testing time of selected task / total actual testing time of task and all its children
