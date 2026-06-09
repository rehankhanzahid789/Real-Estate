<?php
require_once 'includes/auth.php';
$page='login'; $page_title='Reset password — Billah Dee King';
$step = $_POST['step'] ?? 'request';
$err=$ok=null;

if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf']??'')) {
    if ($step==='request') {
        [$s,$m] = send_password_reset($_POST['email']??'');
        $ok = $m; $step='reset';
    } elseif ($step==='reset') {
        [$s,$m] = reset_password($_POST['email']??'', $_POST['code']??'', $_POST['password']??'');
        if ($s) { flash('ok',$m); redirect('login.php'); }
        $err=$m;
    }
}
include 'includes/header.php'; ?>
<div class="container">
  <div class="form-card">
    <h1>Reset your password</h1>
    <p class="sub"><?= $step==='request' ? 'Enter your email and we\'ll send you a reset code.' : 'Enter the code we sent you and a new password.' ?></p>
    <?php if ($err): ?><div class="alert error"><?= e($err) ?></div><?php endif; ?>
    <?php if ($ok): ?><div class="alert success"><?= e($ok) ?></div><?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <?php if ($step==='request'): ?>
        <input type="hidden" name="step" value="request">
        <div class="form-row"><label>Email</label><input type="email" name="email" required></div>
        <button class="btn btn-gold btn-block">Send code</button>
      <?php else: ?>
        <input type="hidden" name="step" value="reset">
        <div class="form-row"><label>Email</label><input type="email" name="email" required value="<?= e($_POST['email']??'') ?>"></div>
        <div class="form-row"><label>Code</label><input name="code" required maxlength="6"></div>
        <div class="form-row"><label>New password</label><input type="password" name="password" required minlength="8"></div>
        <button class="btn btn-gold btn-block">Reset password</button>
      <?php endif; ?>
    </form>
    <div class="form-foot"><a href="login.php">Back to sign in</a></div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
