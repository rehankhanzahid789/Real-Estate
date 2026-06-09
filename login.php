<?php
require_once 'includes/auth.php';
$page='login'; $page_title='Sign in — Billah Dee King';
if (current_user()) redirect('dashboard.php');
$err=null;
if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf']??'')) {
    [$ok,$msg] = login_user($_POST['email']??'', $_POST['password']??'');
    if ($ok) redirect('dashboard.php');
    $err = $msg;
    if (!empty($_SESSION['pending_verify_user'])) redirect('verify-email.php');
}
include 'includes/header.php'; ?>
<div class="container">
  <div class="form-card">
    <h1>Welcome back</h1>
    <p class="sub">Sign in to your Billah Dee King account.</p>
    <?php if ($err): ?><div class="alert error"><?= e($err) ?></div><?php endif; ?>
    <?php if ($m=flash('ok')): ?><div class="alert success"><?= e($m) ?></div><?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <div class="form-row"><label>Email</label><input type="email" name="email" required></div>
      <div class="form-row"><label>Password</label><input type="password" name="password" required></div>
      <button class="btn btn-gold btn-block">Sign in</button>
    </form>
    <div class="form-foot"><a href="forgot-password.php">Forgot password?</a> · New here? <a href="register.php">Create an account</a></div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
