<div>
    {{$row->created_at ? getTimeByFormat($row->created_at,'d M Y, h:i:s a') : ''}}
</div>