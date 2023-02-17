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
                        <form id="regenerate-form">
                            <div class="align-items-end">
                                <div class="form-row">
                                    <div class="col-3">
                                        <label class="mb-0" for="frm-from"><small>From invoice #:</small></label>
                                        <input type="number" name="from" id="frm-from" placeholder="X" class="form-control form-control-sm" min="0" value="0" max="<?= count($invoices) ?>">
                                    </div>

                                    <div class="col-3">
                                        <label class="mb-0" for="frm-to"><small>To Invoice #:</small></label>
                                        <input type="number" name="to" id="frm-to" placeholder="Y" class="form-control form-control-sm" min="0" max="<?= count($invoices) ?>">
                                    </div>

                                    <div class="col-auto ml-auto">
                                        <button type="submit" class="btn btn-primary btn-sm pl-4 pr-4">Regenerate</button>
                                    </div>
                                </div>

                                <br />

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
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const rangeForm = document.getElementById('regenerate-form');

        rangeForm.addEventListener('change', () => {
            const from = document.getElementById('frm-from').value;
            const to = document.getElementById('frm-to').value;

            const checkboxes = document.querySelectorAll('input[type="checkbox"]');

            for (let i = 0; i < checkboxes.length; i++) {
                if (i >= from && i <= to) {
                    checkboxes[i].checked = true;
                } else {
                    checkboxes[i].checked = false;
                }

                if (from > to) {
                    checkboxes[i].checked = false;
                }

                if (from == 0 && to == 0) {
                    checkboxes[i].checked = false;
                }

                if (from == 0 && to == checkboxes.length) {
                    checkboxes[i].checked = true;
                }
            }
        });
    </script>
</body>