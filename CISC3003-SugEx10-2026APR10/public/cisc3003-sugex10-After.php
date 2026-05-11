<?php

include 'includes/book-utilities.inc.php';

$customers = readCustomers('data/customers.txt');

$selectedCustomer = null;
$selectedCustomerId = null;
if (isset($_GET['customer_id'])) {
    $selectedCustomerId = filter_input(INPUT_GET, 'customer_id', FILTER_VALIDATE_INT);
    if ($selectedCustomerId !== false && $selectedCustomerId !== null && isset($customers[$selectedCustomerId])) {
        $selectedCustomer = $customers[$selectedCustomerId];
    }
}

$orders = [];
if ($selectedCustomer !== null) {
    $orders = readOrders($selectedCustomer['id'], 'data/orders.txt');
}

$self = $_SERVER['PHP_SELF'] ?? 'cisc3003-sugex10-After.php';

function h($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>CISC3003 Suggested Exercise 10</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="http://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="css/material.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/demo-styles.css">

    <script src="https://code.jquery.com/jquery-1.7.2.min.js"></script>
    <script src="js/material.min.js"></script>
    <script src="js/jquery.sparkline.2.1.2.js"></script>
</head>

<body>

    <div class="mdl-layout mdl-js-layout mdl-layout--fixed-drawer
            mdl-layout--fixed-header">

        <?php include 'includes/header.inc.php'; ?>
        <?php include 'includes/left-nav.inc.php'; ?>

        <main class="mdl-layout__content mdl-color--grey-50">
            <section class="page-content">

                <div class="mdl-grid">

                    <!-- mdl-cell + mdl-card -->
                    <div class="mdl-cell mdl-cell--7-col card-lesson mdl-card  mdl-shadow--2dp">
                        <div class="mdl-card__title mdl-color--orange">
                            <h2 class="mdl-card__title-text">Customers</h2>
                        </div>
                        <div class="mdl-card__supporting-text">
                            <table class="mdl-data-table  mdl-shadow--2dp">
                                <thead>
                                    <tr>
                                        <th class="mdl-data-table__cell--non-numeric">Name</th>
                                        <th class="mdl-data-table__cell--non-numeric">University</th>
                                        <th class="mdl-data-table__cell--non-numeric">City</th>
                                        <th>Sales</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($customers as $customer) : ?>
                                        <?php $sales = preg_replace('/\s+/', '', $customer['sales']); ?>
                                        <tr>
                                            <td class="mdl-data-table__cell--non-numeric">
                                                <a href="<?= h($self) . '?customer_id=' . h($customer['id']) ?>"><?= h($customer['firstName'] . ' ' . $customer['lastName']) ?></a>
                                            </td>
                                            <td class="mdl-data-table__cell--non-numeric"><?= h($customer['university']) ?></td>
                                            <td class="mdl-data-table__cell--non-numeric"><?= h($customer['city']) ?></td>
                                            <td><span class="inlinesparkline"><?= h($sales) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- / mdl-cell + mdl-card -->


                    <div class="mdl-grid mdl-cell--5-col">



                        <!-- mdl-cell + mdl-card -->
                        <div class="mdl-cell mdl-cell--12-col card-lesson mdl-card  mdl-shadow--2dp">
                            <div class="mdl-card__title mdl-color--deep-purple mdl-color-text--white">
                                <h2 class="mdl-card__title-text">Customer Details</h2>
                            </div>
                            <div class="mdl-card__supporting-text">
                                <?php if ($selectedCustomer === null) : ?>
                                    <p>Select a customer to view details.</p>
                                <?php else : ?>
                                    <h3><?= h($selectedCustomer['firstName'] . ' ' . $selectedCustomer['lastName']) ?></h3>

                                    <?php
                                    $addressParts = [];
                                    if ($selectedCustomer['address'] !== '') {
                                        $addressParts[] = $selectedCustomer['address'];
                                    }
                                    if ($selectedCustomer['city'] !== '') {
                                        $addressParts[] = $selectedCustomer['city'];
                                    }
                                    if ($selectedCustomer['state'] !== '') {
                                        $addressParts[] = $selectedCustomer['state'];
                                    }
                                    if ($selectedCustomer['country'] !== '') {
                                        $addressParts[] = $selectedCustomer['country'];
                                    }
                                    if ($selectedCustomer['postal'] !== '') {
                                        $addressParts[] = $selectedCustomer['postal'];
                                    }
                                    $addressLine = implode(', ', $addressParts);
                                    ?>

                                    <p><strong>Email:</strong> <?= h($selectedCustomer['email']) ?></p>
                                    <p><strong>University:</strong> <?= h($selectedCustomer['university']) ?></p>
                                    <p><strong>Address:</strong> <?= h($addressLine) ?></p>
                                    <p><strong>Phone:</strong> <?= h($selectedCustomer['phone']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div> <!-- / mdl-cell + mdl-card -->

                        <!-- mdl-cell + mdl-card -->
                        <div class="mdl-cell mdl-cell--12-col card-lesson mdl-card  mdl-shadow--2dp">
                            <div class="mdl-card__title mdl-color--deep-purple mdl-color-text--white">
                                <h2 class="mdl-card__title-text">Order Details</h2>
                            </div>
                            <div class="mdl-card__supporting-text">

                                <table class="mdl-data-table  mdl-shadow--2dp">
                                    <thead>
                                        <tr>
                                            <th class="mdl-data-table__cell--non-numeric">Cover</th>
                                            <th class="mdl-data-table__cell--non-numeric">ISBN</th>
                                            <th class="mdl-data-table__cell--non-numeric">Title</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($selectedCustomer === null) : ?>
                                            <tr>
                                                <td class="mdl-data-table__cell--non-numeric" colspan="3">Select a customer to view orders.</td>
                                            </tr>
                                        <?php elseif (count($orders) === 0) : ?>
                                            <tr>
                                                <td class="mdl-data-table__cell--non-numeric" colspan="3">No orders for this customer.</td>
                                            </tr>
                                        <?php else : ?>
                                            <?php foreach ($orders as $order) : ?>
                                                <?php
                                                $isbn = $order['isbn'];
                                                $cover = 'images/tinysquare/' . $isbn . '.jpg';
                                                if (!file_exists(__DIR__ . '/' . $cover)) {
                                                    $cover = 'images/tinysquare/missing.jpg';
                                                }
                                                ?>
                                                <tr>
                                                    <td class="mdl-data-table__cell--non-numeric"><img src="<?= h($cover) ?>" alt="<?= h($order['title']) ?>"></td>
                                                    <td class="mdl-data-table__cell--non-numeric"><?= h($order['isbn']) ?></td>
                                                    <td class="mdl-data-table__cell--non-numeric"><?= h($order['title']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>

                                    </tbody>
                                </table>

                            </div>
                        </div> <!-- / mdl-cell + mdl-card -->


                    </div>


                </div> <!-- / mdl-grid -->

            </section>
        </main>
    </div> <!-- / mdl-layout -->

    <script>
        $(function() {
            $('.inlinesparkline').sparkline('html', {
                type: 'bar',
                barColor: '#1a73e8',
                height: '20px',
                barWidth: 4,
                barSpacing: 1,
                chartRangeMin: 0
            });
        });
    </script>

</body>

</html>
