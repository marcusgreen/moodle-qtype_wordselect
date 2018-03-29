### Version 2.0 of the Moodle WordSelect question type by Marcus Green. March 2018
Multiword mode. If any text is surrouded by double delimiters e.g. [[cat]] it will be 
treated as correct, and any selectable non correct text must be given single delimters
e.g. [mat]. Text can thus include multiple words. Formatting indicates which text is selectable.

Updated phpdocs comment, behat and phpunit tests

Huge credit to https://github.com/lethevinh for ideas, code and behat tests for ensuring
formatting tags are retained and other stuff.

### Version 1.1 of the Moodle WordSelect question type by Marcus Green. July 2017
Fixed a bug that prevented audio files working correctly in the question text. Thanks to Matthias Giger for 
reporting this. Fixed an issue that prevented any files being inserted into the introduction area. Improved
phpdoc of the source code.

### Version 1.0 of the Moodle WordSelect question type by Marcus Green. Sept 2016
Thanks for advice and support to Tim Hunt,Nadav Kavalerchik,Matthias Giger,German Valero and others in 
the Moodle forums.

It is not possible to insert images into the welcome text area using the editor menu, but you can
paste images into that area.
