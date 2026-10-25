<?php
// Temporary helper — delete this file after use!
// Visit: http://localhost/Buli-website/gen_hash.php

$passwords = [
    'staff@123',
    'abebe2026',
    'mekdes2026',
    'girma2026',
    'mypassword',
];

echo "<h2>Password Hash Generator</h2>";
echo "<p style='color:red;font-weight:bold;'>DELETE this file after use!</p>";
echo "<table border='1' cellpadding='8' style='border-collapse:collapse; font-family:monospace;'>";
echo "<tr><th>Password</th><th>Hash (copy this into SQL)</th></tr>";
foreach ($passwords as $p) {
    $hash = password_hash($p, PASSWORD_DEFAULT);
    echo "<tr><td>$p</td><td>$hash</td></tr>";
}
echo "</table>";
echo "<br><hr>";
echo "<h3>Or hash a custom password:</h3>";
if (isset($_POST['pw']) && $_POST['pw']) {
    $h = password_hash($_POST['pw'], PASSWORD_DEFAULT);
    echo "<p><strong>Hash:</strong> <code>$h</code></p>";
}
echo "<form method='POST'>
    <input type='text' name='pw' placeholder='Enter password' style='padding:6px; width:200px;'>
    <button type='submit' style='padding:6px 12px;'>Generate Hash</button>
</form>";
?>
