<?php

session_start();




if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creare magazin nou - My SaaS App</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header class="bg-primary text-white text-center py-5">
        <h1>Creare magazin nou</h1>
    </header>
    <main class="container my-5">
        <h2>Alege un template</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <img src="../public/images/summer.jpg" class="card-img-top" alt="Summer Template">
                    <div class="card-body">
                        <h5 class="card-title">Summer</h5>
                        <p class="card-text">Template cu fructe și legume.</p>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#applyTemplateModal" data-template="summer">Alege</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <img src="../public/images/desserts.jpg" class="card-img-top" alt="Desserts Template">
                    <div class="card-body">
                        <h5 class="card-title">Desserts</h5>
                        <p class="card-text">Template cu prăjituri.</p>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#applyTemplateModal" data-template="desserts">Alege</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <img src="../public/images/sneakers.jpg" class="card-img-top" alt="Sneakers Template">
                    <div class="card-body">
                        <h5 class="card-title">Sneakers</h5>
                        <p class="card-text">Template cu adidași.</p>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#applyTemplateModal" data-template="sneakers">Alege</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal -->
    <div class="modal fade" id="applyTemplateModal" tabindex="-1" aria-labelledby="applyTemplateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="applyTemplateModalLabel">Nume magazin</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="applyTemplateForm" action="../subscribers/apply_template.php" method="post">
                        <div class="form-group">
                            <label for="storeName">Introduceți numele magazinului:</label>
                            <input type="text" class="form-control" id="storeName" name="storeName" required>
                            <input type="hidden" id="template" name="template">
                        </div>
                        <button type="submit" class="btn btn-primary">Validare</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $('#applyTemplateModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var template = button.data('template'); // Extract info from data-* attributes
            var modal = $(this);
            modal.find('#template').val(template);
        });
    </script>
</body>
</html>