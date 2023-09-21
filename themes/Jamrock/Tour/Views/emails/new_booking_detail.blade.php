<?php
$translation = $service->translateOrOrigin(app()->getLocale());
$lang_local = app()->getLocale();
$meta = $booking->getJsonMeta('booking_tour_information');
?>
<div class="b-panel-title">{{$booking->object_model == 'tour' ? __('Tour Booking information') : __('Event Booking information')}}</div>
<div class="b-table-wrap">
    <table class="b-table" cellspacing="0" cellpadding="0">
        <tr>
            <td class="label">{{__('Booking Number')}}</td>
            <td class="val">#{{$booking->id}}</td>
        </tr>
        <tr>
            <td class="label">{{__('Booking Date | Status')}}</td>
            <td class="val"> {{display_datetime($booking->created_at)}} | {{$booking->statusName}}</td>
        </tr>
        @if($booking->gatewayObj)
            <tr>
                <td class="label">{{__('Payment method')}}</td>
                <td class="val">{{$booking->gatewayObj->getOption('name')}}</td>
            </tr>
        @endif
        @if($booking->gatewayObj and $note = $booking->gatewayObj->getOption('payment_note'))
            <tr>
                <td class="label">{{__('Payment Note')}}</td>
                <td class="val">{!! clean($note) !!}</td>
            </tr>
        @endif
        <tr>
            <td class="label">{{$booking->object_model == 'tour' ? __('Tour name') : __('Event name') }}</td>
            <td class="val">
                <a href="{{$service->getDetailUrl()}}">{!! clean($translation->title) !!}</a>
            </td>
        </tr>
        <tr>
            <td class="label">{{__('Pickup Address')}}</td>
            <td class="val">
                {{$meta['from_address'] ?? ''}}
            </td>
        </tr>
        <tr>
            @if($translation->address)
                <td class="label">{{$booking->object_model == 'tour'  ? __('Tour Address') : __('Event Address')}}</td>
                <td class="val">
                    {{$translation->address}}
                </td>
            @endif
        </tr>
        <tr>
            <td class="label">{{__('Pickup Date')}}</td>
            <td class="val">
                {{ isset($meta['pickup_date']['date']) ? display_datetime($meta['pickup_date']['date']) : '' }}
            </td>
        </tr>
        @if(!empty($meta['gg_distance']))
            <tr>
                <td class="label">{{__('Travel Info')}}</td>
                <td class="val">
                    {{$meta['gg_distance']['text'] ?? ''}} | {{$meta['gg_duration']['text'] ?? ''}}
                </td>
            </tr>
        @endif
        <tr>
            <td class="label">{{__('Duration:')}}</td>
            <td class="val">
                {{$service->duration_text ?? ''}}
            </td>
        </tr>
        <tr>
            <td class="label">{{__('Guest')}}</td>
            <td class="val">
                {{$meta['passenger'] ?? 0}}
            </td>
        </tr>
        <tr>
            <td class="label">{{$booking->object_model == 'tour' ? __('Tour Price') : __("Event Price")}}</td>
            <td class="val">
                {{format_money($booking->total_before_extra_price)}}
            </td>
        </tr>

        <tr>
            <td class="label">{{__('Extras')}}</td>
            <td class="val">
            </td>
        </tr>
        @php $extra_price = $booking->getJsonMeta('extra_price')@endphp

        @if(!empty($extra_price))
            @foreach($extra_price as $type)
                <tr>
                    <td class="label" style="font-weight:normal">{{$type['name_'.$lang_local] ?? $type['name']}}: {{format_money($type['price'])}} x {{!empty($type['per_person']) ? $booking->number : 1}}</td>
                    <td class="val no-r-padding">
                        {{format_money($type['total'] ?? 0)}}
                    </td>
                </tr>
            @endforeach
        @endif
        @includeIf('Booking::frontend/booking/extra-detail-email')

        @php
            $list_all_fee = [];
            if(!empty($booking->buyer_fees)){
                $buyer_fees = json_decode($booking->buyer_fees , true);
                $list_all_fee = $buyer_fees;
            }
            if(!empty($vendor_service_fee = $booking->vendor_service_fee)){
                $list_all_fee = array_merge($list_all_fee , $vendor_service_fee);
            }
        @endphp
        @if(!empty($list_all_fee))
            @foreach ($list_all_fee as $item)
                @php
                    $fee_price = $item['price'];
                    if(!empty($item['unit']) and $item['unit'] == "percent"){
                        $fee_price = ( $booking->total_before_fees / 100 ) * $item['price'];
                    }
                @endphp
                <tr>
                    <td class="label">
                        {{$item['name_'.$lang_local] ?? $item['name']}}
                        <i class="icofont-info-circle" data-toggle="tooltip" data-placement="top" title="{{ $item['desc_'.$lang_local] ?? $item['desc'] }}"></i>
                        @if(!empty($item['per_person']) and $item['per_person'] == "on")
                            : {{$booking->total_guests}} * {{format_money( $fee_price )}}
                        @endif
                    </td>
                    <td class="val">
                        @if(!empty($item['per_person']) and $item['per_person'] == "on")
                            {{ format_money( $fee_price * $booking->total_guests ) }}
                        @else
                            {{ format_money( $fee_price ) }}
                        @endif
                    </td>
                </tr>
            @endforeach
        @endif

        <tr>
            <td class="label fsz21">{{__('Total')}}</td>
            <td class="val fsz21"><strong style="color: #FA5636">{{format_money($booking->total)}}</strong></td>
        </tr>
        <tr>
            <td class="label fsz21">{{__('Paid')}}</td>
            <td class="val fsz21"><strong style="color: #FA5636">{{format_money($booking->paid)}}</strong></td>
        </tr>
        @if($booking->total > $booking->paid)
            <tr>
                <td class="label fsz21">{{__('Remain')}}</td>
                <td class="val fsz21"><strong style="color: #FA5636">{{format_money($booking->total - $booking->paid)}}</strong></td>
            </tr>
        @endif
    </table>
</div>
<div class="text-center mt20">
    <a href="{{ route("user.booking_history") }}" target="_blank" class="btn btn-primary manage-booking-btn">{{__('Manage Bookings')}}</a>
</div>
