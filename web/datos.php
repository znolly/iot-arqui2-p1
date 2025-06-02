<?php
require 'conexion.php';

function obtenerDatos($tabla, $colTemp, $colFecha) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT $colTemp, $colFecha FROM $tabla ORDER BY $colFecha DESC LIMIT 20");
    $stmt->execute();
    $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return array_reverse($datos); // los más antiguos primero
}

$sensor1 = obtenerDatos('sensor1', 'sensor1_temp', 'sensor1_date');
$sensor2 = obtenerDatos('sensor2', 'sensor2_temp', 'sensor2_date');

echo json_encode([
    'sensor1' => $sensor1,
    'sensor2' => $sensor2
]);
?>
