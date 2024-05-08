<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 breadcrumb-component">
            <div class="col-sm-6">
                <h1>{{__($title)}}</h1>
            </div>
            <div class="col-sm-6" style="align-self: center">
                <ol class="breadcrumb float-sm-right">
                    @foreach($links as $linkName=>$route)
                        @if($route== null)
                            <li class="breadcrumb-item">
                                {{ $linkName }}
                            </li>
                        @else
                            <li class="breadcrumb-item">
                                <a href="{{ $route }}">{{ $linkName }}</a>
                            </li>
                        @endif
                    @endforeach
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
