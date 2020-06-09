### Version 2.44 of the Moodle WordSelect question type by Marcus Green. Jun 2020
Fixed an issue with embedded audio. If text appears after embedded audio selections
are ignored when the question is submitted.  This only happens when the multimedia filter
is turned on. I have moved the format code to later in the renderer and this fixes the issue.
My thanks to Henny Jellema for reporting this.

Thanks to Tim hunt for an update to styles.css to make CSS rules safer. This can help when
using a custom theme.

### Version 2.43 of the Moodle WordSelect question type by Marcus Green. Feb 2020
This version requires at least Moodle 3.7.0. It will not work correctly
with earlier versions.

valeriia-s reported that word selection would not work if a page contained
multiple instances of the question type. Tim Hunt advised me
on ways to address it and in selection.js. This bought in the need for Moodle 3.7.
https://github.com/marcusgreen/moodle-qtype_wordselect/issues/26

Fix for when forceclean is on from Hubong Nguen for the UK OU.
https://github.com/marcusgreen/moodle-qtype_wordselect/pull/23
There is a plan for forceclean to default to on.
https://tracker.moodle.org/browse/MDL-62352
Forceclean entirely broke Wordselect rendering (and other plugins).

Mahmoud Kassaei from the UK OU reported that trailing puncuation was hard to see on selected text
https://github.com/marcusgreen/moodle-qtype_wordselect/pull/24
The CSS has been adjusted to make it clearer.

Behat (automated) tests broke on read-only and Tim Hunt of the UK OU
contributed code as a fix.
https://github.com/marcusgreen/moodle-qtype_wordselect/pull/25

Added more unit tests.

### Version 2.42 of the Moodle WordSelect question type by Marcus Green. Nov 2019
Improved keyboard navigation, the arrow keys now move forward/backward/up/down. Many thanks to
Huong Nguyen for the coding, including behat tests and to the UK Open University (OU) for funding the
development.

Changed selection javascript to use amd technology, thanks to Mahmoud Kassaei of the OU for the suggestion to do that.

Thanks to Tim Hunt and Sam Marshall of the OU for reporting the issue on templatepath in mobile.php

Thanks to Tim hunt for the fix for 'show num parts correct' and the behat updates.


### Version 2.41 of the Moodle WordSelect question type by Marcus Green. Feb 2019
Added more sample english language questions.
Fixed line-height to ensure correct display in ios/mobile app
Fixed error eslint checker that const is a reserved word (in mobile.js)

### Version 2.4 of the Moodle WordSelect question type by Marcus Green. December 2018
Significant new feature: support for the Moodle Mobile App. Added a collection of
sample english language questions in
examples\en\english_language.xml that can be imported.

Briefly tested with the Embed question filter
https://moodle.org/plugins/filter_embedquestion
and StudentQuiz
https://moodle.org/plugins/mod_studentquiz

Addition of defensive code for issue https://github.com/marcusgreen/moodle-qtype_wordselect/issues/15

### Version 2.32 of the Moodle WordSelect question type by Marcus Green. December 2018
The clear incorrect responses and show number of correct responses checkboxes in Hints in the
Multiple tries section of the editing form had no effect. This bug only had an effect when
 using the Interactive with multiple tries question behaviour. The behaviour is now as
expected with selections being cleared and a count of correct responses being shown. My
thanks to Dr Anna Stefanidou for reporting this issue.

### Version 2.31 of the Moodle WordSelect question type by Marcus Green. August 2018
Fix to grading when using Interactive with multiple tries. Thanks again to Matthias Giger
for reporting this. Fixed wordpenalty in sample questions and added new behat and phpunit
tests.

### Version 2.3 of the Moodle WordSelect question type by Marcus Green. August 2018
Fix to the penalty applied when using the question behaviour Interactive with multiple
tries. Thanks to Matthias Giger and his students who reported that a single wrong
selection would reduce the grade to zero.  Code compliance fixes in the form of
eslinting of javascript.

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
Thanks for advice and support to Tim Hunt, Nadav Kavalerchik, Matthias Giger, German Valero and others in
the Moodle forums.

It is not possible to insert images into the welcome text area using the editor menu, but you can
paste images into that area.
