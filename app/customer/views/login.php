<?php include __DIR__ . '/layout/header.php'; ?>

<div class="container d-flex justify-content-center align-items-center my-5">
    <div class="card shadow-lg rounded-4 w-100" style="max-width: 420px;">
        <div class="card-body p-4">
            <h2 class="text-center mb-4 fw-bold">🔐 Login</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= url('/login') ?>">
                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email address</label>
                    <input type="email" name="email" id="email" class="form-control"
                           placeholder="you@example.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <input type="password" name="password" id="password" class="form-control"
                           placeholder="Enter your password" required>
                </div>

                <!-- Remember Me -->
                <div class="mb-3 form-check">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input"
                        <?= isset($_POST['remember']) ? 'checked' : '' ?>>
                    <label for="remember" class="form-check-label">Remember me (10 days)</label>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Login</button>
            </form>

            <!-- Links -->
            <div class="text-center mt-3">
                <p class="mb-1">Don’t have an account?
                    <a href="<?= url('/register') ?>" class="fw-semibold">Sign up</a>
                </p>
                <a href="<?= url('/forgot-password') ?>" class="text-decoration-none small">Forgot password?</a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
