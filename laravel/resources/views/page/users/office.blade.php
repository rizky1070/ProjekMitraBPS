<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100italic,200,200italic,300,300italic,regular,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Inter:100,200,300,regular,500,600,700,800,900" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <title>Link SUPER TIM</title>
  </head>
  <body>
    <!--<div class="d-lg-none">
        <img src="{{ asset('assets/image/Logo BPS Baru 2.png') }}" class="d-block mx-auto" width="256" alt="">-->
    </div>
   <div style="display: flex; align-items: center;">
    <img src="{{ asset('assets/image/Logo BPS Baru 2.png') }}" width="256" alt="" style="margin-right: 20px;">
    <img src="{{ asset('assets/image/WBK.png') }}" width="70" height="70" alt="">
</div>

      <div class="container mt-5">
        <div class="row d-flex justify-content-center">
            {{-- <div class="d-sm-none d-md-none d-lg-block d-smi-none" style="margin-left: 500px; margin-top: -130px">
                <div class="" style="margin-top: -20px;"></div>
                @auth
                    @if (Auth::user()->roles == 'KETUA' || Auth::user()->roles == 'ADMIN')
                        <div class="seperti-itu text-center">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-danger">LogOut</button>
                            </form>
                            <form action="{{ route('links.index') }}" class="d-inline">
                                <button class="btn btn-primary">Menu Admin</button>
                            </form>
                            <form action="{{ route('link-user') }}" class="d-inline">
                                <button class="btn btn-primary">PRIBADI</button>
                            </form>
                            <form action="{{ route('link-ketua') }}" class="d-inline">
                                <button class="btn btn-primary">SEKRETARIAT</button>
                            </form>
                        </div>
                    @else
                        <div class="seperti-itu text-center">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-danger">LogOut</button>
                            </form>
                            <form action="{{ route('links.index') }}" class="d-inline">
                                <button class="btn btn-primary">Menu Admin</button>
                            </form>
                            <form action="{{ route('link-user') }}" class="d-inline">
                                <button class="btn btn-primary">PRIBADI</button>
                            </form>
                        </div>
                    @endif
                @else
                    <div class="seperti-itu text-center">
                        <a href="{{ route('login') }}" class="btn btn-primary">Log In</a>
                    </div>
                @endauth
            </div>
            <div class="d-lg-none" style="margin-top: -40px; margin-bottom: 70px">
                @auth
                    @if (Auth::user()->roles == 'KETUA' || Auth::user()->roles == 'ADMIN')
                        <div class="seperti-itu text-center">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-danger">LogOut</button>
                            </form>
                            <form action="{{ route('links.index') }}" class="d-inline">
                                <button class="btn btn-primary">Menu Admin</button>
                            </form>
                            <form action="{{ route('link-user') }}" class="d-inline">
                                <button class="btn btn-primary">PRIBADI</button>
                            </form>
                            <form action="{{ route('link-ketua') }}" class="d-inline">
                                <button class="btn btn-primary">SEKRETARIAT</button>
                            </form>
                        </div>
                    @else
                        <div class="seperti-itu text-center">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-danger">LogOut</button>
                            </form>
                            <form action="{{ route('links.index') }}" class="d-inline">
                                <button class="btn btn-primary">Menu Admin</button>
                            </form>
                            <form action="{{ route('link-user') }}" class="d-inline">
                                <button class="btn btn-primary">PRIBADI</button>
                            </form>
                        </div>
                    @endif
                @else
                         <div class="seperti-itu text-center">
                            <div style="margin-bottom: 45px;">
                            <a href="{{ route('login') }}" class="btn btn-primary">Log In</a>
                            </div>
                        <div>
                            <a href="https://kito.link3516.com" style="font-weight: bold; color: white;">Home</a>
                        </div>
                        </div>
                @endauth
            </div> --}}
            <nav class="ml-auto" style="margin-top: -140px; margin-right: 10px">
                <div class="logo"></div>
                <ul>
                    @auth
                        @if (Auth::user()->roles == 'ADMIN' || Auth::user()->roles == 'SEKRETARIAT')
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="btn btn-danger">LogOut</button>
                            </form>
                            <li><a href="{{ route('links.index') }}">Menu Admin</a></li>
                            <li><a href="{{ route('link-user') }}">PRIBADI</a></li>
                            <li><a href="{{ route('link-ketua') }}">SEKRETARIAT</a></li>
                        @else
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="btn btn-danger">LogOut</button>
                            </form>
                            <li><a href="{{ route('links.index') }}">Menu Admin</a></li>
                            <li><a href="{{ route('link-user') }}">PRIBADI</a></li>
                        @endif
                    @else
                       <div class="seperti-itu text-center">
                            <div style="margin-bottom: 45px;">
                            <a href="{{ route('login') }}" class="btn btn-primary">Log In</a>
                            </div>
                        <div>
                            <a href="https://kito.link3516.com" style="font-weight: bold; color: white;">Kanal 3516</a>
                        </div>
                        </div>
                    @endauth
                </ul>
                <div class="menu-toggle" onclick="toggle()">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </nav>
            <div class="col-md-12" style="margin-top: -70px">
               
                    <div class="mt-4"></div>
                    <div class="rounded-border-2 mx-auto">
                        <h1 class="text-center h1-text">SUPER TIM</h1>
                    </div>
              
            </div>
        </div>
        <div class="row d-flex justify-content-center">
            <div class="col-md-12">
                <div class="mt-5"></div>
                <div class="mt-2"></div>
                <div class="seperti-itu">
                    @forelse ($category as $cat)
                        @if ($cat->offices->count() != null)
                            <div class="text pt-4 pb-2">
                                <h1 class="text-h1 text-center">{{ $cat->name }}</h1>
                            </div>
                        @endif
                        @foreach ($cat->offices as $link)
                            @if ($link->name != null)
                                @if (strpos($link->link, 'http') === 0 || strpos($link->link, 'https') === 0)
                                    <a target="_blank" href="{{ $link->link }}" class="luweh-emboh emboh">
                                        <p class="text-emboh text-embohparah">{{ $link->name }}</p>
                                    </a>
                                @else
                                    <a target="_blank" href="https://{{ $link->link }}" class="luweh-emboh emboh">
                                        <p class="text-emboh text-embohparah">{{ $link->name }}</p>
                                    </a>
                                @endif
                            @else
                                @if (strpos($link->link, 'http') === 0 || strpos($link->link, 'https') === 0)
                                    <a target="_blank" href="{{ $link->link }}" class="luweh-emboh emboh">
                                        <p class="text-emboh text-embohparah">{{ $link->link }}</p>
                                    </a>
                                @else
                                    <a target="_blank" href="https://{{ $link->link }}" class="luweh-emboh emboh">
                                        <p class="text-emboh text-embohparah">{{ $link->link }}</p>
                                    </a>
                                @endif
                            @endif
                        @endforeach
                    @empty
                        <a class="luweh-emboh emboh">
                            <p class="text-emboh text-embohparah">User belum memiliki link</p>
                        </a>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="row d-flex justify-content-center">
            <div class="col-md-12">
                <div class="mt-5"></div>
                <div class="mt-2"></div>
            </div>
        </div>
         <footer class="sticky-footer">
            <div class="container my-auto">
            <div class="copyright text-center my-auto">
             <span style="color: white; font-size: smaller;">&copy; Tim Dilan 2022 - {{ date('Y') }}</span>
            </div>
            </div>
        </footer>
        <br>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF" crossorigin="anonymous"></script>
    <script>
        const nav = document.querySelector('nav ul');
        function toggle() {
            nav.classList.toggle('slide');
        }
    </script>
  </body>
</html>