<?php include __DIR__ . '/layout/header.php';?>

<h2>Create an Account</h2>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="<?= url('/register')?>">
    <div class="mb-3">
        <label for="name" class="form-label">Full Name</label>
        <input type="text" name="name" id="name" class="form-control"
               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
    </div>
    <div class="mb-3">
        <label for="phone" class="form-label">Phone</label>
        <input type="text" name="phone" id="phone" class="form-control"
               value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="password2" class="form-label">Confirm Password</label>
        <input type="password" name="password_confirmation" id="password2" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Register</button>
</form>

<p class="mt-3">Already have an account? <a href="<?= url('/login')?>">Login here</a></p>

<?php include __DIR__ . '/layout/footer.php'; ?>
