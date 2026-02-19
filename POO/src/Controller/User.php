<?php

namespace App\Controllers;

use App\Core\Repository;

class User
{
   
    public function __construct(public string $name, public string $email)
    {

    }

    // get tout les utilisateurs
    public static function all()
    {
        // global $conexion;
        // $requete = $conexion->prepare("SELECT * FROM users;");
        // $requete->execute();
        // $AllUsers = $requete->fetchAll();
            $AllUsers = (new \App\Repository\UserRepository(\App\Core\Database::getInstance()->pdo))->getAllUsers();

        $html = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Document</title>
        </head>
        <body>
            '.var_dump($AllUsers).'
        </body>
        </html>
        ';

        return $html;
    }

}
