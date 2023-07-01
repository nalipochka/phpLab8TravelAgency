<!DOCTYPE html>
<html>

<head>
    <title>Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<?php

$errors = array();


if (isset($_POST["registerForm"])) 
{
    $username = $_POST["login"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    include_once("functions.php");
    $link = connectToDb("localhost", "root", "", "agencyDb", 3306);

    if (!preg_match('/^[a-zA-Z0-9]+$/', $username)) 
    {
        $errors['username'] = "The username can only contain English letters and numbers.";
    }

    if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?!.*\d$)[A-Za-z\d!?.]+$/', $password)) 
    {
        $errors['password'] = 'Password does not meet requirements:';

        if (!preg_match('/[A-Z]/', $password)) {
            $errors['password'] .= ' At least one capital letter must be used.';
        }
        if (!preg_match('/\d/', $password)) {
            $errors['password'] .= ' At least one number must be used.';
        }
        if (preg_match('/\d$/', $password)) {
            $errors['password'] .= ' The password must not end with a number.';
        }
        if (!preg_match('/^[A-Za-z\d!?\.]+$/', $password)) {
            $errors['password'] .= ' Prohibited characters used.';
        }
    }
    $query = "SELECT * FROM Users WHERE UserLogin = '$username' OR Email = '$email'";
    $result = mysqli_query($link, $query);
    if(mysqli_num_rows($result) > 0)
    {
        while($row = mysqli_fetch_assoc($result)){
            if($row['UserLogin'] === $username){
                $errors['username'] = "Username already exists.";
            }
            if($row["Email"] === $email){
                $errors['email'] = "Email already exists.";
            }
        }
    }
    if(empty($errors))
    {
        $hashedPass = password_hash($password, PASSWORD_DEFAULT);
        $insertedQuery = "INSERT INTO Users (UserLogin, Email, UserPassword, Photo, Discount, RoleId)
        VALUES ('$username', '$email', '$hashedPass', NULL, 0, 2)";
        if(mysqli_query($link, $insertedQuery) === TRUE){
            header("Location: ?page=1");
            exit();
        }
        else{
            echo "<div class='alert alert-danger'>Error: " . $insertedQuery . "</div> <div>" . mysqli_error($link) . "</div>";
        }
    }
    mysqli_close($link);
}

?>

<body>
    <div class="container">
        <h2 class="my-4 text-center">Registration</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form action="?page=4" method="POST">
                    <div class="form-group">
                        <label for="login">Username:</label>
                        <input type="text" class="form-control" id="login" name="login" required value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
                        <?php if (!empty($errors['username'])) : ?>
                            <small class="text-danger"><?php echo $errors['username']; ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="email">eMail:</label>
                        <input type="email" class="form-control" id="email" name="email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                        <?php if (!empty($errors['email'])) : ?>
                            <small class="text-danger"><?php echo $errors['email']; ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <?php if (!empty($errors['password'])) : ?>
                            <small class="text-danger"><?php echo $errors['password']; ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary btn-block" name="registerForm">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>