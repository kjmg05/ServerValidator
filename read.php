<?php
if (isset($_GET["id"]) && !empty($_GET["id"])) {
    require_once "./config/config.php";

    $sql = "SELECT * FROM ip WHERE id = ?";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $param_id);

        $param_id = $_GET["id"];

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_array(MYSQLI_ASSOC);

                $ip_number = $row["ip_number"];
                $ip_name = $row["ip_name"];
            } else {
                header("location: error.php");
                exit();
            }
        } else {
            echo "Oops! Algo salió mal. Por favor intente de nuevo.";
        }
    }

    $stmt->close();

    $mysqli->close();
} else {
    header("location: error.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Visualizar Registro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper {
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="mt-5 mb-3">Visualizar Registro</h1>
                    <div class="form-group">
                        <label>IP</label>
                        <p><b><?php echo $row["ip_number"]; ?></b></p>
                    </div>
                    <div class="form-group">
                        <label>Nombre del Servidor</label>
                        <p><b><?php echo $row["ip_name"]; ?></b></p>
                    </div>
                    <p><a href="index.php" class="btn btn-primary">Regresar</a></p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>