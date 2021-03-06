<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
} 
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Verificacion de Ip</title>
    <link rel="stylesheet" href="./css/style.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="./js/index.js"></script>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="mt-5 mb-3 clearfix">
                    <h1 class="my-5">Hola, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Bienvenido al sitio.</h1>
                        <h2 class="pull-left">Detalles de IP</h2>
                        <a href="create.php" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Agregar nueva IP</a>
                    </div>
                    <?php
                    require_once "./config/config.php";
                    require_once "emails.php";
                    $to = $all;
                    $subject = "Importante - Servidor no responde";
                    $headers = "From: kenia.gutierrez@farmavalue.biz";
                    $txt = "Los servidore con numero de IP: ";
                    $ip_off = $all_ip_off = "";
                    
                    echo header("refresh: 300");
                    $sql = "SELECT * FROM ip";
                    if ($result = $mysqli->query($sql)) {
                        if ($result->num_rows > 0) {
                            echo '<table class="table table-bordered table-striped">';
                            echo "<thead>";
                            echo "<tr>";
                            echo "<th>#</th>";
                            echo "<th>Ip</th>";
                            echo "<th>Nombre Servidor</th>";
                            echo "<th>Estado</th>";
                            echo "<th></th>";
                            echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";
                            while ($row = $result->fetch_array()) {
                                $ping = exec("ping " . $row['ip_number'], $output, $status);
                                echo "<tr>";
                                echo "<td>" . $row['id'] . "</td>";
                                echo "<td>" . $row['ip_number'] . "</td>";
                                echo "<td>" . $row['ip_name'] . "</td>";
                                if ($status == 0) {
                                    echo "<td style = color:green>Online</td>";
                                } else {
                                    echo "<td style = color:red>Offline</td>";
                                    $ip_off .= $row['ip_number'].", ";
                                    $all_ip_off = substr($ip_off, 0, -2);
                                }
                                echo "<td>";
                                echo '<a href="read.php?id=' . $row['id'] . '" class="mr-3" title="Visualizar" data-toggle="tooltip"><span class="fa fa-eye"></span></a>';
                                echo '<a href="update.php?id=' . $row['id'] . '" class="mr-3" title="Actualizar" data-toggle="tooltip"><span class="fa fa-pencil"></span></a>';
                                echo '<a href="delete.php?id=' . $row['id'] . '" title="Eliminar" data-toggle="tooltip"><span class="fa fa-trash"></span></a>';
                                echo "</td>";
                                echo "</tr>";
                            }
                            echo "</tbody>";
                            echo "</table>";

                            mail($to, $subject, $txt . $all_ip_off . " se encuentra en estado Offline.", $headers);

                            $result->free();
                        } else {
                            echo '<div class="alert alert-danger"><em>No se encontraron registros.</em></div>';
                        }
                    } else {
                        echo "Oops! Algo sali?? mal. Por favor intente de nuevo.";
                    }

                    $mysqli->close();
                    ?>
                </div>
                <a href="logout.php" class="btn btn-danger ml-3"><i class="fa fa-sign-out"></i> Cerrar Sesi??n</a>
            </div>
        </div>
    </div>
</body>

</html>