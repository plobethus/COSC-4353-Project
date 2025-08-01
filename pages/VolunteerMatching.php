<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /pages/Login.php');
    exit;
}

require_once __DIR__ . '/../backend/db.php';

$stmt = $pdo->prepare("
    SELECT user_id,
           email AS volunteer_name
      FROM UserCredentials
     WHERE role = 'volunteer'
     ORDER BY email
");
$stmt->execute();
$volunteers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT event_id,
           event_name
      FROM EventDetails
     ORDER BY event_date ASC
");
$stmt->execute();
$matchedEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

$errors  = $_SESSION['errors']  ?? [];
unset($_SESSION['errors']);
$success = isset($_GET['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Volunteer Matching</title>
  <link rel="stylesheet" href="/css/global.css">
</head>
<body class="centered-page">
  <div class="event-container">
    <h2>Volunteer Matching</h2>

    <?php if ($success): ?>
      <div class="success-message">
        Volunteer matched successfully!
      </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="error-messages">
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form action="/backend/controllers/process_match.php" method="post">
      <div class="form-group">
        <label for="volunteer_name">Volunteer Name <span>*</span></label><br>
        <select id="volunteer_name" name="volunteer_name" required>
          <option value="" disabled selected>Select a volunteer</option>
          <?php foreach ($volunteers as $v): ?>
            <option value="<?= $v['user_id'] ?>">
              <?= htmlspecialchars($v['volunteer_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="matched_event">Matched Event <span>*</span></label><br>
        <select id="matched_event" name="matched_event" required>
          <option value="" disabled selected>Select matched event</option>
          <?php foreach ($matchedEvents as $e): ?>
            <option value="<?= $e['event_id'] ?>">
              <?= htmlspecialchars($e['event_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <button type="submit">Match Volunteer</button>
    </form>
  </div>
</body>
</html>
