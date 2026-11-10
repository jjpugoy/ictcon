<?php
require 'config.php';
$generate = isset($_GET['generate']);
$picked   = array_values(array_intersect($_GET['campus'] ?? [], $CAMPUSES));

page_start('Report (By Campus)');
?>
<div class="links"><a href="index.php">Back to Menu</a></div>
<form method="get">
  Set filters here:
  <?php foreach ($CAMPUSES as $c): ?>
    <label style="display:inline"><input type="checkbox" name="campus[]" value="<?= h($c) ?>" <?= in_array($c, $picked) ? 'checked' : '' ?>> <?= h($c) ?></label>
  <?php endforeach; ?>
  <p><button type="submit" name="generate" value="1" style="font-size:18px;padding:8px 20px;">Generate Report</button></p>
</form>
<?php
if ($generate) {
    if (!$picked) {
        echo "<p class='err'>Please tick at least one campus.</p>";
    } else {
        $in = implode(',', array_fill(0, count($picked), '?'));
        $q = $pdo->prepare("SELECT * FROM Registration WHERE campus IN ($in) ORDER BY campus, studLName, studFName");
        $q->execute($picked);

        $regs = 0; $att = 0; $total = 0;
        echo '<table><tr><th>ID #</th><th>Name</th><th>Campus</th><th>Amount</th><th>Attended</th></tr>';
        foreach ($q as $r) {
            $regs++;
            $total += $r['amountPaid'];
            if ($r['attended'] === 'Yes') $att++;
            echo "<tr><td>" . h($r['idNum']) . "</td>
            <td>" . h($r['studLName'] . ', ' . $r['studFName']) . "</td>
            <td>" . h($r['campus']) . "</td>
            <td>" . number_format($r['amountPaid'], 2) . "</td>
            <td>" . h($r['attended']) . "</td></tr>";
        }
        echo '</table>';
        echo "<p><b># of Registrants:</b> $regs<br>
              <b>Total Collection:</b> " . number_format($total, 2) . "<br>
              <b># of Attendees:</b> $att<br>
              <b>Date Generated:</b> " . date('n/j/Y') . "</p>";
    }
}
page_end();
