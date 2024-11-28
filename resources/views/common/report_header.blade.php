<tr>
    <td class="w-full headers" style="border: unset !important;">
        <img height="80px" width="300px" src="{{ public_path('upload'.'/'.getSettingValue('company_logo')) }}" alt=""
             style="height: 150px;">
        <p> Address : {{ getSettingValue('company_address') }}</p>
        <p> Email : {{ getSettingValue('company_email') }}</p>
        <p> Phone : {{ getSettingValue('company_phone') }}</p>
        @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'today.requisitions.export')
            <p> User Name : {{ auth()->user()->name }}</p>
        @endif
    </td>
</tr>
