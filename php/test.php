<?php
$hash = '$2y$10$heuMG7aElXF5IiS4rCN49.T.smRQfhlCmVuoAh/SPpjQ6YA6qzZO6';
$pass = 'admin';

if (password_verify($pass, $hash)) {
    echo "El hash y la contraseña coinciden correctamente.";
} else {
    echo "El hash NO coincide. Hay un problema con la cadena del hash.";
}
?>