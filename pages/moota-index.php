<div class="wrap">
    <style>
        .money { text-align: right; font-family: monospace; }
        td.money > span:first-child { display: block; float:left; }
        td.money > .amount { display: block; float:right; }
        span.note {
            font-size:85%;
            font-style:italic;
            margin:3px 5px 0 5px;
        }
        .underlined { text-decoration: underline; }
    </style>

    <form method="GET" id="moota-filter">
        <input type="hidden" name="page" value="moota">
        <h2 class="screen-reader-text">Filter Moota</h2>

        <ul class="subsubsub pull-left bank-filter">
        <li><span class="bank-label">Bank: </span>
            <select name="bank" id="mootaBankSelector">

            <?php
            $count = 0;
            $selectedBank = $_GET['bank'];


            foreach ($banks['data'] as $bank):
            ?>
                <option value="<?php echo $bank['bank_id']; ?>"
                    <?php
                        if (
                            (empty($selectedBank) && $count === 0)
                            || $selectedBank == $bank['bank_id']
                        ) {
                            $currBank = $bank;

                            echo 'selected="selected"';
                        }
                    ?>
                ><?php
                    echo strtoupper($bank['bank_type'])
                        . " | a.n: {$bank['atas_nama']},"
                        . " NoRek: {$bank['account_number']}";
                ?></option>
            <?php
            $count++;
            endforeach;
            ?>

            </select>
        </li>
        </ul>

        <h2 class="screen-reader-text">Moota List</h2>
        <table class="wp-list-table widefat fixed striped pages">
            <thead>
            <tr>
                <th class="manage-column column-format"
                    scope="col"
                >Tanggal</th>

                <th class="manage-column column-format"
                    scope="col"
                >Tipe</th>

                <th class="manage-column column-format"
                    scope="col"
                >Jumlah</th>

                <th class="manage-column column-title column-primary"
                    scope="col"
                >Keterangan</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($transactions as $t): ?>
            <tr class="hentry">
                <td><?php
                    $shortDate = moota_short_date($t['date'], $date);
                    $humanDate = moota_human_date($date);
                    echo $shortDate;
                    echo "<br><small>{$humanDate}</small>";
                ?></td>

                <td><?php echo (
                    $t['type'] === 'CR' ? 'Credit' : 'Debit'
                    ); ?></td>

                <td class="money">
                    <span>Rp.</span>
                    <span class="amount"><?php
                        echo moota_rp_format($t['amount'], false);
                        ?></span>
                </td>

                <td class="column-title column-primary page-title"
                ><?php
                    echo $t['description'];

                    if ( !empty($t['note']) && strlen($t['note']) > 0 ) {
                        echo '<br><hr><span class="note">Note: '
                            . $t['note'] .'</span>';
                    }
                ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>

            <tfoot>
            <tr>
                <th class="manage-column column-format"
                    scope="col"
                >Tanggal</th>

                <th class="manage-column column-format"
                    scope="col"
                >Tipe</th>

                <th class="manage-column column-format"
                    scope="col"
                >Jumlah</th>

                <th class="manage-column column-title column-primary"
                    scope="col"
                >Keterangan</th>
            </tr>
            </tfoot>
        </table>
    </form>
</div>

<?php
add_action('admin_footer', function() {
    if ( !empty($_GET['page']) && $_GET['page'] === 'moota' ) {
?>
<script>!(function(){
    var _j = jQuery;
    _j(document).ready(function(){
        var selector = _j('#mootaBankSelector');
        selector.change(function(){
            _j(this).parents('form:first').submit();
        });
    })
})()</script>
<?php
    }
});
