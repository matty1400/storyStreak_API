<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DB connect test</title>
</head>
<body>
    <div>
        <?php

use Illuminate\Support\Facades\DB;

        if(DB::connection()->getPdo()){
            echo "Succes".DB::connection()->getDatabaseName();
        }





        ?>

    </div>
    
</body>
</html>