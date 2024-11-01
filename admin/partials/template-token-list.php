<div class="container-fluid">

    <div class="row">
        <div class="col-md-6">
            <h4 class="my-3"><?php echo __('Manage Tokens', "woomio-for-woocommerce") ?></h4>
        </div>
        <div class="col-md-6">
            <div class="my-3 text-right"><a href="<?php menu_page_url('woomio-for-woocommerce-add-token'); ?>" class="dashicons-before dashicons-plus-alt2 btn btn-sm btn-outline-success"> <?php echo __('Add Token', "woomio-for-woocommerce") ?></a></div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="pt-3">

                <div class="alert alert-success" role="alert" id="alertSuccess" style="display: none;"></div>
                <div class="alert alert-danger" role="alert" id="alertDanger" style="display: none;"></div>

                <div class="spinner-border text-success" role="status" id="loader" style="display: none;">
                    <span class="sr-only"><?php echo __('Loading...', "woomio-for-woocommerce") ?></span>
                </div>

                <table class="table table-hover table-bordered" id="tbl-token-list">
                    <thead>
                    <tr>
                        <th><?php echo __('Campaign', "woomio-for-woocommerce") ?></th>
                        <th><?php echo __('Token Key', "woomio-for-woocommerce") ?></th>
                        <th><?php echo __('# of Coupon', "woomio-for-woocommerce") ?></th>
                        <th><?php echo __('Expire (days)', "woomio-for-woocommerce") ?></th>
                        <th><?php echo __('Created', "woomio-for-woocommerce") ?></th>
                        <th><?php echo __('Action', "woomio-for-woocommerce") ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($tokens as $token): ?>
                    <tr>
                        <td><a target="_blank" href="<?php echo esc_url($token->campaign_url); ?>"><?php echo esc_html($token->campaign_name); ?></a></td>
                        <td><span class="badge badge-light"><?php echo esc_html($token->name); ?></span></td>
                        <td><?php echo esc_html($token->couponCount); ?></td>
                        <td><?php echo esc_html($token->expire_time); ?></td>
                        <td><?php echo esc_html(date("Y-m-d", strtotime($token->created))); ?></td>
                        <td>
                            <a href="<?php menu_page_url('woomio-for-woocommerce-add-token'); ?>&token_id=<?php echo intval($token->id) ?>&action=edit" class="dashicons-before dashicons-edit btn btn-sm btn-outline-primary"></a>
                            <button class="btn btn-sm btn-outline-danger btn-delete-token dashicons-before dashicons-trash" data-id="<?php echo intval($token->id); ?>"></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </div>

    </div>

</div>

