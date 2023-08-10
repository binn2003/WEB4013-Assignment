@extends('admin_dashboard.layouts.app')

@section('style')
    <style>
        .img_admn--user.img-avatar {
            border-radius: 50%;
        }
    </style>
@endsection

@section('wrapper')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Tài khoản</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Tất cả tài khoản</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="card">
                <div class="card-body">
                    <div class="container p-3 row justify-between">
                        <div class="row">
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger" id="deleteAllSelectedRecord">Xoá chọn</button>
                            </div>
                            <form action="{{ route('admin.users.index') }}" class="col-md-8">
                                <div class="form-group row">

                                    <div class="col-md-9">
                                        <input type="text" class="form-control" value="{{ $search }}"
                                            name="search" placeholder="Tìm kiếm tài khoản (họ tên/email/quyền)">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                                    </div>
                                </div>
                            </form>
                            <div class="col-md-2"><a href="{{ route('admin.users.create') }}"
                                    class="btn btn-success radius-30 mt-2 mt-lg-0"><i class="bx bxs-plus-square"></i>Thêm
                                    mới</a></div>
                        </div>
                    </div>
                    <div class="table-responsive border">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>
                                        <input class="form-check-input me-3" type="checkbox" value=""
                                            id="select_all_ids">
                                    </th>
                                    <th>Mã Tài khoản</th>
                                    <th>Ảnh đại diện</th>
                                    <th>Họ Tên</th>
                                    <th>Email</th>
                                    <th>Quyền</th>
                                    <th>Thông tin</th>
                                    <th>Ngày tạo</th>
                                    <th>Chức năng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr id="user_ids{{ $user->user_id }}">
                                        <td>
                                            <div>
                                                <input class="checkbox_ids form-check-input me-3" type="checkbox" name="ids" id=""
                                                value="{{ $user->user_id }}" aria-label="...">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="ms-2">
                                                    <h6 class="mb-0 font-14">ID-{{ $user->user_id }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <img class="img_admn--user img-avatar" width="60" height="60"
                                                style="margin: auto; background-size: cover ;  background-image: url({{ !empty($user->user_image) ? asset('storage/' . $user->user_image) : asset('storage/placeholders/user_placeholder.jpg') }})"
                                                alt="">
                                        </td>
                                        <td>{{ $user->user_name }}</td>
                                        <td>{{ $user->user_email }}</td>
                                        <td>{{ $user->role->name }}</td>
                                        @if ($user->role->name != 'user')
                                            <td>
                                                <a class="btn btn-primary btn-sm"
                                                    href="{{ route('admin.users.show', ['user' => $user->user_id]) }}">Bài
                                                    viết</a>
                                            </td>
                                        @else
                                            <td>
                                            </td>
                                        @endif
                                        <td>{{ date('d/m/Y', strtotime($user->user_created_at)) }}</td>

                                        <td>
                                            <div class="d-flex order-actions">
                                                <a href="{{ route('admin.users.edit', ['user' => $user->user_id]) }}"
                                                    class=""><i class='bx bxs-edit'></i></a>


                                                <a href="#"
                                                    data-url="{{ route('admin.users.destroy', ['user' => $user->user_id]) }}"
                                                    data-id="{{ $user->user_id }}" class="ms-3 btn-del"><i
                                                        class='bx bxs-trash text-danger'></i>
                                                </a>
                                                {{-- <a href="#"
                                                    onclick="event.preventDefault(); document.querySelector('#delete_form_{{ $user->user_id }}').submit();"
                                                    class="ms-3"><i class='bx bxs-trash text-danger'></i></a> --}}
                                                {{-- <a href="#" data-url="{{ route('admin.users.destroy', $user) }}"
                                                    data-id="{{ $user->id }}" class="ms-3 btn-del"><i
                                                        class='bx bxs-trash text-danger'></i>
                                                </a> --}}
                                                <form method="post"
                                                    action="{{ route('admin.users.destroy', ['user' => $user->user_id]) }}"
                                                    id="delete_form_{{ $user->user_id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        @if (!empty($user))
                            <div style="text-align: right; margin-right: 3%">Tổng số {{ $user->count() }} tài khoản</div>
                        @endif
                        {{ $users->links() }}
                    </div>
                </div>
            </div>


        </div>
    </div>
    <!--end page wrapper -->
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            setTimeout(() => {
                $(".general-message").fadeOut();
            }, 5000);

        });

        $(function(e) {
            //selectAll
            $("#select_all_ids").click(function() {
                $('.checkbox_ids').prop('checked', $(this).prop('checked'));
            })

            //deleteSelectAll
            $("#deleteAllSelectedRecord").click(function(e) {
                let message;
                if (confirm("Bạn muốn xoá các mục đã chọn?") == true) {
                    e.preventDefault();
                    var all_ids = [];
                    $('input:checkbox[name=ids]:checked').each(function() {
                        all_ids.push($(this).val());
                    })
                    $.ajax({
                        url: "{{ route('admin.users.deleteAll') }}",
                        type: "DELETE",
                        data: {
                            ids: all_ids,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $.each(all_ids, function(key, val) {
                                $('#user_ids' + val).remove();
                            })
                        }
                    });
                    // message = "Xoá các mục đã chọn thành công";
                    alert('Xoá các mục đã chọn thành công');

                }
            });
        });
    </script>
@endsection
