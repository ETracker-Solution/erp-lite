<tr>
    <td class="w-full headers" style="border: unset !important;">
        @if (file_exists(public_path('upload/'.getSettingValue('company_logo'))))
            <img height="80px" width="300px" src="{{ asset('upload/'.getSettingValue('company_logo')) }}" alt="Company Logo" style="height: 150px;">
        @endif

        <p> Address : {{ getSettingValue('company_address') }}</p>
        <p> Email : {{ getSettingValue('company_email') }}</p>
        <p> Phone : {{ getSettingValue('company_phone') }}</p>
        @if(\Illuminate\Support\Facades\Route::currentRouteName() == 'today.requisitions.export')
            <p> User Name : {{ auth()->user()->name }}</p>
        @endif
    </td>
</tr>
