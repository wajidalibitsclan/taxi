<div class="form-group">
    <label>{{__("Title")}} <span class="text-danger"> *</span></label>
    <input type="text" required value="{{old('title',$translation->title)}}" placeholder="{{__("title")}}" name="title" class="form-control">
</div>

<div class="form-group">
    <label>{{__("Value")}} <span class="text-danger"> *</span></label>
    <input type="text" required value="{{old('value',$translation->value)}}" placeholder="{{__("value")}}" name="value" class="form-control">
</div>

