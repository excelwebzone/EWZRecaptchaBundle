<?php if ($ewz_recaptcha_enabled): ?>
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
<?php endif ?>
