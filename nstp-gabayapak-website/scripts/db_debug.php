<?php
$host = '127.0.0.1';
$port = 3306;
$db = 'nstp-gabayapak_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "Connected to DB $db\n";

    echo "Projects by component and status:\n";
    $stmt = $pdo->query("SELECT Project_Component, Project_Status, COUNT(*) as cnt FROM projects GROUP BY Project_Component, Project_Status ORDER BY Project_Component, Project_Status");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        echo "- Component: " . ($r['Project_Component'] ?? '(null)') . " | Status: " . ($r['Project_Status'] ?? '(null)') . " => " . $r['cnt'] . "\n";
    }

    echo "\nActivities joined to project component/status (recent):\n";
    $stmt = $pdo->query("SELECT p.Project_Component, p.Project_Status, COUNT(a.Activity_ID) as cnt FROM activities a JOIN projects p ON a.project_id = p.Project_ID GROUP BY p.Project_Component, p.Project_Status ORDER BY cnt DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        echo "- Component: " . ($r['Project_Component'] ?? '(null)') . " | Status: " . ($r['Project_Status'] ?? '(null)') . " => " . $r['cnt'] . "\n";
    }

    echo "\nSample current projects (id, component):\n";
    $stmt = $pdo->query("SELECT Project_ID, Project_Component FROM projects WHERE Project_Status = 'current' LIMIT 10");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        echo "- " . ($r['Project_ID'] ?? '') . " | " . ($r['Project_Component'] ?? '') . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
