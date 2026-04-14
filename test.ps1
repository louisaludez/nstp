<?php
$port=11492; 
$proc = Start-Process php -ArgumentList "-S 127.0.0.1:$port router.php" -NoNewWindow -PassThru; 
Start-Sleep -s 1; 

try { 
    $res = Invoke-WebRequest -Method Post -Uri "http://127.0.0.1:$port/login.php" -Body @{username='admin'; password='password'} -SessionVariable session;
} catch {
    $res = $_.Exception.Response
}

try { 
    $page = Invoke-WebRequest "http://127.0.0.1:$port/admin/manage_students" -WebSession $session;
    echo "SUCCESS: "
    echo $page.Content.Substring(0, 100)
} catch { 
    echo "ERROR: "
    echo $_.Exception.Message
    echo $_.Exception.Response.StatusCode
}

Stop-Process -Id $proc.Id;
