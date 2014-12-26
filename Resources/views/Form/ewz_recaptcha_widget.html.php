<?php if ($ewz_recaptcha_enabled): ?>
    <?php if !($ewz_recaptcha_ajax): ?>
        <script src="<?php echo $url_challenge ?>" type="text/javascript"></script>
        <div class="g-recaptcha" data-theme="<?php echo $attr['options']['theme']; ?>" data-type="<?php echo $attr['options']['type']; ?>" data-sitekey="<?php echo $public_key ?>"></div>
    <?php else ?>
        <div id="ewz_recaptcha_div"></div>

        <script type="text/javascript">
            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.onload = function() {
                Recaptcha.create('<?php echo $public_key ?>', 'ewz_recaptcha_div', <?php echo json_encode($attr['options']) ?>);
            }
            script.src = '<?php echo $url_api ?>';
            document.getElementsByTagName('head')[0].appendChild(script);
        </script>
    <?php endif ?>
<?php endif ?>
