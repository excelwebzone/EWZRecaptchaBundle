<?php if ($ewz_recaptcha_enabled): ?>
    <?php if !($ewz_recaptcha_ajax): ?>
        <?php if (isset($attr['options'])): ?>
            <script type="text/javascript">
            var RecaptchaOptions = <?php echo json_encode($attr['options']) ?>;
            </script>
        <?php endif ?>
        <script src="<?php echo $url_challenge ?>" type="text/javascript"></script>
        <noscript>
            <iframe src="<?php echo $url_noscript ?>" height="300" width="500"></iframe><br/>
            <textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
            <input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
        </noscript>
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
