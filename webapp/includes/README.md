QIIMEIntegration code
=====================

### Controllers
* Controllers are responsible for user interaction
* Controllers are the only classes to interact with the superglobals, $_SESSION, $_REQUEST, and $_POST
* Controllers generate most of the HTML (with help from the views)

### Models
* The most important model class is the Project, which accepts input from the Controller, and coordinates everything else
* The Controller gets its Project object from a Workflow object

### OperatingSystem (in Models)
* The OperatingSystem is coordinated by the Project, and by the Roster (in Utils)
* All system commands are contained in this class.

### Database
* The Database is coordinated by the Project
* It stores users, projects, uploaded/downloaded files, and script runs

### Scripts (in Models)
* Scripts are coordinated by the Project
* Each script represents a QIIME python script, such as split_libraries.py
* Each script contains Parameters
* Each script also has names and identifiers
* Most specific Script classes are stored in the \Models\Scripts\QIIME namespace

### Parameters (in Scripts)
* Parameters are coordinated by their Script
* A Parameter usually has a name and a value
* It is responsible for its own rendering as an HTML form element, and as part of an OperatingSystem command
* It is responsible for its own validation

### Utils
* Helper provides several convenience functions, accessible to all other classes
* Roster manages users, i.e. user creation and logging in
