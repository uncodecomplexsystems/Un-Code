How do I install UN-CODE on my local machine as a developer or advanced user?

1. Download the UN-CODE source code here: https://github.com/uncodecomplexsystems/Un-Code/ (click "clone or download" and download as .zip file)
2. Install a local web server with Apache, PHP, MySQL and PHPMyAdmin, for example XAMPP (https://www.apachefriends.org)
3. Put the contents of the .zip file into your local server’s “htdocs” (If you are using a Linux-type system, please make sure, the subfolders “tmp”, “template_c” and “cache” are writeable.)
4. Start the web and database server (in XAMPP: select Apache + MySQL).
5. Within your browser, go to the PHPMyAdmin service (in XAMPP: "localhost/phpmyadmin/").
6. Create two databases. One for project data (e.g. “uncode-project”) and one for user data (e.g. “uncode-user”).
7. Click on the left side on your newly created “uncode-project” database. On the top go to “import”. Select the file “uncode-project.sql” from the UN-CODE .zip (step 1, subdirectory "install") and press OK. This creates the fundamental database structure for your projects.
8. Do the same step as (7) with the “uncode-user” database and import “uncode-user.sql”. This creates the fundamental database structure for your systems' users.

Congratulations, you just completed the installation! Now you can start using UN-CODE via your browser. If you use XAMPP and followed the above steps, just go to: “localhost/uncode” via your browser.
Have fun!