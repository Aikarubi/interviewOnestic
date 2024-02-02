<?php

// Función para leer orders.csv y construir un diccionario de productos y los clientes que los han comprado
function construir_product_customers($clientes_file, $ordenes_file) {
    $clientes = leer_clientes($clientes_file);
    $ordenes = leer_ordenes($ordenes_file);

    $product_customers = [];

    foreach ($ordenes as $orden) {
        $id_cliente = $orden[1];
        $ids_productos = $orden[2];
        foreach ($ids_productos as $id_producto) {
            if (!isset($product_customers[$id_producto])) {
                $product_customers[$id_producto] = [];
            }
            
            if (!in_array($id_cliente, $product_customers[$id_producto])) {
                $product_customers[$id_producto][] = $id_cliente;
            }
        }
    }

    return $product_customers;
}

// generar el archivo product_customers.csv
function generar_product_customers($clientes_file, $ordenes_file, $output_file) {
    $product_customers = construir_product_customers($clientes_file, $ordenes_file);

    $fp = fopen($output_file, 'w');
    fputcsv($fp, ['id', 'customer_ids']); // Escribir la fila de encabezado
    foreach ($product_customers as $id_producto => $clientes) {
        fputcsv($fp, [$id_producto, implode(' ', $clientes)]);
    }
    fclose($fp);
}

// Funciones para leer clientes y órdenes
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

//Rutas
generar_product_customers('../customers.csv', '../orders.csv', '../resultados/product_customers.csv');

?>
