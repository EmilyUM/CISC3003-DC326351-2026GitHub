<?php

function readCustomers($filename)
{
    $customers = [];

    if (!is_readable($filename)) {
        return $customers;
    }

    $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return $customers;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }

        $parts = array_map('trim', explode(';', $line));
        if (count($parts) < 12) {
            continue;
        }

        $id = (int)$parts[0];
        $customers[$id] = [
            'id' => $id,
            'firstName' => $parts[1],
            'lastName' => $parts[2],
            'email' => $parts[3],
            'university' => $parts[4],
            'address' => $parts[5],
            'city' => $parts[6],
            'state' => $parts[7],
            'country' => $parts[8],
            'postal' => $parts[9],
            'phone' => $parts[10],
            'sales' => $parts[11],
        ];
    }

    return $customers;
}

function readOrders($customer, $filename)
{
    $orders = [];
    $customerId = (string)$customer;

    if (!is_readable($filename)) {
        return $orders;
    }

    $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return $orders;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }

        $first = strpos($line, ',');
        if ($first === false) {
            continue;
        }
        $second = strpos($line, ',', $first + 1);
        if ($second === false) {
            continue;
        }
        $third = strpos($line, ',', $second + 1);
        if ($third === false) {
            continue;
        }
        $last = strrpos($line, ',');
        if ($last === false || $last <= $third) {
            continue;
        }

        $orderId = trim(substr($line, 0, $first));
        $custId = trim(substr($line, $first + 1, $second - $first - 1));
        $isbn = trim(substr($line, $second + 1, $third - $second - 1));
        $title = trim(substr($line, $third + 1, $last - $third - 1));
        $category = trim(substr($line, $last + 1));

        if ($custId !== $customerId) {
            continue;
        }

        $orders[] = [
            'orderId' => $orderId,
            'customerId' => $custId,
            'isbn' => $isbn,
            'title' => $title,
            'category' => $category,
        ];
    }

    return $orders;
}

?>
