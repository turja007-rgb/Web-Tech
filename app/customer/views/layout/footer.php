</div> <!-- container -->
</main>
<!-- ===== Footer ===== -->
<footer class="bg-light border-top mt-5">
    <div class="container py-3 text-center small text-muted">
        &copy; <?= date('Y') ?> Bakery. All rights reserved.
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<div id="cart-feedback" class="toast-container position-fixed top-0 end-0 p-4 mt-5"></div>
<!-- Centered Toast Container -->
<div id="toast-center"
     class="toast-container position-fixed top-50 start-50 translate-middle p-3"
     style="z-index: 2000;">
</div>
<script src="<?= url('/public/js/addCart.js') ?>"></script>
<script src="<?= url('/public/js/toast.js') ?>"></script>
<script src="<?= url('/public/js/profileUpdate.js') ?>"></script>
<script src="<?= url('/public/js/profileFieldController.js') ?>"></script>
</body>
</html>
