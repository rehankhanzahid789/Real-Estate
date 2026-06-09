<?php
require_once 'includes/functions.php';
require_once 'includes/mailer.php';
$page='contact'; $page_title='Contact — Billah Dee King';
$err=$ok=null;

if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf'] ?? '')) {
    $n=trim($_POST['name']??''); $em=trim($_POST['email']??''); $s=trim($_POST['subject']??''); $m=trim($_POST['message']??'');
    if ($n===''||$em===''||$m==='') $err='Please complete the required fields.';
    elseif (!filter_var($em,FILTER_VALIDATE_EMAIL)) $err='Invalid email.';
    elseif (strlen($m) > 5000) $err='Message is too long.';
    else {
        db()->prepare('INSERT INTO contact_messages (name,email,subject,message) VALUES (?,?,?,?)')
            ->execute([$n,$em,$s,$m]);
        send_mail(env('EMAIL_FROM_ADDRESS','hello@billahdeeking.com'),
            'New contact form: '.$s,
            "<p><b>From:</b> ".e($n)." &lt;".e($em)."&gt;</p><p>".nl2br(e($m))."</p>");
        $ok='Thank you — your message has been received.';
    }
}
include 'includes/header.php';
?>
<section class="page-head">
  <div class="container">
    <span class="eyebrow">Get in Touch</span>
    <h1>Contact our team</h1>
    <p class="muted" style="max-width:620px">Whether you're considering a private sale or seeking a specific kind of residence, an advisor will respond within one business day.</p>
  </div>
</section>

<section class="section">
  <div class="container contact-grid">
    <div class="contact-info">
      <h3>Our offices</h3>
      <div class="divider-gold"></div>
      <p><strong>New York</strong>1 Park Avenue, Suite 2200<br>New York, NY 10016</p>
      <p><strong>Los Angeles</strong>9560 Wilshire Boulevard<br>Beverly Hills, CA 90212</p>
      <p><strong>Email</strong>hello@billahdeeking.com</p>
      <p><strong>Phone</strong>+1 (212) 555-0144</p>
      <div class="map-placeholder" style="margin-top:24px">Google Maps Embed Placeholder</div>
    </div>

    <div>
      <?php if ($ok): ?><div class="alert success"><?= e($ok) ?></div>
      <?php elseif ($err): ?><div class="alert error"><?= e($err) ?></div><?php endif; ?>
      <form method="post" class="form-card wide" style="margin:0">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <div class="form-grid-2">
          <div class="form-row"><label>Name</label><input name="name" required></div>
          <div class="form-row"><label>Email</label><input type="email" name="email" required></div>
        </div>
        <div class="form-row"><label>Subject</label><input name="subject"></div>
        <div class="form-row"><label>Message</label><textarea name="message" required></textarea></div>
        <button class="btn btn-gold btn-block">Send message</button>
      </form>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
