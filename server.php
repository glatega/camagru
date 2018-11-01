<pre>
<?php
print_r($_SERVER);
$ip = gethostbyname(gethostname());
echo "isp == " . $ip;
?>
</pre>
