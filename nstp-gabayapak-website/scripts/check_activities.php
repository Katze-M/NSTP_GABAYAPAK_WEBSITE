<?php
// Simple DB check script for activities
$host = '127.0.0.1';
$port = 3306;
$db = 'nstp-gabayapak_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$today = date('Y-m-d');
$startWindow = date('Y-m-d', strtotime('-7 days'));
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    echo "Connected to DB $db\n";

    $total = $pdo->query("SELECT COUNT(*) FROM activities")->fetchColumn();
    echo "Total activities: $total\n";

    $withDate = $pdo->query("SELECT COUNT(*) FROM activities WHERE Implementation_Date IS NOT NULL")->fetchColumn();
    echo "With Implementation_Date not null: $withDate\n";

    $upcoming = $pdo->query("SELECT COUNT(*) FROM activities WHERE Implementation_Date IS NOT NULL AND Implementation_Date >= '$startWindow'")->fetchColumn();
    echo "Upcoming (>= $startWindow): $upcoming\n";

        $minDate = $pdo->query("SELECT MIN(Implementation_Date) FROM activities WHERE Implementation_Date IS NOT NULL")->fetchColumn();
        $maxDate = $pdo->query("SELECT MAX(Implementation_Date) FROM activities WHERE Implementation_Date IS NOT NULL")->fetchColumn();
        echo "Date range in activities: " . ($minDate ?? 'NULL') . " -> " . ($maxDate ?? 'NULL') . "\n";

    echo "Sample upcoming activities (limit 10):\n";
    $stmt = $pdo->query("SELECT a.Activity_ID, a.Specific_Activity, a.Implementation_Date, p.Project_ID, p.Project_Name, p.Project_Component, p.Project_Section FROM activities a LEFT JOIN projects p ON a.project_id = p.Project_ID WHERE a.Implementation_Date IS NOT NULL AND a.Implementation_Date >= '$startWindow' ORDER BY a.Implementation_Date LIMIT 10");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($rows)) {
        echo "(none)\n";
    } else {
        foreach ($rows as $r) {
            echo "- [" . ($r['Implementation_Date'] ?? 'NULL') . "] (AID:" . $r['Activity_ID'] . ") " . substr($r['Specific_Activity'],0,60) . " -- Project: " . ($r['Project_Name'] ?? 'N/A') . " (" . ($r['Project_Component'] ?? '') . " / " . ($r['Project_Section'] ?? '') . ")\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
