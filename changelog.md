### Version 2.2 of the Moodle WordSelect question type by Marcus Green. July 2018
Significant new feature, wordpenalty. Configure the fractional value to be deducted
for each incorrectly selected word. Previously this was fixed at 1, i.e. for every
incorrectly selected word one point was deducted down to zero. This could result
in rather harsh grading. Now it can be configured as a fraction e.g. .5 of a mark
deducted for each incorrect selection. Implemented as a list of percentages in the editing
form called incorrect selection penalty. Thanks to to the person who suggested this feature.
Thanks to Daniel Thies for help with testing.

Previously the penalty for incorrect attempts in interactive mode was ignored. Now the percentage
is used to deduct marks for each submission.

### Version 2.1 of the Moodle WordSelect question type by Marcus Green. May 2018
Added privacy classes as part of GDPR compliance. Further inclusion of tags such as
setting color, more credit to lethevinha for that work.

### Version 2.0 of the Moodle WordSelect question type by Marcus Green. April 2018
Multiword mode. If any text is surrounded by double delimiters e.g. [[cat]] it will be 
treated as correct, and any selectable non correct text must be given single delimiters
e.g. [mat]. Text can thus include multiple words. CSS Formatting indicates which text is selectable.

Updated phpdocs comments, Added significant new behat and phpunit tests. Did some testing
to check MathJax expressions will render as correct gaps in normal and multiword mode

New [w] icon with a hint of red. Added an svg vector version so no pixelation on zoom. 
Credit to Troy Patterson for ideas and inspiration on the icon

Huge credit to https://github.com/lethevinh for ideas, code and behat tests for ensuring
formatting tags such as &lt;b&gt; etc are retained and other stuff.

With thanks to German Valero for substantial additions to the documentation at
https://docs.moodle.org/en/Wordselect_question_type

### Version 1.1 of the Moodle WordSelect question type by Marcus Green. July 2017
Fixed a bug that prevented audio files working correctly in the question text. Thanks to Matthias Giger for 
reporting this. Fixed an issue that prevented any files being inserted into the introduction area. Improved
phpdoc of the source code.

### Version 1.0 of the Moodle WordSelect question type by Marcus Green. Sept 2016
Thanks for advice and support to Tim Hunt,Nadav Kavalerchik,Matthias Giger,German Valero and others in 
the Moodle forums.

It is not possible to insert images into the welcome text area using the editor menu, but you can
paste images into that area.
