
<div class="media">
    <div class="media-aside align-self-center">
        <a target="_blank" href="{{profileImagePath($row->photo_url)}}"
        class="b-avatar badge-light-info rounded-circle" target="_self" style="width: 32px; height: 32px;">
        <span class="b-avatar-img">
                <img src="{{profileImagePath($row->photo_url)}}"
                     style="width: 40px; height: 40px; border-radius: 50%;" class="">
            </span>
        </a>
    </div>
    <div class="media-body">
        <a href="{{route('user.users.show',encrypt($row->id))}}" class="font-weight-bold d-block text-nowrap" target="_self">
            {{((strlen($row->name))>13) ? substr($row->name,0,15).'...' : $row->name }} <span
                class="badge badge-pill {{ $row->type =='supervisor' ?'badge-light-success' : 'badge-light-secondary' }}">{{ strtoupper($row->type) }}</span>
        </a> <small class="text-muted"><span>@</span>{{$row->designation->name ?? ""}} <span class="text-info">({{ $row->employee_id ?? 'not set' }})</span></small>
    </div>
</div>
