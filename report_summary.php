<?php
require 'config.php';


$data = [];
foreach ($pdo->query("SELECT campus,
                             COUNT(*) AS registered,
                             SUM(attended='Yes') AS attended,
                             SUM(amountPaid) AS collection
                      FROM Registration GROUP BY campus") as $r) {
    $data[$r['campus']] = $r;
}

page_start('Report (Summary)');
?>
<div class="links"><a href="index.php">Back to Menu</a></div>
<h3 style="text-align:center"><i>Summary Report</i><br><small>(All Campuses)</small></h3>
<table>
  <tr><th>Campus</th><th>Registered</th><th>Attended</th><th>Total Collection</th></tr>
<?php
$tReg = $tAtt = $tCol = 0;
foreach ($CAMPUSES as $c) {
    
    $reg = (int)($data[$c]['registered'] ?? 0);
    $att = (int)($data[$c]['attended'] ?? 0);
    $col = (float)($data[$c]['collection'] ?? 0);
    $tReg += $reg; $tAtt += $att; $tCol += $col;
    echo "<tr><td>" . h($c) . "</td><td>$reg</td><td>$att</td><td>" . number_format($col, 2) . "</td></tr>";
}
echo "<tr><th>TOTALS</th><th>$tReg</th><th>$tAtt</th><th>" . number_format($tCol, 2) . "</th></tr>";
?>
</table>
<p><b>Date Generated:</b> <?= date('n/j/Y') ?></p>
<?php page_end(); ?>
