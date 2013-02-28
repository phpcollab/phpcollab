README

In this package you will find all that it is necessary to build of the pages

There are 2 parts:
- the page (page.php) with one $blockPage object
- and the blocks (list and sheet/form) with $block1, $block2... objects

All blocks have options
- toggle on/off
- icons 0/1

The list block have options
- no checkbox
- no sorting
- limit number of items

With the last version of the file block.class.php, all the elements to build the blocks list/sheet are in POO (no more html, only "a href..." appears in the code, I will see to also make a function for the links)

I must still work on the functions for the form (some exceptions with search, reports...)

I will add more details later on (to describe a standard page and functions of the class)