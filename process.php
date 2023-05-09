<?php

/**
* [Convert .CSV data to MySQL]
* @package   [dancom]
* @category  [CSV]
* @author    Daniel Alexsander Inocêncio [daniel.alexsander00@hotmail.com]
* @copyright [Daniel Alexsander]
* @version   v2
* @since     22/12/2022
* @updated   09/05/2023
*/

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    //Database connection data
    $database_name  = $_POST['database'];
    $host           = $_POST['host'];
    $user           = $_POST['user'];
    $password       = $_POST['password'];
    $table_name     = $_POST['table_name'];

    //Database Connection
    $conn = mysqli_connect($host, $user, $password, $database_name);

    var_dump($conn);

    $fields = array();
    $sql = "SHOW COLUMNS FROM " . $database_name . "." . $table_name . "";
    $result = mysqli_query($conn, $sql);

    while($row = mysqli_fetch_array($result)) {

        if($row['Field'] != 'id') {
            $fields[] = $row['Field'];
        }
    }

    // Show Table Columns Name:
    /*
    $c = 0;
    while($c < count($fields)) {
        echo "<br>" . $fields[$c];
        $c++;
    }
    */

    //Send .csv File to PHP
    $fileName = $_FILES["file"]["tmp_name"];

    if($_FILES["file"]["size"] > 0) {

        $file = fopen($fileName, "r");
        $n = 0;

        //You can choose beetween ; or , or another char that separe the current .csv document
        while(($column = fgetcsv($file, 10000, ";")) !== FALSE) {

            $c          = 0; 
            $sql_data   = '';
            $item       = 0;

            while($c < count($fields)) { 

                //UTF8 Encode doesn't work all the time, if something goes wrong, you can cut it.
                $column[$item] = trim($conn->real_escape_string($column[$item]));

                $sql_data .= $fields[$c] . " = '" . utf8_encode($column[$item]) . "',";
                $item++;
                $c++;
            }

            $sqlInsert = "INSERT INTO " . $table_name . " SET
            " . substr($sql_data, 0, -1) . "
            ";

            $n += 1;

            if($n != 1) {
                $result = mysqli_query($conn, $sqlInsert);
            }

            /*
            if(!empty($result)) {
                $type = "success";
                $message = "The CSV data was successfully imported to the database!";
            } else {
                $type = "danger";
                $message = "Error: The CSV data wasn't imported to the database!";
            }
            */
        }
    }

    ?>

    <!DOCTYPE html>
    <html>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
        <link rel="stylesheet" href="style.css">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <script src="jquery-3.2.1.min.js"></script>
            <title>Registros Importados!</title>
        </head>
        <body class="background-padrao">
            <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-8 sucesso-aviso">
                    <div class="index-texto">
                        <h1>Sucesso!</h1>
                        <p class="index-paragrafo">
                            Os registros foram importados para a tabela: <b><?=$table_name?></b>
                        </p>
                        <p class="index-paragrafo">
                        Para importar novamente, basta clicar no botão “<b>Importar Novamente</b>” no final da página.
                        </p>
                    </div>
                </div>
                <div class="col-sm-2"></div>
            </div>

            <div class="sucesso-tabela">

            <?php

            $sqlSelect = "SELECT * FROM " . $table_name . "";
            $result = mysqli_query($conn, $sqlSelect);

            if(mysqli_num_rows($result) > 0) {

                ?>

                <table class='table table-bordered'>
                    <thead>
                        <tr>
                            <?php 

                                $c = 0; 
                                while($c < count($fields)) { 
                                    echo "<th>" . $fields[$c] . "</th>";
                                    $c++;
                                }

                            ?>
                        </tr>
                    </thead>
                    <tbody>
                <?php

                while($row = mysqli_fetch_array($result)) {
                    ?>

                        <tr>
                            <?php 

                            $c = 0; 
                            while($c < count($fields)) { 
                                echo "<td>" . $row[$fields[$c]] . "</td>";
                                $c++;
                            }

                            ?>
                        </tr>

                    <?php
                }

                ?>

                    </tbody>
                </table>

                <?php 
            }

            ?>

            </div>

            <div class="row">
                <div class="col-sm-2"></div>
                <div class="col-sm-8">
                    <center>
                        <a href="form.html" class="btn btn-default index-botao">Importar Novamente</a>
                    </center>
                </div>
                <div class="col-sm-2"></div>
            </div>
        </body>
    </html>

    <?php

}

?>