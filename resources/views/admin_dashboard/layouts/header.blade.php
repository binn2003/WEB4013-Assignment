<?php
use App\Models\Post;
use App\Models\Comment;
use App\Models\Tag;
$newComment = Comment::latest()
    ->take(5)
    ->get();
foreach ($newComment as $comment) {
    $posts_comments[] = Post::where('id', $comment->post_id)->get();
}

?>

<!--start header -->
<header>
    <div class="topbar d-flex align-items-center">
        <nav class="navbar navbar-expand">
            <div class="mobile-toggle-menu"><i class='bx bx-menu'></i>
            </div>
            {{-- <div class="search-bar flex-grow-1">
                        <div class="position-relative search-bar-box">
                            <input type="text" class="form-control search-control" placeholder="Nhập để tìm kiếm..."> <span class="position-absolute top-50 search-show translate-middle-y"><i class='bx bx-search'></i></span>
                            <span class="position-absolute top-50 search-close translate-middle-y"><i class='bx bx-x'></i></span>
                        </div>
                    </div> --}}
            <div class="top-menu ms-auto">
                <ul class="navbar-nav align-items-center">
                    {{-- <li class="nav-item mobile-search-icon">
                                <a class="nav-link" href="#">   <i class='bx bx-search'></i>
                                </a>
                            </li> --}}

                    {{-- <li class="nav-item dropdown dropdown-large">
                                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"> <i class='bx bx-category'></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <div class="row row-cols-3 g-3 p-3">
                                        <div class="col text-center">
                                            <div class="app-box mx-auto bg-gradient-cosmic text-white"><i class='bx bx-group'></i>
                                            </div>
                                            <div class="app-title">Teams</div>
                                        </div>
                                        <div class="col text-center">
                                            <div class="app-box mx-auto bg-gradient-burning text-white"><i class='bx bx-atom'></i>
                                            </div>
                                            <div class="app-title">Projects</div>
                                        </div>
                                        <div class="col text-center">
                                            <div class="app-box mx-auto bg-gradient-lush text-white"><i class='bx bx-shield'></i>
                                            </div>
                                            <div class="app-title">Tasks</div>
                                        </div>
                                        <div class="col text-center">
                                            <div class="app-box mx-auto bg-gradient-kyoto text-dark"><i class='bx bx-notification'></i>
                                            </div>
                                            <div class="app-title">Feeds</div>
                                        </div>
                                        <div class="col text-center">
                                            <div class="app-box mx-auto bg-gradient-blues text-dark"><i class='bx bx-file'></i>
                                            </div>
                                            <div class="app-title">Files</div>
                                        </div>
                                        <div class="col text-center">
                                            <div class="app-box mx-auto bg-gradient-moonlit text-white"><i class='bx bx-filter-alt'></i>
                                            </div>
                                            <div class="app-title">Alerts</div>
                                        </div>
                                    </div>
                                </div>
                            </li> --}}
                    <li class="nav-item dark-mode d-none d-sm-flex">
                        <a class="nav-link dark-mode-icon" href="javascript:;"><i class="bx bx-sun"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown dropdown-large">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false"> <span
                                class="alert-count">0</span>
                            <i class='bx bx-bell'></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:;">
                                <div class="msg-header">
                                    <p class="msg-header-title">Thông báo</p>
                                    <p class="msg-header-clear ms-auto">Đánh dấu tất cả đã đọc</p>
                                </div>
                            </a>
                            <div class="header-notifications-list">
                                {{-- <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="notify bg-light-primary text-primary"><i class="bx bx-group"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">New Customers<span class="msg-time float-end">14 Sec
                                                ago</span></h6>
                                                    <p class="msg-info">5 new user registered</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="notify bg-light-danger text-danger"><i class="bx bx-cart-alt"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">New Orders <span class="msg-time float-end">2 min
                                                ago</span></h6>
                                                    <p class="msg-info">You have recived new orders</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="notify bg-light-success text-success"><i class="bx bx-file"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">24 PDF File<span class="msg-time float-end">19 min
                                                ago</span></h6>
                                                    <p class="msg-info">The pdf files generated</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="notify bg-light-warning text-warning"><i class="bx bx-send"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">Time Response <span class="msg-time float-end">28 min
                                                ago</span></h6>
                                                    <p class="msg-info">5.1 min avarage time response</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="notify bg-light-info text-info"><i class="bx bx-home-circle"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">New Product Approved <span
                                                class="msg-time float-end">2 hrs ago</span></h6>
                                                    <p class="msg-info">Your new product has approved</p>
                                                </div>
                                            </div>
                                        </a> --}}
                                {{-- <a class="dropdown-item" href="javascript:;">
                                            <div class="d-flex align-items-center">
                                                <div class="notify bg-light-info text-info"><i class="bx bx-home-circle"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="msg-name">New Product Approved <span
                                                class="msg-time float-end">2 hrs ago</span></h6>
                                                    <p class="msg-info">Your new product has approved</p>
                                                </div>
                                            </div>
                                        </a> --}}

                            </div>
                            <a href="javascript:;">
                                <div class="text-center msg-footer">Xem tất cả thông báo</div>
                            </a>
                        </div>
                    </li>

                    <li class="nav-item dropdown dropdown-large">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false"> <span
                                class="alert-count">{{ $newComment->count() }}</span>
                            <i class='bx bx-comment'></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="{{ route('admin.comments.index') }}">
                                <div class="msg-header">
                                    <p class="msg-header-title">Tin nhắn bình luận</p>
                                    <p class="msg-header-clear ms-auto">Đánh dấu tất cả đã đọc</p>
                                </div>
                            </a>

                            <div class="header-message-list">
                                @for ($i = 0; $i < 5; $i++)
                                    <a class="dropdown-item" href="{{ route('posts.show', $posts_comments[$i][0]) }}">
                                        <div class="d-flex align-items-center">
                                            <div class="user-online">
                                                <img class="img_admn--user img-avatar " width="50" height="50"
                                                    style="border-radius: 50% ; margin: auto; background-size: cover ;  background-image: url({{ $posts_comments[$i][0]->comments()->orderBy('id', 'DESC')->take(1)->get()[0]->user->image? asset('storage/' .$posts_comments[$i][0]->comments()->orderBy('id', 'DESC')->take(1)->get()[0]->user->image->path): asset('storage/placeholders/user_placeholder.jpg') }})"
                                                    alt="">
                                            </div>
                                            <div style="margin-left: 10px;" class="flex-grow-1">
                                                <h6 class="msg-name">
                                                    {{ $posts_comments[$i][0]->comments()->orderBy('id', 'DESC')->take(1)->get()[0]->user->name }}<span
                                                        class="msg-time"> đã bình luận bài viết</span></h6>
                                                <h6 class="msg-name">{{ Str::limit($posts_comments[$i][0]->title, 32) }}
                                                </h6>
                                            </div>
                                        </div>
                                    </a>
                                @endfor

                            </div>
                            <a href="{{ route('admin.comments.index') }}">
                                <div class="text-center msg-footer">Xem tất cả</div>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="user-box dropdown">
                <a class="d-flex align-items-center nav-link dropdown-toggle dropdown-toggle-nocaret" href="#"
                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img class="img_admn--user img-avatar " width="50" height="50"
                        style="border-radius: 50% ; margin: auto; background-size: cover ;  background-image: url({{ auth()->user()->image ? asset('storage/' . auth()->user()->image->path) : asset('storage/placeholders/user_placeholder.jpg') }})"
                        alt="">
                    <div class="user-info ps-3">
                        <p class="user-name mb-0">{{ auth()->user()->name }}</p>
                        <p class="designattion mb-0">{{ auth()->user()->role->name }}</p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a target="_blank" class="dropdown-item" href="{{ route('home') }}"><i class="bx bx-home-alt"></i><span>Xem Website</span></a>
                    </li>
                    <li><a target="_blank" class="dropdown-item" href="{{ route('profile') }}"><i class="bx bx-user"></i><span>Tài khoản</span></a>
                    </li>
                    <li><a class="dropdown-item" href="javascript:;"><i class="bx bx-cog"></i><span>Cài đặt</span></a>
                    </li>
                    </li>
                    <li>
                        <div class="dropdown-divider mb-0"></div>
                    </li>

                    <li><a onclick="event.preventDefault(); document.getElementById('nav-logout-form').submit();"
                            class="dropdown-item"><i class='bx bx-log-out-circle'></i><span>Đăng xuất</span></a>
                        <form id="nav-logout-form" action="{{ route('logout') }}" method="POST">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>
<!--end header -->
