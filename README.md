# ICT30010 CMS (Back-end) [Development]
ICT30001 Content Management System (Development)

## Requirements
- XAMPP
- Any Code Editor (Prefer Visual Studio Code)

## Installation
1. Clone the repo to local
2. Open the repo in any editor, e.g. [Visual Studio Code](https://code.visualstudio.com/)
3. Navigate to app/Config, edit the file called Database.php, change your database login credential in the array variable called ``` $default ```
4. In Visual Studio Code, open up the built-in terminal, and type the following code to create and seed the database
    ```sh
    php spark migarte
    ```
5. Once migrated, enter the following code in terminal
    ```sh
    php spark db:seed UserSeeder
    ```
6. After seeded, make sure the ```XAMPP``` is up and runnning, and run the following command in terminal to run the framework
    ```sh
    php spark serve
    ```
7. Open the browser, type in the address http://localhost:8080, and you may see the Home page loads out

# Used-framework
-  [CodeIgnitor 4](https://codeigniter.com/)
