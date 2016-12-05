<html>
<head>

<style>
body {
    font-family: sans-serif, sans;
}

.table {
    border-collapse: collapse !important;
}

.table td {
    background-color: #fff !important;
}

.table th {
    background-color: #E0E0E0 !important;
}

.table-bordered th,
.table-bordered td {
    border: 1px solid #D6D6D6 !important;
}

table {
    background-color: transparent;
}

caption {
    padding-top: 8px;
    padding-bottom: 8px;
    color: #777;
    text-align: left;
}

th {
    text-align: left;
}

.table {
    width: 100%;
    max-width: 100%;
    margin-bottom: 20px;
}

.table > thead > tr > th,
.table > tbody > tr > th,
.table > tfoot > tr > th,
.table > thead > tr > td,
.table > tbody > tr > td,
.table > tfoot > tr > td {
    padding: 8px;
    line-height: 1.42857143;
    vertical-align: top;
    border-top: 1px solid #ddd;
}

.table > thead > tr > th {
    vertical-align: bottom;
    border-bottom: 2px solid #ddd;
}

.table > caption + thead > tr:first-child > th,
.table > colgroup + thead > tr:first-child > th,
.table > thead:first-child > tr:first-child > th,
.table > caption + thead > tr:first-child > td,
.table > colgroup + thead > tr:first-child > td,
.table > thead:first-child > tr:first-child > td {
    border-top: 0;
}

.table > tbody + tbody {
    border-top: 2px solid #ddd;
}

.table .table {
    background-color: #fff;
}

.table-condensed > thead > tr > th,
.table-condensed > tbody > tr > th,
.table-condensed > tfoot > tr > th,
.table-condensed > thead > tr > td,
.table-condensed > tbody > tr > td,
.table-condensed > tfoot > tr > td {
    padding: 5px;
}

.table-bordered {
    border: 1px solid #ddd;
}

.table-bordered > thead > tr > th,
.table-bordered > tbody > tr > th,
.table-bordered > tfoot > tr > th,
.table-bordered > thead > tr > td,
.table-bordered > tbody > tr > td,
.table-bordered > tfoot > tr > td {
    border: 1px solid #ddd;
}

.table-bordered > thead > tr > th,
.table-bordered > thead > tr > td {
    border-bottom-width: 2px;
}

.table-striped > tbody > tr:nth-of-type(odd) {
    background-color: #f9f9f9;
}

.table-hover > tbody > tr:hover {
    background-color: #f5f5f5;
}

table col[class*="col-"] {
    position: static;
    display: table-column;
    float: none;
}

table td[class*="col-"],
table th[class*="col-"] {
    position: static;
    display: table-cell;
    float: none;
}

.table > thead > tr > td.active,
.table > tbody > tr > td.active,
.table > tfoot > tr > td.active,
.table > thead > tr > th.active,
.table > tbody > tr > th.active,
.table > tfoot > tr > th.active,
.table > thead > tr.active > td,
.table > tbody > tr.active > td,
.table > tfoot > tr.active > td,
.table > thead > tr.active > th,
.table > tbody > tr.active > th,
.table > tfoot > tr.active > th {
    background-color: #f5f5f5;
}

.table-hover > tbody > tr > td.active:hover,
.table-hover > tbody > tr > th.active:hover,
.table-hover > tbody > tr.active:hover > td,
.table-hover > tbody > tr:hover > .active,
.table-hover > tbody > tr.active:hover > th {
    background-color: #e8e8e8;
}

.table > thead > tr > td.success,
.table > tbody > tr > td.success,
.table > tfoot > tr > td.success,
.table > thead > tr > th.success,
.table > tbody > tr > th.success,
.table > tfoot > tr > th.success,
.table > thead > tr.success > td,
.table > tbody > tr.success > td,
.table > tfoot > tr.success > td,
.table > thead > tr.success > th,
.table > tbody > tr.success > th,
.table > tfoot > tr.success > th {
    background-color: #dff0d8;
}

.table-hover > tbody > tr > td.success:hover,
.table-hover > tbody > tr > th.success:hover,
.table-hover > tbody > tr.success:hover > td,
.table-hover > tbody > tr:hover > .success,
.table-hover > tbody > tr.success:hover > th {
    background-color: #d0e9c6;
}

.table > thead > tr > td.info,
.table > tbody > tr > td.info,
.table > tfoot > tr > td.info,
.table > thead > tr > th.info,
.table > tbody > tr > th.info,
.table > tfoot > tr > th.info,
.table > thead > tr.info > td,
.table > tbody > tr.info > td,
.table > tfoot > tr.info > td,
.table > thead > tr.info > th,
.table > tbody > tr.info > th,
.table > tfoot > tr.info > th {
    background-color: #d9edf7;
}

.table-hover > tbody > tr > td.info:hover,
.table-hover > tbody > tr > th.info:hover,
.table-hover > tbody > tr.info:hover > td,
.table-hover > tbody > tr:hover > .info,
.table-hover > tbody > tr.info:hover > th {
    background-color: #c4e3f3;
}

.table > thead > tr > td.warning,
.table > tbody > tr > td.warning,
.table > tfoot > tr > td.warning,
.table > thead > tr > th.warning,
.table > tbody > tr > th.warning,
.table > tfoot > tr > th.warning,
.table > thead > tr.warning > td,
.table > tbody > tr.warning > td,
.table > tfoot > tr.warning > td,
.table > thead > tr.warning > th,
.table > tbody > tr.warning > th,
.table > tfoot > tr.warning > th {
    background-color: #fcf8e3;
}

.table-hover > tbody > tr > td.warning:hover,
.table-hover > tbody > tr > th.warning:hover,
.table-hover > tbody > tr.warning:hover > td,
.table-hover > tbody > tr:hover > .warning,
.table-hover > tbody > tr.warning:hover > th {
    background-color: #faf2cc;
}

.table > thead > tr > td.danger,
.table > tbody > tr > td.danger,
.table > tfoot > tr > td.danger,
.table > thead > tr > th.danger,
.table > tbody > tr > th.danger,
.table > tfoot > tr > th.danger,
.table > thead > tr.danger > td,
.table > tbody > tr.danger > td,
.table > tfoot > tr.danger > td,
.table > thead > tr.danger > th,
.table > tbody > tr.danger > th,
.table > tfoot > tr.danger > th {
    background-color: #f2dede;
}

.table-hover > tbody > tr > td.danger:hover,
.table-hover > tbody > tr > th.danger:hover,
.table-hover > tbody > tr.danger:hover > td,
.table-hover > tbody > tr:hover > .danger,
.table-hover > tbody > tr.danger:hover > th {
    background-color: #ebcccc;
}

.table-responsive {
    min-height: .01%;
    overflow-x: auto;
}

.table-striped > tbody > tr:nth-of-type(odd) {
    background-color: #f9f9f9;
}

.text-center {
    text-align: center;
}

.text-right {
    text-align: right;
}

.table.no-border tr, .table.no-border td {
    border: none;
}
</style>

</head>
<body>

<table class="table no-border">
    <tr>
        <td ><h1>BS 23</h1></td>
        <td class="text-right"><?php echo date("d-M-Y");?></td>
    </tr>
    <tr>
        <td>Expenses Report</td>
        <td class="text-right">Report for Period : <?php echo date("d-M-Y", strtotime($dateFrom)) . " to " . date("d-M-Y", strtotime($dateTo)) ?></td>
    </tr>

</table>



<?php if($sortType=="category") {?>

<table class="table table-bordered table-striped">

    <tr>

        <th class="text-center"><strong>Date</strong></th>
        <th class="text-center"><strong>Vendor</strong></th>
        <th class="text-center"><strong>Tax</strong></th>
        <th class="text-center"><strong>Tip</strong></th>
        <th class="text-center"><strong>Total</strong></th>

    </tr>

    <?php
    foreach ($data as $category) {
        ?>

        <tr>
            <td colspan="5"><?php echo $category->category_name ?></td>
        </tr>

        <?php

        $taxTotal = 0;
        $tipTotal = 0;
        $totalTotal = 0;

        foreach ($category->receipts as $receipt) {
            $taxTotal += $receipt->tax;
            $tipTotal += $receipt->tip;
            $totalTotal += $receipt->total;
            ?>

            <tr>
                <td><?php echo date("d-M-Y", strtotime($receipt->receipt_date)); ?></td>
                <td><?php echo $receipt->vendor_name; ?></td>
                <td class="text-right"><?php echo $receipt->tax; ?></td>
                <td class="text-right"><?php echo $receipt->tip; ?></td>
                <td class="text-right"><?php echo $receipt->total; ?></td>

            </tr>


        <?php
        }?>

        <tr>
            <th></th>
            <th></th>
            <th class="text-right"><strong><?php echo number_format($taxTotal,2); ?></strong></th>
            <th class="text-right"><strong><?php echo number_format($tipTotal,2); ?></strong></th>
            <th class="text-right"><strong><?php echo number_format($totalTotal,2); ?></strong></th>
        </tr>
    <?php
    }
    ?>


</table>

<?php }elseif($sortType=="vendor"){?>
    <table class="table table-bordered table-striped">

        <tr>

            <th class="text-center"><strong>Date</strong></th>
            <th class="text-center"><strong>Category</strong></th>
            <th class="text-center"><strong>Tax</strong></th>
            <th class="text-center"><strong>Tip</strong></th>
            <th class="text-center"><strong>Total</strong></th>

        </tr>

        <?php
        foreach ($data as $vendor) {
            ?>

            <tr>
                <td colspan="5"><?php echo $vendor->vendor_name ?></td>
            </tr>

            <?php

            $taxTotal = 0;
            $tipTotal = 0;
            $totalTotal = 0;

            foreach ($vendor->receipts as $receipt) {
                $taxTotal += $receipt->tax;
                $tipTotal += $receipt->tip;
                $totalTotal += $receipt->total;
                ?>

                <tr>
                    <td><?php echo date("d-M-Y", strtotime($receipt->receipt_date)); ?></td>
                    <td><?php echo $receipt->category_name; ?></td>
                    <td class="text-right"><?php echo $receipt->tax; ?></td>
                    <td class="text-right"><?php echo $receipt->tip; ?></td>
                    <td class="text-right"><?php echo $receipt->total; ?></td>

                </tr>


            <?php
            }?>

            <tr>
                <th></th>
                <th></th>
                <th class="text-right"><strong><?php echo number_format($taxTotal,2); ?></strong></th>
                <th class="text-right"><strong><?php echo number_format($tipTotal,2); ?></strong></th>
                <th class="text-right"><strong><?php echo number_format($totalTotal,2); ?></strong></th>
            </tr>
        <?php
        }
        ?>


    </table>
<?php }elseif($sortType=="date"){?>
    <table class="table table-bordered table-striped">

        <tr>

            <th class="text-center"><strong>Category</strong></th>
            <th class="text-center"><strong>Vendor</strong></th>
            <th class="text-center"><strong>Tax</strong></th>
            <th class="text-center"><strong>Tip</strong></th>
            <th class="text-center"><strong>Total</strong></th>

        </tr>

        <?php
        foreach ($data as $date) {
            ?>

            <tr>
                <td colspan="5"><?php echo date("d-M-Y", strtotime($date->receipt_date)) ?></td>
            </tr>

            <?php

            $taxTotal = 0;
            $tipTotal = 0;
            $totalTotal = 0;

            foreach ($date->receipts as $receipt) {
                $taxTotal += $receipt->tax;
                $tipTotal += $receipt->tip;
                $totalTotal += $receipt->total;
                ?>

                <tr>

                    <td><?php echo $receipt->category_name; ?></td>
                    <td><?php echo $receipt->vendor_name; ?></td>
                    <td class="text-right"><?php echo $receipt->tax; ?></td>
                    <td class="text-right"><?php echo $receipt->tip; ?></td>
                    <td class="text-right"><?php echo $receipt->total; ?></td>

                </tr>


            <?php
            }?>

            <tr>
                <th></th>
                <th></th>
                <th class="text-right"><strong><?php echo number_format($taxTotal,2); ?></strong></th>
                <th class="text-right"><strong><?php echo number_format($tipTotal,2); ?></strong></th>
                <th class="text-right"><strong><?php echo number_format($totalTotal,2); ?></strong></th>
            </tr>
        <?php
        }
        ?>


    </table>
<?php }?>

</body>
</html>




