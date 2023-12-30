<!DOCTYPE html>
<html lang="en">

<?php
session_start();
include('./db_connect.php');
ob_start();

ob_end_flush();
?>
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Log in</title>

  <?php include('./header.php'); ?>
  <?php
  if (isset($_SESSION['login_id']))
    header("location:index.php?page=home");

  ?>

</head>
<style>
  body {
    width: 100%;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #007bff;
  }

  .card {
    background-color: #fff;
    border-radius: 6px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 400px;
  }

  .card-body {
    padding: 30px;
  }

  .form-group {
    margin-bottom: 20px;
  }

  .form-group label {
    color: #333;
    font-weight: bold;
  }

  .form-control {
    border-radius: 4px;
  }

  .btn-primary {
    background-color: #007bff;
    border: none;
    width: 100%;
  }

  .btn-primary:hover {
    background-color: #0069d9;
  }

  .alert-danger {
    padding: 10px;
    background-color: #ffcaca;
    border: 1px solid #ff9999;
    border-radius: 4px;
    color: #ff0000;
    margin-bottom: 20px;
  }
</style>

<body>


  <div class="card">
    <div class="card-body">
      <form id="login-form">
        <div class="form-group">
          <label for="username" class="control-label">Username</label>
          <input type="text" id="username" name="username" class="form-control">
        </div>
        <div class="form-group">
          <label for="password" class="control-label">Password</label>
          <input type="password" id="password" name="password" class="form-control">
        </div>
        <button class="btn btn-primary">Login</button>
      </form>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $('#login-form').submit(function(e) {
      e.preventDefault();
      $('button[type="submit"]').attr('disabled', true).html('Logging in...');
      if ($(this).find('.alert-danger').length > 0) {
        $(this).find('.alert-danger').remove();
      }
      $.ajax({
        url: 'ajax.php?action=login',
        method: 'POST',
        data: $(this).serialize(),
        error: err => {
          console.log(err);
          $('button[type="submit"]').removeAttr('disabled').html('Login');
        },
        success: function(resp) {
          if (resp == 1) {
            location.href = 'index.php?page=home';
          } else {
            $('#login-form').prepend('<div class="alert alert-danger">Username or password is incorrect.</div>');
            $('button[type="submit"]').removeAttr('disabled').html('Login');
          }
        }
      });
    });
  </script>
</body>

</html>