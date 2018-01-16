ODK version 1.0
===============

ODK: assign people to positions according to criteria
---------------------------------------------------------
Do you need to hire some people for some positions? Or maybe assign them tasks according to some criteria? Do you, in general, want to sort out open positions with applicants/candidates? ODK will help you do that, even in a large scale.

Installation
------------
1. Install "docker-compose"
2. docker-compose up

For details, check the INSTALL.md file.

What does it do
---------------
This application does exactly that:
1. insert open positions per institution (or, tasks to be assigned)
2. insert applicants (or, people to assign the tasks to)
3. rank the people (one integer per person / the bigger the number, the higher the ranking)
4. insert peoples preferences
5. Check the suggested distribution

The system will NOT assign anyone to a task/positions they don't have in their preferences. Therefore, it is possible to left some people unassigned and/or some positions empty.

For instance, assume a task A with only one position, task B with 2 positions. Assume also two applicants Alice ranked 10 and Bob ranked 5. Let's say Alice's preferences are 1. task A, 2. task B. Assume Bob's preference is only 1. task A. The system will suggest:
    Alice takes task A
    Bob takes nothing
This is not a flaw, it is a feature. The system will assign Alice her higher preference, since she has the highest rank. Then, the only available place for task A will be taken, so only task B will be available. Since Bob does not desire to be assigned task B, he will be left with out an assignment

Extra Features
--------------
Reset: You can delete all data if you like (careful).

Export as csv: All data can be exported as "csv" by clicking on the corresponding button at the top of each page.

Number of preferences per applicant: By default, each applicant can have up to 5 preferences. You can change this by modifying the "APPL_NUMBER_OF_CHOICES" in "www/config.php" file to any positive number.

Wording: If you don't like the texts and words on the UI or you need to adjust them to match your application, edit the "qqq/config.php" file. Always back up the old file before applying any changes.
  If some words are not in the file, you will find them at the top of the rest php files in "www/".
