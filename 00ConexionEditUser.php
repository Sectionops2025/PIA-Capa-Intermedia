<?php
include("00ConexionDB.php");

$val = 1;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    if(
        isset($_POST['genero']) &&
        isset($_POST['name']) &&
        isset($_POST['fecha-nacimiento']) &&
        isset($_POST['password']) &&
        isset($_POST['confirm-password']) 
        
    ) {
        $Gen = trim($_POST['genero']);
        $Name = trim($_POST['name']);
        $Nac = trim($_POST['fecha-nacimiento']);
        $pssw = trim($_POST['password']);
        $pssw2 = trim($_POST['confirm-password']);
        $pfp = $_SESSION['pfp'];

        if (isset($_FILES['foto-perfil']) && $_FILES['foto-perfil']['error'] === UPLOAD_ERR_OK) {
            
            $Foto = base64_encode(file_get_contents($_FILES['foto-perfil']['tmp_name']));
        
        
        } else {
        
            $Foto= $pfp;
        }

        if($val >= 1) {

            $id = $_SESSION['idUsuario'];
            $insertar = " UPDATE USUARIO SET GENERO='$Gen', NOMBRE='$Name', FNACI='$Nac', CONTRASEÑA='$pssw', FOTO='$Foto' WHERE ID_USUARIO ='$id' ";
            $resultado = mysqli_query($conex, $insertar);
            if($resultado) {
                ?>
                
                <h2 class="Existoso">!Edición Exitosa!</h2>
                
                <?php
                 
                 $_SESSION['pfp'] = $Foto;
                 $_SESSION['gen'] = $Gen;
                 $_SESSION['nom'] = $Name;
                 $_SESSION['naci'] = $Nac;
                 $_SESSION['contra'] =  $pssw;

                
            } else {
                ?>
                <h2 class="Error">Error al editar la cuenta.</h2>
                <?php
            }
        } else {
            ?>
            <h2 class="Error">Debe seleccionar una imagen.</h2>
            <?php
        }
    } else {
        ?>
        <h2 class="Error">Por favor, complete todos los campos.</h2>
        <?php
    }
}
?>
