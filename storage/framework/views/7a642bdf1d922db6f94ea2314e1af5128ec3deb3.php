<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar"><?php echo e(__('All Bookings')); ?></h1>
        </div>
        <?php echo $__env->make('admin.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="filter-div d-flex justify-content-between">
            <div class="col-left">
                <?php if(!empty($booking_update)): ?>
                    <form method="post" action="<?php echo e(route('report.admin.booking.bulkEdit')); ?>" class="filter-form filter-form-left d-flex justify-content-start">
                        <?php echo csrf_field(); ?>
                        <select name="action" class="form-control">
                            <option value=""><?php echo e(__("-- Bulk Actions --")); ?></option>
                            <?php if(!empty($statues)): ?>
                                <?php $__currentLoopData = $statues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($status); ?>"><?php echo e(__('Mark as: :name',['name'=>booking_status_to_text($status)])); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                            <option value="delete"><?php echo e(__("DELETE booking")); ?></option>
                        </select>
                        <button data-confirm="<?php echo e(__("Do you want to delete?")); ?>" class="btn-info btn btn-icon dungdt-apply-form-btn" type="button"><?php echo e(__('Apply')); ?></button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="col-left">
                <form method="get" action="" class="filter-form filter-form-right d-flex justify-content-end">
                    <select name="object_model" class="form-control">
                        <option value="">[Service Type]</option>
                        <?php $__currentLoopData = get_bookable_services(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id=>$model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(!$model::isEnable()) continue; ?>
                            <option value="<?php echo e($id); ?>" <?php if($id == request('object_model')): ?> selected <?php endif; ?>><?php echo e(ucfirst($id)); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <select name="status" class="form-control">
                        <option value="">[Status]</option>
                        <?php $__currentLoopData = $statues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($status); ?>" <?php if($status == request('status')): ?> selected <?php endif; ?>><?php echo e(booking_status_to_text($status)); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php if(!empty($booking_manage_others)): ?>
                        <?php
                        $user = !empty(Request()->vendor_id) ? App\User::find(Request()->vendor_id) : false;
                        \App\Helpers\AdminForm::select2('vendor_id', [
                            'configs' => [
                                'ajax'        => [
                                    'url'      => route('user.admin.getForSelect2'),
                                    'dataType' => 'json'
                                ],
                                'allowClear'  => true,
                                'placeholder' => __('-- Vendor --')
                            ]
                        ], !empty($user->id) ? [
                            $user->id,
                            $user->name_or_email . ' (#' . $user->id . ')'
                        ] : false)
                        ?>
                    <?php endif; ?>
                    <input type="text" name="s" value="<?php echo e(Request()->s); ?>" placeholder="<?php echo e(__('Search by name or ID')); ?>" class="form-control">
                    <button class="btn-info btn btn-icon" type="submit"><?php echo e(__('Filter')); ?></button>
                </form>
            </div>
        </div>
        <div class="text-right">
            <p><i><?php echo e(__('Found :total items',['total'=>$rows->total()])); ?></i></p>
        </div>
        <div class="panel booking-history-manager">
            <div class="panel-title"><?php echo e(__('Bookings')); ?></div>
            <div class="panel-body">
                <form action="" class="bravo-form-item">
                    <table class="table table-hover bravo-list-item">
                        <thead>
                        <tr>
                            <th width="80px"><input type="checkbox" class="check-all"></th>
                            <th><?php echo e(__('Service')); ?></th>
                            <th><?php echo e(__('Type')); ?></th>
                            <th><?php echo e(__('Customer')); ?></th>
                            <th><?php echo e(__('From')); ?></th>
                            <th><?php echo e(__('To')); ?></th>
                            <th><?php echo e(__('Pickup Date')); ?></th>
                            <th><?php echo e(__('Return Date')); ?></th>
                            <th><?php echo e(__('Payment Information')); ?></th>
                            <!-- <th  width="80px" ><?php echo e(__('Commission')); ?></th> -->
                            <th width="80px"><?php echo e(__('Status')); ?></th>
                            <th width="150px"><?php echo e(__('Payment Method')); ?></th>
                            <th width="120px"><?php echo e(__('Created At')); ?></th>
                            <th width="80px">Flight #</th>
                            <th width="80px">Pass & Bags</th>
                            <th width="80px"><?php echo e(__('Actions')); ?></th>
                            
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php  $booking = $row; ?>
                            <tr>
                                <td><input type="checkbox" class="check-item" name="ids[]" value="<?php echo e($row->id); ?>">
                                    #<?php echo e($row->id); ?></td>
                                <td>
                                    <?php if($service = $row->service): ?>
                                        <a href="<?php echo e($service->getDetailUrl()); ?>" target="_blank"><?php echo e($service->title ?? ''); ?></a>
                                        <?php if($row->vendor): ?>
                                            <br>
                                            <span><?php echo e(__('by')); ?></span>
                                            <a href="<?php echo e(route('user.admin.detail',['id'=>$row->vendor_id])); ?>"
                                               target="_blank"><?php echo e($row->vendor->name_or_email.' (#'.$row->vendor_id.')'); ?></a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php echo e(__("[Deleted]")); ?>

                                    <?php endif; ?>
                                </td>
                                
                                <td><?php echo e(ucfirst($row->object_model)); ?></td>
                                <td>
                                    <ul>
                                        <li><?php echo e(__("Name:")); ?> <?php echo e($row->first_name); ?> <?php echo e($row->last_name); ?> </li>
                                        <li><?php echo e(__("Email:")); ?> <?php echo e($row->email); ?></li>
                                        <li><?php echo e(__("Phone:")); ?> <?php echo e($row->phone); ?></li>
                                        <li><?php echo e(__("Country:")); ?> <?php echo e($row->country); ?></li>
                                        <li><?php echo e(__("Custom Requirement:")); ?> <?php echo e($row->customer_notes); ?></li>
                                    </ul>
                                </td>

                                <?php
                                    $objectModel = $row->object_model;
                                    if ($objectModel === 'tour') {
                                        $meta = $booking->getJsonMeta('booking_tour_information');
                                    } elseif ($objectModel === 'car') {
                                        $meta = $booking->getJsonMeta('booking_car_information');
                                    }
                                ?>
                                <td> <?php echo e($meta['from_address'] ?? ''); ?></td>
                                <td> <?php echo e($meta['to_address'] ?? ''); ?></td>
                                <td><?php echo e(isset($meta['pickup_date']['date']) ? display_datetime($meta['pickup_date']['date']) : ''); ?></td>
                                <td> <?php echo e(isset($meta['return_date']['date']) ? display_datetime($meta['return_date']['date']) : ''); ?></td>


                            <?php

                                $extra_price = $booking->getJsonMeta('extra_price');
                                $total_extra = 0;
                            ?>

                            <?php if(!empty($extra_price)): ?>
                                <?php $__currentLoopData = $extra_price; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(isset($type['total'])): ?>
                                    <?php
                                        $total_extra =    ($total_extra +  $type['total'])
                                    ?>
                                <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>

                                <td>

                                    <?php echo e(__("Service Charge")); ?> : <?php echo e(format_money($booking->total_before_extra_price)); ?><br/>
                                    <?php echo e(__("Extras")); ?> : <?php echo e($total_extra); ?><br/>
                                    <?php echo e(__("Total")); ?> : <?php echo e(format_money_main($row->total)); ?><br/>
                                    <?php echo e(__("Paid")); ?> : <?php echo e(format_money_main($row->paid)); ?><br/>
                                    <?php echo e(__("Remain")); ?> : <?php echo e(format_money_main($booking->total - $booking->paid)); ?><br/>
                                </td>
                                <!-- <td>
                                    <?php echo e(format_money_main($booking->commission)); ?>

                                </td> -->
                                <td>
                                    <span class="label label-<?php echo e($row->status); ?>"><?php echo e($row->statusName); ?></span>
                                </td>
                                <td>
                                    <?php echo e($row->gatewayObj ? $row->gatewayObj->getDisplayName() : ''); ?>

                                </td>
                                <td><?php echo e(display_datetime($row->updated_at)); ?></td>
                                <td> <?php echo e($meta['pickup_flight'] ?? ''); ?></td>
                                <td> <?php echo e($meta['passenger'] ?? ''); ?> | <?php echo e($meta['baggage'] ?? ''); ?></td>
                                <td>
                                    <?php if($service = $row->service): ?>
                                        <div class="dropdown">
                                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo e(__('Actions')); ?>

                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item" href="#modal_booking_detail" data-ajax="<?php echo e(route('booking.modal',['booking'=>$booking])); ?>" data-toggle="modal" data-id="<?php echo e($booking->id); ?>" data-target="#modal_booking_detail"><?php echo e(__('Detail')); ?></a>
                                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal-paid-<?php echo e($row->id); ?>"><?php echo e(__('Set Paid')); ?></a>
                                                <a class="dropdown-item" href="<?php echo e(route('report.admin.booking.email_preview',['id'=>$row->id])); ?>"><?php echo e(__('Email Preview')); ?></a>
                                            </div>
                                        </div>
                                        <?php echo $__env->make($service->set_paid_modal_file ?? '', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    <?php endif; ?>
                                </td>
                               
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </form>

                <div class="modal" tabindex="-1" id="modal_booking_detail">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?php echo e(__('Booking ID: #')); ?> <span class="user_id"></span></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="d-flex justify-content-center"><?php echo e(__("Loading...")); ?></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(__('Close')); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <?php echo e($rows->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('js'); ?>
    <script>
        $(document).on('click', '#set_paid_btn', function (e) {
            var id = $(this).data('id');
            $.ajax({
                url:bookingCore.url+'/booking/setPaidAmount',
                data:{
                    id: id,
                    remain: $('#modal-paid-'+id+' #set_paid_input').val(),
                },
                dataType:'json',
                type:'post',
                success:function(res){
                    alert(res.message);
                    window.location.reload();
                }
            });
        });
        $('#modal_booking_detail').on('show.bs.modal',function (e){
            var btn = $(e.relatedTarget);
            $(this).find('.user_id').html(btn.data('id'));
            $(this).find('.modal-body').html('<div class="d-flex justify-content-center"><?php echo e(__("Loading...")); ?></div>');
            var modal = $(this);
            $.get(btn.data('ajax'), function (html){
                    modal.find('.modal-body').html(html);
                }
            )
        })
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/saeeccmd/taxi.saeedantechnology.com/modules/Report/Views/admin/booking/index.blade.php ENDPATH**/ ?>