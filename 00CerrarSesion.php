<?php
session_start();

unset($_SESSION['user']);
unset($_SESSION['contra']);
unset($_SESSION['pfp']);
unset($_SESSION['nom']);
unset($_SESSION['apell']);
unset($_SESSION['Edad']);
unset($_SESSION['naci']);
unset($_SESSION['Correo']);
unset($_SESSION['gen']);


header("Location: Login.php");
exit;
?>