<?php
require 'config.php';
$mode = $_GET['mode'] ?? 'list';   
$error = '';


if (isset($_GET['del'])) {
    $pdo->prepare("DELETE FROM Registration WHERE idNum=?")->execute([$_GET['del']]);
    header('Location: registration.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($_POST['mode'] === 'add') {
            
            $pdo->prepare("INSERT INTO Registration (idNum,campus,studFName,studLName,amountPaid,attended) VALUES (?,?,?,?,?,'No')")
                ->execute([trim($_POST['idNum']), $_POST['campus'], trim($_POST['studFName']), trim($_POST['studLName']), $_POST['amountPaid']]);
        } else {
            $pdo->prepare("UPDATE Registration SET campus=?, studFName=?, studLName=?, amountPaid=? WHERE idNum=?")
                ->execute([$_POST['campus'], trim($_POST['studFName']), trim($_POST['studLName']), $_POST['amountPaid'], $_POST['idNum']]);
        }
        header('Location: registration.php');
        exit;
    } catch (PDOException $e) {
        $error = "Save failed - is the ID # already registered?";
        $mode = $_POST['mode'];
    }
}

page_start('Registration');
if ($error) echo "<p class='err'>" . h($error) . "</p>";

if ($mode === 'add' || $mode === 'edit') {
    $row = ['idNum'=>'','campus'=>'','studFName'=>'','studLName'=>'','amountPaid'=>''];
    if ($mode === 'edit') {
        $s = $pdo->prepare("SELECT * FROM Registration WHERE idNum=?");
        $s->execute([$_GET['id']]);
        $row = $s->fetch() ?: $row;
    }
    ?>
    <form class="box" method="post">
      <input type="hidden" name="mode" value="<?= h($mode) ?>">
      <label>ID #</label>
      <input name="idNum" value="<?= h($row['idNum']) ?>" required <?= $mode==='edit' ? 'readonly' : '' ?>>
      <label>First Name</label><input name="studFName" value="<?= h($row['studFName']) ?>" required>
      <label>Last Name</label><input name="studLName" value="<?= h($row['studLName']) ?>" required>
      <label>Campus</label>
      <select name="campus" required>
        <?php foreach ($CAMPUSES as $c): ?>
          <option <?= $c === $row['campus'] ? 'selected' : '' ?>><?= h($c) ?></option>
        <?php endforeach; ?>
      </select>
      <label>Amount Paid</label><input type="number" step="0.01" name="amountPaid" value="<?= h($row['amountPaid']) ?>" required>
      <p><button type="submit">Save</button> <a href="registration.php">Cancel</a></p>
    </form>
    <?php
} else {
    echo '<div class="links"><a href="registration.php?mode=add">Register a Student Here</a> | <a href="index.php">Back to Menu</a></div>';
    echo '<table><tr><th>ID #</th><th>Name</th><th>Campus</th><th>Amount</th><th>Actions</th></tr>';
    foreach ($pdo->query("SELECT * FROM Registration ORDER BY studLName, studFName") as $r) {
        echo "<tr><td>" . h($r['idNum']) . "</td>
        <td>" . h($r['studLName'] . ', ' . $r['studFName']) . "</td>
        <td>" . h($r['campus']) . "</td>
        <td>" . number_format($r['amountPaid'], 2) . "</td>
        <td><a href='registration.php?mode=edit&id=" . h($r['idNum']) . "'>Edit</a> |
        <a href='registration.php?del=" . h($r['idNum']) . "' onclick=\"return confirm('Delete this registration?')\">Del</a></td></tr>";
    }
    echo '</table>';
}
page_end();
