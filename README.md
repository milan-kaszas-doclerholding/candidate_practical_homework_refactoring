#Jvuillemin homework remarks: system of batch classes launching process classes
* changed composer.json (psr4 namespaces + allowed myself to require php>=5.4.0 for using traits)
* DID NOT changed ApiCall.php and Config.php, but changed file location for coherent namespace
* batch and process classes abstractions (abstracts, interfaces)
* batch output factory
* Api and Config singleton accessors (Singletonable trait)
* absolutely no time to provide automated tests (huge workload in my current position, really sorry)
* feel free to [contact me](mailto:ekkinox@gmail.com) if any questions

#Task:
Refactor the LanguageBatchBo!
The goals are:
* increase the inner quality of the code and
* (optional) increase test coverage with unit tests

#Rules:
* Create local git repo for the project
* Commit after every step when the system is in working condition
* The interface of the LanguageBatchBo can't be changed (the generate_language_files.php should remain the same), but (of course) it's content can change and can split into new classes.
* The ApiCall, and Config classes are simplified versions of the original ones, they can not be changed
* The exceptions can be changed
* The console output of can be changed
* Only PHPUnit can be used for testing
* Commenting is not necessary
* You can clone this repo, but the homework should be sent to us through email (with the git files)