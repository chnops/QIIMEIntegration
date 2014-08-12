QIIMEIntegration code
=====================

### Controllers
* Controllers are responsible for user interaction
* Controllers are the only classes to interact with the superglobals, $_SESSION, $_GET, and $_POST
* Controllers generate most of the HTML (with help from the views)

### Models
* The most important model class is the Project, which accepts input from the Controller, and coordinates everything else
* The Workflow object is used to provide a Project object to the Controller

### OperatingSystem (in Models)
* The OperatingSystem is coordinated by the Project, and by the Roster (in Utils)
* All system() and exec() commands are contained in this class.
* It is responsible for abstracting OS behavior, like creating and listing directories

### Database
* The Database is coordinated by the Project
* It keeps track of 
	* users
	* projects
	* uploaded/downloaded files
	* script runs

### Scripts (in Models)
* Scripts are coordinated by the Project
* Each script represents a QIIME python script, such as split_libraries.py
* Each script has names and identifiers
* Each script has a collection of Parameters
* Most concrete Script classes are stored in the \Models\Scripts\QIIME namespace

### Parameters (in Scripts)
* Parameters are coordinated by their Script
* A Parameter usually has a name and a value
* It is responsible for its own rendering as an HTML form element, and as part of an OperatingSystem command
* It is responsible for its own validation

### Utils
* Helper provides several convenience functions, accessible to all other classes
* Roster manages users, i.e. user creation and logging in
