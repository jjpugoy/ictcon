<?php
require 'config.php';
$id  = trim($_GET['id'] ?? '');
$msg = '';
$msgClass = 'err';
$student = null;

if ($id !== '') {
    $s = $pdo->prepare("SELECT * FROM Registration WHERE idNum=?");
    $s->execute([$id]);
    $student = $s->fetch();

    if (!$student) {
        // Prompt 1
        $msg = "ID # is NOT YET REGISTERED";
    } elseif ($student['attended'] === 'Yes') {
        // Prompt 3
        $msg = "Student Attendance RECORD ALREADY EXISTS";
    } else {
        $pdo->prepare("UPDATE Registration SET attended='Yes' WHERE idNum=?")->execute([$id]);
        $student['attended'] = 'Yes';
        // Prompt 2
        $msg = "Student Attendance is SUCCESSFULLY RECORDED";
        $msgClass = 'ok';
    }
}

page_start('Attendance');
?>
<div class="links"><a href="index.php">Back to Menu</a></div>
<form method="get">
  Input ID #: <input name="id" value="<?= h($id) ?>" required autofocus>
  <button type="submit">Submit</button>
</form>
<?php
if ($msg) echo "<p class='$msgClass'>" . h($msg) . "</p>";

if ($student) {
    echo '<table><tr><th>ID #</th><th>Name</th><th>Campus</th><th>Amount</th><th>Action</th></tr>';
    echo "<tr><td>" . h($student['idNum']) . "</td>
    <td>" . h($student['studLName'] . ', ' . $student['studFName']) . "</td>
    <td>" . h($student['campus']) . "</td>
    <td>" . number_format($student['amountPaid'], 2) . "</td>
    <td>Attended</td></tr></table>";
}
page_end();
