# Hackernews API

1. The database of the project is attached to your mail
2. Do composer update to install the vendor folder
3. The API postman collection is send to your email, you just need to import it in json format thats all
4. On the postman baseUrl, replace the localhost with your live api
   For example replace "http://127.0.0.1:5533/" with "https://yourdomain.com/" (dont forget the last slash, very important)
5. Also on the .env file, you need to replace "http://127.0.0.1:5533" too
6. The 2 GET requests are "fetch-data" and "update-author"
7. This is how you will use it on postman 
https://yourdomain.com/api/fetch-data
https://yourdomain.com/api/update-author

Immediately you send the first request by clicking the "Send" button on postman, check the database, it will updated the database tables (stories, comments, polls, h_jobs, authors)

If you send the second request (https://yourdomain.com/api/update-author) check the database and see that the authors table is updated with their details


## Create Jobs 
1. I wrote 2 cronjobs, one to get the items from the API and spread into their respective tables, the second cronjob is to update author tables. I can't do it in one cronjob because it might take time to return response.
2. To run the cronjob on your vscode terminal, kindly use this command "php artisan schedule:run" to run all the cronjobs and check your database for the update.

## Important Notice
1. I had to avoid duplicated before inserting into the database
2. I used 4 tables (stories, comments, authors, h_jobs, polls) to get the API spread across their respective tables with respect to their categories with indexes and foreign keys and also other tables like categories which stores the categories from the API.

