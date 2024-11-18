<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.head')
    @stack('style')

</head>

<body>


    <div class="main-wrapper" id="app">
        @include('layouts.sidebar')
        <div class="page-wrapper">
          @include('layouts.navbar')
          <div class="page-content">
            @yield('main')
          </div>
        </div>
      </div>

    @include('layouts.script')
    @stack('script')
    @yield('additonal-script')
</body>
</html>
