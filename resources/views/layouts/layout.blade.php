<!DOCTYPE html>

<html lang="en" translate="no" class="notranslate">

  <head>

     @include('includes.head')

  </head>

  <body>

    @include('includes.Header')

    <main class="container-lg pb-5" id="main">
      @yield('content')
    </main>

    <div id='loader-container'>
      <div class='loader'>
        <div></div>
        <div></div>
        <div></div>
      </div>
    </div>

    @yield('scripts')
  </body>

</html>
