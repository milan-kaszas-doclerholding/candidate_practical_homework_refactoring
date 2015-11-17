#jvuillemin remarks:
###Scalable system of batch classes launching process classes ...
* to install correctly, **please run composer update** after moving to my branch **for autoload regen** (psr4 updates)
* changed composer.json (psr4 namespaces + allowed myself to require php>=5.4.0 for using traits)
* **did not** changed apicall.php and config.php as requested, but changed file location for coherent namespace
* batch and process classes abstractions (abstracts, interfaces)
* batch output factory: 
    - if testing under windows CLI, run ```$languageBatchBo = new \Language\LanguageBatchBo(Batch\Output\OutputFactory::TYPE_CLI);```
    - if testing under linux CLI, keep as it is, colorized output will be used
* api and config singleton accessors (singletonable trait)
* absolutely no time to provide automated tests *(huge workload in my current position, really sorry)*
* feel free to [contact me](mailto:ekkinox@gmail.com) if any questions

#task:
refactor the languagebatchbo!
the goals are:
* increase the inner quality of the code and
* (optional) increase test coverage with unit tests

#rules:
* create local git repo for the project
* commit after every step when the system is in working condition
* the interface of the languagebatchbo can't be changed (the generate_language_files.php should remain the same), but (of course) it's content can change and can split into new classes.
* the apicall, and config classes are simplified versions of the original ones, they can not be changed
* the exceptions can be changed
* the console output of can be changed
* only phpunit can be used for testing
* commenting is not necessary
* you can clone this repo, but the homework should be sent to us through email (with the git files)