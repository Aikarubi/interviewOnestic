<?php

// Función para obtener el nombre completo del cliente
function getFullName($customerId, $customers) {
    foreach ($customers as $customer) {
        if ($customer['id'] == $customerId) {
            return $customer['firstname'] . ' ' . $customer['lastname'];
        }
    }
    return 'Cliente no encontrado';
}

// Leer customers.csv
$customersFile = fopen('../customers.csv', 'r');
$headers = fgetcsv($customersFile); // Leer encabezados
$customers = [];
while (($row = fgetcsv($customersFile)) !== false) {
    $customers[] = array_combine($headers, $row);
}
fclose($customersFile);

// Leer orders.csv
$ordersFile = fopen('../orders.csv', 'r');
$headers = fgetcsv($ordersFile); // Leer encabezados
$orders = [];
while (($row = fgetcsv($ordersFile)) !== false) {
    $orders[] = array_combine($headers, $row);
}
fclose($ordersFile);

// Leer products.csv
$productsFile = fopen('../products.csv', 'r');
$headers = fgetcsv($productsFile); // Leer encabezados
$products = [];
while (($row = fgetcsv($productsFile)) !== false) {
    $products[] = array_combine($headers, $row);
}
fclose($productsFile);

// Calcular total de euros gastados por cada cliente
$customerTotals = [];
foreach ($orders as $order) {
    $customerId = $order['customer'];
    $productIds = explode(' ', $order['products']);
    $totalEuros = 0;
    foreach ($productIds as $productId) {
        $cost = $products[$productId]['cost'];
        $totalEuros += $cost;
    }
    if (!isset($customerTotals[$customerId])) {
        $customerTotals[$customerId] = 0;
    }
    $customerTotals[$customerId] += $totalEuros;
}

// Ordenar clientes por total de euros gastados (orden descendente)
arsort($customerTotals);

// Escribir resultados en customer_ranking.csv
$customerRankingFile = fopen('../resultados/customer_ranking.csv', 'w');
fputcsv($customerRankingFile, ['id', 'firstname', 'lastname', 'total_euros']);
foreach ($customerTotals as $customerId => $totalEuros) {
    $fullName = getFullName($customerId, $customers);
    list($firstName, $lastName) = explode(' ', $fullName);
    fputcsv($customerRankingFile, [$customerId, $firstName, $lastName, $totalEuros]);
}
fclose($customerRankingFile);

echo "Se ha creado customer_ranking.csv satisfactoriamente.";

?>
