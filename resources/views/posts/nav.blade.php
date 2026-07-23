@vite(['resources/css/app.css', 'resources/js/app.js'])
@include('CDNfiles.animateCss')
  @include('CDNfiles.bootsrapIconCDN')


<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid d-flex align-items-center justify-content-between">
    
    {{-- Left Section: Welcome and Toggle --}}
    <div class="d-flex align-items-center">
      @auth
        <div class="ms-3 animate__animated animate__bounceIn animate__fadeInLeft">
          <span class="fw-bold text-primary text-capitalize">Welcome, {{ auth()->user()->name }}!</span>
        </div>
      @endauth
      <div class="vr fw-bold ms-3" style="height: 40px;"></div>
    </div>

    {{-- Mobile Toggle + Dark Mode Toggle Icon --}}
    <div class="d-flex align-items-center">
      
      <!-- Dark Mode Icon: Always Visible -->
      <div class="me-3">
        @include('CDNfiles.darkLightIcon')
      </div>

      <!-- Bootstrap Hamburger -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
              aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </div>

  {{-- Collapsible Section --}}
  <div class="collapse navbar-collapse col-7" id="navbarNavAltMarkup">
    <div class="navbar-nav text-capitalize ms-3">
      <a class="nav-link active" href="{{ route('show.post') }}">Community</a>
      <a class="nav-link active" href="{{ route('user.myprofile') }}">My Profile</a>
      <a class="nav-link" href="{{ route('createData') }}">Create Post</a>
      <a class="nav-link" href="{{ route('chat.index') }}">Chat</a>

      <!-- Tutorials Dropdown -->
      
      <!-- Mobile Logout -->
      <form class="d-block d-lg-none ms-2" action="{{ route('logout') }}" method="get">
        @csrf
        <button class="btn btn-primary">Logout</button>
      </form>
    </div>
  </div>

  <!-- Desktop Logout -->
  <form class="d-none d-lg-block me-4" action="{{ route('logout') }}" method="get">
    @csrf
    <button class="btn btn-primary">Logout</button>
  </form>
</nav>

 
<nav class="navbar navbar-expand-lg bg-dark">
  <!-- Your existing navbar code remains the same until the Tutorials section -->
  
  <!-- Replace the Tutorials Dropdown with this: -->
  <div class="nav-scroller-container position-relative">
    <!-- Left scroll button -->
    <button class="scroll-button left-scroll hidden" aria-label="Scroll left">
        <i class="bi bi-chevron-left"></i>
    </button>

    <!-- Scrollable nav -->
    <div class="nav-scroller-tutorials" id="tutorialScroller">
        <nav class="nav">
            <a class="nav-link {{ request()->routeIs('tutorial.html') ? 'active fw-bold' : '' }}" href="{{ route('tutorial.html') }}">HTML</a>
            <a class="nav-link {{ request()->routeIs('tutorial.css') ? 'active fw-bold' : '' }}" href="{{ route('tutorial.css') }}">CSS</a>
            <a class="nav-link {{ request()->routeIs('tutorial.js') ? 'active fw-bold' : '' }}" href="{{ route('tutorial.js') }}">JavaScript</a>
            <a class="nav-link {{ request()->routeIs('tutorial.php') ? 'active fw-bold' : '' }}" href="{{ route('tutorial.php') }}">PHP</a>
            <a class="nav-link {{ request()->routeIs('tutorial.laravel') ? 'active fw-bold' : '' }}" href="{{ route('tutorial.laravel') }}">Laravel</a>
            <a class="nav-link {{ request()->routeIs('tutorial.vue') ? 'active fw-bold' : '' }}" href="{{ route('tutorial.vue') }}">Vue.JS</a>
            <a class="nav-link {{ request()->routeIs('tutorial.react') ? 'active fw-bold' : '' }}" href="{{ route('tutorial.react') }}">React.JS</a>
            <a class="nav-link {{ request()->routeIs('tutorial.python') ? 'active fw-bold' : '' }}" href="{{ route('tutorial.python') }}">Python</a>
            <a class="nav-link {{ request()->routeIs('tutorial.java') ? 'active fw-bold' : '' }}" href="{{ route('tutorial.java') }}">Java</a>
            <a class="nav-link {{ request()->routeIs('tutorial.csharp') ? 'active fw-bold' : '' }}" href="{{ route('tutorial.csharp') }}">C#</a>
             <a class="nav-link {{ request()->routeIs('tutorial.cpp') ? 'active fw-bold' : '' }}" href="{{ route('tutorial.cpp') }}">C++</a>
            <a class="nav-link {{ request()->routeIs('tutorial.ruby') ? 'active fw-bold' : '' }}" href="{{ route('tutorial.ruby') }}">Ruby</a>
            <a class="nav-link {{ request()->routeIs('tutorial.mysql') ? 'active fw-bold' : '' }}" href="{{ route('tutorial.mysql') }}">MySQL</a>
<a class="nav-link {{ request()->routeIs('tutorial.jquery') ? 'active fw-bold' : '' }}" href="{{ route('tutorial.jquery') }}">jQuery</a>

 </nav>
    </div>
          

       

    <!-- Right scroll button -->
    <button class="scroll-button right-scroll" aria-label="Scroll right">
        <i class="bi bi-chevron-right"></i>
    </button>
</div>

  <!-- Rest of your navbar code remains the same -->
</nav>

    
@include('CDNfiles.horizontalScroll')
@include('CDNfiles.dark&light')