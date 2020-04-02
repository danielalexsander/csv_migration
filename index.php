<?php
    //Database Name
    $database_name = "test_csv";

    //Database Connection
    $conn = mysqli_connect("192.168.15.102", "root", "", $database_name);

    if(isset($_GET['table'])) {
        $table_name = $_GET['table'];
    }

    $fields = array();
    $sql = "SHOW COLUMNS FROM " . $database_name . "." . $table_name . "";
    $result = mysqli_query($conn, $sql);
    
    while($row = mysqli_fetch_array($result)) {

        if($row['Field'] != 'id') {
            $fields[] = $row['Field'];
        }
    }
    
    /*Show Table Columns Name:
    $c = 0;
    while($c < count($fields)) {
        echo "<br>" . $fields[$c];
        $c++;
    }*/

    if (isset($_POST["import"])) {
        
        $fileName = $_FILES["file"]["tmp_name"];
        
        if ($_FILES["file"]["size"] > 0) {
            
            //OPTIONAL
            $sql = "DELETE FROM " . $table_name . "";
            $result = mysqli_query($conn, $sql);
            //OPTIONAL

            $file = fopen($fileName, "r");
            $n = 0;

            //You can choose beetween ; or , or another char
            while (($column = fgetcsv($file, 10000, ";")) !== FALSE) {

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

                
                if (! empty($result)) {
                    $type = "success";
                    $message = "The CSV data was successfully imported to the database!";
                } else {
                    $type = "error";
                    $message = "Error: The CSV data wasn't imported to the database!";
                }
            }
        }
    }

?>

<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="style.css">
<head>
<script src="jquery-3.2.1.min.js"></script>
</head>
<body>
    <h2>Import CSV File to Mysql Using PHP</h2>
    
    <div id="response" class="<?php if(!empty($type)) { echo $type . " display-block"; } ?>"><?php if(!empty($message)) { echo $message; } ?></div>
        <div class="outer-scontainer">
            <div class="row">
                <form class="form-horizontal" action="" method="post" name="frmCSVImport" id="frmCSVImport" enctype="multipart/form-data">
                    <div class="input-row">
                        <label class="col-md-4 control-label">Choose CSV File</label> 
                        <input type="file" name="file" id="file" accept=".csv">
                        <br>
                        <button type="submit" id="submit" name="import" class="btn-submit">Import</button>
                        <br>
                    </div>
                </form>
            </div>
        <?php
            $sqlSelect = "SELECT * FROM " . $table_name . "";
            $result = mysqli_query($conn, $sqlSelect);
            
        if (mysqli_num_rows($result) > 0) {
            ?>
            <table id='userTable'>
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
            <?php
                while ($row = mysqli_fetch_array($result)) {
                    ?>
                    <tbody>
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
</body>
</html>