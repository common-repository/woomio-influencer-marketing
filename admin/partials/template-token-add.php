<div class="container-fluid">

    <div class="row">
        <div class="col">
            <h4 class="my-4"><?php echo __('Add Tokens', "woomio-for-woocommerce") ?></h4>
        </div>
    </div>

    <div class="row justify-content-between">

        <div class="col-md-8">
            <div class="pt-2">

                <div class="alert alert-success" role="alert" id="alertSuccess" style="display: none;"></div>
                <div class="alert alert-danger" role="alert" id="alertDanger" style="display: none;"></div>

                <form action="javascript:void(0)" id="form-token">

	                <?php if(isset($token['id'])): ?>
                        <input type="hidden" name="id" value="<?php echo intval($token['id']); ?>">
	                <?php endif; ?>

                    <div class="form-group mb-3">
                        <label for="campaign_name"><?php echo __('Campaign Name', "woomio-for-woocommerce") ?></label>
                        <input type="text" class="form-control" id="campaign_name" name="campaign_name" value="<?php echo esc_html($token['campaign_name']); ?>" maxlength="250" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="campaign_url"><?php echo __('Campaign URL', "woomio-for-woocommerce") ?></label>
                        <input type="url" class="form-control" id="campaign_url" name="campaign_url" value="<?php echo esc_url($token['campaign_url']); ?>" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="token"><?php echo __('Token', "woomio-for-woocommerce") ?></label>
                        <input type="text" class="form-control" id="token" name="name" value="<?php echo esc_html($token['name']); ?>" maxlength="250" required <?php if(isset($token['id'])) echo 'readonly'; ?>>
                    </div>

                    <div class="form-group mb-4">
                        <label for="expire_time"><?php echo __('Expire time (in days)', "woomio-for-woocommerce") ?></label>
                        <input type="number" class="form-control" id="expire_time" name="expire_time" value="<?php echo esc_html($token['expire_time']); ?>" required>
                    </div>

                    <div class="form-group mb-3">
                        <table class="table table-striped table-sm mb-4" id="tbl-token-form">
                            <thead>
                            <tr>
                                <th><input type="checkbox" id="wfwSelectAllCodes"></th>
                                <th><?php echo __('Coupon Code', 'woomio-for-woocommerce') ?></th>
                                <th><?php echo __('Coupon Type', 'woomio-for-woocommerce') ?></th>
                                <th><?php echo __('Description', 'woomio-for-woocommerce') ?></th>
                                <th><?php echo __('Expiry Date', 'woomio-for-woocommerce') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($coupons_codes as $id => $coupon): ?>
                            <tr>
                                <td><input type="checkbox" class="wfwCodeCheck" name="coupons[]" value="<?php echo $id; ?>" <?php if(in_array($id, $associated_coupon_ids)) echo 'checked'; ?>></td>
                                <td><?php echo esc_html($coupon['code']); ?></td>
                                <td><?php echo esc_html($coupon['code_type']); ?></td>
                                <td><?php echo esc_html($coupon['description']); ?></td>
                                <td><?php echo esc_html($coupon['date_expires']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <button type="submit" class="btn btn-outline-primary btn-block mt-5" id="btnSubmit">
                        <span class="btn-text text-uppercase" id="btnText"><?php echo __('Submit', "woomio-for-woocommerce") ?></span>
                        <div class="spinner-border text-success" role="status" id="loader" style="display: none;">
                            <span class="sr-only"><?php echo __('Loading', "woomio-for-woocommerce") ?></span>
                        </div>
                    </button>

                </form>
            </div>
        </div>

        <div class="col-md-4">
            <h4><?php echo __('Instruction', "woomio-for-woocommerce") ?></h4>
            <ol>
                <li>Go to <a href="https://measure.woomio.com/#/campaigns" target="_blank">Woomio campaign page</a> and select campaign</li>
                <li>Once campaign is selected, go to <strong>Coupons</strong> menu.</li>
                <li>On Coupon page, click on <strong>Get API Key</strong></li>
                <li>You will get your token from the popup window.</li>
                <li>Copy that token and add it here.</li>
            </ol>

            <?php if(empty($coupons_codes)): ?>
                <p><?php echo __('It looks like you do not have coupons to add.', "woomio-for-woocommerce") ?></p>
                <p><a class="btn btn-sm btn-outline-success" href="<?php echo site_url('wp-admin/post-new.php?post_type=shop_coupon'); ?>"><?php echo __('Add Coupon', "woomio-for-woocommerce") ?></a></p>
            <?php endif; ?>

        </div>

    </div>

</div>

