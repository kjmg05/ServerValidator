<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "./config/config.php";
$ip_number = $ip_name = "";
$ip_number_err = $ip_name_err = "";

if (isset($_POST["id"]) && !empty($_POST["id"])) {
    $id = $_POST["id"];

    $input_num = $_POST["ip_number"];
    if (empty($input_num)) {
        $ip_number_err = "Por favor, ingrese el numero IP";
    } elseif (!filter_var($input_num, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^[a-zA-Z0-9.]+$/")))) {
        $ip_number_err = "Por favor, ingrese el numero IP";
    } else {
        $ip_number = $input_num;
    }

    $input_name = $_POST["ip_name"];
    if (empty($input_name)) {
        $ip_name_err = "Por favor, ingrese el nombre del servidor.";
    } else {
        $ip_name = $input_name;
    }

    if (empty($ip_number_err) && empty($ip_name_err)) {
        $sql = "UPDATE ip SET ip_number=?, ip_name=? WHERE id=?";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param('ssi', $param_num, $param_name, $param_id);
            $param_num = $ip_number;
            $param_name = $ip_name;
            $param_id = $id;


            if ($stmt->execute()) {
                header("location: index.php");
                exit();
            } else {
                echo "Oops! Algo salió mal. Por favor intente de nuevo.";
            }
        }

        $stmt->close();
    }

    $mysqli->close();
} else {
    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        $id =  trim($_GET["id"]);

        $sql = "SELECT * FROM ip WHERE id = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("i", $param_id);

            $param_id = $id;

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
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Actualizar Registro</title>
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
                    <h2 class="mt-5">Actualizar Registro</h2>
                    <p>Por favor, edite los valores para actualizar los datos.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group">
                            <label>Numero IP</label>
                            <input type="text" name="ip_number" class="form-control <?php echo (!empty($ip_number_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $ip_number; ?>">
                            <span class="invalid-feedback"><?php echo $ip_number_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label>Nombre del Servidor</label>
                            <input name="ip_name" class="form-control <?php echo (!empty($ip_name_err)) ? 'is-invalid' : ''; ?>"><?php echo $ip_name; ?></input>
                            <span class="invalid-feedback"><?php echo $ip_name_err; ?></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $id; ?>" />
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>