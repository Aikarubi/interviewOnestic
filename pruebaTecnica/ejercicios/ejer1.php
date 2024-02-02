<?php

// Función para leer customers.csv y devolver un array asociativo que mapea el id del cliente
function leer_clientes($filename) {
    $clientes = [];
    if (($handle = fopen($filename, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $clientes[(int)$data[0]] = [$data[1], $data[2]];
        }
        fclose($handle);
    }
    return $clientes;
}

// Función para leer products.csv y devolver un array asociativo que mapea el id del producto
function leer_productos($filename) {
    $productos = [];
    if (($handle = fopen($filename, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $productos[(int)$data[0]] = [$data[1], floatval($data[2])];
        }
        fclose($handle);
    }
    return $productos;
}

// Función para leer orders.csv y devolver un array
function leer_ordenes($filename) {
    $ordenes = [];
    if (($handle = fopen($filename, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $id_orden = (int)$data[0];
            $id_cliente = (int)$data[1];
            $ids_productos = array_map('intval', explode(' ', $data[2]));
            $ordenes[] = [$id_orden, $id_cliente, $ids_productos];
        }
        fclose($handle);
    }
    return $ordenes;
}

// Función para calcular el costo total de una orden
function calcular_costo_orden($orden, $productos) {
    $costo_total = 0;
    foreach ($orden as $id_producto) {
        $costo_total += $productos[$id_producto][1]; // Acumular el costo de cada producto
    }
    return $costo_total;
}

// Función principal para procesar los datos y escribir order_prices.csv
function generar_precios_clientes($clientes_file, $productos_file, $ordenes_file, $output_file) {
    $clientes = leer_clientes($clientes_file);
    $productos = leer_productos($productos_file);
    $ordenes = leer_ordenes($ordenes_file);


    $totales_clientes = array_fill_keys(array_keys($clientes), 0);

    foreach ($ordenes as $orden) {
        $id_cliente = $orden[1];
        $ids_productos = $orden[2];
        $costo_total = calcular_costo_orden($ids_productos, $productos);

        $totales_clientes[$id_cliente] += $costo_total;
    }


    $fp = fopen($output_file, 'w');
    fputcsv($fp, ['id', 'euros']); // Escribir la fila de encabezado
    foreach ($totales_clientes as $id_cliente => $total) {
        fputcsv($fp, [$id_cliente, $total]);
    }
    fclose($fp);
}

//Rutas
generar_precios_clientes('../customers.csv', '../products.csv', '../orders.csv', '../resultados/order_prices.csv');

?>
