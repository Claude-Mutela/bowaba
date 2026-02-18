<?php
/**
 * Admin Layout — Footer partial
 * Include at the bottom of every admin page.
 */
?>
  </main>
  <!-- End Page Content -->

  <!-- Footer -->
  <footer style="padding: 16px 28px; border-top: 1px solid var(--border-color); background: var(--card-bg); text-align: center;">
    <p style="margin:0; font-size:12px; color: var(--text-secondary);">
      &copy; <?= date('Y') ?> <strong style="color: var(--bbc-blue);">BowaBanCongo</strong> — Interface d'administration CMS
    </p>
  </footer>

</div><!-- End .admin-main -->

<!-- Bootstrap 5.3 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- TinyMCE — self-hosted local -->
<script src="<?= $adminBase ?? '../' ?>../assets/tinymce/tinymce.min.js"></script>
<!-- Admin JS -->
<script src="<?= $adminBase ?? '../' ?>assets/js/admin.js"></script>

<?php if (isset($extraScripts)): ?>
  <?= $extraScripts ?>
<?php endif; ?>

</body>
</html>
