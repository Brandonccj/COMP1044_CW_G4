</div> <footer class="site-footer">
        &copy; <?php echo date("Y"); ?> Internship Result Management System. All rights reserved.
    </footer>

    <?php 
    $js_path = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || strpos($_SERVER['PHP_SELF'], '/assessor/') !== false) ? '../assets/script.js?v=' . time() : 'assets/script.js?v=' . time(); 
    ?>
    <script src="<?php echo $js_path; ?>"></script>
</body>
</html>