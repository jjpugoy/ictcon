<?php
require 'config.php';

$all     = isset($_GET['reveal']) ? isset($_GET['all']) : true;
$picked  = array_values(array_intersect($_GET['campus'] ?? [], $CAMPUSES));
$reveal  = isset($_GET['reveal']);
$winner  = null;
$noPool  = false;

if ($reveal) {
    
    $sql = "SELECT * FROM Registration WHERE attended='Yes'";
    $params = [];
    if (!$all && $picked) {
        $sql .= " AND campus IN (" . implode(',', array_fill(0, count($picked), '?')) . ")";
        $params = $picked;
    }
    $sql .= " ORDER BY RAND() LIMIT 1";
    $q = $pdo->prepare($sql);
    $q->execute($params);
    $winner = $q->fetch();
    $noPool = !$winner;
}

page_start('Raffle');
?>
<div class="links"><a href="index.php">Back to Menu</a></div>
<form method="get">
  Set filters here:
  <label style="display:inline"><input type="checkbox" name="all" value="1" <?= $all ? 'checked' : '' ?>> All</label>
  <?php foreach ($CAMPUSES as $c): ?>
    <label style="display:inline"><input type="checkbox" name="campus[]" value="<?= h($c) ?>" <?= in_array($c, $picked) ? 'checked' : '' ?>> <?= h($c) ?></label>
  <?php endforeach; ?>
  <p><button type="submit" name="reveal" value="1" style="font-size:18px;padding:8px 20px;">Reveal the Lucky Winner!</button></p>
</form>
<?php
if ($noPool) {
    echo "<p class='err'>No attendees found for the selected filter.</p>";
} elseif ($winner) {
    echo "<table><tr><th>ID #</th><th>Name</th><th>Campus</th></tr>
    <tr><td>" . h($winner['idNum']) . "</td>
    <td>" . h($winner['studLName'] . ', ' . $winner['studFName']) . "</td>
    <td>" . h($winner['campus']) . "</td></tr></table>
    <p class='winner'><i>CONGRATULATIONS!!!</i></p>";
}
page_end();
