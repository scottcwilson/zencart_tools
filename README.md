# zencart_tools
Tools I use for managing the [Zen Cart documentation](https://github.com/zencart/documentation), which installed on [docs.zen-cart.com](https://docs.zen-cart.com/). 

### checkdoc
This script looks for a bunch of anti-patterns in the content folder, so that the documentation looks consistent. 

It is run from a shell window in `zencart_documentation/content`.

### build\_doc.php
This script creates the [configuration documentation](https://docs.zen-cart.com/user/admin_pages/configuration/) component of the [Zen Cart configuration documentation](https://docs.zen-cart.com/).

It should be installed in the admin folder of a new release and run from a browser window.

### checkdefines.php 
This script can be run from the admin_folder or the top level of the cart, to report on unused defines in those two areas respectively. 

It should be installed at the top level and in the admin folder of a new release and run from a browser window in these two locations.

### catalog\_find\_define
This script can be run on the output of `admin/checkdefines.php` to verify that a define which is not used in admin is also not used on the catalog side. 

It is run from a shell window in top level of a new release.

### find\_notifiers
This script builds the [list of notifiers](https://docs.zen-cart.com/dev/code/notifiers_list/) you see in the [Zen Cart documentation](https://docs.zen-cart.com/).
It is run from a shell window in top level of a new release.

# Tasks to Do at Release Time
- Search the documentation in https://github.com/zencart/documentation for the string RELEASETIME and update those files. 

- Run the script build\_doc.php as noted above.

- Install the View Schema mod and run it.
https://www.zen-cart.com/downloads.php?do=file&id=2270
Do an Inspect in Google Chrome on the output and copy the element
with id="pageWrapper" into a new file in zencart_documentation/content/dev/schema.  Remove the opening and closing div tags. 



