<!DOCTYPE html>

<html lang="en">

  <head>

     @include('includes.head')

  </head>

  <body>

    @include('includes.Header')

    <main class="container-lg" id="main">
      @yield('content')
    </main>

    <div id='loader-container'>
      <div class='loader'>
        <div></div>
        <div></div>
        <div></div>
      </div>
    </div>

    <script type="text/javascript" src="/js/app.js"></script>
    @yield('scripts')
  </body>

</html>