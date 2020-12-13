<!DOCTYPE html>

<html lang="en">

  <head>

     @include('includes.head')

  </head>

  <body>

    @include('includes.header')

    <main class="container-lg" id="main">
      @yield('content')
    </main>


    <footer class="row">

        @include('includes.footer')

    </footer>

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