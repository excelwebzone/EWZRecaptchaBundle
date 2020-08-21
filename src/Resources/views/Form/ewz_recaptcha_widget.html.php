<?php if ($ewz_recaptcha_enabled): ?>
    <?php if (!$ewz_recaptcha_ajax): ?>
        <?php if ($attr['options']['size'] == 'invisible' && !isset($attr['options']['callback'])): ?>
            <?php $attr['options']['callback'] = 'onReCaptchaSuccess' ?>

            <script>
                var onReCaptchaSuccess = function() {
                    var errorDivs = document.getElementsByClassName('recaptcha-error');
                    if (errorDivs.length) {
                        errorDivs[0].className = '';
                    }

                    var errorMsgs = document.getElementsByClassName('recaptcha-error-message');
                    if (errorMsgs.length) {
                        errorMsgs[0].parentNode.removeChild(errorMsgs[0]);
                    }

                    var forms = document.getElementsByClassName('recaptcha-form');
                    if (forms.length) {
                        var recaptchaSubmitEvent = document.createEvent('Event');
                        recaptchaSubmitEvent.initEvent('submit', true, true);
                        forms[0].addEventListener('submit', function (e) {
                            e.target.submit();
                        }, false);
                        forms[0].dispatchEvent(recaptchaSubmitEvent);
                    }
                };
            </script>
        <?php endif ?>

        <script src="<?php echo $url_challenge ?>"
            <?php if (isset($attr['options']['defer']) && $attr['options']['defer']): ?> defer<?php endif ?>
            <?php if (isset($attr['options']['async']) && $attr['options']['async']): ?> async<?php endif ?>
        ></script>
        <div class="g-recaptcha" data-theme="<?php echo $attr['options']['theme'] ?>" data-size="<?php echo $attr['options']['size'] ?>" data-type="<?php echo $attr['options']['type'] ?>" data-sitekey="<?php echo $public_key ?>"
            <?php if (isset($attr['options']['callback'])): ?>data-callback="<?php echo $attr['options']['callback'] ?>"<?php endif ?>
            <?php if (isset($attr['options']['expiredCallback'])): ?>data-expired-callback="<?php echo $attr['options']['expiredCallback'] ?>"<?php endif ?>
            <?php if (isset($attr['options']['bind'])): ?>data-bind="<?php echo $attr['options']['bind'] ?>"<?php endif ?>
            <?php if (isset($attr['options']['badge'])): ?>data-badge="<?php echo $attr['options']['badge'] ?>"<?php endif ?>
        ></div>
        <noscript>
            <div style="width: 302px; height: 352px;">
                <div style="width: 302px; height: 352px; position: relative;">
                    <div style="width: 302px; height: 352px; position: absolute;">
                        <iframe src="https://<?php echo $ewz_recaptcha_apihost ?>/recaptcha/api/fallback?k=<?php echo $public_key ?>"
                                style="width: 302px; height:352px; border-style: none; overflow: hidden;"
                        >
                        </iframe>
                    </div>
                    <div style="width: 250px; height: 80px; position: absolute; border-style: none; bottom: 21px; left: 25px; margin: 0; padding: 0; right: 25px;">
                        <textarea id="g-recaptcha-response" name="g-recaptcha-response"
                                  class="g-recaptcha-response"
                                  style="width: 250px; height: 80px; border: 1px solid #c1c1c1; margin: 0; padding: 0; resize: none;"
                        >
                        </textarea>
                    </div>
                </div>
            </div>
        </noscript>
    <?php else: ?>
        <div id="ewz_recaptcha_div"></div>

        <script>
        (function() {
            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.onload = function() {
                Recaptcha.create('<?php echo $public_key ?>', 'ewz_recaptcha_div', <?php echo json_encode($attr['options']) ?>);
            };
            script.src = '<?php echo $url_api ?>';
            <?php if (isset($attr['options']['defer']) && $attr['options']['defer']): ?>script.defer = true;<?php endif ?>
            <?php if (isset($attr['options']['async']) && $attr['options']['async']): ?>script.async = true;<?php endif ?>
            document.getElementsByTagName('head')[0].appendChild(script);
        })();
        </script>
    <?php endif ?>
<?php endif ?>
