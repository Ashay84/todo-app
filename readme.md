Requirements 

PHP 7.4.9

Laravel 8

Mysql 8

DB_NAME = todo_app

After cloning
    Copy .env.example and rename it to .env
    
    Create database with name todo_app
    
    Username and password from mysql server should match in .env
    
    run composer install
    
    then php artisan migrate --seed to add tables and test user data


Test user credentials seeded


email : test@app.com


password : 12345678


As a part of this, you will be expected to complete the following

    Functionality :
        Writing backend API for the To Do List application.
        Users Should be able to create a Task or Subtask
            POST {{url}}/api/task
        Users Should be able to delete a Task ( Soft Delete )
            DEL {{url}}/api/task/3
        Users Should be able to mark a Task Complete
            PATCH {{url}}/api/task/1
        If the main task is marked as completed, all the related sub tasks should be marked as completed.
        Users Should be able to View the list of all the Tasks and Subtasks that are pending. It is fair to assume that tasks will always be sorted based on `due-date` (ascending).
            GET {{url}}/api/task?status=PENDING
        Users should be able to filter Tasks based on `due-date` should be possible using the following - Today, This Week, Next Week, Overdue.
            GET {{url}}/api/task?due_date_in_words=Overdue
        Search (on `title`) should be available.
            GET {{url}}/api/task?title=wash

	Scheduler:
        All tasks which are soft deleted for more than a month should be permanently deleted from the system.
            run php artisan delete:soft_deleted_tasks to delete using command,cron needs to be setup for this command already added in Kernel file
	Task Properties
        A Task will have a `title` and `due-date`.
        There are only 2 states applicable for a task. Pending or Completed.
        Tasks can have related sub-tasks.


