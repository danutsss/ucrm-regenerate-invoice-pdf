<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice PDF regenerate</title>
    <link rel="stylesheet" href="<?php echo rtrim(htmlspecialchars($ucrmPublicUrl, ENT_QUOTES), '/'); ?>/assets/fonts/lato/lato.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="public/main.css">
</head>

<body>
    <div id="header">
        <h1>Select invoice to regenerate</h1>
    </div>
    <div id="content" class="container-fluid ml-0 mr-0">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="export-form">
                            <div class="align-items-end">
                                <div class="form-row">
                                    <div class="col-6 input-group">
                                        <input type="number" class="form-control form-control-sm" id="frm-select-all" name="frm-select-all" placeholder="Input selection value" />
                                        <div class="input-group-append">
                                            <button class="btn btn-primary btn-sm pl-4 pr-4" type="submit">Regenerate</button>
                                        </div>
                                    </div>
                                </div>

                                # of invoices: <?= count($invoices) ?>


                                <!-- create a table with all of the invoices -->
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">Invoice #</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">Organization</th>
                                            <th scope="col">Regenerate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($invoices as $invoice) {
                                            printf(
                                                '<tr>
                                                        <td>%s (id: %s)</td>
                                                        <td>%s</td>
                                                        <td>%s</td>
                                                        <td>
                                                            <div>
                                                                <input type="checkbox" name="regenerate[]" value="%s" />
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    ',
                                                $invoice['number'],
                                                $invoice['id'],
                                                $invoice['createdDate'],
                                                $invoice['organizationName'],
                                                $invoice['id']
                                            );
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        // When user inputs a value in the field, select the number of checkboxes equal to the value.
        document.getElementById('frm-select-all').addEventListener('input', function() {
            var checkboxes = document.querySelectorAll('input[type="checkbox"]');
            var value = parseInt(this.value);

            if (value > checkboxes.length) {
                value = checkboxes.length;
            }

            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = i < value;
            }
        });
    </script>
</body>