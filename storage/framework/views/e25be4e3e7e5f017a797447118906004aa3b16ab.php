<script>
    var bookingCore = {
        url:'<?php echo e(url( app_get_locale() )); ?>',
        url_root:'<?php echo e(url('')); ?>',
        admin_url:'<?php echo e(route('admin.index')); ?>',
        booking_decimals:<?php echo e((int)get_current_currency('currency_no_decimal',2)); ?>,
        thousand_separator:'<?php echo e(get_current_currency('currency_thousand')); ?>',
        decimal_separator:'<?php echo e(get_current_currency('currency_decimal')); ?>',
        currency_position:'<?php echo e(get_current_currency('currency_format')); ?>',
        currency_symbol:'<?php echo e(currency_symbol()); ?>',
        currency_rate:'<?php echo e(get_current_currency('rate',1)); ?>',
        date_format:'<?php echo e(get_moment_date_format()); ?>',
        map_provider:'<?php echo e(setting_item('map_provider')); ?>',
        map_gmap_key:'<?php echo e(setting_item('map_gmap_key')); ?>',
        map_options:{
            map_lat_default:'<?php echo e(setting_item('map_lat_default')); ?>',
            map_lng_default:'<?php echo e(setting_item('map_lng_default')); ?>',
            map_clustering:'<?php echo e(setting_item('map_clustering')); ?>',
            map_fit_bounds:'<?php echo e(setting_item('map_fit_bounds')); ?>',
        },
        routes:{
            login:'<?php echo e(route('login')); ?>',
            register:'<?php echo e(route('auth.register')); ?>',
            checkout:'<?php echo e(is_api() ? route('api.booking.doCheckout') : route('booking.doCheckout')); ?>'
        },
        module:{
            hotel:'<?php echo e(route('hotel.search')); ?>',
            car:'<?php echo e(route('car.search')); ?>',
            tour:'<?php echo e(route('tour.search')); ?>',
            space:'<?php echo e(route('space.search')); ?>',
            flight:"<?php echo e(route('flight.search')); ?>"
        },
        currentUser: <?php echo e((int)Auth::id()); ?>,
        isAdmin : <?php echo e(is_admin() ? 1 : 0); ?>,
        rtl: <?php echo e(setting_item_with_lang('enable_rtl') ? "1" : "0"); ?>,
        markAsRead:'<?php echo e(route('core.notification.markAsRead')); ?>',
        markAllAsRead:'<?php echo e(route('core.notification.markAllAsRead')); ?>',
        loadNotify : '<?php echo e(route('core.notification.loadNotify')); ?>',
        pusher_api_key : '<?php echo e(setting_item("pusher_api_key")); ?>',
        pusher_cluster : '<?php echo e(setting_item("pusher_cluster")); ?>',
        language: '<?php echo e(app()->getLocale()); ?>',
    };
    <?php if(auth()->user()): ?>
        bookingCore.media = {
        groups:<?php echo json_encode(config('bc.media.groups')); ?>,
    }
    <?php endif; ?>
    var i18n = {
        warning:"<?php echo e(__("Warning")); ?>",
        success:"<?php echo e(__("Success")); ?>",
        confirm_delete:"<?php echo e(__("Do you want to delete?")); ?>",
        confirm:"<?php echo e(__("Confirm")); ?>",
        cancel:"<?php echo e(__("Cancel")); ?>",
    };
    var daterangepickerLocale = {
        "applyLabel": "<?php echo e(__('Apply')); ?>",
        "cancelLabel": "<?php echo e(__('Cancel')); ?>",
        "fromLabel": "<?php echo e(__('From')); ?>",
        "toLabel": "<?php echo e(__('To')); ?>",
        "customRangeLabel": "<?php echo e(__('Custom')); ?>",
        "weekLabel": "<?php echo e(__('W')); ?>",
        "first_day_of_week": <?php echo e(setting_item("site_first_day_of_the_weekin_calendar","1")); ?>,
        "daysOfWeek": [
            "<?php echo e(__('Su')); ?>",
            "<?php echo e(__('Mo')); ?>",
            "<?php echo e(__('Tu')); ?>",
            "<?php echo e(__('We')); ?>",
            "<?php echo e(__('Th')); ?>",
            "<?php echo e(__('Fr')); ?>",
            "<?php echo e(__('Sa')); ?>"
        ],
        "monthNames": [
            "<?php echo e(__('January')); ?>",
            "<?php echo e(__('February')); ?>",
            "<?php echo e(__('March')); ?>",
            "<?php echo e(__('April')); ?>",
            "<?php echo e(__('May')); ?>",
            "<?php echo e(__('June')); ?>",
            "<?php echo e(__('July')); ?>",
            "<?php echo e(__('August')); ?>",
            "<?php echo e(__('September')); ?>",
            "<?php echo e(__('October')); ?>",
            "<?php echo e(__('November')); ?>",
            "<?php echo e(__('December')); ?>"
        ],
    };
</script>
<?php /**PATH /var/www/testing.jamaicataxi.com/htdocs/modules/Layout/parts/global-script.blade.php ENDPATH**/ ?>