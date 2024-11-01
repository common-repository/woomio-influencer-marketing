<div class="container-fluid">

    <div class="row">
        <div class="col">
            <h3 class="my-4"><?php echo __('Sales record', "woomio-for-woocommerce") ?></h3>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="pt-2">
                <table class="table table-hover table-bordered" id="tbl-token-sales">
                    <thead>
                    <tr>
                        <th><?php echo __('Campaign', "woomio-for-woocommerce") ?></th>
                        <th><?php echo __('Token', "woomio-for-woocommerce") ?></th>
                        <th><?php echo __('Coupon', "woomio-for-woocommerce") ?></th>
                        <th><?php echo __('# of Sales', "woomio-for-woocommerce") ?></th>
                        <th><?php echo __('Amount', "woomio-for-woocommerce") ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($salesDetail as $token => $tokenSales): ?>
                        <?php foreach($tokenSales as $coupon => $currencySales): ?>
                            <?php foreach($currencySales as $currency => $sales): ?>
                            <?php
                                $tokenDetails = explode('||', $token);
                                $token_name = $tokenDetails[0] ?? '';
                                $campaign_name = $tokenDetails[1] ?? '';
                                $campaign_url = $tokenDetails[2] ?? '#';
                            ?>
                            <tr>
                                <td><a target="_blank" href="<?php echo esc_url($campaign_url); ?>"><?php echo esc_html($campaign_name); ?></a></td>
                                <td><span class="badge badge-light"><?php echo esc_html($token_name); ?></span></td>
                                <td><span class="badge badge-light"><?php echo esc_html(strtoupper($coupon)); ?></span></td>
                                <td><?php echo esc_html(count($sales)); ?></td>
                                <td><strong><?php echo esc_html($currency . " " . number_format(array_sum($sales), 2)); ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
