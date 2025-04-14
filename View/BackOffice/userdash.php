<?php
require_once __DIR__ . '/../../Config/database.php';
require_once __DIR__ . '/../../Controller/UserController.php';


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Users Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <title>Voysync</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<style>
    .navbar {
        position: fixed;
        top: 0;
        width: 100%;
        background-color:rgb(146, 182, 188);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 30px;
        z-index: 1000;
        box-shadow: 0 0 2px #c9cddb;
    }

    .navbar_content {
        display: flex;
        align-items: center;
        column-gap: 25px;
    }

    .logout-btn {
        color: white;
        text-decoration: none;
        padding: 8px 16px;
        background-color:rgb(65, 178, 188);
        border-radius: 4px;
        margin-right: 30px;
        transition: background-color 0.3s;
    }

    .logout-btn:hover {
        background-color:rgb(65, 178, 188);
        color: #1c4771;
    }
</style>

<nav class="navbar">
    <div class="navbar_content">

        <h1>User Dashboard</h1>
        <a href="../../Controller/logout.php" id="logout" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
        </a>
    </div>

</nav>
<br><br><br><br><br><br><br>

<body>

    <div class="main_body">

        <div class="container">

            <a href="UserAddBack.php" class="btn btn-dark mb-3">Ajouter un utilisateur</a>

            <table class="table table-hover text-center" style="background-color:rgb(166, 181, 177);; color: #1D548DFF;">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nom</th>
                        <th scope="col">Prénom</th>
                        <th scope="col">Email</th>
                        <th scope="col">Mot de passe</th>
                        <th scope="col">Telephone</th>
                        <th scope="col">Role</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $conn = Database::connect();
                    $sql = "SELECT * FROM utilisateurs";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($rows as $row) {
                        ?>
                        <tr>
                            <td><?php echo $row["id"] ?></td>
                            <td><?php echo $row["nom"] ?></td>
                            <td><?php echo $row["prenom"] ?></td>
                            <td><?php echo $row["email"] ?></td>
                            <td><?php echo $row["password"] ?></td>
                            <td><?php echo $row["telephone"] ?></td>
                            <td><?php echo $row["role"] ?></td>
                            <td>
                                <a href="UserUpdateBack.php?id=<?php echo $row["id"] ?>" class="link-dark"><i
                                        class="fa-solid fa-pen-to-square fs-5 me-3"></i></a>
                                <a href="UserDelete.php?id=<?php echo $row['id']; ?>"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ?')" class="link-dark">
                                    <i class="fa-solid fa-trash fs-5"></i>
                                </a>
                            </td>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>
    <script>
        function confirmDelete(userId) {
            if (confirm("Are you sure for this operation?")) {
                window.location.href = "UserDelete.php?id=" + userId;
            }
        }
    </script>

</body>

</html>
<script src="../js/script.js"></script>

<script>
    document.getElementById("logout").addEventListener("click", function (e) {
        e.preventDefault(); // Empêche le lien de se comporter normalement
        if (confirm("Voulez-vous vraiment vous déconnecter ?")) {
            window.location.href = this.href;
        }
    });
</script>