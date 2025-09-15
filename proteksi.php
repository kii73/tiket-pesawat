<?php

include "./koneksi.php";

function check_remember_token()
{
  global $mysql;

  if (isset($_COOKIE["remember_token"])) {
    $remember_token = $_COOKIE["remember_token"];
    $result = $mysql->query("SELECT * FROM users WHERE remember_token='$remember_token'")->num_rows;
    if ($result == 0) {
      return false;
    }

    return true;
  }
}

function proteksi()
{
  if (!check_remember_token()) {
?>
    <script>
      window.location.replace("login.php");
    </script>
<?php
  }
}