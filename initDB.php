<?php
//Initializes the database
//NEED TO HIDE PASSWORDS (put in .gitignore...or just host?)
$users = new mysqli("localhost:3306", "root", "");
//Create database
$createDB = $users->query("CREATE DATABASE Users");
if(!$createDB)
{
    $databases = $users->query("SHOW DATABASES");
    echo var_dump($databases->fetch_all());
}
else
{
    echo "Successfully created database";
}

//Select database
$users->select_db('users');
//Create tables
$createTable = $users->query("CREATE TABLE login_info (Username varchar(255), Passcode varchar(255))");
if(!$createTable)
{
    $getDBTables = $users->query("SELECT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS");
    echo var_dump($getDBTables->fetch_all());
}
else
{
    "Successfully created table";
}

//Add admin
$insertUser = $users->query("INSERT INTO login_info (Username, Passcode) VALUES ('admin', 'password')");
if(!$insertUser)
{
    $userList = $users->query("SELECT * FROM login_info");
    echo var_dump($userList->fetch_all());
}
else
{
    echo "Successfully added user";
}

$users->close();
?>