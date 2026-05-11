<?php

function readCustomers($file = 'data/customers.txt') {
    $customers = [];
    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $parts = explode(';', $line);
            if (count($parts) >= 12 && is_numeric($parts[0])) {
                $customers[] = $parts;
            }
        }
    }
    return $customers;
}

function readOrders($file = 'data/orders.txt') {
    $orders = [];
    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $parts = explode(',', $line);
            if (count($parts) >= 5 && is_numeric($parts[0])) {
                $orders[] = $parts;
            }
        }
    }
    return $orders;
}

function readBooks($file = 'data/books.txt') {
    $books = [];
    if (file_exists($file)) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $parts = explode(',', $line);
            if (count($parts) >= 4 && is_numeric($parts[0])) {
                $books[$parts[1]] = [
                    'isbn' => $parts[1],
                    'title' => $parts[3]
                ];
            }
        }
    }
    return $books;
}

?>