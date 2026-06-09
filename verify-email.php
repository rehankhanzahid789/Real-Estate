<?php
require_once 'includes/auth.php';
$page='register'; $page_title='Verify email — Billah Dee King';
$uid = $_SESSION['pending_verify_user'] ?? null;
if (!$uid) redirect('login.php');
$err=null;
if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf']??'')) {
    [$ok,$msg] = verify_email_code($uid, $_POST['code']??'');
    if ($ok) { unset($_SESSION['pending_verify_user']); flash('ok',$msg); redirect('login.php'); }
    $err=$msg;
}
include 'includes/header.php'; ?>
<div class="container">
  <div class="form-card">
    <h1>Verify your email</h1>
    <p class="sub">We sent a 6-digit code to your inbox. Enter it below to activate your account.</p>
    <?php if ($err): ?><div class="alert error"><?= e($err) ?></div><?php endif; ?>
    <?php if ($m=flash('ok')): ?><div class="alert success"><?= e($m) ?></div><?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <div class="form-row"><label>Verification code</label><input name="code" required maxlength="6" style="letter-spacing:8px;text-align:center;font-size:1.2rem"></div>
      <button class="btn btn-gold btn-block">Verify</button>
    </form>
    <div class="form-foot">Wrong account? <a href="logout.php">Start over</a></div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
