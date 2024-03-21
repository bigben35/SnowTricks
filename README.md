# SnowTricks

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/0f656d90e0354bca9fc9f1163b0eb103)](https://app.codacy.com/gh/bigben35/SnowTricks/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

# My Symfony Project 
SnowTricks - Project 6 PHP/SYMFONY OpenClassroomsTraining

# Description
SnowTricks is a collaborative website to promote Snowboarding to the general public and assist in learning tricks.

# Prerequisites
PHP 8.0.12  
Symfony 6

# Installation
Clone the Git repository to your local machine : **git clone https://github.com/bigben35/SnowTricks.git**   
Navigate to the project folder : **cd mon-projet-symfony**   
Install dependencies with Composer : **composer install**    
Run **composer require symfony/console** to use the symfony console command instead of php/bin console (personal preference). 
Create a database in PhpMyAdmin (for example) with the desired name. It will be used in the .env file to establish the connection between the application and the database.    
Create the database : **symfony console doctrine:database:create**      
Run migrations : **symfony console doctrine:migrations:migrate**     
Load fixtures (demo data) : **symfony console doctrine:fixtures:load**, then edit a trick to add one or more illustrations and one or more videos.   
Start the Symfony server : **symfony server:start**    

And there you go! You can now access the application by navigating to http://localhost:8000 in your browser.  
You can switch to https with the command : **symfony server:ca:install**

Don't forget to modify the .env file with your configuration information before launching the installation.

You can log in as an administrator with username:**admin** -  password:**admin**, as an user ("ROLE_USER") with username:**bo** and password:**azerty** or create your own user account. 

# Email
For registration and sending a confirmation email, I used mailhog : **https://github.com/mailhog/MailHog** and here is the version I used: **MailHog_windows_amd64.exe**  
 


