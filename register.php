<?php
require_once 'includes/auth.php';
$page='register'; $page_title='Create account — Billah Dee King';
if (current_user()) redirect('dashboard.php');
$err=$ok=null;
if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_check($_POST['csrf']??'')) {
    [$success,$msg] = register_user($_POST['name']??'',$_POST['email']??'',$_POST['password']??'');
    if ($success) { flash('ok',$msg); redirect('verify-email.php'); }
    $err=$msg;
}
include 'includes/header.php'; ?>
<div class="container">
  <div class="form-card">
    <h1>Create your account</h1>
    <p class="sub">Save favorites, track inquiries and receive curated off-market listings.</p>
    <?php if ($err): ?><div class="alert error"><?= e($err) ?></div><?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <div class="form-row"><label>Full name</label><input name="name" required></div>
      <div class="form-row"><label>Email</label><input type="email" name="email" required></div>
      <div class="form-row"><label>Password</label><input type="password" name="password" required minlength="8"></div>
      <button class="btn btn-gold btn-block">Create account</button>
    </form>
    <div class="form-foot">Already have an account? <a href="login.php">Sign in</a></div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
