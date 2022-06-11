<?php
require_once "./config/config.php";

$username = $password = $confirm_password = $email = "";
$username_err = $password_err = $confirm_password_err = $email_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["username"]))) {
        $username_err = "Por favor, ingrese el usuario.";
    } elseif (!preg_match('/^[a-zA-Z0-9_.]+$/', trim($_POST["username"]))) {
        $username_err = "Los usuarios solo pueden tener letras, numeros, punto o guion bajo";
    } else {
        $sql = "SELECT id FROM users WHERE username = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $param_username);

            $param_username = trim($_POST["username"]);

            if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $username_err = "Este usuario ya existe. Intente nuevamente.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Algo salió mal. Por favor intente de nuevo.";
            }

            $stmt->close();
        }
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Por favor, ingrese una contraseña.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Por favor, confirme la contraseña.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "La contraseña no coincide.";
        }
    }

    if (empty(trim($_POST["email"]))) {
        $email_err = "Por favor, ingrese el correo electronico.";
    } elseif (!empty(trim($_POST["email"])) && filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL) === false) {
        $email_err = "Por favor, ingrese el correo electronico.";
    }
    else {
        $email = trim($_POST["email"]);
    }

    if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err)) {

        $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("sss", $param_username, $param_password, $param_email);

            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $param_email = $email;

            if ($stmt->execute()) {
                header("location: login.php");
            } else {
                echo "Oops! Algo salió mal. Por favor intente de nuevo.";
            }

            $stmt->close();
        }
    }
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Registro de usuarios</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font: 14px sans-serif;
        }

        .wrapper {
            width: 360px;
            padding: 20px;
        }
    </style>
</head>

<body>
    <section class="vh-100" style="background-color: #508bfc;">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card shadow-2-strong" style="border-radius: 1rem;">
                        <div class="card-body p-5 text-center">
                            <h3 class="mb-4">Registro de usuarios</h3>
                            <form id="myForm" method="POST" class="needs-validation" novalidate="" autocomplete="off" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <div class="mb-3 form-group">
                                    <label class="mb-2 text-muted" for="username">Usuario</label>
                                    <input id="myInput" type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                                    <span class="invalid-feedback"><?php echo $username_err; ?></span>
                                </div>
                                <div class="mb-3 form-group">
                                    <label class="text-muted" for="password">Contraseña</label>
                                    <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                                </div>
                                <div class="mb-3 form-group">
                                    <label class="text-muted" for="confirm_password">Confirmar Contraseña</label>
                                    <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                                    <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                                </div>
                                <div class="mb-3 form-group">
                                    <label class="mb-2 text-muted" for="email">Correo Electronico</label>
                                    <input id="myInput" type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                                    <span class="invalid-feedback"><?php echo $email_err; ?></span>
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary" value="Registrar">
                                </div>
                                <p>Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>.</p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>