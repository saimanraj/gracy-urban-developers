<?php
// ===============================================
// CONTACT.PHP – FINAL (PHPMailer + SMTP)
// ===============================================

// Block direct access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.html");
    exit;
}

// -----------------------------------------------
// 1. LOAD PHPMailer
// -----------------------------------------------
require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

$mail = new PHPMailer\PHPMailer\PHPMailer(true);

// -----------------------------------------------
// 2. COLLECT FORM DATA
// -----------------------------------------------
$name          = trim($_POST['name'] ?? '');
$phone         = trim($_POST['phone'] ?? '');
$email         = trim($_POST['email'] ?? '');
$service       = trim($_POST['service'] ?? 'General Enquiry');
$location      = trim($_POST['location'] ?? '');
$building_type = trim($_POST['building_type'] ?? '');
$ulb_name      = trim($_POST['ulb_name'] ?? '');
$property_type = trim($_POST['property_type'] ?? '');
$budget        = trim($_POST['budget'] ?? '');
$price         = trim($_POST['price'] ?? '');
$message       = trim($_POST['message'] ?? '');

if ($name === '' || $phone === '') {
    exit("Name and Phone Number are required.");
}

// -----------------------------------------------
// 3. SAVE LEAD TO CSV
// -----------------------------------------------
$csv = __DIR__ . "/leads.csv";
$data = [
    date("Y-m-d H:i:s"),
    $service,
    $name,
    $phone,
    $email,
    $location,
    $building_type,
    $ulb_name,
    $property_type,
    $budget,
    $price,
    $message
];

$f = fopen($csv, "a");
fputcsv($f, $data);
fclose($f);

// -----------------------------------------------
// 4. PREPARE EMAIL CONTENT
// -----------------------------------------------
$body  = "New Enquiry Received\n\n";
$body .= "Service: $service\n";
$body .= "Name: $name\n";
$body .= "Phone: $phone\n";
if ($email)         $body .= "Email: $email\n";
if ($location)      $body .= "Location: $location\n";
if ($building_type) $body .= "Building Type: $building_type\n";
if ($ulb_name)      $body .= "ULB Name: $ulb_name\n";
if ($property_type) $body .= "Property Type: $property_type\n";
if ($budget)        $body .= "Budget: $budget\n";
if ($price)         $body .= "Expected Price: $price\n";
$body .= "\nMessage:\n$message\n";

// -----------------------------------------------
// 5. SEND EMAIL (SMTP – HOSTINGER)
// -----------------------------------------------
$mailSent = false;

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.hostinger.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@gracyud.in';
    $mail->Password   = 'Gracy@2025';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('info@gracyud.in', 'Gracy Urban Developers');
    $mail->addAddress('info@gracyud.in');

    if ($email) {
        $mail->addReplyTo($email, $name);
    }

    $mail->Subject = "New Enquiry – $service";
    $mail->Body    = $body;

    $mail->send();
    $mailSent = true;

} catch (Exception $e) {
    file_put_contents(
        __DIR__ . "/mail_error.log",
        date("Y-m-d H:i:s") . " - " . $mail->ErrorInfo . PHP_EOL,
        FILE_APPEND
    );
    $mailSent = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Enquiry Submitted | Gracy Urban Developers</title>
<link rel="icon" href="favicon.jpg">
<link rel="stylesheet" href="styles.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
<header>
  <div class="header-inner">
    <div class="logo">
      <img src="img/logo.jpg" class="logo-img" alt="Gracy Urban Developers">
      <div class="logo-text">
        <span>GRACY</span> URBAN DEVELOPERS
        <p style="font-size:12px;font-style:italic;color:red;">
          Expert approvals. Faster Clearances.
        </p>
      </div>
    </div>
  </div>
</header>

<main>
  <div class="wrapper section">
    <h1>Thank You</h1>

    <?php if ($mailSent): ?>
      <p>Your enquiry has been submitted successfully. Our team will contact you shortly.</p>
    <?php else: ?>
      <p>Your enquiry was saved, but email delivery failed.<br>
      Please call us directly.</p>
      <strong>📞 7700998977</strong>
    <?php endif; ?>

    <a href="index.html" class="btn btn-primary">Return to Home</a>
  </div>
</main>

<footer>
  <small>&copy; <?= date("Y") ?> Gracy Urban Developers.</small>
</footer>
</body>
</html>
